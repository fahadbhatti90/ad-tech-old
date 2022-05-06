<?php

namespace App\Http\Controllers;

use App\Models\UserRolesModels\Role;
use App\Models\VCModel;
use App\Models\AMSModel;
use App\Models\MWSModel;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Models\AgencyModels\AgencyModel;
use App\Models\ClientModels\ClientModel;
use Illuminate\Support\Facades\Validator;
use App\Models\AccountModels\AccountModel;
class SuperAdminController extends Controller
{
    /**
     * $tempAccountName
     *
     * @var undefined
     */
    private $tempAccountName;


    public function __construct()
    {
    }

    /**
     * dashboard
     *
     * @return void
     */
    public function dashboard()
    {
        $data['pageTitle'] = 'Dashboard';
        $data['pageHeading'] = 'Dashboard';
        return view('subpages.admin.dashboard')->with($data);
    }//end dashboard function
    
/***********************************************************************************************************/
/**                                         Brands MODULE                                               **/
/*********************************************************************************************************/

    /**
     * brands
     *
     * @return  $data array
     */
    public function brands()
    {
        $data = [];
        $data["brands"] = ClientModel::with("brandAssignedUsers")->orderBy('id', 'desc')->get();
        return $data;
    }//end functon
    /**
     * brandsAddPopupData
     *
     * @return $data array
     */
    public function brandsAddPopupData()
    {
        $data = [];
        $data["users"] = User::has("managers")->where('deleted_at', NULL)->get();
        return $data;
    }//end functon
    /**
     * brandsEditPopupData
     *
     * @return $data array
     */
    public function brandsEditPopupData(Request $request)
    {
        $brandId = $request->brandId;
        $data = [];
        $brandData = ClientModel::with("brandAssignedUsers")->where('id', $brandId)->first();
        $brandAssignedUsers = $brandData->brandAssignedUsers;
        $data["users"] = $this->_checksForAssignedUsers($brandAssignedUsers);
        return $data;
    }//end functon
    /**
     * getBrandAssignedUsers
     *
     * @return $data array
     */
    public function getBrandAssignedUsers(Request $request)
    {
        $brandId = $request->brandId;
        $data = [];
        $brandData = ClientModel::with("brandAssignedUsers")->where('id', $brandId)->first();
        $brandAssignedUsers = $brandData->brandAssignedUsers;
        $data["brandAssignedUsers"] = $brandAssignedUsers;
        return $data;
    }//end functon

    /**
     * manageClient
     *
     * @param Request $request
     * @return void
     */
    public function manageClient(Request $request)
    {
        switch ($request->opType) {
            case 2:
                return $this->clientUpdate($request);
                break;
            case 3:
                return $this->clientUpdatePassword($request);
                break;

            default:
                return $this->clientAdd($request);
                break;
        }//end switch
    }//end function

 
    /*************************************Private funcitons for clients**************************************/


    /**
     * @param $request
     * @return array
     */
    private function clientAdd($request)
    {
        $usersString = $request->selectedUsers;
        if (empty(trim($usersString))){
            return $response = array(
                'status' => false,
                'title' => "Error",
                'message' => "Please Select Any User To Assign Brand",
            );
        }
        $validator = Validator::make($request->all(), [
            'clientName' => 'required|string|max:199',
            'clientEmail' => 'required|string|email',
            'selectedUsers' => 'required',
            //'password' => 'required|min:7|max:30',
            //'agency' => 'required|integer|min:1'
        ]);//end validate
         $userName=$request->clientName;
         $userEmail=$request->clientEmail;
         $selectedUsers = explode(',',trim($request->selectedUsers));

        if ($validator->fails()) {
            return $response = array(
                'status' => false,
                'title' => "Error Invalid Data",
                'message' => $validator->errors(),
            );
        }//end if
         $countUsers = ClientModel::where('email', $userEmail)->withTrashed()->count();
    if ( $countUsers > 0) {
         $checkUserDeleted = ClientModel::onlyTrashed()
                ->where('email', $userEmail)
                ->get();  
      if(count($checkUserDeleted) > 0){
        $userRestored=ClientModel::withTrashed()->Where('email', '=', $userEmail)->restore();
         $userUpdate=ClientModel::where('email', $userEmail)
          ->update(['email' => $userEmail, 'name' => $userName, 'password' => Hash::make($request->password)]);
          $GetserId = ClientModel::where('email', $userEmail)->first();
          $brandId = $GetserId->id;
          $clientModel = ClientModel::find($brandId);
          $clientModel->brandAssignedUsers()->detach();
          foreach ($selectedUsers as $selectedUsers){
              $clientModel->brandAssignedUsers()->attach($selectedUsers);
          }
          //dd($brandId);
            return $response = array(
            'status' => true,
            //'userId' =>  $user->id,
            'title' => "Success",
            'message' => "Brand Added Successfully",
        );

    }else{
        return $response = array(
                    'status' => false,
                    'title' => "Error",
                    'message' => "This Email Has Already Been Taken.",
                );
    }
       }else{
       $brand =  ClientModel::create([
            'name' => ucfirst ($request->clientName),
            'fkAgencyId' => 1,
            'email' => $request->clientEmail,
            'password' => Hash::make('123456'),
        ]);//end create function
       $brandId = $brand->id;
        $clientModel = ClientModel::find($brandId);
        $clientModel->brandAssignedUsers()->detach();
       foreach ($selectedUsers as $selectedUsers){
           $clientModel->brandAssignedUsers()->attach($selectedUsers);
       }
       //exit;
        //dd($brandId);
    }

        return $response = array(
            'status' => true,
            'title' => "Success",
            'message' => "Brand Added Successfully",
        );
        
    }//end function 

