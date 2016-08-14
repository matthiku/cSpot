<?php

namespace App\Events;

use App\Models\Item;

use App\Events\Event;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class CspotItemUpdated extends Event
{
    use SerializesModels;

    /**
     * Create a new event instance.
     *
     * @param  Item     $item
     * @return void
     */
    public function __construct(Item $item)
    {
        //
        $this->item = $item;
    }

    /**
     * Get the channels the event should be broadcast on.
     *
     * @return array
     */
    public function broadcastOn()
    {
        return [];
    }
}
