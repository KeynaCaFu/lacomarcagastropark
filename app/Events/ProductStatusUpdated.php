<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ProductStatusUpdated implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public int $productId;
    public int $localId;
    public string $status;
    public string $productName;

    public function __construct(int $productId, int $localId, string $status, string $productName)
    {
        $this->productId   = $productId;
        $this->localId     = $localId;
        $this->status      = $status;
        $this->productName = $productName;
    }

    public function broadcastOn(): Channel
    {
        return new Channel('establishment-updates.' . $this->localId);
    }

    public function broadcastWith(): array
    {
        return [
            'product_id'   => $this->productId,
            'local_id'     => $this->localId,
            'status'       => $this->status,
            'product_name' => $this->productName,
        ];
    }
}