    /**
     * @param $request
     * @return array
     */
    private function clientUpdate($request)
    {
        $usersString = $request->selectedUsers;
        if (empty(trim($usersString))){
            return $response = array(
                'status' => false,
                'title' => "Error",
                'message' => "Please Select Any User To Assign Brand",
            );
        }
        $validator = Validator::make($request->all(), [
            'id' => 'required|integer|min:1',
            'clientName' => 'required|string|max:199',
            'clientEmail' => 'required|string|email',
            'selectedUsers' => 'required',
            //'agency' => 'required|integer|min:1'
        ]);//end validate

        if ($validator->fails()) {
            return $response = array(
                'status' => false,
                'title' => "Error Invalid Data",
                'message' => $validator->errors(),
            );
        }//end if   
        if (!ClientModel::checkClientAvaiable($request->id)) {
            return $response = array(
                'status' => false,
                'title' => "Error",
                'message' => "No Such Brand Found $request->id",
            );
        }//end if
        $selectedUsers = explode(',',trim($request->selectedUsers));
        $client = ClientModel::find($request->id);
        $client->name =  ($request->clientName);
        $client->fkAgencyId = 1;
        if ($client->email != $request->clientEmail) {
            if (!$client->isEmailUnique($request->clientEmail)) {
                return $response = array(
                    'status' => false,
                    'title' => "Error",
                    'message' => "The Brand Email Has Already Been Taken.",
                );
            }//end if
            $client->email = $request->clientEmail;
        }//end if
        $client->save();

        $client->brandAssignedUsers()->detach();
        foreach ($selectedUsers as $selectedUsers){
            $client->brandAssignedUsers()->attach($selectedUsers);
        }
        return $response = array(
            'status' => true,
            'title' => "Success",
            'message' => "Brand Updated Successfully",
        );//end array
    }//end function

