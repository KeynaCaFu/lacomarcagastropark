<?php

namespace App\Events;

use App\Models\Order;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class OrderCancelled implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public int $localId;
    public int $orderId;
    public string $orderNumber;
    public string $customerName;
    public string $reason;

    public function __construct(Order $order, string $customerName)
    {
        $this->localId      = $order->local_id;
        $this->orderId      = $order->order_id;
        $this->orderNumber  = $order->order_number;
        $this->customerName = $customerName;
        $this->reason       = $order->cancellation_reason ?? 'Cancelada por el cliente';
    }

    public function broadcastOn(): Channel
    {
        return new Channel('orders.' . $this->localId);
    }

    public function broadcastWith(): array
    {
        return [
            'order_id'      => $this->orderId,
            'order_number'  => $this->orderNumber,
            'local_id'      => $this->localId,
            'customer_name' => $this->customerName,
            'reason'        => $this->reason,
            'message'       => "El cliente {$this->customerName} canceló la orden {$this->orderNumber}",
        ];
    }
}
