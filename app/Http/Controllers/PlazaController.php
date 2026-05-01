<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Local;
use App\Models\Schedule;
use App\Models\Event;
use App\Models\LocalReview;
use App\Models\ProductReview;
use App\Models\Review;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use App\Models\Order;
use App\Models\OrderItem;
class PlazaController extends Controller
{
    /**
     * Plaza Principal: mostrar todos los locales, productos y categorías
     */
    public function index(Request $request)
    {
        $categorias = Product::where('status', 'Available')
            ->distinct()
            ->pluck('category')
            ->filter()
            ->values()
            ->map(function ($category) {
                return [
                    'nombre' => $category,
                    'slug' => Str::slug($category),
                    'icono' => $this->getCategoryIcon($category),
                ];
            })
            ->toArray();

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

        if ($request->has('categoria') && $request->categoria !== 'todos') {
            $categoria = $request->categoria;
            $productos = $productos->filter(function ($product) use ($categoria) {
                return $product->category_slug === $categoria;
            })->values();
        }

        $locales = Local::where('status', 'Active')
            ->with(['gallery' => function ($query) {
                $query->limit(1);
            }])
            ->get()
            ->map(function ($local) {
                $local->isOpenNow = Schedule::isCurrentlyOpen($local->local_id);
                return $local;
            });

        // Horarios de hoy para todos los locales (una sola query) — usados por el timer JS
        $horariosPorLocal = Schedule::todayForLocals($locales->pluck('local_id')->toArray())
            ->get()
            ->keyBy('local_id')
            ->map(fn($s) => [
                'opening_time' => $s->opening_time?->format('H:i'),
                'closing_time' => $s->closing_time?->format('H:i'),
            ]);

        $productosAleatorios = collect();
        foreach ($locales as $local) {
            $productosLocal = $local->products()
                ->where('tbproduct.status', 'Available')
                ->with(['productReviews.review' => function ($query) {
                    $query->select('review_id', 'rating');
                }])
                ->inRandomOrder()
                ->limit(2)
                ->get();
            $productosAleatorios = $productosAleatorios->merge($productosLocal);
        }

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
        $yesterday = now()->subDay()->toDateString();
        $oneMonthLater = now()->addDays(30)->toDateString();

        // Eventos de hoy: mostrar TODOS los eventos del día actual (sin límite de 24 horas)
        $eventosHoy = Event::select('event_id', 'title', 'description', 'start_at', 'location', 'image_url')
            ->active()
            ->whereDate('start_at', $today)
            ->orderBy('start_at', 'asc')
            ->get();

        // Próximos incluye: futuros Y eventos de ayer (últimos que aún son visibles)
        $eventosProximos = Event::select('event_id', 'title', 'description', 'start_at', 'location', 'image_url')
            ->active()
            ->notExpired()
            ->where(function($query) use ($today, $yesterday) {
                $query->whereDate('start_at', '>', $today) // Eventos futuros
                      ->orWhere(function($q) use ($yesterday) {
                          $q->whereDate('start_at', '=', $yesterday); // O de ayer (últimas 24h de visibilidad)
                      });
            })
            ->where('start_at', '<=', $oneMonthLater . ' 23:59:59')
            ->orderBy('start_at', 'asc')
            ->get();

        return view('plaza.index', [
            'locales'          => $locales,
            'productos'        => $productosAleatorios,
            'categorias'       => $categorias,
            'stats'            => $stats,
            'categoria_actual' => $request->categoria ?? 'todos',
            'eventosHoy'       => $eventosHoy,
            'eventosProximos'  => $eventosProximos,
            'horariosPorLocal' => $horariosPorLocal,
        ]);
    }

