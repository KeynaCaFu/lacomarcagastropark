<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Data\ReviewData;

class ReviewController extends Controller
{
    protected $reviewData;

    public function __construct(ReviewData $reviewData)
    {
        $this->reviewData = $reviewData;
    }

    public function index(Request $request)
    {
        $user = $request->user();
        $local = $user->locals()->first();

        $localReviews = collect();
        $productReviews = collect();

        $localStats = [
            'total' => 0,
            'average' => 0,
            'month_total' => 0,
            'distribution' => [5 => 0, 4 => 0, 3 => 0, 2 => 0, 1 => 0],
        ];

        $productStats = [
            'total' => 0,
            'average' => 0,
            'month_total' => 0,
            'distribution' => [5 => 0, 4 => 0, 3 => 0, 2 => 0, 1 => 0],
        ];

        if ($local) {
            $localReviews = $this->reviewData->getLocalReviewsByLocal($local->local_id);
            $productReviews = $this->reviewData->getProductReviewsByLocal($local->local_id);

            $localStats = $this->reviewData->getStats($localReviews);
            $productStats = $this->reviewData->getStats($productReviews);
        }

        return view('reviews.index', compact(
            'local',
            'localReviews',
            'productReviews',
            'localStats',
            'productStats'
        ));
    }

    public function respond(Request $request, $id)
    {
        $request->validate([
            'response' => 'required|string|max:1000',
            'review_type' => 'required|in:local,product'
        ], [
            'response.required' => 'La respuesta es obligatoria.',
            'response.max' => 'La respuesta no puede tener más de 1000 caracteres.',
            'review_type.required' => 'El tipo de reseña es obligatorio.',
            'review_type.in' => 'Tipo de reseña inválido.'
        ]);

        $user = $request->user();
        $local = $user->locals()->first();

        if (!$local) {
            return redirect()->route('reviews.index')
                ->with('error', 'No tienes un local asignado.');
        }

        $ok = false;

        if ($request->review_type === 'local') {
            $ok = $this->reviewData->respondToLocalReview(
                $id,
                $local->local_id,
                $request->response
            );
        }

        if ($request->review_type === 'product') {
            $ok = $this->reviewData->respondToProductReview(
                $id,
                $local->local_id,
                $request->response
            );
        }

        if (!$ok) {
            return redirect()->route('reviews.index')
                ->with('error', 'La reseña no pertenece a tu local o a tus productos.');
        }

        return redirect()->route('reviews.index')
            ->with('success', 'Respuesta guardada correctamente.');
    }

    
    public function updateResponse(Request $request, $reviewId)
{
    $request->validate([
        'response' => 'required|string|max:1000'
    ], [
        'response.required' => 'La respuesta es obligatoria.',
        'response.max' => 'La respuesta no puede tener más de 1000 caracteres.'
    ]);

    $user = $request->user();
    $local = $user->locals()->first();

    if (!$local) {
        return redirect()->route('reviews.index')
            ->with('error', 'No tienes un local asignado.');
    }

    $review = \App\Models\Review::find($reviewId);

    if (!$review) {
        return redirect()->route('reviews.index')
            ->with('error', 'La reseña no existe.');
    }

    $belongsToLocal = \App\Models\LocalReview::where('review_id', $reviewId)
        ->where('local_id', $local->local_id)
        ->exists();

    $productIds = \Illuminate\Support\Facades\DB::table('tblocal_product')
        ->where('local_id', $local->local_id)
        ->pluck('product_id');

    $belongsToProduct = \App\Models\ProductReview::where('review_id', $reviewId)
        ->whereIn('product_id', $productIds)
        ->exists();

    if (!$belongsToLocal && !$belongsToProduct) {
        return redirect()->route('reviews.index')
            ->with('error', 'No puedes editar una respuesta de una reseña que no pertenece a tu local.');
    }

    $review->update([
        'response' => $request->response
    ]);

    return redirect()->route('reviews.index')
        ->with('success', 'Respuesta actualizada correctamente.');
}

public function deleteResponse(Request $request, $reviewId)
{
    $user = $request->user();
    $local = $user->locals()->first();

    if (!$local) {
        return redirect()->route('reviews.index')
            ->with('error', 'No tienes un local asignado.');
    }

    $review = \App\Models\Review::find($reviewId);

    if (!$review) {
        return redirect()->route('reviews.index')
            ->with('error', 'La reseña no existe.');
    }

    $belongsToLocal = \App\Models\LocalReview::where('review_id', $reviewId)
        ->where('local_id', $local->local_id)
        ->exists();

    $productIds = \Illuminate\Support\Facades\DB::table('tblocal_product')
        ->where('local_id', $local->local_id)
        ->pluck('product_id');

    $belongsToProduct = \App\Models\ProductReview::where('review_id', $reviewId)
        ->whereIn('product_id', $productIds)
        ->exists();

    if (!$belongsToLocal && !$belongsToProduct) {
        return redirect()->route('reviews.index')
            ->with('error', 'No puedes eliminar una respuesta de una reseña que no pertenece a tu local.');
    }

    $review->update([
        'response' => null
    ]);

    return redirect()->route('reviews.index')
        ->with('success', 'Respuesta eliminada correctamente.');
}






























    
}