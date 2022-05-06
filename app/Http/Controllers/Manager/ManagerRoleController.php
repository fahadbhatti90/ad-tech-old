<?php

namespace App\Http\Controllers\Manager;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ManagerRoleController extends Controller
{
    public function dashboard(){
        $data['pageTitle'] = 'Manager';
        $data['pageHeading'] = 'Manager';
        return view("manager.dashboard")->with($data);
    }//end function
}
