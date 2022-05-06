<?php

namespace App\Http\Controllers\ClientAuth;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Events\SendClientLoginStatus;

class ClientLoginController extends Controller
{
    public function __construct()
    {
        $this->middleware('guest:client')->except('logout');
    }
    public function ShowloginForm(){
        return view("client.login");
    }//get login function

    public function login(Request $request){
        $this->validate($request,[
            "email" => 'required|email|string',
            'password' => 'required',
        ]);//end validation

        //Attempting Login
        $credentials = $request->only('email', 'password');
        if (Auth::guard("client")->attempt($credentials,$request->remember)) {
            // Authentication passed...
            try {
                broadcast(new SendClientLoginStatus(auth()->guard("client")->user()->id));
            } catch (\Throwable $th) {
                Log::info($th->getMessage());
            }
            return redirect()->intended(route("client.dashboard"));
        } 

        return redirect()->back()
        ->withInput($request->only("email","remember"))
        ->withstatus("Email or password is not valid!");

    }//post login function

    public function logout(){
        if(auth()->guard("client")->check())
        try {
            broadcast(new SendClientLoginStatus(auth()->guard("client")->user()->id));
        } catch (\Throwable $th) {
            Log::info($th->getMessage());
        }
        Auth::guard('client')->logout();
        return redirect()->route("client.login");
    }

}//end class
