<?php

namespace App\Providers;

use Illuminate\Support\Facades\Event;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array
     */
    protected $listen = [
        'App\Events\NewMessageGenerated' => [
            'App\Listeners\NewMessagesListener',
        ],
        'App\Events\CspotItemUpdated' => [
            'App\Listeners\CspotItemsListener',
        ],
        'App\Events\UserLogin' => [
            'App\Listeners\UserLoginListener',
        ],
        'App\Events\SyncPresentation' => [
            'App\Listeners\SyncPresentationListener',
        ],
    ];

    /**
     * Register any other events for your application.
     *
     * @return void
     */
    public function boot()
    {
        //

        parent::boot();
    }
}
