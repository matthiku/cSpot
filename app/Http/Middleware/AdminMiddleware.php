<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;


class AdminMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if (Auth::user()->id<>1 && !Auth::user()->is_admin ) {
            return redirect('home')->with('error', 'Error! Unauthorized!');
        }
        return $next($request);
    }
}
