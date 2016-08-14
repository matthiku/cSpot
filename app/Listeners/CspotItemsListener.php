<?php

namespace App\Listeners;

use App\Models\PlanCache;

use App\Events\CspotItemUpdated;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class CspotItemsListener
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
     * @param  CspotItemUpdated  $event
     * @return void
     */
    public function handle(CspotItemUpdated $event)
    {
        // get item
        $key  = 'offline-'.$event->item->plan_id;
        $key .= '-'.(1*$event->item->seq_no).'-%'; 

        // delete cached data of this item as it's now outdated
        $cache = PlanCache::where('key', 'like', $key)->delete();

    }
}
