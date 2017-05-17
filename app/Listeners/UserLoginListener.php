<?php

namespace App\Listeners;

use Log;
use Carbon\Carbon;
use App\Models\Login;
use App\Events\UserLogin;
use Illuminate\Http\Request;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class UserLoginListener
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
     * @param  UserLogin  $event
     * @return void
     */
    public function handle(UserLogin $event)
    {
        Log::info('User logged in successfully: '.$event->user->fullName );

        //
        $login = new Login;
        $login->addr = $event->request->ip();
        $event->user->logins()->save($login);

        // write last login date into users table
        $event->user->update(['last_login' => Carbon::now()]);
    }
}
