<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Data\ProductData;
use App\Data\SupplierData;
use App\Models\Supplier;
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
    
            return view('dashboard', compact('local','totals','categories','recentProducts','supplierTotals','recentSuppliers'
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


        return view('dashboard', compact('local', 'totals', 'categories', 'recentProducts', 'supplierTotals', 'recentSuppliers'
        ));
    }
}
