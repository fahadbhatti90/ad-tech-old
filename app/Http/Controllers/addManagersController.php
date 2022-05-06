<?php

namespace App\Http\Controllers;

use App\Mail\BuyBoxEmailAlertMarkdown;
use App\Models\AgencyModels\AgencyModel;
use App\Models\ClientModels\ClientModel;
use App\Models\AccountModels\AccountModel;
use App\Models\UserRolesModels\Role;
use App\User;
use App\Models\UserRoles\UserRole;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Database\Eloquent\softDeletes;

class addManagersController extends Controller
{

    public function viewManagers()
    {
        $data = [];
        $data["clients"] = User::has("managers")
            ->with("userAssignedbrands")
            ->where('deleted_at', NULL)
            ->where('id', '!=', 2)
            ->orderBy('id', 'desc')
            ->get();
        $data["adminUsers"] = User::has("admins")
            ->where('deleted_at', NULL)
            ->where('id', '!=', 1)
            ->get();
        $data["brands"] = ClientModel::with("agency")
            ->with("brandAssignedUsers")
            ->orderBy('id', 'desc')
            ->get();
        return $data;
    }//end functon

    /**
     * manageClient
     *
     * @param Request $request
     * @return void
     */
    public function managerOperations(Request $request)
    {
        switch ($request->opType) {
            case 2:
                return $this->managerUpdate($request);
                break;
            case 3:
                return $this->managerUpdatePassword($request);
                break;

            default:
                return $this->managerAdd($request);
                break;
        }//end switch
    }//end function

    /**
     * manageClient
     *
     * @param Request $request
     * @return userbrands
     */
    public function checkUserBrands(Request $request)
    {
        $userId = $request->userId;
        $userAssignedBrands = User::with("userAssignedbrands")->find($userId);
        $assignedBrandsList = [];
        $brandIds = [];
        foreach ($userAssignedBrands->userAssignedbrands as $userAssignedBrand) {
            $assignedBrandsList[] = array(
                'brandId' => $userAssignedBrand->pivot->fkBrandId,
                'managerId' => $userAssignedBrand->pivot->fkManagerId,
                'name' => $userAssignedBrand->name,
            );
            $brandIds[] = $userAssignedBrand->pivot->fkBrandId;
        }
        if (isset($assignedBrandsList) && count($assignedBrandsList) > 0) {
            $notAssignedBrandsIds = [];
            $notAssignedBrandsNames = [];
            foreach ($assignedBrandsList as $assignedBrandsValues) {
                $checkBrandId = $assignedBrandsValues['brandId'];
                $checkManagerId = $assignedBrandsValues['managerId'];
                $checkBrandName = $assignedBrandsValues['name'];
                /*loost on users on all brands assigned to current user.
                  Match if these brands assigned to any other user or not.
                  If these are not assigned to any user,
                  then show them on popup and assing to other user,
                  before you delete it
                */
                $checkBrands = ClientModel::with("brandAssignedUsers")->find($checkBrandId);
                $matchBrandsArray = [];
                foreach ($checkBrands->brandAssignedUsers as $checkBrandsValues) {
                    //match if brand assigned to this user that is not assigned to other user
                    if (trim($userId) != trim($checkBrandsValues->pivot->fkManagerId)) {
                        $matchBrandsArray[] = $checkBrandsValues->pivot->fkBrandId;
                    }
                }
                if (isset($matchBrandsArray) && count($matchBrandsArray) == 0 && empty($matchBrandsArray)) {
                    $notAssignedBrandsIds[] = $checkBrandId;
                    $notAssignedBrandsNames[$checkBrandId] = $checkBrandName;
                }
            }
            if (isset($notAssignedBrandsNames) && count($notAssignedBrandsNames) == 0 && empty($notAssignedBrandsNames)) {
                $notAssignedBrandsIds = implode(',', $notAssignedBrandsIds);
                return $response = array(
                    'status' => false,
                    'title' => "Success",
                    'message' => ["Assign these brands to other users."],
                );
            } else {
                $notAssignedBrandsIds = implode(',', $notAssignedBrandsIds);
                return $response = array(
                    'status' => true,
                    'title' => "Error",
                    'notAssignedBrandsIds' => $notAssignedBrandsIds,
                    'notAssignedBrandsNames' => $notAssignedBrandsNames,
                    'message' => ["Assign these brands to other users"],
                );
            }
        } else {
            return $response = array(
                'status' => false,
                /*'assignedBrandsList' => $assignedBrandsList,*/
                'title' => "Success",
                'message' => ["Assign these brands to other users."],
            );
        }


    }

