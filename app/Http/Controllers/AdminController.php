<?php

namespace App\Http\Controllers;

use DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Events\SendClientLoginStatus;

class AdminController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth.guestUser')->except(['logout']);
    }
    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\RedirectResponse|\Illuminate\View\View
     */
    public function login()
    {
        return view('login');
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     *
     */
    public function authenticate(Request $request)
    {
        $this->validate($request,[
            "email" => 'required|email|string',
            'password' => 'required',
        ]);//end validation

        $credentials = $request->only('email', 'password');

        if (Auth::attempt($credentials,$request->remember)) {
            /*Authentication passed...
            *get route to redirect after successfull login.
            *definition app/librarires/HelperFunction.php*/
            $redirectToDashboard = redirectToDashboard();
            try {
                broadcast(new SendClientLoginStatus(auth()->user()->id, session("activeRole"), true));
            } catch (\Throwable $th) {
                \Log::info($th->getMessage());
            }
            
           return ["isLogged"=>true,"user"=>auth()->user(),"activeRole"=>session("activeRole")]; // redirect()->route($redirectToDashboard);

        } else {
            return ["isLogged"=>false];
            // redirect()->route("login")->withInput($request->only("email","remember"))
            // ->withstatus("Email or password is not valid!");
        }
    }

    /**
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function logout(Request $request)
    {
        try {
            broadcast(new SendClientLoginStatus(auth()->user()->id, session("activeRole"), false));
        } catch (\Throwable $th) {
            \Log::info($th->getMessage());
        }
        Auth::guard("web")->logout();
        session()->flush();
        session()->regenerateToken();
        return [
            "isLogged"=>false,
            "csrf"=>\csrf_token(),
        ];
        // return redirect('/');
    }


}
