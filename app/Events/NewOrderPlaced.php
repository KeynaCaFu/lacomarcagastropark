<?php

namespace App\Events;

use App\Models\Order;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class NewOrderPlaced implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public int $localId;
    public int $orderId;
    public string $orderNumber;
    public string $customerName;
    public float $totalAmount;
    public int $quantity;
    public string $time;
    public array $items;

    public function __construct(Order $order, string $customerName, array $items)
    {
        $this->localId      = $order->local_id;
        $this->orderId      = $order->order_id;
        $this->orderNumber  = $order->order_number;
        $this->customerName = $customerName;
        $this->totalAmount  = (float) $order->total_amount;
        $this->quantity     = $order->quantity;
        $this->time         = $order->time;
        $this->items        = $items;
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
            'total_amount'  => $this->totalAmount,
            'quantity'      => $this->quantity,
            'time'          => $this->time,
            'items'         => $this->items,
            'message'       => "Nueva orden {$this->orderNumber} de {$this->customerName}",
        ];
    }
}
