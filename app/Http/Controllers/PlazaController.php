<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Local;
use App\Models\Schedule;
use App\Models\Event;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Cache;
use App\Helpers\PlazaHelper;

class PlazaController extends Controller
{
    /**
     * Plaza Principal: mostrar todos los locales, productos y categorías
     * Optimizado con caché y selects específicos
     */
    public function index(Request $request)
    {
        // Obtener categorías únicas - cacheadas por 1 hora
        $categorias = Cache::remember(
            'plaza:all_categories',
            60, // 1 hora
            function () {
                $cats = Product::availableCategories()->pluck('category');
                return PlazaHelper::formatCategories($cats);
            }
        );
        $categorias = collect($categorias); // Convertir a Collection

        // Obtener productos disponibles con scope optimizado
        $productos = Product::forPlaza()
            ->active()
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
        // Solo traer columnas necesarias
        $locales = Local::select('local_id', 'name', 'status', 'image_logo')
            ->where('status', 'Active')
            ->get();

        // Obtener horarios para hoy - optimizado con scope
        $now = now();
        $dayOfWeek = $now->translatedFormat('l');
        $dayInSpanish = PlazaHelper::translateDayToSpanish($dayOfWeek);
        
        // Usar scope optimizado: una sola query para todos los locales
        $schedulesByLocal = [];
        if ($dayInSpanish) {
            $schedulesByLocal = Schedule::todayForLocals($locales->pluck('local_id')->toArray())
                ->get()
                ->keyBy('local_id');
        }
        
        // Asignar estados de apertura sin hacer más queries
        $currentTime = $now->format('H:i:s');
        $locales = $locales->map(function ($local) use ($schedulesByLocal, $currentTime) {
            $local->isOpenNow = false;
            
            if (isset($schedulesByLocal[$local->local_id])) {
                $schedule = $schedulesByLocal[$local->local_id];
                $openingTime = $schedule->opening_time ? \Carbon\Carbon::parse($schedule->opening_time)->format('H:i:s') : null;
                $closingTime = $schedule->closing_time ? \Carbon\Carbon::parse($schedule->closing_time)->format('H:i:s') : null;
                
                if ($openingTime && $closingTime) {
                    $local->isOpenNow = $currentTime >= $openingTime && $currentTime < $closingTime;
                }
            }
            return $local;
        });

        // Obtener productos aleatorios con scopes optimizados
        $productosAleatorios = Product::forPlaza()
            ->active()
            ->inRandomOrder()
            ->limit(10)
            ->get();

        // Calcular calificación global eficientemente
        $calificacionGlobal = 0;
        if ($locales->isNotEmpty()) {
            $totalRating = $locales->sum(function ($local) {
                return $local->average_rating;
            });
            $calificacionGlobal = round($totalRating / $locales->count(), 1);
        }

        // Obtener estadísticas
        $stats = [
            'total_locales' => $locales->count(),
            'total_productos' => $productos->count(),
            'horario_apertura' => '10:00',
            'horario_cierre' => '22:00',
            'calificacion' => $calificacionGlobal,
        ];

        // Obtener eventos activos de manera eficiente
        // Eventos de hoy y próximos (próximos 30 días sin límite de cantidad)
        $today = now()->toDateString();
        $oneMonthLater = now()->addDays(30)->toDateString();

        $eventosHoy = Event::select('event_id', 'title', 'description', 'start_at', 'location', 'image_url')
            ->active()
            ->whereDate('start_at', $today)
            ->orderBy('start_at', 'asc')
            ->get();

        $eventosProximos = Event::select('event_id', 'title', 'description', 'start_at', 'location', 'image_url')
            ->active()
            ->whereDate('start_at', '>', $today)
            ->whereDate('start_at', '<=', $oneMonthLater)
            ->orderBy('start_at', 'asc')
            ->get();

        return view('plaza.index', [
            'locales' => $locales,
            'productos' => $productosAleatorios,
            'categorias' => $categorias,
            'stats' => $stats,
            'categoria_actual' => $request->categoria ?? 'todos',
            'eventosHoy' => $eventosHoy,
            'eventosProximos' => $eventosProximos,
        ]);
    }

