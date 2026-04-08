<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Data\ReportData;
use App\Models\Order;
use Carbon\Carbon;
use PDF;
use Maatwebsite\Excel\Facades\Excel;

class ReportController extends Controller
{
    protected $reportData;

    public function __construct()
    {
        $this->reportData = new ReportData();
    }

    /**
     * Mostrar vista principal de reportes
     */
    public function index(Request $request)
    {
        $user = $request->user();
        
        // Obtener todos los locales del usuario
        $userLocals = $user->locals;
        
        if ($userLocals->isEmpty()) {
            return redirect()->route('dashboard')->with('error', 'No tienes un local asignado');
        }
        
        // Obtener local seleccionado o usar el primero
        $localId = $request->get('local_id', $userLocals->first()->local_id);
        $local = $userLocals->firstWhere('local_id', $localId) ?? $userLocals->first();

        // Obtener período predefinido o personalizado
        $period = $request->get('period', 'month');
        $startDate = $request->get('start_date');
        $endDate = $request->get('end_date');

        // Si es personalizado, validar fechas
        if ($period === 'custom' && $startDate && $endDate) {
            $validation = $this->reportData->validateDateRange($startDate, $endDate);
            if (!$validation['valid']) {
                return back()->with('error', $validation['message']);
            }
            $start = $validation['start'];
            $end = $validation['end'];
        } else {
            $periodData = $this->reportData->getPeriodDates($period);
            $start = $periodData['start'];
            $end = $periodData['end'];
        }

        // Obtener datos del reporte
        $orderStats = $this->reportData->getOrdersByOrigin($local->local_id, $start, $end);
        $revenueStats = $this->reportData->getRevenueByOrigin($local->local_id, $start, $end);
        $dailyTrend = $this->reportData->getDailyTrend($local->local_id, $start, $end);
        $orders = $this->reportData->getOrdersByLocal($local->local_id, $start, $end);
        $topItems = $this->reportData->getTopSellingItems($local->local_id, $start, $end);
        
        // Verificar si hay datos
        $hasData = $orderStats['total'] > 0;

        // Datos para la vista
        $data = [
            'local' => $local,
            'userLocals' => $userLocals,
            'orderStats' => $orderStats,
            'revenueStats' => $revenueStats,
            'dailyTrend' => $dailyTrend,
            'orders' => $orders,
            'topItems' => $topItems,
            'hasData' => $hasData,
            'period' => $period,
            'startDate' => $startDate ?? $start->format('Y-m-d'),
            'endDate' => $endDate ?? $end->format('Y-m-d'),
            'periodLabel' => $this->reportData->getPeriodDates($period)['label'] ?? 'Personalizado',
        ];

        return view('reports.orders-report', $data);
    }

    /**
     * API: Obtener datos en JSON para AJAX
     */
    public function getData(Request $request)
    {
        $user = $request->user();
        $userLocals = $user->locals;

        if ($userLocals->isEmpty()) {
            return response()->json(['error' => 'No local found'], 403);
        }

        $localId = $request->get('local_id', $userLocals->first()->local_id);
        $local = $userLocals->firstWhere('local_id', $localId) ?? $userLocals->first();

        $period = $request->get('period', 'month');
        $startDate = $request->get('start_date');
        $endDate = $request->get('end_date');

        if ($period === 'custom' && $startDate && $endDate) {
            $validation = $this->reportData->validateDateRange($startDate, $endDate);
            if (!$validation['valid']) {
                return response()->json(['error' => $validation['message']], 422);
            }
            $start = $validation['start'];
            $end = $validation['end'];
        } else {
            $periodData = $this->reportData->getPeriodDates($period);
            $start = $periodData['start'];
            $end = $periodData['end'];
        }

        $orderStats = $this->reportData->getOrdersByOrigin($local->local_id, $start, $end);
        $revenueStats = $this->reportData->getRevenueByOrigin($local->local_id, $start, $end);
        $dailyTrend = $this->reportData->getDailyTrend($local->local_id, $start, $end);

        return response()->json([
            'orderStats' => $orderStats,
            'revenueStats' => $revenueStats,
            'dailyTrend' => $dailyTrend,
        ]);
    }

