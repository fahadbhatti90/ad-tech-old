<?php

namespace App\Http\Controllers\ClientAuth;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Artisan;
use App\Models\AccountModels\AccountModel;
use App\Models\ClientModels\CampaignsIdModel;
use App\Models\ClientModels\CampaignTagsModel;
use App\Models\Inventory\InventoryBrandsModel;
use App\Models\ClientModels\CampaignTagsAssignmentModel;

class CompaignTaggingController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth.manager');
    } //end constructor

    /**
     * campainStrategyType
     *
     * @return void
     */
    public function compaignStrategyType()
    {   
        return view("client.campaignTag")
            ->with("pageTitle", "Campaign Tagging");
    } //end function

    /**
     * compaignList
     *
     * @param Request $request
     * @return void
     */
    public function compaignList()
    {
        $CampaignsTN = CampaignsIdModel::getCompleteTableName();
        $InventoryBrandsTN = InventoryBrandsModel::getTableName();
        $accounts = AccountModel::where("fkBrandId",getBrandId())
        ->where("fkAccountType",1)
        ->select("fkId")
        ->get()
        ->map(function($item,$value){
            return $item->fkId;
        }); 
        if(count($accounts)<= 0){
            $accountsString = "";
        }
        else{
            $accountsString = implode(',',$accounts->toArray());
        }
        $campaignData =  CampaignsIdModel::with("tag:fkTagId,tag,campaignId,type,fkAccountId")
        ->with("accounts:id,accountName,fkId")
        ->with("accounts.brand_alias:fkAccountId,overrideLabel")
        ->select("$CampaignsTN.campaignId", "campaignName", DB::raw($CampaignsTN.".fkAccountId"))
        ->whereIn(DB::raw($CampaignsTN.".fkAccountId"),$accounts)
        ->groupBy(DB::raw("$CampaignsTN.campaignId"))->get()->map(function($item,$index){
            return [
                "Sr.#" => $index+1,
                "campaignId" => $item->campaignId,
                "campaignName" => $item->campaignName,
                "fkAccountId" => $item->fkAccountId,
                "accounts" => $item->accounts ? count($item->accounts->brand_alias) > 0 && $item->accounts->brand_alias[0]->overrideLabel != null ? $item->accounts->brand_alias[0]->overrideLabel : $item->accounts->accountName : "No Account Found",
                "tag" => $item->tag,
            ];
        });
        
        return $campaignData;
    } //end function            
    /*****************************************Tags*************************************************/
    /**
     * addTag
     *
     * @param Request $request
     * @return void
     */
    public function addTag(Request $request)
    {
        $tag = $request->tag;
        $tag = CampaignTagsModel::firstOrCreate([
            "fkManagerId" => auth()->user()->id,
            "tag" => $tag,
        ]);
        return [
            "status" => true
        ];

    } //end function
    /**
     * editTag
     *
     * @param ProductTableTagsModel $tag
     * @param Request $request
     * @return void
     */
    public function editTag(CampaignTagsModel $tag, Request $request)
    {
        if (CampaignTagsModel::isDuplicateTag($request->tagId, $request->tagName)) {
            return [
                "status" => false,
                "message" => "Tag Already Exists",
            ];
        }
        $tag->tag = $request->tagName;
        if(count($tag->compaigns) > 0)
        $tag->compaigns()->where("fkTagId",$request->tagId)->update([
            "tag"=> $request->tagName
        ]);   
        if($tag->save()) {
            return [
                "status" => true,
                "message" => "Tag updated successfully",
                "tags"=>$this->getAllTags()["data"]
            ];
        }
        return [
            "status" => false,
            "message" => "Fail To Edit Tag",
        ];
    } //end function+
    /**
     * getAllTags
     *
     * @return void
     */
    public function getAllTags()
    {
        Artisan::call('cache:clear');
        $tags = CampaignTagsModel::withCount("compaigns")->where("fkManagerId", auth()->user()->id)->get();
        return [
            "status" => true,
            "data" => $tags
        ];
        
    } //end function

    public function unAssignSingleTag(Request $request){
        $campaignId = $request->campaignId;
        $accountId = $request->accountId;
        $tagType = $request->tagType;
        $tagId = $request->tagId;
        $status = CampaignTagsAssignmentModel::where("campaignId", $campaignId)
        ->where("fkAccountId", $accountId)
        ->where("fkTagId", $tagId)
        ->where("type", $tagType)
        ->delete();
        return [
            "status" => $status
        ];
    }//end function


    /**
     * getAllTagsToDelete
     *
     * @return void
     */
    public function getAllTagsToDelete(Request $request)
    {
        $asins = $request->asins;
        
        $asinArray = [];
        foreach ($asins as $key => $value) {
            $asinArray[] = $key;
        }
        $accounts = AccountModel::select("id")->get()->map(function($item,$value){
            return $item->id;
        });
        if(!CampaignTagsAssignmentModel::whereIn("campaignId", $asinArray)->whereIn("fkAccountId", $accounts)->exists())
        return [
            "status" => true
        ];
        $status = CampaignTagsAssignmentModel::whereIn("campaignId", $asinArray)->whereIn("fkAccountId", $accounts)->delete();

        return [
            "status" => $status
        ];

    } //end function
    /**
     * asignTag
     *
     * @param Request $request
     * @return void
     */
    public function asignTag(Request $request)
    {
        $data = [];
        $asinToUpdate = [];
        foreach ($request->asins as $key => $campaingData) {
            foreach ($request->tagsObj as $tagId => $tagName) {
                array_push($data, [
                    "campaignId" => $key,
                    'fkAccountId' => $campaingData["accountId"],
                    'fkTagId' => $tagId,
                    'tag' => $tagName,
                    'type' => $request->type,
                    "uniqueColumn" => $tagId . "|" . $key. "|" . $campaingData["accountId"] ."|".$request->type,
                    "createdAt" => date('Y-m-d H:i:s'),
                    "updatedAt" => date('Y-m-d H:i:s'),
                ]);
            }
            array_push($asinToUpdate, $key);
        }
        CampaignTagsAssignmentModel::insertOrUpdate($data);
        return [
            "status" => true
        ];
    } //end function
    /**
     * deleteTag
     *
     * @param CampaignTagsModel $tag
     * @return void
     */
    public function deleteTag(CampaignTagsModel $tag)
    {
        DB::beginTransaction();
        try {
            CampaignTagsAssignmentModel::where("fkTagId", $tag->id)->delete();
            $tag->delete();
            DB::commit();
            return [
                "status" => true,
                "tags"=>$this->getAllTags()["data"]
            ];
        } catch (\Exception $e) {
            DB::rollback();
            return [
                "status" => false,
            ];
        } //end catch
    } //end function

    /*****************************************Tags*************************************************/

}