    /**
     * Vista detallada de un local con sus productos
     */
    public function show($id)
    {
        // Buscar local por su primary key - optimizado con selects
        $local = Local::select('local_id', 'name', 'status', 'description', 'contact', 'image_logo')
            ->where('local_id', $id)
            ->firstOrFail();

        // Obtener productos con scope optimizado
        $productos = Product::forPlaza()
            ->active()
            ->byLocal($id)
            ->with(['gallery' => function ($query) {
                $query->select('product_gallery_id', 'product_id', 'image_url');
            }])
            ->get();

        // Extraer categorías únicas de los productos de este local
        $categoriasData = $productos->pluck('category')
            ->unique()
            ->filter();
        
        $categorias = collect(PlazaHelper::formatCategories($categoriasData));

        // Obtener horario del día actual
        $now = now();
        $dayOfWeek = $now->translatedFormat('l');
        $dayInSpanish = PlazaHelper::translateDayToSpanish($dayOfWeek);

        $horarioHoy = null;
        $estaAbierto = false;
        
        if ($dayInSpanish) {
            $horarioHoy = Schedule::where('local_id', $id)
                ->where('day_of_week', $dayInSpanish)
                ->first();
            
            // Usar el método del modelo para verificar si está abierto
            $estaAbierto = Schedule::isCurrentlyOpen($id);
        }

        // Obtener todos los locales activos para el combobox
        $localesDisponibles = Local::select('local_id', 'name', 'status')
            ->where('status', 'Active')
            ->orderBy('name', 'asc')
            ->get();

        // Obtener eventos activos
        $today = now()->toDateString();
        $oneMonthLater = now()->addDays(30)->toDateString();

        $eventosHoy = Event::select('event_id', 'title', 'description', 'start_at', 'location', 'image_url')
            ->active()
            ->whereDate('start_at', $today)
            ->orderBy('start_at', 'asc')
            ->get();

        $eventosProximos = Event::select('event_id', 'title', 'description', 'start_at', 'location', 'image_url')
            ->active()
            ->whereDate('start_at', '>', $today)
            ->whereDate('start_at', '<=', $oneMonthLater)
            ->orderBy('start_at', 'asc')
            ->get();

        return view('plaza.show', [
            'local' => $local,
            'productos' => $productos,
            'categorias' => $categorias,
            'horarioHoy' => $horarioHoy,
            'diaActual' => $dayInSpanish,
            'estaAbierto' => $estaAbierto,
            'localesDisponibles' => $localesDisponibles,
            'eventosHoy' => $eventosHoy,
            'eventosProximos' => $eventosProximos,
        ]);
    }

    /**
     * Obtener datos del local (productos + categorías) en JSON para cambio sin refrescamiento
     * Optimizado con eager loading para evitar N+1 queries
     */
    public function getLocalData($id)
    {
        try {
            // Buscar local
            $local = Local::select('local_id', 'name', 'status', 'description', 'contact', 'image_logo')
                ->where('local_id', $id)
                ->firstOrFail();

            // Obtener productos del local con eager loading de relaciones
            $productos = Product::forPlaza()
                ->active()
                ->byLocal($id)
                ->with([
                    'gallery' => function ($query) {
                        $query->select('product_gallery_id', 'product_id', 'image_url');
                    },
                    'productReviews.review' => function ($query) {
                        $query->select('review_id', 'rating');
                    }
                ])
                ->get();

            // Extraer categorías únicas
            $categoriasData = $productos->pluck('category')
                ->unique()
                ->filter();
            
            $categorias = collect(PlazaHelper::formatCategories($categoriasData));

            // Obtener horario
            $now = now();
            $dayOfWeek = $now->translatedFormat('l');
            $dayInSpanish = PlazaHelper::translateDayToSpanish($dayOfWeek);

            $horarioHoy = null;
            $estaAbierto = false;
            
            if ($dayInSpanish) {
                $horarioHoy = Schedule::where('local_id', $id)
                    ->where('day_of_week', $dayInSpanish)
                    ->first();
                
                $estaAbierto = Schedule::isCurrentlyOpen($id);
            }

            // Formatear productos para JSON
            $productosFormateados = $productos->map(function ($product) {
                // Calcular rating promedio
                $reviews = $product->productReviews;
                $averageRating = 0;
                if ($reviews->isNotEmpty()) {
                    $totalRating = 0;
                    $count = 0;
                    
                    foreach ($reviews as $productReview) {
                        if ($productReview->review && isset($productReview->review->rating)) {
                            $totalRating += $productReview->review->rating;
                            $count++;
                        }
                    }
                    
                    $averageRating = $count > 0 ? round($totalRating / $count) : 0;
                }

                return [
                    'product_id' => $product->product_id,
                    'name' => $product->name,
                    'description' => $product->description,
                    'category' => $product->category,
                    'photo_url' => $product->photo_url ?? asset('images/product-placeholder.png'),
                    'price' => $product->price,
                    'average_rating' => $averageRating,
                ];
            })->values();

            return response()->json([
                'success' => true,
                'local' => [
                    'local_id' => $local->local_id,
                    'name' => $local->name,
                    'description' => $local->description,
                    'logo_url' => $local->logo_url,
                ],
                'productos' => $productosFormateados,
                'categorias' => $categorias->values(),
                'horarioHoy' => $horarioHoy,
                'diaActual' => $dayInSpanish,
                'estaAbierto' => $estaAbierto,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'No se pudo cargar el local',
                'error' => $e->getMessage()
            ], 404);
        }
    }

