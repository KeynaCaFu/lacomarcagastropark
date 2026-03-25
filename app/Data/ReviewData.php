<?php

namespace App\Data;

use App\Models\LocalReview;

class ReviewData
{
    public function getByLocal($localId)
    {
        return LocalReview::with(['review', 'user', 'local'])
            ->where('local_id', $localId)
            ->orderByDesc('local_review_id')
            ->get();
    }

    public function respond($localReviewId, $localId, $response)
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
}