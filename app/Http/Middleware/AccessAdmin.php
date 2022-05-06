<?php

namespace App\Http\Middleware;

use Closure;
use Auth;
class AccessAdmin
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
        //if user is admin then set activeRole = 2 in session
        if (Auth::check() && Auth::user()->hasAnyRole(2)){
            ifSessionBrandGotUnassigned();
             session(['activeRole' => 2]);
            return $next($request);
        }
        //if user not logged in redirect to login page
        if(!Auth::check()){
            return response()->json([
                "isLogged"=>false,
                "Auth::check()"=>Auth::check(),
                "activeRole"=>session()->has("activeRole")?session("activeRole"):0,
                "csrf"=>\csrf_token()
            ], 401);
        }
        
        /*
        *check redirct by default if above conditions faild  
        definition app/librarires/HelperFunction.php*/
        $redirectToDashboard = redirectToDashboard();
        return response()->json(["isLogged"=>true,"user"=>auth()->user(),"activeRole"=>session("activeRole")], 200);

    }
}