    public function addBrandManagers(Request $request)
    {
        $deleteUserId = trim($request->deleteUserId);
        if (!empty($deleteUserId)) {
            $selectedUsers = trim($request->selectedUsers);
            if (!empty($selectedUsers)) {
                $selectedUsersArray = explode(',', $selectedUsers);
            } else {
                $selectedUsersArray = [];
            }
            $selectedBrands = trim($request->assignedBrandToOtherIds);
            if (!empty($selectedBrands)) {
                $selectedBrandsArray = explode(',', $selectedBrands);
            } else {
                $selectedBrandsArray = [];
            }
            if (!empty($selectedUsersArray) && !empty($selectedBrandsArray)) {
                if (isset($selectedBrandsArray) && !empty($selectedBrandsArray)) {
                    foreach ($selectedBrandsArray as $selectedBrand) {
                        $clientModel = ClientModel::find($selectedBrand);
                        if (isset($selectedBrandsArray) && !empty($selectedBrandsArray)) {
                            $clientModel->brandAssignedUsers()->detach();
                            foreach ($selectedUsersArray as $selectedUserValue) {
                                $clientModel->brandAssignedUsers()->attach($selectedUserValue);
                            }
                        }
                    }
                }
                $userModel = User::find($deleteUserId);
                $userModel->userAssignedBrands()->detach();
                $res = User::where('id', $deleteUserId)->delete();
                return $response = array(
                    'status' => true,
                    'title' => "Success",
                    'message' => "User Deleted Successfully",
                );
            } else {
                return $response = array(
                    'status' => false,
                    'title' => "Error",
                    'message' => ["There is some error."],
                );
            }

        } else {
            return $response = array(
                'status' => false,
                'title' => "Error",
                'message' => ["There is some error."],
            );
        }


    }

    public function getUsersByType(Request $request)
    {
        $assignWithUserType = $request->assignWithUserType;
        if ($assignWithUserType == 'Admin') {
            $getUsers = User::has("admins")->where('deleted_at', NULL)->where('id', '!=', 1)->get();
        } else {
            $getUsers = User::has("managers")->where('deleted_at', NULL)->where('id', '!=', 2)->get();
        }

        $getUsersCount = $getUsers->count();
        $usersArray = array();
        $asinsMetricsString = '';
        $usersArray = array();
        if ($getUsersCount > 0) {
            foreach ($getUsers as $users) {
                $userId = $users->id;
                $userName = $users->name . '<' . $users->email . '>';
                $usersArray[$userId] = trim($userName);

            }//end foreach
        }
        if (empty($usersArray)) {
            return $response = array(
                'status' => false,
                'title' => "Error",
                'message' => ["No record found."],
            );
        } else {
            return $response = array(
                'usersArray' => $usersArray,
                'status' => true,
            );
        }

    }

    /**
     * edit form data
     *
     * @param $userId
     * @return $brands array
     */
    public function getEditManagerData(Request $request)
    {
        $userId = $request->userId;
        $data = [];
        $userData = User::with("userAssignedbrands")->where('id', $userId)->first();
        $userAssignedbrands = $userData->userAssignedbrands;
        $data["brands"] = $this->_checksForAssignedBrands($userAssignedbrands);
        return $data;
    }

