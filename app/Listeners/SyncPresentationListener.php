<?php

namespace App\Listeners;

use App\Events\SyncPresentation;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class SyncPresentationListener
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
     * @param  SyncPresentation  $event
     * @return void
     */
    public function handle(SyncPresentation $event)
    {
        // broadcast this event to all other presenters
    }
}
