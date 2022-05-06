<?php

namespace App\Http\Controllers;

use App\Models\AgencyModels\AgencyModel;
use App\Models\ClientModels\ClientModel;
use App\Models\UserRoles\UserRole;
use App\Models\UserRolesModels\Role;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;


class UserSettingsController extends Controller
{
     /**
     * manageClient
     *
     * @param Request $request
     * @return void
     */
    public function UserSettingsController (Request $request)
    {
        return $this->adminUpdate($request);
    }//end function

    /*************************************Private funcitons for clients**************************************/


    /**
     * @param $request
     * @return array
     */
    private function adminAdd($request)
    {
        $validator = Validator::make($request->all(), [
            'clientName' => 'required|string|max:199',
            'clientEmail' => 'required|string|email|unique:tbl_client,email',
            'password' => 'required|min:7|max:30',
            //'agency' => 'required|integer|min:1'
        ]);//end validate

        if ($validator->fails()) {
            return $response = array(
                'status' => false,
                'title' => "Error Invalid Data",
                'message' => $validator->errors(),
            );
        }//end if

         $userId=Auth::user()->id;
         $userEmail=Auth::user()->email;

        $client = User::find($userId);
        $client->name =  ($request->clientName);
        //$client->fkAgencyId = $request->agency;
        if ($client->email != $request->clientEmail) {
            if (!$client->isEmailUnique($request->clientEmail)) {
                return $response = array(
                    'status' => false,
                    'title' => "Error",
                    'message' => ["This Email Has Already Been Taken."],
                );
            }//end if
            $client->email = $request->clientEmail;
        }//end if
        $client->save();
        return $response = array(
            'status' => true,
            'title' => "Success",
            'message' => "Record Updated Successfully",
        );//end array

    }//end function

    

    /*************************************Private funcitons for Admins ENDS**************************************/
}
