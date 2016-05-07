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


        // provide the PATH to the (custom) logos to all views
        if ( strtolower(env('USE_CUSTOM_LOGOS')) == 'yes' ) {
            view()->share('logoPath', 'images/custom/');
        } else { 
            view()->share('logoPath', 'images/'); 
        }

        // provide a list (array) of user-id's with Admin rights to all views (for page feedback messages)
        view()->share('administrators', findAdmins('id'));
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
