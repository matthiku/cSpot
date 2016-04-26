<?php

namespace App\Providers;


use Auth;
use App\Models\Item;
use App\Models\Plan;
use App\Models\User;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        //
        Item::updated( function ($item) {
            // when a user changes an item, we 
            // update the 'changer' field on the parent model (plan)
            $plan = Plan::find($item->plan_id);
            $plan->update(['changer' => Auth::user()->first_name]);
        });


        // provide a list (array) of users with Admin rights to all views (for page feedback messages)
        $users = User::get();
        $admins = [];
        foreach ($users as $user) {
            if ($user->isAdmin()) {
                array_push($admins, $user->id);
            }
        }
        view()->share('administrators', $admins);
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }
}
