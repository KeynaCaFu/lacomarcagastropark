<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\Data\ReportData;
use App\Models\Order;
use Carbon\Carbon;
use PDF;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class ReportController extends Controller
{
    protected $reportData;

    public function __construct()
    {
        $this->reportData = new ReportData();
    }

    /**
     * Mostrar vista principal de reportes
     * se puede filtrar por producto específico y por rango de fechas
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

        // Obtener producto seleccionado (opcional)
        $productId = $request->get('product_id');
        $productError = null;

        // Si se especifica un producto, validar que tenga costo registrado
        if ($productId) {
            $costValidation = $this->reportData->validateProductCost($productId, $local->local_id);
            if (!$costValidation['valid']) {
                $productError = $costValidation['message'];
                // CP-206-02: Manejar gracefully - setear hasData a false
                $orderStats = ['total' => 0, 'web' => ['count' => 0, 'percentage' => 0], 'presential' => ['count' => 0, 'percentage' => 0]];
                $revenueStats = ['total' => 0, 'web' => ['revenue' => 0, 'percentage' => 0], 'presential' => ['revenue' => 0, 'percentage' => 0]];
                $dailyTrend = [];
                $orders = collect();
                $topItems = collect();
                $hasData = false;
                $selectedProduct = null;
            } else {
                // Obtener datos filtrados por producto
                $orderStats = $this->reportData->getOrdersByOriginByProduct($local->local_id, $productId, $start, $end);
                $revenueStats = $this->reportData->getRevenueByOriginByProduct($local->local_id, $productId, $start, $end);
                $dailyTrend = $this->reportData->getDailyTrendByProduct($local->local_id, $productId, $start, $end);
                $orders = $this->reportData->getOrdersByLocal($local->local_id, $start, $end);
                $topItems = $this->reportData->getTopSellingItems($local->local_id, $start, $end);
                $hasData = $orderStats['total'] > 0;
                
                // Obtener datos del producto
                $products = $this->reportData->getLocalProducts($local->local_id);
                $selectedProduct = $products->firstWhere('product_id', $productId);
            }
        } else {
            // Obtener datos generales
            $orderStats = $this->reportData->getOrdersByOrigin($local->local_id, $start, $end);
            $revenueStats = $this->reportData->getRevenueByOrigin($local->local_id, $start, $end);
            $dailyTrend = $this->reportData->getDailyTrend($local->local_id, $start, $end);
            $orders = $this->reportData->getOrdersByLocal($local->local_id, $start, $end);
            $topItems = $this->reportData->getTopSellingItems($local->local_id, $start, $end);
            $hasData = $orderStats['total'] > 0;
            $selectedProduct = null;
        }

        // Obtener lista de productos disponibles para el filtro
        $availableProducts = $this->reportData->getLocalProducts($local->local_id);

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
            'availableProducts' => $availableProducts,
            'selectedProduct' => $selectedProduct,
            'productId' => $productId,
            'productError' => $productError,
        ];

        return view('reports.orders-report', $data);
    }

    /**
     * Mostrar vista de reportes por producto
     * Permite análisis detallado de un producto específico
     */
    public function products(Request $request)
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

        // Obtener producto seleccionado (opcional)
        $productId = $request->get('product_id');
        $productError = null;

        // Productos disponibles para este local
        $availableProducts = $this->reportData->getLocalProducts($local->local_id);

        // Si se especifica un producto, validar que tenga costo registrado
        if ($productId) {
            $costValidation = $this->reportData->validateProductCost($productId, $local->local_id);
            if (!$costValidation['valid']) {
                $productError = $costValidation['message'];
                // Manejar gracefully - setear hasData a false
                $orderStats = ['total' => 0, 'web' => ['count' => 0, 'percentage' => 0], 'presential' => ['count' => 0, 'percentage' => 0]];
                $revenueStats = ['total' => 0, 'web' => ['revenue' => 0, 'percentage' => 0], 'presential' => ['revenue' => 0, 'percentage' => 0]];
                $dailyTrend = [];
                $hasData = false;
            } else {
                // Obtener datos filtrados por producto
                $orderStats = $this->reportData->getOrdersByOriginByProduct($local->local_id, $productId, $start, $end);
                $revenueStats = $this->reportData->getRevenueByOriginByProduct($local->local_id, $productId, $start, $end);
                $dailyTrend = $this->reportData->getDailyTrendByProduct($local->local_id, $productId, $start, $end);
                $hasData = $orderStats['total'] > 0;
            }
        } else {
            // Sin producto seleccionado: mostrar datos del local completo
            $orderStats = $this->reportData->getOrdersByOrigin($local->local_id, $start, $end);
            $revenueStats = $this->reportData->getRevenueByOrigin($local->local_id, $start, $end);
            $dailyTrend = $this->reportData->getDailyTrend($local->local_id, $start, $end);
            $hasData = $orderStats['total'] > 0;
        }

        $data = [
            'local' => $local,
            'userLocals' => $userLocals,
            'period' => $period,
            'startDate' => $startDate,
            'endDate' => $endDate,
            'availableProducts' => $availableProducts,
            'orderStats' => $orderStats,
            'revenueStats' => $revenueStats,
            'dailyTrend' => $dailyTrend,
            'hasData' => $hasData,
            'productId' => $productId,
            'productError' => $productError,
        ];

        return view('reports.products-report', $data);
    }

    /**
     * API: Obtener datos en JSON para AJAX
     * Soportar filtrado por producto
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
        $productId = $request->get('product_id');

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

        // Si se especifica producto, validar que tenga costo
        if ($productId) {
            $costValidation = $this->reportData->validateProductCost($productId, $local->local_id);
            if (!$costValidation['valid']) {
                return response()->json([
                    'error' => $costValidation['message'],
                    'product_id' => $productId
                ], 422);
            }
            
            // Obtener datos filtrados por producto
            $orderStats = $this->reportData->getOrdersByOriginByProduct($local->local_id, $productId, $start, $end);
            $revenueStats = $this->reportData->getRevenueByOriginByProduct($local->local_id, $productId, $start, $end);
            $dailyTrend = $this->reportData->getDailyTrendByProduct($local->local_id, $productId, $start, $end);
        } else {
            // Obtener datos generales
            $orderStats = $this->reportData->getOrdersByOrigin($local->local_id, $start, $end);
            $revenueStats = $this->reportData->getRevenueByOrigin($local->local_id, $start, $end);
            $dailyTrend = $this->reportData->getDailyTrend($local->local_id, $start, $end);
        }

        return response()->json([
            'orderStats' => $orderStats,
            'revenueStats' => $revenueStats,
            'dailyTrend' => $dailyTrend,
        ]);
    }

    /**
     * API: Obtener datos completos para vista de productos con gráficas
     */
    public function getProductItems(Request $request)
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

        // Obtener datos generales
        $orderStats = $this->reportData->getOrdersByOrigin($local->local_id, $start, $end);
        $revenueStats = $this->reportData->getRevenueByOrigin($local->local_id, $start, $end);
        $dailyTrend = $this->reportData->getDailyTrend($local->local_id, $start, $end);
        $topItems = $this->reportData->getTopSellingItems($local->local_id, $start, $end);

        // Formatear topItems para JSON
        $items = $topItems->map(function ($item) {
            return [
                'name' => $item->name,
                'total_quantity' => $item->total_quantity,
                'order_count' => $item->order_count,
                'average_per_order' => round($item->total_quantity / $item->order_count, 2)
            ];
        })->toArray();

        return response()->json([
            'hasData' => $orderStats['total'] > 0,
            'orderStats' => $orderStats,
            'revenueStats' => $revenueStats,
            'dailyTrend' => $dailyTrend,
            'items' => $items,
            'periodLabel' => $this->reportData->getPeriodDates($period)['label'] ?? 'Personalizado'
        ]);
    }

    /**
     * API: Obtener datos completos para un producto específico (AJAX para productos-report)
     */
    public function getProductData(Request $request)
    {
        $user = $request->user();
        $userLocals = $user->locals;

        if ($userLocals->isEmpty()) {
            return response()->json(['error' => 'No local found'], 403);
        }

        $localId = $request->get('local_id', $userLocals->first()->local_id);
        $local = $userLocals->firstWhere('local_id', $localId) ?? $userLocals->first();

        $productId = $request->get('product_id');
        
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

        // Obtener datos según si hay producto seleccionado o no
        if ($productId) {
            // Validar que el producto tenga costo
            $costValidation = $this->reportData->validateProductCost($productId, $local->local_id);
            if (!$costValidation['valid']) {
                return response()->json([
                    'error' => $costValidation['message'],
                    'hasData' => false
                ], 422);
            }

            // Obtener datos filtrados por producto
            $orderStats = $this->reportData->getOrdersByOriginByProduct($local->local_id, $productId, $start, $end);
            $revenueStats = $this->reportData->getRevenueByOriginByProduct($local->local_id, $productId, $start, $end);
            $dailyTrend = $this->reportData->getDailyTrendByProduct($local->local_id, $productId, $start, $end);
        } else {
            // Sin producto seleccionado: mostrar datos locales
            $orderStats = $this->reportData->getOrdersByOrigin($local->local_id, $start, $end);
            $revenueStats = $this->reportData->getRevenueByOrigin($local->local_id, $start, $end);
            $dailyTrend = $this->reportData->getDailyTrend($local->local_id, $start, $end);
        }

        // Obtener desglose por origen
        $items = [
            [
                'type' => 'web',
                'name' => 'En Línea',
                'total_quantity' => $orderStats['web']['count'],
                'order_count' => $orderStats['web']['count'],
                'revenue' => $revenueStats['web']['revenue']
            ],
            [
                'type' => 'presential',
                'name' => 'Presencial',
                'total_quantity' => $orderStats['presential']['count'],
                'order_count' => $orderStats['presential']['count'],
                'revenue' => $revenueStats['presential']['revenue']
            ]
        ];

        // Obtener productos más vendidos
        $topItems = $this->reportData->getTopSellingItems($local->local_id, $start, $end);
        $topItemsFormatted = $topItems->map(function ($item) {
            return [
                'name' => $item->name,
                'total_quantity' => $item->total_quantity,
                'order_count' => $item->order_count,
                'revenue' => $item->total_revenue ?? 0
            ];
        })->toArray();

        return response()->json([
            'hasData' => $orderStats['total'] > 0,
            'orderStats' => $orderStats,
            'revenueStats' => $revenueStats,
            'dailyTrend' => $dailyTrend,
            'items' => $items,
            'topItems' => $topItemsFormatted,
            'periodLabel' => $this->reportData->getPeriodDates($period)['label'] ?? 'Personalizado'
        ]);
    }

    /**
     * Exportar reporte a PDF descargable
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
        $productId = $request->get('product_id');

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

        // Si se especifica producto, validar que tenga costo
        if ($productId) {
            $costValidation = $this->reportData->validateProductCost($productId, $local->local_id);
            if (!$costValidation['valid']) {
                return redirect()->back()->with('error', $costValidation['message']);
            }
            
            $orderStats = $this->reportData->getOrdersByOriginByProduct($local->local_id, $productId, $start, $end);
            $revenueStats = $this->reportData->getRevenueByOriginByProduct($local->local_id, $productId, $start, $end);
        } else {
            $orderStats = $this->reportData->getOrdersByOrigin($local->local_id, $start, $end);
            $revenueStats = $this->reportData->getRevenueByOrigin($local->local_id, $start, $end);
        }

        $topItems = $this->reportData->getTopSellingItems($local->local_id, $start, $end);
        $hasData = $orderStats['total'] > 0;

        $data = [
            'local' => $local,
            'orderStats' => $orderStats,
            'revenueStats' => $revenueStats,
            'topItems' => $topItems,
            'hasData' => $hasData,
            'startDate' => $startDate ?? $start->format('Y-m-d'),
            'endDate' => $endDate ?? $end->format('Y-m-d'),
            'exportDate' => Carbon::now()->format('d/m/Y H:i'),
        ];

        $filename = 'reporte_' . Str::slug($local->name) . '_' . now()->format('Y-m-d_His') . '.pdf';
        $pdf = PDF::loadView('reports.orders-report-pdf', $data);
        
        return $pdf->download($filename);
    }

    /**
     * Descargar reporte como HTML descargable
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
        $productId = $request->get('product_id');

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

        // CA2: Si se especifica producto, validar que tenga costo
        if ($productId) {
            $costValidation = $this->reportData->validateProductCost($productId, $local->local_id);
            if (!$costValidation['valid']) {
                return redirect()->back()->with('error', $costValidation['message']);
            }
            
            $orderStats = $this->reportData->getOrdersByOriginByProduct($local->local_id, $productId, $start, $end);
            $revenueStats = $this->reportData->getRevenueByOriginByProduct($local->local_id, $productId, $start, $end);
        } else {
            $orderStats = $this->reportData->getOrdersByOrigin($local->local_id, $start, $end);
            $revenueStats = $this->reportData->getRevenueByOrigin($local->local_id, $start, $end);
        }

        $topItems = $this->reportData->getTopSellingItems($local->local_id, $start, $end);
        $hasData = $orderStats['total'] > 0;

        $data = [
            'local' => $local,
            'orderStats' => $orderStats,
            'revenueStats' => $revenueStats,
            'topItems' => $topItems,
            'hasData' => $hasData,
            'startDate' => $startDate ?? $start->format('Y-m-d'),
            'endDate' => $endDate ?? $end->format('Y-m-d'),
            'exportDate' => Carbon::now()->format('d/m/Y H:i'),
        ];

        $filename = 'reporte_pedidos_' . Str::slug($local->name) . '_' . now()->format('Y-m-d_His') . '.html';
        $html = view('reports.orders-report-export', $data)->render();

        return response($html)
            ->header('Content-Type', 'text/html; charset=utf-8')
            ->header('Content-Disposition', 'attachment; filename="' . $filename . '"');
    }

    /**
     * Exportar reporte a Excel
     * CA3: El reporte es exportable en Excel
     * CA2: Soportar filtrado por producto
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
        $productId = $request->get('product_id');

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

   
        if ($productId) {
            $costValidation = $this->reportData->validateProductCost($productId, $local->local_id);
            if (!$costValidation['valid']) {
                return redirect()->back()->with('error', $costValidation['message']);
            }
            
            $orderStats = $this->reportData->getOrdersByOriginByProduct($local->local_id, $productId, $start, $end);
            $revenueStats = $this->reportData->getRevenueByOriginByProduct($local->local_id, $productId, $start, $end);
        } else {
            $orderStats = $this->reportData->getOrdersByOrigin($local->local_id, $start, $end);
            $revenueStats = $this->reportData->getRevenueByOrigin($local->local_id, $start, $end);
        }

        $topItems = $this->reportData->getTopSellingItems($local->local_id, $start, $end);

        // Crear spreadsheet con PhpSpreadsheet
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        
        // Encabezados
        $sheet->setCellValue('A1', 'REPORTE DE PEDIDOS');
        $sheet->setCellValue('A2', $local->name);
        $sheet->setCellValue('A3', 'Período: ' . $start->format('d/m/Y') . ' - ' . $end->format('d/m/Y'));
        
        // Hacer encabezados negrita
        $sheet->getStyle('A1:A3')->getFont()->setBold(true);
        
        // Resumen
        $sheet->setCellValue('A5', 'RESUMEN POR TIPO DE PEDIDO');
        $sheet->getStyle('A5')->getFont()->setBold(true);
        
        $sheet->setCellValue('A6', 'Tipo');
        $sheet->setCellValue('B6', 'Cantidad');
        $sheet->setCellValue('C6', 'Porcentaje');
        $sheet->setCellValue('D6', 'Ingresos (₡)');
        $sheet->setCellValue('E6', 'Promedio (₡)');
        $sheet->getStyle('A6:E6')->getFont()->setBold(true);
        
        // Datos de resumen
        $webAvg = $orderStats['web']['count'] > 0 
            ? $revenueStats['web']['revenue'] / $orderStats['web']['count'] 
            : 0;
        $presentialAvg = $orderStats['presential']['count'] > 0 
            ? $revenueStats['presential']['revenue'] / $orderStats['presential']['count'] 
            : 0;
        $totalAvg = $orderStats['total'] > 0 
            ? $revenueStats['total'] / $orderStats['total'] 
            : 0;
        
        $sheet->setCellValue('A7', 'En Línea');
        $sheet->setCellValue('B7', $orderStats['web']['count']);
        $sheet->setCellValue('C7', $orderStats['web']['percentage'] . '%');
        $sheet->setCellValue('D7', $revenueStats['web']['revenue']);
        $sheet->setCellValue('E7', round($webAvg, 2));
        
        $sheet->setCellValue('A8', 'Presencial');
        $sheet->setCellValue('B8', $orderStats['presential']['count']);
        $sheet->setCellValue('C8', $orderStats['presential']['percentage'] . '%');
        $sheet->setCellValue('D8', $revenueStats['presential']['revenue']);
        $sheet->setCellValue('E8', round($presentialAvg, 2));
        
        $sheet->setCellValue('A9', 'TOTAL');
        $sheet->setCellValue('B9', $orderStats['total']);
        $sheet->setCellValue('C9', '100%');
        $sheet->setCellValue('D9', $revenueStats['total']);
        $sheet->setCellValue('E9', round($totalAvg, 2));
        $sheet->getStyle('A9:E9')->getFont()->setBold(true);
        
        // Productos más vendidos
        $sheet->setCellValue('A11', 'PRODUCTOS MÁS VENDIDOS');
        $sheet->getStyle('A11')->getFont()->setBold(true);
        
        $sheet->setCellValue('A12', 'Producto');
        $sheet->setCellValue('B12', 'Cantidad Vendida');
        $sheet->setCellValue('C12', 'Transacciones');
        $sheet->getStyle('A12:C12')->getFont()->setBold(true);
        
        $row = 13;
        foreach ($topItems as $item) {
            $sheet->setCellValue('A' . $row, $item->name);
            $sheet->setCellValue('B' . $row, $item->total_quantity);
            $sheet->setCellValue('C' . $row, $item->order_count);
            $row++;
        }
        
        // Autoajustar columnas
        $sheet->getColumnDimension('A')->setAutoSize(true);
        $sheet->getColumnDimension('B')->setAutoSize(true);
        $sheet->getColumnDimension('C')->setAutoSize(true);
        $sheet->getColumnDimension('D')->setAutoSize(true);
        $sheet->getColumnDimension('E')->setAutoSize(true);
        
        // Descargar archivo
        $filename = 'reporte_pedidos_' . Str::slug($local->name) . '_' . now()->format('Y-m-d_His') . '.xlsx';
        $writer = new Xlsx($spreadsheet);
        
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        
        $writer->save('php://output');
        exit;
    }
}
