<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Data\ProductData;

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
            return view('dashboard', compact('local', 'totals', 'categories', 'recentProducts'));
        }

        $productData = new ProductData();

        $totals = $productData->countTotalsByLocal($local->local_id);
        $categories = $productData->getCategoriesByLocal($local->local_id);
        $recentProducts = Product::byLocal($local->local_id)
            ->orderByDesc('product_id')
            ->limit(8)
            ->get(['product_id','name','status','price','category']);

        return view('dashboard', compact('local', 'totals', 'categories', 'recentProducts'));
    }
}
