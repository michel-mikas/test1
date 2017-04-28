<?php

namespace App\Http\Middleware;

use Closure;
use Session;
use Illuminate\Support\Facades\Auth;

class RedirectIfAuthenticated
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
        $guards = array('admin', 'user', 'chef');
        $guard = $request->segments()[1];
        if(in_array($guard, $guards)) {
            if(Auth::guard($guard)->check()) {
                return $next($request);
            }
        }
        Session::flash('flash_error', 'You are off ' .$guard. ', please login again');
        return redirect(url(Session::get('lang'). '/' .$guard. '/login'));
    }
}
