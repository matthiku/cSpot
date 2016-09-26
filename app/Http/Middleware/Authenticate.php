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
    public function handle($request, Closure $next, $guard = null)
    {
        if (Auth::guard($guard)->guest()) {
            if ($request->ajax()) {
                return response('Unauthorized.', 401);
            } else {
                return redirect()->guest('login');
            }
        }

        // this route is only for admins...
        if ($request->path()=='admin/logs' && ! Auth::user()->isAdmin() ) {
            return redirect('home');
        }

        Log::info($request->ip().' handling an incoming request for '.Auth::user()->getFullName . ' to path '.$request->path());

        return $next( $request );

    }
    
}
