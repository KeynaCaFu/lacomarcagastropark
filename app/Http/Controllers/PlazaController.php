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
            });

        // Obtener productos disponibles
        $productos = Product::where('status', 'Available')
            ->with('locals')
            ->get();

        // Filtrar por categoría si se proporciona
        if ($request->has('categoria') && $request->categoria !== 'todos') {
            $categoria = $request->categoria;
            $productos = $productos->filter(function ($product) use ($categoria) {
                return Str::slug($product->category) === $categoria;
            })->values();
        }

        // Shuffle productos para mostrar de forma aleatoria
        $productosAleatorios = $productos->shuffle()->take(20);

        // Obtener locales activos con sus productos disponibles
        $locales = Local::where('status', 'Active')
            ->with(['gallery' => function ($query) {
                $query->limit(1);
            }])
            ->get()
            ->map(function ($local) {
                // Contar productos disponibles de este local
                $productosCount = Product::whereHas('locals', function ($query) use ($local) {
                    $query->where('tblocal_product.local_id', $local->local_id)
                        ->where('tblocal_product.is_available', true);
                })->where('status', 'Available')->count();

                return [
                    'id' => $local->local_id,
                    'nombre' => $local->name,
                    'descripcion' => $local->description ?? 'Explora nuestro menú',
                    'estado' => $local->status === 'Active' ? 'abierto' : 'cerrado',
                    'imagen' => $local->image_logo ?? 'https://via.placeholder.com/400x300?text=' . urlencode($local->name),
                    'calificacion' => '4.8',
                    'tiempo_entrega' => '20-30 min',
                    'productos_count' => $productosCount,
                ];
            });

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
