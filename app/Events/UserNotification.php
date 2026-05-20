<?php

namespace App\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class UserNotification implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(
        public int    $userId,
        public string $type,      // 'order_update' | 'review_reply'
        public string $message,
        public string $section,   // 'pedidos' | 'resenas'
        public string $icon = 'bell',
        public array  $extra = []
    ) {}

    public function broadcastOn(): PrivateChannel
    {
        return new PrivateChannel("App.Models.User.{$this->userId}");
    }

    public function broadcastAs(): string
    {
        return 'UserNotification';
    }

    public function broadcastWith(): array
    {
        return array_merge([
            'type'    => $this->type,
            'message' => $this->message,
            'section' => $this->section,
            'icon'    => $this->icon,
        ], $this->extra);
    }
}