    /**
     * Vista detallada de un local con sus productos
     */
    public function show(Request $request, $id)
    {
        $local = Local::where('local_id', $id)->firstOrFail();

        $productos = Product::whereHas('locals', function ($query) use ($id) {
            $query->where('tblocal_product.local_id', $id);
        })
            ->with([
                'gallery',
                'productReviews.review' => function ($query) {
                    $query->select('review_id', 'rating');
                }
            ])
            ->get();

        // IDs de productos actualmente inactivos (para pre-inicializar disabledProductIds en Vue)
        $productosInactivosIds = $productos
            ->where('status', 'Unavailable')
            ->pluck('product_id')
            ->values();

        // Categorías solo de productos disponibles (para no mostrar categorías vacías)
        $categorias = $productos
            ->where('status', 'Available')
            ->pluck('category')
            ->unique()
            ->filter()
            ->map(function ($category) {
                return [
                    'nombre' => $category,
                    'slug' => Str::slug($category),
                    'icono' => $this->getCategoryIcon($category),
                ];
            })
            ->values();

        $now = now();
        $dayOfWeek = $now->format('l');

        $dayTranslation = [
            'Monday'    => 'Lunes',
            'Tuesday'   => 'Martes',
            'Wednesday' => 'Miércoles',
            'Thursday'  => 'Jueves',
            'Friday'    => 'Viernes',
            'Saturday'  => 'Sábado',
            'Sunday'    => 'Domingo',
        ];

        $dayInSpanish = $dayTranslation[$dayOfWeek] ?? null;

        $horarioHoy  = null;
        $estaAbierto = false;

        if ($dayInSpanish) {
            $horarioHoy = Schedule::where('local_id', $id)
                ->where('day_of_week', $dayInSpanish)
                ->first();
            $estaAbierto = Schedule::isCurrentlyOpen($id);
        }

        // Obtener todos los locales activos para el combobox
        $localesDisponibles = Local::select('local_id', 'name', 'status')
            ->where('status', 'Active')
            ->orderBy('name', 'asc')
            ->get();

        // Obtener eventos activos
        $today = now()->toDateString();
        $yesterday = now()->subDay()->toDateString();
        $oneMonthLater = now()->addDays(30)->toDateString();

        // Eventos de hoy: mostrar TODOS los eventos del día actual (sin límite de 24 horas)
        $eventosHoy = Event::select('event_id', 'title', 'description', 'start_at', 'location', 'image_url')
            ->active()
            ->whereDate('start_at', $today)
            ->orderBy('start_at', 'asc')
            ->get();

        // Próximos incluye: futuros Y eventos de ayer (últimos que aún son visibles en 24h)
        $eventosProximos = Event::select('event_id', 'title', 'description', 'start_at', 'location', 'image_url')
            ->active()
            ->notExpired()
            ->where(function($query) use ($today, $yesterday) {
                $query->whereDate('start_at', '>', $today) // Eventos futuros
                      ->orWhere(function($q) use ($yesterday) {
                          $q->whereDate('start_at', '=', $yesterday); // O de ayer (últimas 24h de visibilidad)
                      });
            })
            ->where('start_at', '<=', $oneMonthLater . ' 23:59:59')
            ->orderBy('start_at', 'asc')
            ->get();

        // Obtener reseñas del local
        $reviews = LocalReview::with(['review', 'user'])
            ->where('local_id', $id)
            ->whereHas('review')
            ->orderBy('created_at', 'desc')
            ->get();

        // Calcular estadísticas de reseñas del local
        $allRatings = LocalReview::with('review')
            ->where('local_id', $id)
            ->whereHas('review')
            ->get()
            ->pluck('review.rating')
            ->filter();

        $localStats = [
            'average' => $allRatings->count() ? round($allRatings->avg(), 1) : 0,
            'total'   => $allRatings->count(),
        ];

        return view('plaza.show', [
            'local' => $local,
            'productos' => $productos,
            'productosInactivosIds' => $productosInactivosIds,
            'categorias' => $categorias,
            'horarioHoy' => $horarioHoy,
            'diaActual' => $dayInSpanish,
            'estaAbierto' => $estaAbierto,
            'localesDisponibles' => $localesDisponibles,
            'eventosHoy' => $eventosHoy,
            'eventosProximos' => $eventosProximos,
            'reviews' => $reviews,
            'localStats' => $localStats,
        ]);
    }

    /**
     * Ver detalles de un producto
     */
    
