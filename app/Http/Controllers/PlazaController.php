<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Local;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class PlazaController extends Controller
{
    /**
     * Plaza Principal: mostrar todos los locales, productos y categorías
     */
    public function index(Request $request)
    {
        // Obtener categorías únicas de los productos disponibles
        $categorias = Product::where('status', 'Available')
            ->distinct()
            ->pluck('category')
            ->filter() // Remover valores null
            ->values()
            ->map(function ($category) {
                return [
                    'nombre' => $category,
                    'slug' => Str::slug($category),
                    'icono' => $this->getCategoryIcon($category),
                ];
            })
            ->toArray();

        // Obtener productos disponibles
        $productos = Product::where('status', 'Available')
            ->with('locals')
            ->get()
            ->map(function ($product) {
                $product->category_slug = Str::slug($product->category);
                return $product;
            });

        // Filtrar por categoría si se proporciona
        if ($request->has('categoria') && $request->categoria !== 'todos') {
            $categoria = $request->categoria;
            $productos = $productos->filter(function ($product) use ($categoria) {
                return $product->category_slug === $categoria;
            })->values();
        }

        // Obtener locales activos con sus productos disponibles
        $locales = Local::where('status', 'Active')
            ->with(['gallery' => function ($query) {
                $query->limit(1);
            }])
            ->get();

        // Obtener 2 productos aleatorios de cada local
        $productosAleatorios = collect();
        foreach ($locales as $local) {
            $productosLocal = $local->products()
                ->where('tbproduct.status', 'Available')
                ->inRandomOrder()
                ->limit(2)
                ->get();
            $productosAleatorios = $productosAleatorios->merge($productosLocal);
        }

        // Obtener estadísticas
        $stats = [
            'total_locales' => $locales->count(),
            'total_productos' => $productos->count(),
            'horario_apertura' => '10:00',
            'horario_cierre' => '22:00',
            'calificacion' => '4.8',
        ];

        return view('plaza.index', [
            'locales' => $locales,
            'productos' => $productosAleatorios,
            'categorias' => $categorias,
            'stats' => $stats,
            'categoria_actual' => $request->categoria ?? 'todos',
        ]);
    }

    /**
     * Vista detallada de un local con sus productos
     */
    public function show($id)
    {
        // Buscar local por su primary key (local_id)
        $local = Local::where('local_id', $id)->firstOrFail();

        // Obtener productos de este local
        $productos = Product::whereHas('locals', function ($query) use ($id) {
            $query->where('tblocal_product.local_id', $id);
        })
            ->where('status', 'Available')
            ->with('gallery')
            ->get();

        // Extraer categorías únicas de los productos de este local
        $categorias = $productos->pluck('category')
            ->unique()
            ->filter()
            ->map(function ($category) {
                return [
                    'nombre' => $category,
                    'slug' => Str::slug($category),
                    'icono' => $this->getCategoryIcon($category),
                ];
            });

        return view('plaza.show', [
            'local' => $local,
            'productos' => $productos,
            'categorias' => $categorias,
        ]);
    }

    /**
     * Obtener productos filtrados por categoría (AJAX)
     */
    public function getProductosByCategory(Request $request)
    {
        $categoria = $request->query('categoria', 'todos');

        // Obtener productos disponibles con sus locales
        $productosQuery = Product::where('status', 'Available')
            ->with(['locals' => function ($query) {
                $query->select('tblocal.local_id', 'tblocal.name');
            }]);

        // Filtrar por categoría si no es 'todos'
        if ($categoria !== 'todos') {
            $productosQuery->whereRaw("LOWER(REPLACE(REPLACE(`category`, ' ', ''), '-', '')) = ?", 
                [strtolower(str_replace([' ', '-'], '', $categoria))]
            );
        }

        $productos = $productosQuery->get();

        // Mapear datos para retornar en JSON
        $productosFormateados = $productos->map(function ($product) {
            $localFirst = $product->locals->first();
            return [
                'id' => $product->product_id ?? $product->id,
                'name' => $product->name,
                'price' => number_format($product->price ?? 0, 2),
                'photo_url' => $product->photo_url ?? asset('images/product-placeholder.jpg'),
                'category' => $product->category ?? 'Sin categoría',
                'local' => $localFirst?->name ?? 'Local desconocido',
                'local_id' => $localFirst?->local_id ?? null,
            ];
        })->values();

        return response()->json([
            'success' => true,
            'data' => $productosFormateados,
            'total' => $productosFormateados->count(),
            'categoria' => $categoria,
        ]);
    }

    /**
     * Búsqueda dinámica de locales y productos (AJAX)
     */
    public function search(Request $request)
    {
        $query = $request->query('q', '');
        
        // Validar que la búsqueda tenga al menos 1 carácter
        if (strlen($query) < 1) {
            return response()->json([
                'success' => true,
                'data' => ['locales' => [], 'productos' => []],
            ]);
        }

        // Buscar locales por nombre o descripción (case-insensitive)
        $locales = Local::where(function ($q) use ($query) {
            $q->whereRaw("LOWER(name) LIKE ?", ['%' . strtolower($query) . '%'])
              ->orWhereRaw("LOWER(description) LIKE ?", ['%' . strtolower($query) . '%']);
        })
        ->where('status', 'Active')
        ->limit(5)
        ->get()
        ->map(function ($local) {
            return [
                'id' => $local->local_id,
                'name' => $local->name,
                'photo_url' => $local->logo_url ?? asset('images/local-placeholder.jpg'),
                'rating' => $local->average_rating ?? '4.5',
                'route' => route('plaza.show', $local->local_id),
            ];
        });

        // Buscar productos por nombre o categoría (case-insensitive)
        $productos = Product::where(function ($q) use ($query) {
            $q->whereRaw("LOWER(name) LIKE ?", ['%' . strtolower($query) . '%'])
              ->orWhereRaw("LOWER(category) LIKE ?", ['%' . strtolower($query) . '%']);
        })
        ->where('status', 'Available')
        ->with(['locals' => function ($query) {
            $query->select('tblocal.local_id', 'tblocal.name');
        }])
        ->limit(5)
        ->get()
        ->map(function ($product) {
            $localFirst = $product->locals->first();
            return [
                'id' => $product->product_id ?? $product->id,
                'name' => $product->name,
                'price' => number_format($product->price ?? 0, 2),
                'photo_url' => $product->photo_url ?? asset('images/product-placeholder.jpg'),
                'category' => $product->category ?? 'Sin categoría',
                'local' => $localFirst?->name ?? 'Local',
                'local_id' => $localFirst?->local_id ?? null,
            ];
        });

        return response()->json([
            'success' => true,
            'data' => [
                'locales' => $locales->values(),
                'productos' => $productos->values(),
            ],
        ]);
    }

    /**
     * Retornar icono según categoría
     */
    private function getCategoryIcon($category)
    {
        $icons = [
            'hamburguesería' => 'fa-burger',
            'pizza' => 'fa-pizza-slice',
            'sushi' => 'fa-fish',
            'postres' => 'fa-cake-candles',
            'bebidas' => 'fa-glass-water',
            'comida rápida' => 'fa-fire',
            'ensaladas' => 'fa-leaf',
            'sandwich' => 'fa-sandwich',
        ];

        $categoryLower = Str::slug($category);
        foreach ($icons as $key => $icon) {
            if (str_contains($categoryLower, Str::slug($key))) {
                return $icon;
            }
        }

        return 'fa-utensils'; // Default icon
    }
}