    /**
     * deleteClient
     *
     * @param ClientModel $client
     * @return void
     */
    public function deleteClient($client){
        //$userUpdate=ClientModel::where('id',$client)->update(['fkManagerId' => NULL]);
        /*get admin id*/
        $getAdmin = User::has("admins")->where('deleted_at', NULL)->where('id','!=', 1)->first();
        if ($getAdmin) {
            $getAdminCount = $getAdmin->count();
            if ($getAdminCount > 0) {
                $adminId = $getAdmin->id;
                $getParentBrand = ClientModel::where('isParentBrand', 1)->withTrashed()->first();
                if ($getParentBrand) {
                    $parentBrandCount = $getParentBrand->count();
                    if ($parentBrandCount > 0) {
                        $parentBrandId = $getParentBrand->id;
                        /*update parent brand id in account table*/
                        $accountUpdate=AccountModel::where('fkBrandId',$client)
                            ->update(['fkBrandId' => $parentBrandId]);
                        /*delete associated brands starts*/
                        $clientModel = ClientModel::find($client);
                        $clientModel->brandAssignedUsers()->detach();
                        /*delete associated brands ends*/
                        /*now delete brand*/
                        $deleteBrand = ClientModel::where('id',$client)->delete();
                        return array(
                            'status' => true,
                            'message' => "Brand Deleted Successfully",
                            // 'message' => $th->getMessage(),
                        );
                        /*assign deleted brand to parent brand ends*/
                            } else {
                        return array(
                            'status' => false,
                            'message' => "Master Brand Not Found",
                        );
                            }
                        } else {
                        return array(
                            'status' => false,
                            'message' => "Master Brand Not Found",
                            // 'message' => $th->getMessage(),
                        );
                        }
                        }else{
                return array(
                    'status' => false,
                    'message' => "Please Add Admin Before You Delete This Brand",
                    // 'message' => $th->getMessage(),
                );
            }
                }else{
                return array(
                    'status' => false,
                    'message' => "Please Add Admin Before You Delete This Brand",
                    // 'message' => $th->getMessage(),
                );
                }
                        /*$accountUpdate=AccountModel::where('fkBrandId',$client)
                        ->update(['deleted_at' => date('Y-m-d H:i:s')]);*/
        /*delete associated brands*/
        /*$clientModel = ClientModel::find($client);
        $clientModel->brandAssignedUsers()->detach();
        $deleteBrand = ClientModel::where('id',$client)->delete();*/



        //$deleteAccount=AccountModel::where('id',$client)->destroy();
        return array(
            'status' => true,
            'message' => "Brand Deleted Successfully",
            // 'message' => $th->getMessage(),
        );
    }//end function 

    /**
     * @param $request
     * @return array
     */
    private function clientUpdatePassword($request){
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
        if(!ClientModel::checkClientAvaiable($request->id))
        {
            return $response = array(
                'status' => false,
                'title' => "Error",
                'message' => "No Such Client Found $request->id",
            );
        }//end if

        $client = ClientModel::find($request->id);
        $client->password = Hash::make($request->password);
        $client->save();
        return $response = array(
            'status' => true,
            'title' => "Success",
            'message' => "Client Password Updated Successfully",
        );
    }//end function

/*************************************Private funcitons for clients ENDS**************************************/


    
/***********************************************************************************************************/
/**                                         ACCOUNTS MODULE                                              **/
/*********************************************************************************************************/




    /**
     * This function is used to render Account Details
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function accounts()
    {
        $parentBrandId ='';
        $data = [];
        $getParentBrand = ClientModel::where('isParentBrand', 1)->withTrashed()->first();
        if ($getParentBrand) {
            $parentBrandCount = $getParentBrand->count();
            if ($parentBrandCount > 0) {
                $parentBrandId = $getParentBrand->id;
                $amsAccounts = AccountModel::where("fkBrandId",'!=',$parentBrandId)
                    ->select("id","fkId")
                    /*->where("fkBrandId",getBrandId())*/
                    ->where("fkAccountType",1)

                    ->get()
                    ->map(function($item,$value){
                        return $item->fkId;
                    });
                $scAccounts = AccountModel::where("fkBrandId",'!=',$parentBrandId)
                    ->select("id","fkId")
                    /*->where("fkBrandId",getBrandId())*/
                    ->where("fkAccountType",2)
                    ->get()
                    ->map(function($item,$value){
                        return $item->fkId;
                    });

