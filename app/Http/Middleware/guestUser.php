<?php

namespace App\Http\Middleware;

use Closure;
use Auth;

class guestUser
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
        if (Auth::check())//if login
        if (Auth::user()->hasAnyRoles([1,2,3])){
            return response()->json(["isLogged"=>true,"user"=>auth()->user(),"activeRole"=>session("activeRole")],200);
        }
        session()->forget('activeRole');
        return $next($request);
    }
}