    public function showProduct($local_id, $product_id)
{
    try {
        $local = Local::where('local_id', $local_id)->firstOrFail();

        $product = Product::where('product_id', $product_id)
            ->where('status', 'Available')
            ->with([
                'gallery',
                'productReviews.review' => function ($query) {
                    $query->select('review_id', 'rating', 'comment', 'date', 'created_at');
                }
            ])
            ->firstOrFail();

        $gallery = $product->gallery ?? collect();

        $reviews = ProductReview::where('product_id', $product_id)
            ->with(['review', 'user'])
            ->whereHas('review')
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function ($productReview) {
                $user = $productReview->user;
                $review = $productReview->review;

                return [
                    'product_review_id' => $productReview->product_review_id,
                    'user_id'           => $productReview->user_id,
                    'reviewer_name'     => $user->full_name ?? $user->name ?? 'Cliente',
                    'rating'            => $review->rating ?? 0,
                    'comment'           => $review->comment ?? '',
                    'response'          => $review->response ?? null,
                    'created_at'        => $review->created_at ?? $review->date ?? $productReview->created_at,
                ];
            })
            ->values();

        $today = now()->toDateString();
        $yesterday = now()->subDay()->toDateString();
        $oneMonthLater = now()->addDays(30)->toDateString();

        $eventosHoy = Event::select('event_id', 'title', 'description', 'start_at', 'location', 'image_url')
            ->active()
            ->whereDate('start_at', $today)
            ->orderBy('start_at', 'asc')
            ->get();

        $eventosProximos = Event::select('event_id', 'title', 'description', 'start_at', 'location', 'image_url')
            ->active()
            ->notExpired()
            ->where(function($query) use ($today, $yesterday) {
                $query->whereDate('start_at', '>', $today)
                      ->orWhere(function($q) use ($yesterday) {
                          $q->whereDate('start_at', '=', $yesterday);
                      });
            })
            ->where('start_at', '<=', $oneMonthLater . ' 23:59:59')
            ->orderBy('start_at', 'asc')
            ->get();

        return view('plaza.product-detail', [
            'local' => $local,
            'product' => $product,
            'gallery' => $gallery,
            'reviews' => $reviews,
            'eventosHoy' => $eventosHoy,
            'eventosProximos' => $eventosProximos,
        ]);
    } catch (\Exception $e) {
        abort(404, 'Producto no encontrado');
    }
}

    /**
     * Obtener datos del local en JSON para cambio dinámico en combobox (AJAX)
     */
    public function getLocalData($id)
    {
        try {
            // Buscar local
            $local = Local::where('local_id', $id)->firstOrFail();

            // Obtener TODOS los productos del local (activos e inactivos)
            $todosProductos = Product::whereHas('locals', function ($query) use ($id) {
                $query->where('tblocal_product.local_id', $id);
            })
                ->with([
                    'gallery',
                    'productReviews.review' => function ($query) {
                        $query->select('review_id', 'rating');
                    }
                ])
                ->get();

            // IDs de productos inactivos para pre-inicializar disabledProductIds en Vue
            $productosInactivosIds = $todosProductos
                ->where('status', 'Unavailable')
                ->pluck('product_id')
                ->values()
                ->toArray();

            $productos = $todosProductos->map(function ($product) {
                return [
                    'product_id'     => $product->product_id,
                    'local_id'       => $product->pivot->local_id ?? null,
                    'name'           => $product->name,
                    'description'    => $product->description,
                    'category'       => $product->category,
                    'status'         => $product->status,
                    'photo_url'      => $product->photo_url ? asset($product->photo_url) : null,
                    'price'          => $product->price,
                    'average_rating' => $product->average_rating,
                    'gallery'        => $product->gallery ? $product->gallery->map(fn($img) => [
                        'image_url' => $img->image_url ? asset($img->image_url) : null
                    ])->toArray() : []
                ];
            })->toArray();

            // Categorías solo de productos disponibles
            $categoriasArray = [];
            $categoriasNames = [];
            foreach ($todosProductos->where('status', 'Available') as $product) {
                if ($product->category && !in_array($product->category, $categoriasNames)) {
                    $categoriasArray[] = [
                        'nombre' => $product->category,
                        'slug'   => Str::slug($product->category),
                        'icono'  => $this->getCategoryIcon($product->category),
                    ];
                    $categoriasNames[] = $product->category;
                }
            }
            $categorias = $categoriasArray;

            // Obtener horario del día actual
            $now = now();
            $dayOfWeek = $now->format('l');

            $dayTranslation = [
                'Monday'    => 'Lunes',
                'Tuesday'   => 'Martes',
                'Wednesday' => 'Miércoles',
                'Thursday'  => 'Jueves',
                'Friday'    => 'Viernes',
                'Saturday'  => 'Sábado',
                'Sunday'    => 'Domingo',
            ];

            $dayInSpanish = $dayTranslation[$dayOfWeek] ?? null;
            $horarioHoy = null;
            $estaAbierto = false;

            if ($dayInSpanish) {
                $horarioHoy = Schedule::where('local_id', $id)
                    ->where('day_of_week', $dayInSpanish)
                    ->first();
                $estaAbierto = Schedule::isCurrentlyOpen($id);
            }

            return response()->json([
                'success'              => true,
                'local' => [
                    'local_id'    => $local->local_id,
                    'name'        => $local->name,
                    'description' => $local->description,
                    'logo_url'    => $local->image_logo ? asset($local->image_logo) : null,
                ],
                'horarioHoy' => $horarioHoy ? [
                    'opening_time' => $horarioHoy->opening_time ? \Carbon\Carbon::parse($horarioHoy->opening_time)->format('H:i') : null,
                    'closing_time' => $horarioHoy->closing_time ? \Carbon\Carbon::parse($horarioHoy->closing_time)->format('H:i') : null,
                    'status'       => $horarioHoy->status,
                ] : null,
                'diaActual'            => $dayInSpanish,
                'estaAbierto'          => $estaAbierto,
                'categorias'           => $categorias,
                'productos'            => $productos,
                'productosInactivosIds' => $productosInactivosIds,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener datos del local',
            ], 404);
        }
    }   


    /**
     * Obtener productos filtrados por categoría (AJAX)
     */
    public function getProductosByCategory(Request $request)
    {
        $categoria = $request->query('categoria', 'todos');

        $productosQuery = Product::where('status', 'Available')
            ->with([
                'locals' => function ($query) {
                    $query->select('tblocal.local_id', 'tblocal.name');
                },
                'productReviews.review' => function ($query) {
                    $query->select('review_id', 'rating');
                }
            ]);

        if ($categoria !== 'todos') {
            $productosQuery->whereRaw("LOWER(REPLACE(REPLACE(`category`, ' ', ''), '-', '')) = ?",
                [strtolower(str_replace([' ', '-'], '', $categoria))]
            );
        }

        $productos = $productosQuery->get();

        $productosFormateados = $productos->map(function ($product) {
            $localFirst = $product->locals->first();

            $reviews = $product->productReviews;
            if ($reviews->isNotEmpty()) {
                $totalRating  = $reviews->sum(function ($pr) { return $pr->review->rating ?? 0; });
                $averageRating = round($totalRating / $reviews->count());
            } else {
                $averageRating = 0;
            }

            return [
                'id'             => $product->product_id ?? $product->id,
                'name'           => $product->name,
                'price'          => number_format($product->price ?? 0, 2),
                'photo_url'      => $product->photo_url ?? asset('images/product-placeholder.png'),
                'category'       => $product->category ?? 'Sin categoría',
                'local'          => $localFirst?->name ?? 'Local desconocido',
                'local_id'       => $localFirst?->local_id ?? null,
                'average_rating' => $averageRating,
            ];
        })->values();

        return response()->json([
            'success'  => true,
            'data'     => $productosFormateados,
            'total'    => $productosFormateados->count(),
            'categoria'=> $categoria,
        ]);
    }

    /**
     * Retornar icono según categoría
     */
    private function getCategoryIcon($category)
    {
        $icons = [
            'hamburguesería' => 'fa-burger',
            'pizza'          => 'fa-pizza-slice',
            'sushi'          => 'fa-fish',
            'postres'        => 'fa-cake-candles',
            'bebidas'        => 'fa-glass-water',
            'comida rápida'  => 'fa-fire',
            'ensaladas'      => 'fa-leaf',
            'sandwich'       => 'fa-sandwich',
        ];

        $categoryLower = Str::slug($category);
        foreach ($icons as $key => $icon) {
            if (str_contains($categoryLower, Str::slug($key))) {
                return $icon;
            }
        }

        return 'fa-utensils';
    }


