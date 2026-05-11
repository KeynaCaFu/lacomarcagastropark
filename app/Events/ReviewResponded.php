<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ReviewResponded implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public int $userId;
    public int $reviewId;
    public string $localName;
    public string $productName;

    public function __construct(
        int $userId,
        int $reviewId,
        string $localName,
        string $productName
    ) {
        $this->userId      = $userId;
        $this->reviewId    = $reviewId;
        $this->localName   = $localName;
        $this->productName = $productName;
    }

    /**
     * Canal privado por usuario — solo el cliente que hizo la reseña recibe la notificación (CA2)
     */
    public function broadcastOn(): Channel
    {
        return new Channel('user.' . $this->userId);
    }

    public function broadcastWith(): array
    {
        return [
            'user_id'      => $this->userId,
            'review_id'    => $this->reviewId,
            'local_name'   => $this->localName,
            'product_name' => $this->productName,
            'message'      => "El gerente respondió tu reseña en {$this->localName}",
        ];
    }
}