    /**
     * Exportar reporte a PDF
     */
    public function exportPDF(Request $request)
    {
        $user = $request->user();
        $userLocals = $user->locals;

        if ($userLocals->isEmpty()) {
            return redirect()->back()->with('error', 'No tienes un local asignado');
        }

        $localId = $request->get('local_id', $userLocals->first()->local_id);
        $local = $userLocals->firstWhere('local_id', $localId) ?? $userLocals->first();

        $period = $request->get('period', 'month');
        $startDate = $request->get('start_date');
        $endDate = $request->get('end_date');

        if ($period === 'custom' && $startDate && $endDate) {
            $validation = $this->reportData->validateDateRange($startDate, $endDate);
            if (!$validation['valid']) {
                return back()->with('error', $validation['message']);
            }
            $start = $validation['start'];
            $end = $validation['end'];
        } else {
            $periodData = $this->reportData->getPeriodDates($period);
            $start = $periodData['start'];
            $end = $periodData['end'];
        }

        $orderStats = $this->reportData->getOrdersByOrigin($local->local_id, $start, $end);
        $revenueStats = $this->reportData->getRevenueByOrigin($local->local_id, $start, $end);
        $topItems = $this->reportData->getTopSellingItems($local->local_id, $start, $end);
        $hasData = $orderStats['total'] > 0;

        $data = [
            'local' => $local,
            'orderStats' => $orderStats,
            'revenueStats' => $revenueStats,
            'topItems' => $topItems,
            'hasData' => $hasData,
            'exportDate' => Carbon::now()->format('d/m/Y H:i'),
        ];

        return view('reports.orders-report-pdf', $data);
    }

    /**
     * Descargar reporte como HTML imprimible
     */
    public function downloadHTML(Request $request)
    {
        $user = $request->user();
        $userLocals = $user->locals;

        if ($userLocals->isEmpty()) {
            return redirect()->back()->with('error', 'No tienes un local asignado');
        }

        $localId = $request->get('local_id', $userLocals->first()->local_id);
        $local = $userLocals->firstWhere('local_id', $localId) ?? $userLocals->first();

        $period = $request->get('period', 'month');
        $startDate = $request->get('start_date');
        $endDate = $request->get('end_date');

        if ($period === 'custom' && $startDate && $endDate) {
            $validation = $this->reportData->validateDateRange($startDate, $endDate);
            if (!$validation['valid']) {
                return back()->with('error', $validation['message']);
            }
            $start = $validation['start'];
            $end = $validation['end'];
        } else {
            $periodData = $this->reportData->getPeriodDates($period);
            $start = $periodData['start'];
            $end = $periodData['end'];
        }

        $orderStats = $this->reportData->getOrdersByOrigin($local->local_id, $start, $end);
        $revenueStats = $this->reportData->getRevenueByOrigin($local->local_id, $start, $end);
        $topItems = $this->reportData->getTopSellingItems($local->local_id, $start, $end);
        $hasData = $orderStats['total'] > 0;

        $data = [
            'local' => $local,
            'orderStats' => $orderStats,
            'revenueStats' => $revenueStats,
            'topItems' => $topItems,
            'hasData' => $hasData,
            'exportDate' => Carbon::now()->format('d/m/Y H:i'),
        ];

        $filename = 'reporte_pedidos_' . $local->name . '_' . now()->format('Y-m-d_His') . '.html';

        return view('reports.orders-report-export', $data);
    }

    /**
     * Exportar reporte a Excel
     */
    public function exportExcel(Request $request)
    {
        $user = $request->user();
        $userLocals = $user->locals;

        if ($userLocals->isEmpty()) {
            return redirect()->back()->with('error', 'No tienes un local asignado');
        }

        $localId = $request->get('local_id', $userLocals->first()->local_id);
        $local = $userLocals->firstWhere('local_id', $localId) ?? $userLocals->first();

        $period = $request->get('period', 'month');
        $startDate = $request->get('start_date');
        $endDate = $request->get('end_date');

        if ($period === 'custom' && $startDate && $endDate) {
            $validation = $this->reportData->validateDateRange($startDate, $endDate);
            if (!$validation['valid']) {
                return back()->with('error', $validation['message']);
            }
            $start = $validation['start'];
            $end = $validation['end'];
        } else {
            $periodData = $this->reportData->getPeriodDates($period);
            $start = $periodData['start'];
            $end = $periodData['end'];
        }

        $orderStats = $this->reportData->getOrdersByOrigin($local->local_id, $start, $end);
        $revenueStats = $this->reportData->getRevenueByOrigin($local->local_id, $start, $end);
        $topItems = $this->reportData->getTopSellingItems($local->local_id, $start, $end);

        // Construir datos para Excel
        $filename = 'reporte_pedidos_' . $local->name . '_' . now()->format('Y-m-d_His') . '.xlsx';

        return Excel::download(
            new \App\Exports\OrdersReportExport($local, $orderStats, $revenueStats, $topItems, $start, $end),
            $filename
        );
    }
}
