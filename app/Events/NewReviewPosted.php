<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class NewReviewPosted implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public int $localId;
    public int $reviewId;
    public string $clientName;
    public string $productName;
    public int $rating;

    public function __construct(
        int $localId,
        int $reviewId,
        string $clientName,
        string $productName,
        int $rating
    ) {
        $this->localId      = $localId;
        $this->reviewId     = $reviewId;
        $this->clientName   = $clientName;
        $this->productName  = $productName;
        $this->rating       = $rating;
    }

    public function broadcastOn(): Channel
    {
        return new Channel('local.' . $this->localId);
    }

    public function broadcastWith(): array
    {
        return [
            'local_id'     => $this->localId,
            'review_id'    => $this->reviewId,
            'client_name'  => $this->clientName,
            'product_name' => $this->productName,
            'rating'       => $this->rating,
            'message'      => "Nueva reseña de {$this->clientName} en {$this->productName}",
        ];
    }
}