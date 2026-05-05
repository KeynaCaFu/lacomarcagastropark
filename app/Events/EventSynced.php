<?php

namespace App\Events;

use App\Models\Event;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class EventSynced implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public string $action;
    public int $eventId;
    public ?string $title;
    public ?string $description;
    public ?string $startAt;
    public ?string $location;
    public ?string $imageUrl;

    /**
     * @param string $action  'upsert' para crear/activar/editar, 'remove' para desactivar/eliminar
     */
    public function __construct(string $action, Event $event)
    {
        $this->action      = $action;
        $this->eventId     = $event->event_id;
        $this->title       = $event->title;
        $this->description = $event->description;
        $this->startAt     = $event->start_at?->toIso8601String();
        $this->location    = $event->location;
        $this->imageUrl    = $event->image_url;
    }

    public function broadcastOn(): Channel
    {
        return new Channel('public-events');
    }

    public function broadcastWith(): array
    {
        return [
            'action'      => $this->action,
            'event_id'    => $this->eventId,
            'title'       => $this->title,
            'description' => $this->description,
            'start_at'    => $this->startAt,
            'location'    => $this->location,
            'image_url'   => $this->imageUrl,
        ];
    }
}
