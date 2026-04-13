<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Local;
use App\Models\Schedule;
use App\Models\LocalReview;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

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
    public function show(Request $request, $id)
    {
        $local = Local::where('local_id', $id)->firstOrFail();

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

        $now = now();
        $dayOfWeek = $now->translatedFormat('l');

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

        // ── RESEÑAS DEL LOCAL ──────────────────────────────────────────────
        $reviews = LocalReview::with(['review', 'user'])
            ->where('local_id', $id)
            ->whereHas('review')
            ->orderByDesc('local_review_id')
            ->paginate(6);

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
        // ──────────────────────────────────────────────────────────────────

        // ── LOCALES DISPONIBLES para el selector ──────────────────────────
        $localesDisponibles = Local::where('status', 'Active')->get();
        // ──────────────────────────────────────────────────────────────────

        return view('plaza.show', [
            'local'              => $local,
            'productos'          => $productos,
            'categorias'         => $categorias,
            'horarioHoy'         => $horarioHoy,
            'diaActual'          => $dayInSpanish,
            'estaAbierto'        => $estaAbierto,
            'reviews'            => $reviews,
            'localStats'         => $localStats,
            'localesDisponibles' => $localesDisponibles,
        ]);
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
}