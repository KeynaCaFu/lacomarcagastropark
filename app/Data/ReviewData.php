<?php

namespace App\Data;

use App\Models\LocalReview;
use App\Models\ProductReview;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ReviewData
{
    public function getLocalReviewsByLocal($localId)
    {
        return LocalReview::with(['review', 'user'])
            ->where('local_id', $localId)
            ->whereHas('review')
            ->orderByDesc('local_review_id')
            ->get();
    }

    public function getProductReviewsByLocal($localId)
    {
        $productIds = DB::table('tblocal_product')
            ->where('local_id', $localId)
            ->pluck('product_id');

        return ProductReview::with(['review', 'user', 'product'])
            ->whereIn('product_id', $productIds)
            ->whereHas('review')
            ->orderByDesc('product_review_id')
            ->get();
    }

    public function respondToLocalReview($localReviewId, $localId, $response)
    {
        $localReview = LocalReview::with('review')
            ->where('local_review_id', $localReviewId)
            ->where('local_id', $localId)
            ->first();

        if (!$localReview || !$localReview->review) {
            return false;
        }

        $localReview->review->update([
            'response' => $response
        ]);

        return true;
    }

    public function respondToProductReview($productReviewId, $localId, $response)
    {
        $productIds = DB::table('tblocal_product')
            ->where('local_id', $localId)
            ->pluck('product_id');

        $productReview = ProductReview::with('review')
            ->where('product_review_id', $productReviewId)
            ->whereIn('product_id', $productIds)
            ->first();

        if (!$productReview || !$productReview->review) {
            return false;
        }

        $productReview->review->update([
            'response' => $response
        ]);

        return true;
    }

    public function getStats($reviews)
    {
        $items = collect($reviews)->filter(function ($item) {
            return $item->review != null;
        });

        $total = $items->count();

        $average = $total > 0
            ? round($items->avg(function ($item) {
                return $item->review->rating;
            }), 1)
            : 0;

       
$monthTotal = $items->filter(function ($item) {
    if (!$item->review || !$item->review->date) {
        return false;
    }
    try {
        $date = Carbon::parse($item->review->date);
        return $date->month == now()->month && $date->year == now()->year;
    } catch (\Exception $e) {
        return false;
    }
})->count();

        $distribution = [
            5 => $items->filter(fn($item) => (int)$item->review->rating === 5)->count(),
            4 => $items->filter(fn($item) => (int)$item->review->rating === 4)->count(),
            3 => $items->filter(fn($item) => (int)$item->review->rating === 3)->count(),
            2 => $items->filter(fn($item) => (int)$item->review->rating === 2)->count(),
            1 => $items->filter(fn($item) => (int)$item->review->rating === 1)->count(),
        ];

        return [
            'total' => $total,
            'average' => $average,
            'month_total' => $monthTotal,
            'distribution' => $distribution,
        ];
    }
}