                $vcAccounts = AccountModel::where("fkBrandId",'!=',$parentBrandId)
                    ->select("id","fkId")
                    /*->where("fkBrandId",getBrandId())*/
                    ->where("fkAccountType",3)
                    ->get()
                    ->map(function($item,$value){
                        return $item->fkId;
                    });
               // dd($vcAccounts);
                $data["amsProfile"] = AMSModel::whereNotIn('id', $amsAccounts)->get();
                $data["mwsSeller"] = MWSModel::whereNotIn('mws_config_id', $scAccounts)->get();
                $data["vcVendor"] = VCModel::whereNotIn('vendor_id', $vcAccounts)->get();
            }else{
                $data["amsProfile"] = AMSModel::doesntHave('accounts')->get();
                $data["mwsSeller"] = MWSModel::doesntHave('accounts')->get();
                $data["vcVendor"] = VCModel::doesntHave('accounts')->get();
            }
        }else{
            $data["amsProfile"] = AMSModel::doesntHave('accounts')->get();
            $data["mwsSeller"] = MWSModel::doesntHave('accounts')->get();
            $data["vcVendor"] = VCModel::doesntHave('accounts')->get();
        }

        $data["brands"] = ClientModel::with("agency")->get();
        $data["managers"] = User::has("managers")->get();
        $data["agencies"] = AgencyModel::all();
        $data["accounts"] = $this->GetAllAssociatedAccounts();
        return ($data);
    }//end functon
    private function GetAllAssociatedAccounts(){
        return AccountModel::with("accountType")->with(['client'])->orderBy('id', 'desc')->get()
        ->map(function($item,$index){
            return [
                "id" => $item->id,
                "sr" => $index+1,
                "accountType" => $item->accountType->name,
                "accountName" => $item->accountName,
                "fkBrandId" => $item->fkBrandId,
                "brandName" => $item->brand ? $item->brand->name : "No Brand Found",
                "created_at" => date('Y-m-d H:i', strtotime($item->created_at)),
            ];
        });
    }
    /** Master Brand
     * manageAccount
     *
     * @param Request $request
     * @return void
     */
    public function manageAccount(Request $request)
    {
        // return $request;
        switch ($request->opType) {
            case 2:
                return $this->accountUpdate($request);
                break;
        
            default:
                return $this->accountAdd($request);
                break;
        }//end switch
    }//end function

