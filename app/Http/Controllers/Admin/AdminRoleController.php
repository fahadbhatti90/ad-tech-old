<?php

//namespace App\Http\Controllers;
namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class AdminRoleController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth.admin');
    }

    public function dashboard(){
        $data['pageTitle'] = 'Admin';
        $data['pageHeading'] = 'Admin';
        return view("admin.dashboard")->with($data);
    }//end function
}
