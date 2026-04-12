<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Local;
use App\Models\Schedule;
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
            ->with([
                'locals',
                'productReviews.review' => function ($query) {
                    $query->select('review_id', 'rating');
                }
            ])
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

        // Obtener horarios para hoy y cachear resultados para evitar N+1 queries
        $now = now();
        $dayOfWeek = $now->translatedFormat('l');
        $dayTranslation = [
            'Monday' => 'Lunes', 'Tuesday' => 'Martes', 'Wednesday' => 'Miércoles',
            'Thursday' => 'Jueves', 'Friday' => 'Viernes', 'Saturday' => 'Sábado', 'Sunday' => 'Domingo',
        ];
        $dayInSpanish = $dayTranslation[$dayOfWeek] ?? null;
        
        // Obtener schedules de hoy para todos los locales en UNA sola query (sin toArray)
        $schedulesByLocal = [];
        if ($dayInSpanish) {
            $schedulesData = Schedule::whereIn('local_id', $locales->pluck('local_id'))
                ->where('day_of_week', $dayInSpanish)
                ->where('status', true)
                ->get();
            
            foreach ($schedulesData as $schedule) {
                $schedulesByLocal[$schedule->local_id] = $schedule;
            }
        }
        
        // Asignar estados de apertura sin hacer más queries
        $currentTime = $now->format('H:i:s');
        $locales = $locales->map(function ($local) use ($schedulesByLocal, $currentTime) {
            $local->isOpenNow = false;
            
            if (isset($schedulesByLocal[$local->local_id])) {
                $schedule = $schedulesByLocal[$local->local_id];
                $openingTime = $schedule->opening_time ? $schedule->opening_time->format('H:i:s') : null;
                $closingTime = $schedule->closing_time ? $schedule->closing_time->format('H:i:s') : null;
                
                if ($openingTime && $closingTime) {
                    $local->isOpenNow = $currentTime >= $openingTime && $currentTime < $closingTime;
                }
            }
            return $local;
        });

        // Obtener productos aleatorios: simplificar a una query única en lugar de bucle
        $productosAleatorios = Product::where('status', 'Available')
            ->with([
                'locals' => function ($query) {
                    $query->select('tblocal.local_id', 'tblocal.name');
                },
                'productReviews.review' => function ($query) {
                    $query->select('review_id', 'rating');
                }
            ])
            ->inRandomOrder()
            ->limit(10)
            ->get();

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

        // Obtener productos de este local con eager loading de reseñas
        $productos = Product::whereHas('locals', function ($query) use ($id) {
            $query->where('tblocal_product.local_id', $id);
        })
            ->where('status', 'Available')
            ->with([
                'gallery',
                'productReviews.review' => function ($query) {
                    $query->select('review_id', 'rating');
                }
            ])
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

        // Obtener horario del día actual
        $now = now();
        $dayOfWeek = $now->translatedFormat('l');
        
        $dayTranslation = [
            'Monday' => 'Lunes',
            'Tuesday' => 'Martes',
            'Wednesday' => 'Miércoles',
            'Thursday' => 'Jueves',
            'Friday' => 'Viernes',
            'Saturday' => 'Sábado',
            'Sunday' => 'Domingo',
        ];

        $dayInSpanish = $dayTranslation[$dayOfWeek] ?? null;

        $horarioHoy = null;
        $estaAbierto = false;
        
        if ($dayInSpanish) {
            $horarioHoy = Schedule::where('local_id', $id)
                ->where('day_of_week', $dayInSpanish)
                ->first();
            
            // Usar el método del modelo para verificar si está abierto
            $estaAbierto = Schedule::isCurrentlyOpen($id);
        }

        return view('plaza.show', [
            'local' => $local,
            'productos' => $productos,
            'categorias' => $categorias,
            'horarioHoy' => $horarioHoy,
            'diaActual' => $dayInSpanish,
            'estaAbierto' => $estaAbierto,
        ]);
    }

    /**
     * Obtener productos filtrados por categoría (AJAX)
     */
    public function getProductosByCategory(Request $request)
    {
        $categoria = $request->query('categoria', 'todos');

        // Obtener productos disponibles con sus locales y reseñas
        $productosQuery = Product::where('status', 'Available')
            ->with([
                'locals' => function ($query) {
                    $query->select('tblocal.local_id', 'tblocal.name');
                },
                'productReviews.review' => function ($query) {
                    $query->select('review_id', 'rating');
                }
            ]);

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
            
            // Calcular rating sin hacer queries adicionales
            $reviews = $product->productReviews;
            if ($reviews->isNotEmpty()) {
                $totalRating = $reviews->sum(function ($productReview) {
                    return $productReview->review->rating ?? 0;
                });
                $averageRating = round($totalRating / $reviews->count());
            } else {
                $averageRating = 0;
            }
            
            return [
                'id' => $product->product_id ?? $product->id,
                'name' => $product->name,
                'price' => number_format($product->price ?? 0, 2),
                'photo_url' => $product->photo_url ?? asset('images/product-placeholder.png'),
                'category' => $product->category ?? 'Sin categoría',
                'local' => $localFirst?->name ?? 'Local desconocido',
                'local_id' => $localFirst?->local_id ?? null,
                'average_rating' => $averageRating,
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

    /**
     * Agregar producto al carrito (sesión)
     */
    public function addToCart(Request $request)
    {
        $validated = $request->validate([
            'product_id' => 'required|integer|exists:tbproduct,product_id',
            'local_id' => 'required|integer|exists:tblocal,local_id',
            'quantity' => 'required|integer|min:1',
            'customization' => 'nullable|string|max:500',
            'customer_name' => 'required|string|max:255',
            'customer_email' => 'required|email|max:255',
            'customer_phone' => 'required|string|max:20',
            'delivery_address' => 'nullable|string|max:500',
            'additional_notes' => 'nullable|string|max:500',
        ]);

        // Obtener producto para validar que existe en el local
        $product = Product::where('product_id', $validated['product_id'])
            ->whereHas('locals', function ($query) use ($validated) {
                $query->where('tblocal.local_id', $validated['local_id']);
            })
            ->first();

        if (!$product) {
            return response()->json([
                'success' => false,
                'message' => 'Producto no disponible en este local'
            ], 422);
        }

        // Obtener carrito actual de sesión
        $cart = session()->get('cart', []);

        // Crear identificador único para el item basado en producto y customización
        $itemKey = $product->product_id . '_' . md5($validated['customization'] ?? '');

        // Verificar si el item ya existe en el carrito
        $existingItem = null;
        foreach ($cart as $key => $item) {
            if ($item['item_key'] === $itemKey && $item['local_id'] === $validated['local_id']) {
                $existingItem = $key;
                break;
            }
        }

        // Si existe, incrementar cantidad; si no, agregar nuevo item
        if ($existingItem !== null) {
            $cart[$existingItem]['quantity'] += $validated['quantity'];
        } else {
            $newItem = [
                'item_key' => $itemKey,
                'product_id' => $product->product_id,
                'local_id' => $validated['local_id'],
                'name' => $product->name,
                'price' => $product->price,
                'quantity' => $validated['quantity'],
                'customization' => $validated['customization'] ?? '',
                'photo_url' => $product->photo_url ?? asset('images/product-placeholder.png'),
                'added_at' => now()->toIso8601String(),
                // Datos del cliente
                'customer_name' => $validated['customer_name'],
                'customer_email' => $validated['customer_email'],
                'customer_phone' => $validated['customer_phone'],
                'delivery_address' => $validated['delivery_address'] ?? '',
                'additional_notes' => $validated['additional_notes'] ?? '',
            ];
            $cart[] = $newItem;
        }

        // Guardar carrito en sesión
        session()->put('cart', $cart);

        return response()->json([
            'success' => true,
            'message' => $existingItem !== null ? 'Cantidad actualizada en el carrito' : 'Producto agregado al carrito',
            'cart_count' => count($cart),
            'cart' => $cart,
        ]);
    }

    public function viewCart()
    {
        $cart = session()->get('cart', []);
        return view('plaza.carrito.view', compact('cart'));
    }
}
