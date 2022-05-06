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
use App\Models\Brands\brandAssociation;

class BrandsController extends Controller
{
    /**
     * $tempAccountName
     *
     * @var undefined
     */
    private $tempAccountName;

    /***********************************************************************************************************/
    /**                                         Brands MODULE                                              **/
    /*********************************************************************************************************/
    /**
     * This function is used to render Account Details
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function brandAssociate()
    {
        $data = [];
        //$data["clients"] = ClientModel::all();
        $data["clients"] = ClientModel::doesntHave('manager')->get();
        $data["managers"] = User::has("managers")->get();
        $data["agencies"] = AgencyModel::all();
        $data["mwsSeller"] = MWSModel::doesntHave('accounts')->get();
        $data["vcVendor"] = VCModel::doesntHave('accounts')->get();
        $data["accounts"] = ClientModel::with("manager")->has('manager')->get();
        $data['pageTitle'] = 'Associate Brands';
        $data['pageHeading'] = 'Associate Brands';
        return view("subpages.brnadAssociate.brandAssociate")
            ->with($data);
    }//end functon

    /**
     * manageAccount
     *
     * @param Request $request
     * @return void
     */
    public function manageBrandAssociation(Request $request)
    {
        // return $request;
        return $this->addBrandAssociation($request);
    }//end function

    /*************************************Private funcitons for Brands**************************************/
    /**
     * deleteAccount
     *
     * @param ClientModel $account
     * @return void
     */

    public function deleteBrandAssociation($client)
    {
        $deleteManagerAssociation = ClientModel::where('id', $client)->update(['fkManagerId' => NULL]);
        $deleteAccountAssociation = AccountModel::where('fkBrandId', $client)->update(['fkManagerId' => NULL]);
        return array(
            'status' => true,
            'message' => "Brand Association Deleted Successfully"
        );
    }//end function

    /**
     * addBrandAssociation
     *
     * @param mixed $request
     * @return void
     */
    private function addBrandAssociation($request)
    {
        $validator = Validator::make($request->all(), [
            'managerId' => 'required|integer|min:1'
        ]);//end validate

        if ($validator->fails()) {
            return $response = array(
                'status' => false,
                'title' => "Error Invalid Data",
                'message' => $validator->errors(),
            );
        }//end if
        $ids = array();
        $ids["brandId"] = explode(",", $request->brandId);

        $accountArrays = array();
        foreach ($ids as $Idkey => $Idvalue) {
            if (is_array($Idvalue)) {
                foreach ($Idvalue as $key => $value) {
                    if (!empty(trim($value))) {
                        $client = ClientModel::find($value);
                        $client->fkManagerId = $request->managerId;
                        $client->updated_at = date('Y-m-d H:i:s');
                        $client->save();
                        $updatedClientId = $client->id;
                        $accountUpdate = AccountModel::where('fkBrandId', $updatedClientId)
                            ->update(['fkManagerId' => $request->managerId]);
                    }
                    // }
                }//end foreach
            }//end if
        }//end foreach
        return $response = array(
            'status' => true,
            'title' => "Success",
            'message' => "Brand Association Completed Successfully",
        );
        return $response = array(
            'status' => false,
            'title' => "Error",
            'message' => ["Some Error Occure (No Result found against some ID's)" . json_encode($ids)],
        );

    }//end function

    /************************************Private funcitons for Accounts ENDS*************************************/


}
