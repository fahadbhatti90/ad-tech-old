<?php

namespace App\Http\Controllers\ClientAuth;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Artisan;
use App\Models\AccountModels\AccountModel;
use App\Models\ClientModels\AsinTagsModel;
use App\Models\ProductSegments\ProductSegments;
use App\Models\ClientModels\ProductTableTagsModel;
use App\Http\Controllers\ClientAuth\ClientController;
use App\models\ProductSegments\ProductSegmentGroupsModel;

class ProductTableTagsController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth.manager');
    } //end constructor
    
    /**
     * addTag
     *
     * @param Request $request
     * @return void
     */
    public function addTag(Request $request)
    {
        $tag = $request->tag;
        $tag = ProductTableTagsModel::firstOrCreate([
            "fkManagerId" => auth()->user()->id,
            "tag" => $tag,
        ]);
        return response([
            "status" => true,
        ]);

    } //end function
    /**
     * editTag
     *
     * @param Request $request
     * @return void
     */
    public function editTag(ProductTableTagsModel $tag, Request $request)
    {
        if (ProductTableTagsModel::isDuplicateTag($request->tagId, $request->tagName)) {
            return [
                "status" => false,
                "message" => "Tag Already Exists",
            ];
        }
        $tag->tag = $request->tagName;
        if(count($tag->products) > 0)
        $tag->products()->where("fkTagId",$request->tagId)->update([
            "tag"=> $request->tagName
        ]);  
        if($tag->save()) {
            $tags = $this->getAllTags()["data"];
            return [
                "status" => true,
                "message" => "Tag updated successfully",
                "tags"=>$tags,
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
        $tags = ProductTableTagsModel::withCount("products")->where("fkManagerId", auth()->user()->id)->get();
        return [
            "status" => true,
            "data" => $tags,
        ];
    } //end function
    /**
     * getAllTagsForFilter
     *
     * @return void
     */
    public function getAllTagsForFilter()
    {
        Artisan::call('cache:clear');
        $segmentsIndependent = ProductSegments::doesntHave("segment_group")->select("id","segmentName","fkGroupId")->get();
        $segmentsGroup = ProductSegmentGroupsModel::with("segments:id,segmentName,fkGroupId")->select("id","groupName")->get();
        $tags = ProductTableTagsModel::select("id","tag")->where("fkManagerId", auth()->user()->id)->get();
        return [
            "status" => true,
            "data" => [
                "tags"=>$tags,
                "segmentsI"=>$segmentsIndependent,
                "segmentsG"=>$segmentsGroup,
            ],
        ];
    } //end function
    
    /**
     * getAllTagsToDelete
     *
     * @return void
     */
    public function getAllTagsToDelete(Request $request)
    {
        Artisan::call('cache:clear');
        $asins = $request->asins;
        $asinArray = [];
        foreach ($asins as $key => $value) {
            $asinArray[] = $key;
        }
        $accounts = AccountModel::select("id")->get()->map(function($item,$value){
            return $item->id;
        });
        if(!AsinTagsModel::whereIn("asin", $asinArray)->whereIn("fkAccountId", $accounts)->exists()){

            return [
                "status" => true,
            ];
        }

        AsinTagsModel::whereIn("asin", $asinArray)->whereIn("fkAccountId", $accounts)->delete();
      
        return [
            "status" => true,
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
        // return $request->asins["B00006IBOU"]->ffm;
        $data = [];
        $asinToUpdate = [];
        foreach ($request->asins as $asin => $details) {
            foreach ($request->tagsObj as $tagId => $tagName) {
                array_push($data, [
                    "asin" => $asin,
                    'fkAccountId' => $details["accountId"],
                    'fkTagId' => $tagId,
                    'tag' => $tagName,
                    "fullFillmentChannel" => $details["ffm"],
                    "uniqueColumn" => $tagId . "|" . $asin . "|" . $details["accountId"],
                    "createdAt" => date('Y-m-d H:i:s'),
                    "updatedAt" => date('Y-m-d H:i:s'),
                ]);
                # code...
            }
            array_push($asinToUpdate, $asin);
        }
        AsinTagsModel::insertOrUpdate($data);
        
        return [
            "status" => true,
        ];
    } //end function
    /**
     * deleteTag
     *
     * @param ProductTableTagsModel $tag
     * @return void
     */
    public function deleteTag(ProductTableTagsModel $tag)
    {
        DB::beginTransaction();
        try {
            AsinTagsModel::where("fkTagId", $tag->id)->delete();
            $tag->delete();
            $tags = $this->getAllTags()["data"];
            DB::commit();
            return [
                "status" => true,
                "tags"=>$tags,
            ];
        } catch (\Exception $e) {
            DB::rollback();
            return [
                "status" => false,
            ];
        } //end catch
    } //end function


    public function unAssignSingleTag(Request $request){
        $asin = $request->asin;
        $accountId = $request->accountId;
        $tagId = $request->tagId;
        $status = AsinTagsModel::where("asin", $asin)
        ->where("fkAccountId", $accountId)
        ->where("fkTagId", $tagId)
        ->delete();
        return [
            "status" => $status,
        ];
    }//end function
} //end class
