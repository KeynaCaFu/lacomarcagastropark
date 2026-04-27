<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Data\ProductData;
use App\Data\SupplierData;
use App\Models\Supplier;
use App\Models\Order;
use Carbon\Carbon;

class LocalDashboardController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        $local = $user->locals()->first();
        
        if (!$local) {
            // Si el gerente no tiene local asignado, mostrar dashboard vacío
            $totals = ['total' => 0, 'available' => 0, 'unavailable' => 0];
            $categories = collect();
            $recentProducts = collect();
            $supplierTotals = ['total' => 0];
            $recentSuppliers = collect();
            $salesLastMonth = 0;
            $activeOrders = 0;
            $recentOrders = collect();
            $ordersByStatus = collect();
    
            return view('dashboard', compact('local','totals','categories','recentProducts','supplierTotals','recentSuppliers','salesLastMonth','activeOrders','recentOrders','ordersByStatus'
            ));
            
        }

        $productData = new ProductData();
        $supplierData = new SupplierData();
        $totals = $productData->countTotalsByLocal($local->local_id);
        $categories = $productData->getCategoriesByLocal($local->local_id);
        $recentProducts = Product::byLocal($local->local_id)
            ->orderByDesc('product_id')
            ->limit(8)
            ->get(['product_id','name','status','price','category']);

        $supplierTotals = $supplierData->countTotalsByLocal($local->local_id);
        $recentSuppliers = Supplier::byLocal($local->local_id)
            ->orderByDesc('supplier_id')
            ->limit(8)
            ->get(['supplier_id', 'name', 'phone', 'email']);

        // NUEVAS MÉTRICAS DE VENTAS
        $lastMonth = Carbon::now()->subMonth();
        
        // Ventas del último mes
        $salesLastMonth = Order::where('local_id', $local->local_id)
            ->whereDate('date', '>=', $lastMonth)
            ->whereIn('status', ['Delivered', 'Completed'])
            ->sum('total_amount');
        
        // Órdenes activas
        $activeOrders = Order::where('local_id', $local->local_id)
            ->whereIn('status', ['Pending', 'In Progress', 'Ready'])
            ->count();
        
        // Órdenes recientes
        $recentOrders = Order::where('local_id', $local->local_id)
            ->orderByDesc('created_at')
            ->limit(10)
            ->get(['order_id', 'total_amount', 'status', 'date', 'created_at']);
        
        // Órdenes por estado para gráfico
        $ordersByStatus = Order::where('local_id', $local->local_id)
            ->selectRaw('status, COUNT(*) as count')
            ->groupBy('status')
            ->get()
            ->mapWithKeys(function ($item) {
                return [$item->status => $item->count];
            });

        return view('dashboard', compact('local', 'totals', 'categories', 'recentProducts', 'supplierTotals', 'recentSuppliers', 'salesLastMonth', 'activeOrders', 'recentOrders', 'ordersByStatus'
        ));
    }
}
