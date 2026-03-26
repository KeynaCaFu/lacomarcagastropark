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
}