<?php

namespace App\Http\Controllers;

use App\Models\Local;
use App\Models\User;
use App\Models\Event;
use Illuminate\Http\Request;

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

        // Distribución por estado (para mini gráfico o badges)
        $statusBreakdown = [
            'Active' => $activeLocales,
            'Inactive' => $inactiveLocales,
        ];

        return view('admin.dashboard', compact(
            'totalLocales', 'activeLocales', 'inactiveLocales',
            'statusBreakdown', 'topManagers', 'recentLocales',
            'totalUsuarios', 'usuariosActivos', 'usuariosInactivos',
            'totalEventos', 'eventosActivos', 'eventosProximos'
        ));
    }
}
