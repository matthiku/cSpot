<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class SyncPresentation implements ShouldBroadcast
{
    use InteractsWithSockets, SerializesModels;

    public $syncData;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct($syncData)
    {
        //
        $this->syncData = $syncData;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return Channel|array
     */
    public function broadcastOn()
    {
        return new Channel('sync-presentation');
    }
}
