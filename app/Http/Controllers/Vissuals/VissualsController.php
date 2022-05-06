<?php

namespace App\Http\Controllers\Vissuals;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Models\Vissuals\VissualsProfile;
use App\Models\AccountModels\AccountModel;
use App\Models\Vissuals\VissualsCampaigns;
use App\Models\ClientModels\CampaignTagsAssignmentModel;
use App\Models\Vissuals\VisualsCampaignAsinsModel;

class VissualsController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth.manager');
    }
    public function loadVissuals(){
        $profileIds = AccountModel::select("id","fkId")
        ->where("fkBrandId",getBrandId())
        ->where("fkAccountType",1)
        ->get()
        ->map(function($item,$value){
            return $item->fkId;
        });
        $data["profiles"]= VissualsProfile::with("accounts:id,fkId","accounts.brand_alias:fkAccountId,overrideLabel")
        ->select("id","profileId","name")->whereIn("id",$profileIds)->get();
        return [$data];
    }
    
    public function AsinPerformanceVisuals(){
        $profileIds = AccountModel::select("id","fkId")
        ->where("fkBrandId",getBrandId())
        ->where("fkAccountType",1)
        ->get()
        ->map(function($item,$value){
            return $item->fkId;
        });
        $data["profiles"]= VissualsProfile::with("accounts:id,fkId","accounts.brand_alias:fkAccountId,overrideLabel")
        ->select("id","profileId","name")->whereIn("id",$profileIds)->get();
        return [$data,getBrandId()];
    }

    public function getCampaigns(Request $request){
        $profileId = explode("|", $request->profileId);
        $profileId = $profileId[0];
        $campaigns = VissualsCampaigns::select("campaignId","campaignName")->where("fkProfileId",$profileId)->get();
        $campaignIds = $campaigns->map(function($item,$value){
            return $item->campaignId;
        });
        $filteredCampaigns = $campaigns->map(function($item,$value){
            return [
                "campaignId"=>"$item->campaignId",
                "name"=>"$item->campaignName",
            ];
        });    
        $productType = CampaignTagsAssignmentModel::select("fkTagId","tag","type")
        ->where("type",1)
        ->whereIn("campaignId",$campaignIds)
        ->groupBy("fkTagId")
        ->get();
       
        $stretagyType = CampaignTagsAssignmentModel::select("fkTagId","tag","type")
        ->where("type",2)
        ->whereIn("campaignId",$campaignIds)
        ->groupBy("fkTagId")
        ->get();
        
        return [
            "campaigns"=>($filteredCampaigns),
            "productType"=>($productType),
            "stretagyType"=>($stretagyType),
        ];
    }

    public function getTagCampaigns(Request $request){
        $profileId = $request->profileId;
        $fkTagIdP = $request->fkTagIdP;
        $fkTagIdS = $request->fkTagIdS;
        $productType = $request->productType;
        $strategyType = $request->strategyType;
        $accountIds = AccountModel::select("id","fkId")
        ->where("fkBrandId",getBrandId())
        ->where("fkAccountType",1)
        ->get()
        ->map(function($item,$value){
            return $item->id;
        });

        $campaigns = CampaignTagsAssignmentModel::select("campaignId")
        ->where("fkTagId",$fkTagIdP)
        ->orWhere("fkTagId",$fkTagIdS)
        ->where("type",$productType)
        ->orWhere("type",$strategyType)
        ->whereIn("fkAccountId",$accountIds)
        ->groupBy("campaignId")
        ->get();
        $campaignIds = $campaigns->map(function($item,$value){
            return $item->campaignId;
        });
       
        $campaigns = VissualsCampaigns::select("campaignId","campaignName")
        ->whereIn("campaignId",$campaignIds)
        ->where("fkProfileId",$profileId)
        ->groupBy("campaignId")
        ->get();
        
        return [
            "campaigns"=>$campaigns,
        ];
    }

    public function AsinLevelSpData(Request $request){
        $spNames = explode(",",$request->sp);
        $spData = [];
        foreach ($spNames as $key => $sp) {
            $spname = "$sp";
            $params = "(?,?,?,?,?)";
            $data = [];
            $profile = explode("|", $request->profileId)[1];
            $spData["$sp"] = DB::connection("mysqlDb2")->select("CALL $spname $params",["$request->startDate","$request->endDate",$profile,"$request->campaignId","$request->ASIN"]);
            
        }
        return $spData;
    }

    public function getAsinPerformanceVisualsCampaigns(Request $request){
        $profileId = explode("|", $request->profileId);
        $profileId = $profileId[0];
        $campaigns = VissualsCampaigns::select("campaignId","campaignName")
        ->where("fkProfileId",$profileId)
        ->where("campaign_type","SP")
        ->get();
        $campaignIds = $campaigns->map(function($item,$value){
            return $item->campaignId;
        });
        $filteredCampaigns = $campaigns->map(function($item,$value){
            return [
                "campaignId"=>"$item->campaignId",
                "name"=>"$item->campaignName",
            ];
        });    
        
        return [
            "campaigns"=>($filteredCampaigns),
        ];
    }
    
    public function getAsinPerformanceVisualsAsins(Request $request){
        $profileId = explode("|", $request->profileId);
        $profileId = $profileId[1];
        if($request->campaignId == "All"){
            $asins = VisualsCampaignAsinsModel::select("asin")
            ->where("fkProfileId",$profileId)
            ->distinct()
            ->get();    
            return [
                "asins"=>($asins),
            ];
        }
        $campaignId = explode(",", $request->campaignId);
        $asins = VisualsCampaignAsinsModel::select("asin")
        ->where("fkProfileId",$profileId)
        ->whereIn("campaignId",$campaignId)
        ->distinct()
        ->get();    
        
        return [
            "asins"=>($asins),
        ];
    }
    
    public function getSpsData(Request $request){
        $spCount = count($request->spDetails);
        $spDetails = $request->spDetails;
        $params = "";
        $data = [];
        foreach ($spDetails as $key => $value) {
            $params = "("; 
            $paramsArray = [];
            $paramsCount = count($value->params);
            for ($i=0; $i < $paramsCount; $i++) { 
                if($i+1 == $paramsCount)
                $params .= "?";
                else
                $params .= "?,";
            }
            $params .= ")";
          
            $data["$value->spName"] = DB::connection("mysqlDb2")->select("CALL $value->spName $params",$value->params);    
        }
        // $data["tableData"] = DB::connection("mysqlDb2")->select("CALL $spname $params",[$profile]);
        // $data["grandTotals"] = DB::connection("mysqlDb2")->select("CALL $spname2 $params",[$profile]);
        return $data;
    }

    public function spPopulateCampaignPerformance(Request $request){
        $spname = "spPopulateCampaignPerformance";
        $params = "(?,?,?,?)";
        $data = [];
        $profile = explode("|", $request->profileId)[1];
        $data = DB::connection("mysqlDb2")->select("CALL $spname $params",[$request->startDate,$request->endDate,$profile,$request->campaignId]);
        return $data;
    }
    public function spPopulateCampaignEfficiency(Request $request){
        $spname = "spPopulateCampaignEfficiency";
        $params = "(?,?,?,?)";
        $profile = explode("|", $request->profileId)[1];
        return DB::connection("mysqlDb2")->select("CALL $spname $params",[$request->startDate,$request->endDate,$profile,$request->campaignId]);
    }
    public function spPopulateCampaignAwareness(Request $request){
        $spname = "spPopulateCampaignAwareness";
        $params = "(?,?,?,?)";
        $profile = explode("|", $request->profileId)[1];
        return DB::connection("mysqlDb2")->select("CALL $spname $params",[$request->startDate,$request->endDate,$profile,$request->campaignId]);
    }
    public function spPopulateCampaignMTD(Request $request){
        $spname = "spPopulateCampaignMTD";
        $params = "(?,?)";
        $profile = explode("|", $request->profileId)[1];
        return DB::connection("mysqlDb2")->select("CALL $spname $params",[$profile,$request->campaignId]);
    }
    public function spPopulatePresentationWowTable(Request $request){
        $spname = "spPopulatePresentationWowTable";
        $params = "(?,?)";
        $profile = explode("|", $request->profileId)[1];
        return DB::connection("mysqlDb2")->select("CALL $spname $params",[$profile,$request->campaignId]);
    }
    public function spPopulatePresentationDODTable(Request $request){
        $spname = "spPopulatePresentationDODTable";
        $params = "(?,?)";
        $profile = explode("|", $request->profileId)[1];
        return DB::connection("mysqlDb2")->select("CALL $spname $params",[$profile,$request->campaignId]);
    }
    public function spPopulatePresentationAdType(Request $request){
        $spname = "spPopulatePresentationAdType";
        $spname2 = "spPopulatePresentationAdTypeGrandTotal";
        $params = "(?,?,?)";
        $profile = explode("|", $request->profileId)[1];
        $data = [];
        $data["tableData"] = DB::connection("mysqlDb2")->select("CALL $spname $params",[$request->startDate,$request->endDate,$profile]);
        $data["grandTotals"] = DB::connection("mysqlDb2")->select("CALL $spname2 $params",[$request->startDate,$request->endDate,$profile]);
        return $data;
    }
    public function spPerformancePre30Day(Request $request){
        $spname = "spPerformancePre30Day";
        $spname2 = "spPerformancePre30DayGrandTotal";
        $params = "(?)";
        $profile = explode("|", $request->profileId)[1];
        $data = [];
        $data["tableData"] = DB::connection("mysqlDb2")->select("CALL $spname $params",[$profile]);
        $data["grandTotals"] = DB::connection("mysqlDb2")->select("CALL $spname2 $params",[$profile]);
        return $data;
    }
    public function spPerformanceytd(Request $request){
        $spname = "spPerformanceytd";
        $spname2 = "spViewYTDGrandTotal";
        $params = "(?)";
        $profile = explode("|", $request->profileId)[1];
        $data = [];
        $data["tableData"] = DB::connection("mysqlDb2")->select("CALL $spname $params",[$profile]);
        $data["grandTotals"] = DB::connection("mysqlDb2")->select("CALL $spname2 $params",[$profile]);
        return $data;
    }
    public function spCalculateCustomCampTagingVisual(Request $request){
        $spname = "spCalculateCustomCampTagingVisual";
        $spname2 = "spCalculateCustomCampTagingVisualGrandTotal";
        $params = "(?,?,?)";
        $profile = explode("|", $request->profileId)[1];
        $data = [];
        $data["tableData"] = DB::connection("mysqlDb2")->select("CALL $spname $params",[$request->startDate,$request->endDate,$profile]);
        $data["grandTotals"] = DB::connection("mysqlDb2")->select("CALL $spname2 $params",[$request->startDate,$request->endDate,$profile]);
        return $data;
    }
    public function spCalculateStragTypeCampTagingVisual(Request $request){
        $spname = "spCalculateStragTypeCampTagingVisual";
        $spname2 = "spCalculateStragTypeCampTagingVisualGrandTotal";
        $params = "(?,?,?)";
        $profile = explode("|", $request->profileId)[1];
        $data = [];
        $data["tableData"] = DB::connection("mysqlDb2")->select("CALL $spname $params",[$request->startDate,$request->endDate,$profile]);
        $data["grandTotals"] = DB::connection("mysqlDb2")->select("CALL $spname2 $params",[$request->startDate,$request->endDate,$profile]);
        return $data;
    }
    public function spCalculateProdTypeCampTagingVisual(Request $request){
        $spname = "spCalculateProdTypeCampTagingVisual";
        $spname2 = "spCalculateProdTypeCampTagingVisualGrandTotal";
        $params = "(?,?,?)";
        $profile = explode("|", $request->profileId)[1];
        $data = [];
        $data["tableData"] = DB::connection("mysqlDb2")->select("CALL $spname $params",[$request->startDate,$request->endDate,$profile]);
        $data["grandTotals"] = DB::connection("mysqlDb2")->select("CALL $spname2 $params",[$request->startDate,$request->endDate,$profile]);
        return $data;
    }
    public function spPopulatePresentationTopCampiagnTable(Request $request){
        $spname = "spPopulatePresentationTopCampiagnTable";
        $params = "(?,?,?,?)";
        $profile = explode("|", $request->profileId)[1];    
        return DB::connection("mysqlDb2")->select("CALL $spname $params",[$request->startDate,$request->endDate,$profile,$request->TopXcampaign]);
    }
    public function spCalculatePreformancePrecentages(Request $request){
        $spname = "spCalculatePreformancePrecentages";
        $params = "(?,?,?,?)";
        $profile = explode("|", $request->profileId)[1];
        $data = [];
        $data[$spname] = DB::connection("mysqlDb2")->select("CALL $spname $params",[$request->startDate,$request->endDate,$profile,$request->campaignId]);
        $grandTotal = $this->spCalculateCampaignPerformanceGrandTotal($request);
        $data[$grandTotal[0]]=$grandTotal[1];
        return $data;
    }
    public function spCalculateCampaignPerformanceGrandTotal(Request $request){
        $spname = "spCalculateCampaignPerformanceGrandTotal";
        $params = "(?,?,?,?)";
        $profile = explode("|", $request->profileId)[1];
        $data = DB::connection("mysqlDb2")->select("CALL $spname $params",[$request->startDate,$request->endDate,$profile,$request->campaignId]);
        return [$spname,$data];
    }

    public function spCalculateEfficiencyPrecentages(Request $request){
        $spname = "spCalculateEfficiencyPrecentages";
        $params = "(?,?,?,?)";
        $profile = explode("|", $request->profileId)[1];
        $data = [];
        $data[$spname] = DB::connection("mysqlDb2")->select("CALL $spname $params",[$request->startDate,$request->endDate,$profile,$request->campaignId]);
        $grandTotal = $this->spCalculateCampaignEfficiencyGrandTotal($request);
        $data[$grandTotal[0]]=$grandTotal[1];
        return $data;
    }
    public function spCalculateCampaignEfficiencyGrandTotal(Request $request){
        $spname = "spCalculateCampaignEfficiencyGrandTotal";
        $params = "(?,?,?,?)";
        $profile = explode("|", $request->profileId)[1];
        $data = DB::connection("mysqlDb2")->select("CALL $spname $params",[$request->startDate,$request->endDate,$profile,$request->campaignId]);
        return [$spname,$data];
    }

    public function spCalculateAwarenessPrecentages(Request $request){
        $spname = "spCalculateAwarenessPrecentages";
        $params = "(?,?,?,?)";
        $profile = explode("|", $request->profileId)[1];
        $data = [];
        $data[$spname] = DB::connection("mysqlDb2")->select("CALL $spname $params",[$request->startDate,$request->endDate,$profile,$request->campaignId]);
        $grandTotal = $this->spCalculateCampaignAwarenessGrandTotal($request);
        $data[$grandTotal[0]]=$grandTotal[1];
        return $data;
    }
    public function spCalculateCampaignAwarenessGrandTotal(Request $request){
        $spname = "spCalculateCampaignAwarenessGrandTotal";
        $params = "(?,?,?,?)";
        $profile = explode("|", $request->profileId)[1];
        $data = DB::connection("mysqlDb2")->select("CALL $spname $params",[$request->startDate,$request->endDate,$profile,$request->campaignId]);
        return [$spname,$data];
    }
    public function spCalculateMTDPercentages(Request $request){
        $spname = "spCalculateMTDPercentages";
        $params = "(?,?)";
        $profile = explode("|", $request->profileId)[1];
        return DB::connection("mysqlDb2")->select("CALL $spname $params",[$profile,$request->campaignId]);
    }
    public function spCalculateWowPercentages(Request $request){
        $spname = "spCalculateWowPercentages";
        $params = "(?,?)";
        $profile = explode("|", $request->profileId)[1];
        return DB::connection("mysqlDb2")->select("CALL $spname $params",[$profile,$request->campaignId]);
    }
    public function spPopulatePresentationCpgYTDTable(Request $request){
        $spname = "spPopulatePresentationCpgYTDTable";
        $params = "(?,?)";
        $profile = explode("|", $request->profileId)[1];
        return DB::connection("mysqlDb2")->select("CALL $spname $params",[$profile,$request->campaignId]);
    }
    public function spPopulatePresentationWTDTable(Request $request){
        $spname = "spPopulatePresentationWTDTable";
        $params = "(?,?)";
        $profile = explode("|", $request->profileId)[1];
        return DB::connection("mysqlDb2")->select("CALL $spname $params",[$profile,$request->campaignId]);
    }
    public function spCalculateYTDPercentages(Request $request){
        $spname = "spCalculateYTDPercentages";
        $params = "(?,?)";
        $profile = explode("|", $request->profileId)[1];
        return DB::connection("mysqlDb2")->select("CALL $spname $params",[$profile,$request->campaignId]);
    }
    public function spCalculateWTDPercentages(Request $request){
        $spname = "spCalculateWTDPercentages";
        $params = "(?,?)";
        $profile = explode("|", $request->profileId)[1];
        return DB::connection("mysqlDb2")->select("CALL $spname $params",[$profile,$request->campaignId]);
    }
    public function spCalculateDODPrecentages(Request $request){
        $spname = "spCalculateDODPrecentages";
        $params = "(?,?)";
        $profile = explode("|", $request->profileId)[1];
        return DB::connection("mysqlDb2")->select("CALL $spname $params",[$profile,$request->campaignId]);
    }
    public function spCalculateAMSScoreCards(Request $request){
        $spname = "spCalculateAMSScoreCards";
        $params = "(?,?,?,?)";
        $profile = explode("|", $request->profileId)[1];
        return DB::connection("mysqlDb2")->select("CALL $spname $params",[$request->startDate,$request->endDate,$profile,$request->campaignId]);
    }
    
}