public function storeLocalReview(Request $request, $localId)
{
    try {
        $request->validate([
            'rating'  => 'required|integer|min:1|max:5',
            'comment' => 'required|string|min:10|max:500',
        ]);

        $userId = Auth::id();

        if (!$userId) {
            return response()->json(['error' => 'Debes iniciar sesión para dejar una reseña.'], 401);
        }

        // CA-3: Solo puede reseñar si tiene un pedido previo en ese local
        $tienePedido = Order::where('local_id', $localId)
            ->whereIn('status', [Order::STATUS_DELIVERED])
            ->whereIn('order_id', function ($query) use ($userId) {
                $query->select('order_id')
                    ->from('tbuser_order')
                    ->where('user_id', $userId);
            })
            ->exists();

        if (!$tienePedido) {
            return response()->json([
                'error' => 'Solo puedes reseñar un local en el que hayas realizado un pedido previo.'
            ], 403);
        }

        // Crear reseña (permite múltiples por usuario/local)
        $review = Review::create([
            'rating'   => $request->rating,
            'comment'  => $request->comment,
            'date'     => now(),
            'response' => null,
        ]);

        $localReview = LocalReview::create([
            'review_id' => $review->review_id,
            'local_id'  => $localId,
            'user_id'   => $userId,
        ]);

        // Devolver la nueva reseña completa para renderizarla sin recargar
        $user = Auth::user();
        $nombre = $user->full_name ?? $user->name ?? 'Cliente';
        $partes = explode(' ', trim($nombre));
        $iniciales = '';
        foreach (array_slice($partes, 0, 2) as $p) {
            $iniciales .= strtoupper(substr($p, 0, 1));
        }

        return response()->json([
            'success' => true,
            'message' => 'Reseña guardada correctamente.',
            'review'  => [
                'local_review_id' => $localReview->local_review_id,
                'local_id'        => (int) $localId,
                'nombre'          => $nombre,
                'iniciales'       => $iniciales ?: 'CL',
                'rating'          => $request->rating,
                'comment'         => $request->comment,
                'date'            => now()->toISOString(),
            ],
        ], 200);

    } catch (\Illuminate\Validation\ValidationException $e) {
        return response()->json([
            'error' => collect($e->errors())->flatten()->first()
        ], 422);
    } catch (\Throwable $e) {
        return response()->json(['error' => $e->getMessage()], 500);
    }
}

