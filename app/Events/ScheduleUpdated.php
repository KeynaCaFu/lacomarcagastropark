<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ScheduleUpdated implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public array $schedules;
    public int $localId;

    public function __construct(array $schedules, int $localId)
    {
        $this->schedules = $schedules;
        $this->localId   = $localId;
    }

    public function broadcastOn(): Channel
    {
        return new Channel('establishment-updates.' . $this->localId);
    }

    public function broadcastWith(): array
    {
        return [
            'local_id'  => $this->localId,
            'schedules' => $this->schedules,
        ];
    }
}