    /**
     * Obtener productos filtrados por categoría (AJAX)
     * Optimizado: scopes + aggregate ratings en BD + paginación
     */
    public function getProductosByCategory(Request $request)
    {
        $categoria = $request->query('categoria', 'todos');
        $page = $request->query('page', 1);
        $perPage = 12; // Productos por página

        // Query base optimizada con scopes
        $productosQuery = Product::forPlaza()
            ->active();

        // Filtrar por categoría si no es 'todos'
        if ($categoria !== 'todos') {
            $productosQuery->byCategory($categoria);
        }

        // Obtener total antes de paginar
        $total = $productosQuery->count();

        // Paginar resultados
        $productos = $productosQuery
            ->skip(($page - 1) * $perPage)
            ->take($perPage)
            ->get();

        // Mapear datos para retornar en JSON
        $productosFormateados = $productos->map(function ($product) {
            $localFirst = $product->locals->first();
            
            // Calcular rating promedio de forma más eficiente
            $reviews = $product->productReviews;
            
            // Calcular solo si hay reseñas
            $averageRating = 0;
            if ($reviews->isNotEmpty()) {
                $totalRating = 0;
                $count = 0;
                
                foreach ($reviews as $productReview) {
                    if ($productReview->review && isset($productReview->review->rating)) {
                        $totalRating += $productReview->review->rating;
                        $count++;
                    }
                }
                
                $averageRating = $count > 0 ? round($totalRating / $count) : 0;
            }
            
            return [
                'id' => $product->product_id,
                'name' => $product->name,
                'price' => number_format($product->price ?? 0, 2),
                'photo_url' => $product->photo_url ?? asset('images/product-placeholder.png'),
                'category' => $product->category ?? 'Sin categoría',
                'local' => $localFirst?->name ?? 'Local desconocido',
                'local_id' => $localFirst?->local_id ?? null,
                'average_rating' => $averageRating,
            ];
        })->values();

        // Calcular total de páginas
        $totalPages = ceil($total / $perPage);

        return response()->json([
            'success' => true,
            'data' => $productosFormateados,
            'pagination' => [
                'total' => $total,
                'per_page' => $perPage,
                'current_page' => (int)$page,
                'total_pages' => $totalPages,
                'has_more' => $page < $totalPages,
            ],
            'categoria' => $categoria,
        ]);
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

    /**
     * Vista completa de detalles del producto (no modal)
     */
    public function showProduct($local_id, $product_id)
    {
        // Obtener el local
        $local = Local::where('local_id', $local_id)->firstOrFail();

        // Obtener el producto con toda su información usando whereHas para la relación
        $product = Product::where('product_id', $product_id)
            ->whereHas('locals', function ($query) use ($local_id) {
                $query->where('tblocal.local_id', $local_id);
            })
            ->active()
            ->with(['gallery' => function ($query) {
                $query->orderBy('product_gallery_id', 'asc');
            }])
            ->firstOrFail();

        // Obtener reseñas del producto
        $reviews = $product->productReviews()
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        // Calcular rating promedio
        $averageRating = $product->average_rating ?? 0;

        return view('plaza.product-detail', [
            'local' => $local,
            'product' => $product,
            'gallery' => $product->gallery,
            'reviews' => $reviews,
            'averageRating' => $averageRating,
        ]);
    }
}
