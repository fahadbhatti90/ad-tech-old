<?php

namespace App\Http\Controllers;

use App\Mail\BuyBoxEmailAlertMarkdown;
use App\Models\AgencyModels\AgencyModel;
use App\Models\ClientModels\ClientModel;
use App\Models\UserRoles\UserRole;
use App\Models\UserRolesModels\Role;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class addAdminController extends Controller
{
    /**
     * @return view
     */
    public function viewAdmins()
    {
        $data = [];
        $data['agencies'] =  User::has("admins")->where('deleted_at', NULL)->get();
        return $data;
    }//end functon
    /**
     * manage Admins
     *
     * @param Request $request
     * @return void
     */
    public function adminOperations(Request $request)
    {
        switch ($request->opType) {
            case 2:
                return $this->adminUpdate($request);
                break;
            case 3:
                return $this->adminUpdatePassword($request);
                break;

            default:
                return $this->adminAdd($request);
                break;
        }//end switch
    }//end function
    /*************************************Private funcitons for Admins**************************************/
    /**
     * @param $request
     * @return array
     */
    private function adminAdd($request)
    {
        $validator = Validator::make($request->all(), [
            'clientName' => 'required|string|max:199',
            //'clientEmail' => 'required|string|email|unique:users,email',
            'clientEmail' => 'required|string|email',
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
        $userName = $request->clientName;
        $userEmail = $request->clientEmail;
        $userPassword = $request->password;
        $countUsers = User::where('email', $userEmail)->withTrashed()->count();

        if ($countUsers > 0) {
            $checkUserDeleted = User::onlyTrashed()
                ->where('email', $userEmail)
                ->get();
            if (count($checkUserDeleted) > 0) {
                $userRestored = User::withTrashed()->Where('email', '=', $userEmail)->restore();
                $userUpdate = User::where('email', $userEmail)
                    ->update(['email' => $userEmail, 'name' => $userName, 'password' => Hash::make($request->password)]);
                $GetserId = User::where('email', $userEmail)->first();
                $GetserId->roles()->detach();
                $userId = $GetserId->id;
                UserRole::create([
                    'roleId' => 2,
                    'userId' => $userId
                ]);//end create function
                UserRole::create([
                    'roleId' => 3,
                    'userId' => $userId
                ]);
                //echo ;
                $emailData = array(
                    'email' => $userEmail,
                    'name' => $userName,
                    'password' => $userPassword
                );
                $sendEmail = $this->_sendSuccessUserRegistrationEmailAlert($emailData);
                return $response = array(
                    'status' => true,
                    //'userId' =>  $user->id,
                    'title' => "Success",
                    'message' => "Agency Added Successfully",
                );

            } else {
                return $response = array(
                    'status' => false,
                    'title' => "Error",
                    'message' => ["This Email Has Already Been Taken."],
                );
            }
        } else {

            $user = User::create([
                'name' => ucfirst($request->clientName),
                //'fkAgencyId' => $request->agency,
                'email' => $request->clientEmail,
                'password' => Hash::make($request->password),
            ]);//end create function
            $userId = $user->id;
            UserRole::create([
                'roleId' => 2,
                'userId' => $userId
            ]);//end create function
            UserRole::create([
                'roleId' => 3,
                'userId' => $userId
            ]);
        }

        $emailData = array(
            'email' => $userEmail,
            'name' => $userName,
            'password' => $userPassword
        );
        $sendEmail = $this->_sendSuccessUserRegistrationEmailAlert($emailData);
        return $response = array(
            'status' => true,
            //'userId' =>  $user->id,
            'title' => "Success",
            'message' => "Agency Added Successfully",
        );
    }//end function

    /**
     * @param $request
     * @return array
     */
    private function adminUpdate($request)
    {
        $validator = Validator::make($request->all(), [
            'id' => 'required|integer|min:1',
            'clientName' => 'required|string|max:199',
            'clientEmail' => 'required|string|email',
            //'agency' => 'required|integer|min:1'
        ]);//end validate

        if ($validator->fails()) {
            return $response = array(
                'status' => false,
                'title' => "Error Invalid Data",
                'message' => $validator->errors(),
            );
        }//end if

        $userEmail = $request->clientEmail;
        $userName = $request->clientName;
        if (!User::checkUserAvaiable($request->id)) {
            return $response = array(
                'status' => false,
                'title' => "Error",
                'message' => ["No Such User Found $request->id"],
            );
        }//end if

        $client = User::find($request->id);
        $client->name = ($request->clientName);
        //$client->fkAgencyId = $request->agency;
        $oldEmail = $client->email;
        if ($oldEmail != $request->clientEmail) {
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
        $emailData = array(
            'email' => $request->clientEmail,
            'name' => $userName,
            'customerEmail' => $request->clientEmail
        );

        $sendEmail = $this->_sendSuccessUserAccountUpdateEmailAlert($emailData);

        if ($oldEmail != $request->clientEmail) {
            $oldEmailData = array(
                'email' => $oldEmail,
                'name' => $userName,
                'customerEmail' => $request->clientEmail
            );
            $sendOldEmail = $this->_sendSuccessUserAccountUpdateEmailAlert($oldEmailData);
        }
        return $response = array(
            'status' => true,
            'title' => "Success",
            'message' => "Record Updated Successfully",
        );//end array
    }//end function

    /**
     * deleteAdmin
     *
     * @param User $client
     * @return void
     */
    public function deleteAdmin($client)
    {
        $GetserId = User::where('id', $client)->first();
        $GetserId->roles()->detach();

        $res = User::where('id', $client)->delete();
        //$client->delete();
        return array(
            'status' => true,
            'message' => "Agency Deleted Successfully",
            // 'message' => $th->getMessage(),
        );
    }//end function

    /**
     * @param $request
     * @return array
     */
    private function adminUpdatePassword($request)
    {
        $validator = Validator::make($request->all(), [
            'id' => 'required|integer|min:1',
            'password' => 'required|min:7|max:30'
        ]);//end validate

        if ($validator->fails()) {
            return $response = array(
                'status' => false,
                'title' => "Error Invalid Data",
                'message' => $validator->errors(),
            );
        }//end if
        $userPassword = $request->password;
        if (!User::checkUserAvaiable($request->id)) {
            return $response = array(
                'status' => false,
                'title' => "Error",
                'message' => ["No Such User Found $request->id"],
            );
        }//end if

        $client = User::find($request->id);
        $client->password = Hash::make($request->password);
        $client->save();
        $usrEmail = $client->email;
        $userName = $client->name;
        $emailData = array(
            'name' => $userName,
            'email' => $usrEmail,
            'password' => $userPassword
        );
        $sendEmail = $this->_sendSuccessUserPasswordUpdateEmailAlert($emailData);

        return $response = array(
            'status' => true,
            'title' => "Success",
            'message' => "User Password Updated Successfully",
        );
    }//end function
    /**
     * @param $emailData
     * Type Array
     * @return array
     */
    private function _sendSuccessUserRegistrationEmailAlert($emailData)
    {
        $userEmail = $emailData['email'];
        $messages = array();
        $messages[0] = "<p><b>Dear " . $emailData['name'] . ",</p>";
        $messages[1] = "<p>This email notification is to inform you that your account is created successfully.</p>";
        $messages[2] = "<p>Use following credentials to login.</p>";
        $messages[3] = "<p><b>Email :</b> " . $emailData['email'] . "</p>";
        $messages[4] = "<p><b>Password :</b> " . ($emailData['password']) . "</p>";
        $bodyHTML = ((new BuyBoxEmailAlertMarkdown('', $messages))->render());

        $data = [];
        $data["toEmails"] = array(
            $userEmail,
        );

        $data["subject"] = "Account Created Successfully.";
        $data["bodyHTML"] = $bodyHTML;

        return SendMailViaPhpMailerLib($data);

    }//end function
    /**
     * @param $emailData
     * Type Array
     * @return array
     */
    private function _sendSuccessUserAccountUpdateEmailAlert($emailData)
    {
        $userEmail = $emailData['email'];
        $messages = array();
        $messages[0] = "<p><b>Dear " . $emailData['name'] . ",</p>";
        $messages[1] = "<p>This email notification is to inform you that your account is updated successfully.</p>";
        $messages[2] = "<p>Use following credentials to login.</p>";
        $messages[3] = "<p><b>Name :</b> " . $emailData['name'] . "</p>";
        $messages[4] = "<p><b>Email :</b> " . $emailData['customerEmail'] . "</p>";
        $bodyHTML = ((new BuyBoxEmailAlertMarkdown('', $messages))->render());

        $data = [];
        $data["toEmails"] = array(
            $userEmail,
        );

        $data["subject"] = "Account Updated Successfully.";
        $data["bodyHTML"] = $bodyHTML;

        return SendMailViaPhpMailerLib($data);

    }//end function
    /**
     * @param $emailData
     * Type Array
     * @return array
     */
    private function _sendSuccessUserPasswordUpdateEmailAlert($emailData)
    {
        $userEmail = $emailData['email'];
        $messages = array();
        $messages[0] = "<p><b>Dear " . $emailData['name'] . ",</p>";
        $messages[1] = "<p>This email notification is to inform you that your account password is updated successfully.</p>";
        $messages[2] = "<p>Use following password to login.</p>";
        $messages[3] = "<p><b>Password :</b> " . $emailData['password'] . "</p>";
        $bodyHTML = ((new BuyBoxEmailAlertMarkdown('', $messages))->render());

        $data = [];
        $data["toEmails"] = array(
            $userEmail,
        );

        $data["subject"] = "Password Updated Successfully.";
        $data["bodyHTML"] = $bodyHTML;

        return SendMailViaPhpMailerLib($data);

    }//end

    /*************************************Private funcitons for Admins ENDS**************************************/
}

