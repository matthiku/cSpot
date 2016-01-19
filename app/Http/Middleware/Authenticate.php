<?php

namespace App\Http\Middleware;

use Log;
use Closure;
use Illuminate\Support\Facades\Auth;

class Authenticate
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string|null  $guard
     * @return mixed
     */
    public function handle($request, Closure $next, $guard = null, $role = 'all')
    {
        if (Auth::guard($guard)->guest()) {
            if ($request->ajax()) {
                return response('Unauthorized.', 401);
            } else {
                return redirect()->guest('login');
            }
        }


        // routes for all users
        if ($role == 'all') 
        {
            Log::info('handling an incoming request for '.Auth::user()->name . ' to path '.$request->path());

            return $next( $request );
        }


        // routes only for users with this role
        Log::info('handling an incoming request for '.Auth::user()->name.' to path '.$request->path().' with role '.$role  );

        if ( Auth::user()->hasRole($role) ) 
        {
            return $next( $request );
        }
        else 
        {
            if ($request->ajax()) {
                return response('Unauthorized.', 401);
            } else {
                return redirect('home')->with('error', 'Not authorized!');
            }
        }
    }
}