public function storeProductReview(Request $request, $productId)
{
    $userId = Auth::id();

    if (!$userId) {
        return response()->json([
            'success' => false,
            'error' => 'Debes iniciar sesión para publicar una reseña.'
        ], 401);
    }

    $request->validate([
        'rating' => 'required|integer|min:1|max:5',
        'comment' => 'required|string|min:10|max:500',
    ]);

    $haComprado = Order::where('status', 'Delivered')
        ->whereHas('user', function ($q) use ($userId) {
            $q->where('tbuser_order.user_id', $userId);
        })
        ->whereHas('items', function ($q) use ($productId) {
            $q->where('product_id', $productId);
        })
        ->exists();

    if (!$haComprado) {
        return response()->json([
            'success' => false,
            'error' => 'Solo puedes reseñar productos que hayas pedido y recibido.'
        ], 403);
    }

    // BLOQUEO DE DOBLE RESEÑA
    $yaExiste = ProductReview::where('product_id', $productId)
        ->where('user_id', $userId)
        ->exists();

    if ($yaExiste) {
        return response()->json([
            'success' => false,
            'error' => 'Ya habías publicado una reseña para este producto.'
        ], 409);
    }

    $review = Review::create([
        'rating' => $request->rating,
        'comment' => trim($request->comment),
        'date' => now(),
    ]);

    $productReview = ProductReview::create([
        'review_id' => $review->review_id,
        'product_id' => $productId,
        'user_id' => $userId,
        'responded_by' => null,
    ]);

    $user = Auth::user();

    return response()->json([
        'success' => true,
        'review' => [
            'product_review_id' => $productReview->product_review_id,
            'user_id'=> $userId,
            'nombre' => $user->full_name ?? $user->name ?? 'Cliente',
            'rating' => $review->rating,
            'comment' => $review->comment,
            'date' => $review->created_at ?? now(),
        ]
    ], 201);
}

    /**
     * Obtener todos los horarios de todos los locales (para recalc automático en index)
     */
    public function getAllSchedules()
    {
        $locales = Local::where('status', 'Active')->get();
        $schedules = [];

        foreach ($locales as $local) {
            $schedules[$local->local_id] = Schedule::where('local_id', $local->local_id)
                ->get()
                ->map(fn($s) => [
                    'day_of_week' => $s->day_of_week,
                    'opening_time' => $s->opening_time?->format('H:i'),
                    'closing_time' => $s->closing_time?->format('H:i'),
                    'status' => (bool) $s->status,
                ])
                ->toArray();
        }

        return response()->json([
            'success' => true,
            'schedules' => $schedules
        ]);
    }

    public function deleteProductReview($productReviewId)
    {
        $review = ProductReview::where('product_review_id', $productReviewId)
            ->where('user_id', auth()->id())
            ->firstOrFail();

        $review->review()->delete(); // elimina la review padre
        $review->delete();

        return response()->json(['success' => true]);
    }

    public function deleteLocalReview($localId, $localReviewId)
    {
        $review = LocalReview::where('local_review_id', $localReviewId)
            ->where('local_id', $localId)
            ->where('user_id', auth()->id())
            ->firstOrFail();

        $review->review()->delete();
        $review->delete();

        return response()->json(['success' => true]);
    }

}
