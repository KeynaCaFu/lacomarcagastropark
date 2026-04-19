<?php

namespace App\Http\Controllers;

use App\Models\Local;
use App\Models\User;
use App\Models\Event;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Http\Request;
use Carbon\Carbon;

class AdminDashboardController extends Controller
{
    public function index(Request $request)
    {
        // Estadísticas de Locales
        $totalLocales = Local::count();
        $activeLocales = Local::where('status', 'Active')->count();
        $inactiveLocales = Local::where('status', 'Inactive')->count();

        // Estadísticas de Usuarios
        $totalUsuarios = User::count();
        $usuariosActivos = User::where('status', 'Active')->count();
        $usuariosInactivos = User::where('status', 'Inactive')->count();

        // Estadísticas de Eventos
        $totalEventos = Event::count();
        $eventosActivos = Event::where('is_active', true)->count();
        $eventosProximos = Event::where('start_at', '>=', now())->count();

        // Top gerentes por cantidad de locales asignados
        $topManagers = User::query()
            ->where('role_id', 2)
            ->where('status', 'Active')
            ->withCount('locals')
            ->orderByDesc('locals_count')
            ->limit(5)
            ->get(['user_id', 'full_name']);

        // Últimos locales creados
        $recentLocales = Local::query()
            ->with(['users' => function($q){ $q->where('role_id', 2); }])
            ->orderByDesc('created_at')
            ->limit(8)
            ->get(['local_id','name','status','created_at']);

        // Distribución por estado
        $statusBreakdown = [
            'Active' => $activeLocales,
            'Inactive' => $inactiveLocales,
        ];

        // NUEVAS MÉTRICAS - Ventas del último mes
        $totalSalesLastMonth = $this->getTotalSalesLastMonth();
        
        // Pedidos activos (status: Pending, In Progress, Ready)
        $activeOrders = Order::whereIn('status', ['Pending', 'In Progress', 'Ready'])->count();
        
        // Ranking de locales más activos en el último mes
        $rankingStores = $this->getRankingStoresLastMonth();

        return view('admin.dashboard', compact(
            'totalLocales', 'activeLocales', 'inactiveLocales',
            'statusBreakdown', 'topManagers', 'recentLocales',
            'totalUsuarios', 'usuariosActivos', 'usuariosInactivos',
            'totalEventos', 'eventosActivos', 'eventosProximos',
            'totalSalesLastMonth', 'activeOrders', 'rankingStores'
        ));
    }

    /**
     * Obtener total de ventas de todos los locales en el ÚLTIMO MES
     */
    private function getTotalSalesLastMonth()
    {
        $lastMonth = Carbon::now()->subMonth();
        
        $total = Order::whereDate('date', '>=', $lastMonth)
            ->whereIn('status', ['Delivered', 'Completed'])
            ->sum('total_amount');

        return $total ?? 0;
    }

    /**
     * Obtener ranking de locales más activos en el ÚLTIMO MES
     */
    private function getRankingStoresLastMonth()
    {
        $lastMonth = Carbon::now()->subMonth();

        // Obtener total de ventas de TODOS los locales para calcular porcentajes
        $totalSalesAllStores = Order::whereDate('date', '>=', $lastMonth)
            ->whereIn('status', ['Delivered', 'Completed'])
            ->sum('total_amount');

        $locales = Local::query()
            ->withCount(['orders as month_orders' => function($query) use ($lastMonth) {
                $query->whereDate('date', '>=', $lastMonth)
                      ->whereIn('status', ['Delivered', 'Completed']);
            }])
            ->where('status', 'Active')
            ->orderByDesc('month_orders')
            ->limit(10)
            ->get(['local_id', 'name']);

        return $locales->map(function($local) use ($lastMonth, $totalSalesAllStores) {
            $sales = Order::where('local_id', $local->local_id)
                ->whereDate('date', '>=', $lastMonth)
                ->whereIn('status', ['Delivered', 'Completed'])
                ->sum('total_amount');

            // Calcular porcentaje
            $percentage = $totalSalesAllStores > 0 ? ($sales / $totalSalesAllStores) * 100 : 0;

            // Obtener rating del local - Cargar directamente desde BD
            $rating = \DB::table('tblocal_review')
                ->where('local_id', $local->local_id)
                ->join('tbreview', 'tblocal_review.review_id', '=', 'tbreview.review_id')
                ->avg('tbreview.rating');
            
            $rating = $rating ? round($rating, 1) : 0;

            return [
                'id' => $local->local_id,
                'name' => $local->name,
                'orders_count' => $local->month_orders,
                'sales' => $sales ?? 0,
                'percentage' => round($percentage, 2),
                'rating' => $rating
            ];
        });
    }

    // ========== MÉTODOS API PARA ACTUALIZACIÓN AUTOMÁTICA ==========

    /**
     * API: Obtener total de ventas del ÚLTIMO MES (JSON)
     */
    public function getApiSalesTotal()
    {
        $total = $this->getTotalSalesLastMonth();
        
        if ($total == 0) {
            return response()->json([
                'message' => 'No hay datos registrados en el último mes',
                'total' => '$0.00'
            ]);
        }

        return response()->json([
            'total' => '$' . number_format($total, 2, '.', ',')
        ]);
    }

    /**
     * API: Obtener cantidad de pedidos activos (JSON)
     */
    public function getApiActiveOrders()
    {
        $count = Order::whereIn('status', ['Pending', 'In Progress', 'Ready'])->count();
        
        return response()->json([
            'count' => $count,
            'message' => $count == 0 ? 'No hay pedidos activos' : ''
        ]);
    }

    /**
     * API: Obtener ranking de locales más activos del ÚLTIMO MES (JSON)
     */
    public function getApiRankingStores()
    {
        $ranking = $this->getRankingStoresLastMonth();
        
        if ($ranking->isEmpty()) {
            return response()->json([
                'message' => 'No hay datos registrados en el último mes',
                'ranking' => []
            ]);
        }

        return response()->json([
            'ranking' => $ranking->toArray()
        ]);
    }
}
