<?php

namespace App\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class OrderStatusUpdated implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public int $orderId;
    public string $status;
    public string $updatedAt;

    public function __construct(int $orderId, string $status, string $updatedAt)
    {
        $this->orderId   = $orderId;
        $this->status    = $status;
        $this->updatedAt = $updatedAt;
    }

    public function broadcastOn(): PrivateChannel
    {
        return new PrivateChannel('order.' . $this->orderId);
    }

    public function broadcastWith(): array
    {
        return [
            'order_id'   => $this->orderId,
            'status'     => $this->status,
            'updated_at' => $this->updatedAt,
        ];
    }
}