/*************************************Private funcitons for Accounts**************************************/
    /**
     * deleteAccount
     *
     * @param AccountModel $account
     * @return void
     */
    public function deleteAccount($account){


        $getAdmin = User::has("admins")->where('deleted_at', NULL)->where('id','!=', 1)->first();
        if ($getAdmin) {
            $getAdminCount = $getAdmin->count();
            if ($getAdminCount > 0) {
                $adminId = $getAdmin->id;
                $getParentBrand = ClientModel::where('isParentBrand', 1)->withTrashed()->first();
                if ($getParentBrand) {
                    $parentBrandCount = $getParentBrand->count();
                    if ($parentBrandCount > 0) {
                        $parentBrandId = $getParentBrand->id;
                        /*update parent brand id in account table*/
                        $accountUpdate=AccountModel::where('id',$account)
                            ->update(['fkBrandId' => $parentBrandId
                            ]);
                        return array(
                            'status' => true,
                            'message' => "Account Unassociated Successfully",
                            'tableData' => $this->accounts()
                            // 'message' => $th->getMessage(),
                        );
                        /*assign deleted brand to parent brand ends*/
                    } else {
                        return array(
                            'status' => false,
                            'message' => "Parent Brand Not Found",
                        );
                    }
                } else {
                    return array(
                        'status' => false,
                        'message' => "Parent Brand Not Found",
                        // 'message' => $th->getMessage(),
                    );
                }
            }else{
                return array(
                    'status' => false,
                    'message' => "Please Add Admin Before You Delete This Account",
                    // 'message' => $th->getMessage(),
                );
            }
        }else{
            return array(
                'status' => false,
                'message' => "Please Add Admin Before You Delete This Account",
                // 'message' => $th->getMessage(),
            );
        }
        /*$accountUpdate=AccountModel::where('id',$account)
        ->update(['fkBrandId' => NULL
            ,'fkManagerId' => NULL
                  ]);*/
        //$deleteAccount=AccountModel::where('id',$account)->delete();
        return array(
            'status' => true,
            'message' => "Account Unassociated Successfully.",
            // 'message' => $th->getMessage(),
        );
    }//end function 
    
    /**
     * getAccountName
     *
     * @param mixed $accountType
     * @param mixed $id
     * @return void
     */
    private function getAccountName($accountType, $id){
        switch ($accountType) {
            case 1:
                    // $this->tempAccountName = "AMS_".$id;
                    // return true;
                    $mod = AMSModel::where("id",$id);
                    if($mod->exists()){
                        $this->tempAccountName = $mod->first()->name;
                        return true;
                    }
                    return false;
                break;
            case 2:
                    // $this->tempAccountName = "MWS_".$id;
                    // return true;
                    $mod = MWSModel::where("mws_config_id",$id);
                    if($mod->exists()){
                        $this->tempAccountName = $mod->first()->merchant_name;
                        return true;
                    }
                    return false;
                break;
            default:
                    // $this->tempAccountName = "VC_".$id;
                    // return true;
                    $mod = VCModel::where("vendor_id",$id);
                    if($mod->exists()){
                        $this->tempAccountName = $mod->first()->vendor_name;
                        return true;
                    }
                    return false;
                break;
        }//switch ends
    }//function ends
    /**
     * accountAdd
     *
     * @param mixed $request
     * @return void
     */
    private function accountAdd($request)
    {
        $ids = array();
        $ids["amaIds"] = explode(",",$request->amsProfile);
        $ids["sellerId"] = explode(",",$request->sellerId);
        $ids["vendorId"] = explode(",",$request->vendorId);
        
        $accountArrays = array();
        foreach ($ids as $Idkey => $Idvalue) {
            $accountArray = array();
            switch ($Idkey) {
                case 'amaIds':
                    $accountType = 1;
                    break;
                case 'sellerId':
                    $accountType = 2;
                    break;
                default:
                    $accountType = 3;
                    break;
            }
            if(is_array($Idvalue))
            foreach ($Idvalue as $key => $value) {
                if($this->getAccountName($accountType,$value)){
                    
                    $getManagerId = ClientModel::where('id', $request->clientId)->first();
                    //$managerId = $getManagerId->fkManagerId;
                    $accountArray["fkId"] = $value;
                    $accountArray["accountName"] = $this->tempAccountName;
                    $accountArray["fkAccountType"] = $accountType;
                    $accountArray["fkBrandId"] = $request->clientId;
                    //$accountArray["fkManagerId"] = $managerId;
                    $accountArray["marketPlaceID"] = 0;
                    //$accountArray["creationDate"] = date('Y-m-d H:i:s');
                    $accountArray["created_at"] = date('Y-m-d H:i:s');
                    $accountArray["updated_at"] = date('Y-m-d H:i:s');
                    //$this->tempAccountName ="";
                    $countAccountExist = AccountModel::where('fkId', $value)->where('fkAccountType', $accountType)->withTrashed()->count();
                    if ( $countAccountExist > 0) {
                        $accountRestored=AccountModel::withTrashed()->where('fkId', $value)->where('fkAccountType', $accountType)->restore();       
                        $accountUpdate=AccountModel::where('fkId', $value)->where('fkAccountType', $accountType)
                        ->update($accountArray);
                    }else{
                       $accountInsert= AccountModel::insert($accountArray);
            
                    }
                   // array_push($accountArrays,$accountArray);
                }
            }//end foreach

        }//end foreach
        return $response = array(
                'status' => true,
                'title' => "Success",
                'message' => "Accounts Associated Successfully",
                'tableData' => $this->accounts()
                );
       
       
    }//end function

    /**
     * @param $request
     * @return array
     */
    private function accountUpdate($request)
    {
        
        if (!ClientModel::checkClientAvaiable($request->id)) {
            return $response = array(
                'status' => false,
                'title' => "Error",
                'message' => "No Such Client Found $request->id",
            );
        }//end if

        $client = ClientModel::find($request->id);
        $client->name = $request->clientName;
        $client->fkAgencyId = $request->agency;
        if ($client->email != $request->clientEmail) {
            if (!$client->isEmailUnique($request->clientEmail)) {
                return $response = array(
                    'status' => false,
                    'title' => "Error",
                    'message' => "Email is not Unique",
                );
            }//end if
            $client->email = $request->clientEmail;
        }
        $client->password = Hash::make($request->password);
        $client->save();
        return $response = array(
            'status' => true,
            'title' => "Success",
            'message' => "Client Updated Successfully",
        );
    }//end function

    /**
     * @param $brands
     * @param $userAssignedBrands
     * Type Array
     * @return array
     */
    private function _checksForAssignedUsers($brandAssignedUsers)
    {
        $assignedUsers = $brandAssignedUsers;
        $assignedUsersArray = [];
        if (!empty($assignedUsers)){
            foreach ($assignedUsers as $assignedUser){
                $assignedUsersArray[] = $assignedUser->id;
            }
        }
        $users = User::has("managers")->where('deleted_at', NULL)->get();
        $usersArray = [];
        foreach ($users as $values) {
            $id = $values->id;
            $userName = $values->name;
            $userEmail = $values->email;
            $isChecked = in_array($id, $assignedUsersArray) ? 'true' : 'false';
            $usersArray[] = [
                'id' => $id,
                'userName' => $userName,
                'userEmail' => $userEmail,
                'isChecked' => $isChecked
            ];
        }
        return $data = $usersArray;
    }

/************************************Private funcitons for Accounts ENDS*************************************/




}//end class
