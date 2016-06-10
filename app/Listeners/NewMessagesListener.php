<?php

namespace App\Listeners;

use App\Events\NewMessageGenerated;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class NewMessagesListener
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  NewMessageGenerated  $event
     * @return void
     */
    public function handle(NewMessageGenerated $event)
    {
        // not implemented ...
    }
}
