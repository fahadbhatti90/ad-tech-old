<?php

namespace App\Http\Middleware;

use Closure;
use Auth;
class AccessManager
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
        //if user is manager then set activeRole = 3 in session
        if (Auth::check() && Auth::user()->hasAnyRole(3))
        {
            ifSessionBrandGotUnassigned();
            session(['activeRole' => 3]);
            return $next($request);
        }
        //if user not logged in redirect to login page
        if(!Auth::check()){
            return response()->json([
                "isLogged"=>false, 
                "Auth::check()"=>Auth::check(),
                "activeRole"=>session()->has("activeRole")?session("activeRole"):0,
                "csrf"=>\csrf_token(),
                "csrf"=>\csrf_token()
            ], 401);
        }
        /*
        *check redirct by default if above conditions faild  
        definition app/librarires/HelperFunction.php*/
        $redirectToDashboard = redirectToDashboard();
        $respon = ["isLogged"=>true,"user"=>auth()->user(),"activeRole"=>session("activeRole")];
        
        return response()->json(session("activeRole") == 3 ? $respon : ["isLogged"=>false,"activeRole"=>session()->has("activeRole")?session("activeRole"):0], session("activeRole") == 3 ? 200 : 401);
    }
}