    /*************************************Private funcitons for Managers**************************************/
    /**
     * @param $request
     * @return array
     */
    private function managerAdd($request)
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
        $brandsWithComma = trim($request->selectedBrands);
        if (!empty($brandsWithComma)) {
            $selectedBrands = explode(',', trim($request->selectedBrands));
        } else {
            $selectedBrands = [];
        }

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
                    'roleId' => 3,
                    'userId' => $userId
                ]);//end create function
                //echo ;
                $emailData = array(
                    'email' => $userEmail,
                    'name' => $userName,
                    'password' => $userPassword
                );

                $userModel = User::find($userId);
                if (isset($selectedBrands) && !empty($selectedBrands)) {
                    //$clientModel->userAssignedbrands()->detach();
                    foreach ($selectedBrands as $selectedBrands) {
                        $userModel->userAssignedbrands()->attach($selectedBrands);
                    }
                }
                $sendEmail = $this->_sendSuccessUserRegistrationEmailAlert($emailData);
                return $response = array(
                    'status' => true,
                    //'userId' =>  $user->id,
                    'title' => "Success",
                    'message' => "User Added Successfully",
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
                'roleId' => 3,
                'userId' => $userId
            ]);//end create function
            $userModel = User::find($userId);
            //$clientModel->userAssignedbrands()->detach();
            if (isset($selectedBrands) && !empty($selectedBrands)) {
                foreach ($selectedBrands as $selectedBrands) {
                    $userModel->userAssignedbrands()->attach($selectedBrands);
                }
            }
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
            'message' => "User Added Successfully",
        );
    }//end function

    /**
     * @param $request
     * @return array
     */
    private function managerUpdate($request)
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
        $brandsWithComma = $request->selectedBrands;
        if (!empty($brandsWithComma)) {
            $selectedBrands = explode(',', trim($request->selectedBrands));
        } else {
            $selectedBrands = [];
        }

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
        $userModel = User::find($request->id);
        //$clientModel->userAssignedbrands()->detach();
        if (isset($selectedBrands) && !empty($selectedBrands)) {
            foreach ($selectedBrands as $selectedBrands) {
                $userModel->userAssignedbrands()->attach($selectedBrands);
            }
        }
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
     * deleteClient
     *
     * @param ClientModel $client
     * @return void
     */
    public function deleteClient($client)
    {
        $userModel = User::find($client);
        $userModel->userAssignedBrands()->detach();
        $res = User::where('id', $client)->delete();/*        $userUpdate = ClientModel::where('fkManagerId', $client)->update(['fkManagerId' => NULL]);
        $accountUpdate = AccountModel::where('fkManagerId', $client)->update(['fkManagerId' => NULL])*/;
        // $client->delete();
        return array(
            'status' => true,
            'message' => "User Deleted Successfully"
        );
    }//end function

    /**
     * @param $request
     * @return array
     */
    private function managerUpdatePassword($request)
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
        $userEmail = $client->email;
        $userName = $client->name;
        $emailData = array(
            'name' => $userName,
            'email' => $userEmail,
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

    /**
     * @param $brands
     * @param $userAssignedBrands
     * Type Array
     * @return array
     */
    private function _checksForAssignedBrands($userAssignedbrands)
    {
        $assignedBrands = $userAssignedbrands;
        $assignedBrandsArray = [];
        if (!empty($assignedBrands)){
            foreach ($assignedBrands as $assignedBrand){
                $assignedBrandsArray[] = $assignedBrand->id;
            }
        }
        $brands = ClientModel::with("agency")
            ->orderBy('id', 'desc')
            ->get();
        $brandsArray = [];
        foreach ($brands as $values) {
            $id = $values->id;
            $brandName = $values->name;
            $isChecked = in_array($id, $assignedBrandsArray) ? 'true' : 'false';
            $brandsArray[] = [
                'id' => $id,
                'metricName' => $brandName,
                'isChecked' => $isChecked,
            ];
        }
        return $data = $brandsArray;
    }
    /*************************************Private funcitons for clients ENDS**************************************/
}

