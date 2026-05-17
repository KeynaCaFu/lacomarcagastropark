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

    public int    $localId;
    public int    $reviewId;
    public int    $reviewEntryId;
    public string $reviewType;
    public string $clientName;
    public string $productName;
    public int    $rating;
    public string $comment;

    public function __construct(
        int    $localId,
        int    $reviewId,
        int    $reviewEntryId,
        string $reviewType,
        string $clientName,
        string $productName,
        int    $rating,
        string $comment = ''
    ) {
        $this->localId       = $localId;
        $this->reviewId      = $reviewId;
        $this->reviewEntryId = $reviewEntryId;
        $this->reviewType    = $reviewType;
        $this->clientName    = $clientName;
        $this->productName   = $productName;
        $this->rating        = $rating;
        $this->comment       = $comment;
    }

    public function broadcastOn(): Channel
    {
        return new Channel('local.' . $this->localId);
    }

    public function broadcastWith(): array
    {
        return [
            'local_id'        => $this->localId,
            'review_id'       => $this->reviewId,
            'review_entry_id' => $this->reviewEntryId,
            'review_type'     => $this->reviewType,
            'client_name'     => $this->clientName,
            'product_name'    => $this->productName,
            'rating'          => $this->rating,
            'comment'         => $this->comment,
            'message'         => $this->comment,
        ];
    }
}