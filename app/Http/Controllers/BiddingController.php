<?php

namespace App\Http\Controllers;

use App\Mail\BuyBoxEmailAlertMarkdown;
use App\Models\AccountModels\AccountModel;
use App\Models\DayPartingModels\PortfolioAllCampaignList;
use App\Models\DayPartingModels\Portfolios;
use App\Models\BiddingRule;
use App\Models\Vissuals\VissualsProfile;
use Artisan;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Models\AMSModel;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Rap2hpoutre\FastExcel\FastExcel;
use Rap2hpoutre\FastExcel\SheetCollection;

class BiddingController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth.manager');
    }

    /**
     * This function is used to render bidding rule form and data
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index(Request $request)
    {
        $data["brands"] = AccountModel::with("ams")
            ->with("brand_alias")
            ->where('fkAccountType', 1)
            ->where('fkBrandId', getBrandId())
            ->get();
        $data["preset"] = BiddingRule::getBiddingRulesPresetList();
        $data['pageTitle'] = 'Bidding Rule';
        $data['pageHeading'] = 'Bidding Rule';
        $data['allData'] = BiddingRule::getBiddingRulesListSpecificUser();
        return view('subpages.ams.biddingRules.dashboard')->with($data);
    }

    /**
     * This function is used to get Profile List
     * @return array
     */
    public function getProfileList($id = null)
    {
        $profileIds = AccountModel::select("id", "fkId")
            ->where("fkBrandId", getBrandId())
            ->where("fkAccountType", 1)
            ->get()
            ->map(function ($item, $value) {
                return $item->fkId;
            });
        $data["profiles"] = VissualsProfile::with("accounts:id,fkId", "accounts.brand_alias:fkAccountId,overrideLabel")
            ->select("id", "profileId", "name")->whereIn("id", $profileIds)->get();
            $scheduleBidRule = isset($id) ? BiddingRule::getSpecificBiddingRule($id) : null;
            $pfCampaings = null;
            if($scheduleBidRule){
                $pfCampaings = $this->GetPfCampaignsData($scheduleBidRule->profileId, $scheduleBidRule->sponsoredType, $scheduleBidRule->type);
            }
        return [
            "profiles"=>$data["profiles"] , 
            "presetRules"=>$this->presetRuleList(), 
            "ActiveBarnd"=>getBrandId(),
            "SelectedBidRule"=> $scheduleBidRule,
            "pfCampaings"=> $pfCampaings,
        ];
    }
    private function GetPfCampaignsData($profileFkId, $sponsoredType, $portfolioCampaignType){
        $responseData = [];
        $profileIdArray = explode("|", $profileFkId);
        $profileFkId = $profileIdArray[0];
        switch ($portfolioCampaignType) {
            case "Campaign":
            {
                $fetchCampaignRecord = array(
                    'fkProfileId' => $profileFkId,
                    'sponsored_type' => $sponsoredType
                );
                $allCampaigns = PortfolioAllCampaignList::getCampaignListOfSpecificProfile($fetchCampaignRecord);
                $responseData = $allCampaigns->map(function($item,$value){
                    return [
                        "id"=>"$item->id",
                        "name"=>"$item->name",
                    ];
                });
                break;
            }
            case "Portfolio":
            {
                $fetchPortfolioRecord = array(
                    'fkProfileId' => $profileFkId,
                );
                $allPortfolios = Portfolios::getPortfoliosList($fetchPortfolioRecord);
                $responseData = $allPortfolios->map(function($item,$value){
                    return [
                        "id"=>"$item->id",
                        "name"=>"$item->name",
                    ];
                });
                break;
            }
        }
        return $responseData;
    }
    /**
     * This function is used to get all preset rule list
     * @return bool
     */
    public function presetRuleList()
    {
        return BiddingRule::getBiddingRulesPresetList();
    }

    /**
     * This function used to store rules in DB
     * Method Type POST
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function storeRules(Request $request)
    {
        $response = array(); // define response array
        if ($request->input('formType') == 'add') {
            $validator = Validator::make($request->all(), [
                'ruleName' => 'required|unique:tbl_ams_bidding_rules|max:255',
            ]); // end validation
            // if validation fails
            if ($validator->fails()) {
                $response = array(
                    'status' => false,
                    'title' => 'fail',
                    'message' => $validator->messages()->all()
                );
            } else {
                    $user_id = Auth::user()->id;
                    $profileFkId = $request->input('profileFkId');
                    $fKPreSetRule = $request->input('fKPreSetRule');
                    $rule_name = $request->input('ruleName');
                    $sponsored_type = $request->input('sponsoredType');
                    $type = $request->input('type');
                    $pfCampaigns = $request->input('pfCampaigns');
                    $lookBackPeriod = $request->input('lookBackPeriod');
                    $frequency = $request->input('frequency');
                    $metric = $request->input('metric');
                    $condition = $request->input('condition');
                    $integerValues = $request->input('integerValues');
                    $thenClause = $request->input('thenClause');
                    $bidBy = $request->input('bidBy');
                    $andOr = $request->input('andOr');
                    $ccEmails = $request->input('ccEmails');
                    // Structure Column
                    $lookBackPeriodValue = $this->ValueConversion('lookBackPeriod', $lookBackPeriod);
                    $integerValue = $this->ValueConversion('integerValues', $integerValues);
                    //$bidByValue = $this->ValueConversion('bidByValues', $bidBy);
                    $bidByValue = $bidBy;
                    $pfCampaignsArray = implode(',', $pfCampaigns);
                    // check pre-set rule and make data array structure data
                    if (isset($fKPreSetRule) && $fKPreSetRule != NULL) { // if select pre-set value
                        $dataArray = array(
                            'fkUserId' => $user_id,
                            'fkBrandId' => getBrandId(),
                            'fKPreSetRule' => (int)$fKPreSetRule,
                            'profileId' => $profileFkId,
                            'ruleName' => $rule_name,
                            'sponsoredType' => $sponsored_type,
                            'type' => $type, // portfolio OR campaign type
                            'lookBackPeriod' => $lookBackPeriod,
                            'lookBackPeriodDays' => $lookBackPeriodValue,
                            'pfCampaigns' => $pfCampaignsArray,
                            'frequency' => $frequency,
                            'metric' => implode(',', $metric),
                            'condition' => implode(',', $condition),
                            'integerValues' => implode(',', $integerValue),
                            'thenClause' => $thenClause,
                            'bidBy' => $bidByValue,
                            'andOr' => isset($andOr) ? $andOr : 'NA',
                            // 'ccEmails' => preg_replace('/["\[\]\']/', '', $ccEmails),
                            'ccEmails' =>implode(',', $ccEmails),
                            'createdAt' => date('Y-m-d H:i:s'),
                            'updatedAt' => date('Y-m-d H:i:s'),
                        );
                    } else {
                        $dataArray = array(
                            'fkUserId' => $user_id,
                            'fkBrandId' => getBrandId(),
                            'fKPreSetRule' => 0,
                            'ruleName' => $rule_name,
                            'sponsoredType' => $sponsored_type,
                            'type' => $type, // portfolio OR campaign type
                            'lookBackPeriod' => $lookBackPeriod,
                            'lookBackPeriodDays' => $lookBackPeriodValue,
                            'pfCampaigns' => $pfCampaignsArray,
                            'profileId' => $profileFkId,
                            'frequency' => $frequency,
                            'metric' => implode(',', $metric),
                            'condition' => implode(',', $condition),
                            'integerValues' => implode(',', $integerValue),
                            'thenClause' => $thenClause,
                            'bidBy' => $bidByValue,
                            'andOr' => isset($andOr) ?  $andOr : 'NA',
                            // 'ccEmails' => preg_replace('/["\[\]\']/', '', $ccEmails),
                            'ccEmails' =>implode(',', $ccEmails),
                            'createdAt' => date('Y-m-d H:i:s'),
                            'updatedAt' => date('Y-m-d H:i:s'),
                        ); // end else
                    }
                    $type = $request->input('formType');
                    $res = BiddingRule::storeBiddingRule($dataArray, $type);
                    if ($res == true) {
                        $response = array(
                            'status' => 'Success',
                            'title' => 'Success',
                            'message' => 'Bidding Rule Added Succussfully.',
                            "tableData" => $this->GetbiddingRuleListForResponse()
                        );
                    } else {
                        $response = array(
                            'status' => 'fail',
                            'title' => $res,
                            'message' => 'Bidding Rule is not insert into DB.'
                        );
                    }
                } // end else
            } elseif ($request->input('formType') == 'edit') {
            $formtype = $request->input('formType');
            $bidRuleId = $request->input('bidRuleId');
            $profileFkId = $request->input('profileFkId');
            $fKPreSetRuleValue = $request->input('edit_fKPreSetRule');
            if (isset($fKPreSetRuleValue) && $fKPreSetRuleValue != NULL) {
                $fKPreSetRule = $fKPreSetRuleValue;
            } else {
                $fKPreSetRule = 0;
            }
            $rule_name = $request->input('ruleName');
            $sponsored_type = $request->input('sponsoredType');
            $type = $request->input('type');
            $pfCampaigns = $request->input('pfCampaigns');
            $lookBackPeriod = $request->input('lookBackPeriod');
            $frequency = $request->input('frequency');
            $metric = $request->input('metric');
            $condition = $request->input('condition');
            $integerValues = $request->input('integerValues');
            $thenClause = $request->input('thenClause');
            $bidBy = $request->input('bidBy');
            $andOr = $request->input('andOr');
            $ccEmails = $request->input('ccEmails');
            // Structure Column
            $lookBackPeriodValue = $this->ValueConversion('lookBackPeriod', $lookBackPeriod);
            $integerValue = $this->ValueConversion('integerValues', $integerValues);
            //$bidByValue = $this->ValueConversion('bidByValues', $bidBy);
            $bidByValue = $bidBy;
            $pfCampaignsArray = implode(',', $pfCampaigns);
            $dataArray = array(
                'id' => $bidRuleId,
                'fKPreSetRule' => (int)$fKPreSetRule,
                'ruleName' => $rule_name,
                'sponsoredType' => $sponsored_type,
                'type' => $type, // portfolio OR campaign type
                'lookBackPeriod' => $lookBackPeriod,
                'lookBackPeriodDays' => $lookBackPeriodValue,
                'pfCampaigns' => $pfCampaignsArray,
                'profileId' => $profileFkId,
                'frequency' => $frequency,
                'metric' => implode(',', $metric),
                'condition' => implode(',', $condition),
                'integerValues' => implode(',', $integerValue),
                'thenClause' => $thenClause,
                'bidBy' => $bidByValue,
                'andOr' => isset($andOr) ?  $andOr : 'NA',
                // 'ccEmails' => preg_replace('/["\[\]\']/', '', $ccEmails),
                'ccEmails' =>implode(',', $ccEmails),
                'createdAt' => date('Y-m-d H:i:s'),
                'updatedAt' => date('Y-m-d H:i:s'),
            ); // end else
            $res = BiddingRule::storeBiddingRule($dataArray, $formtype);
            if ($res) {
                $response = array(
                    'status' => 'Success',
                    'title' => 'Success',
                    'message' => 'Bidding Rule Updated Succussfully.',
                    "tableData" => $this->GetbiddingRuleListForResponse()
                );
            } else {
                $response = array(
                    'status' => 'fail',
                    'title' => 'fail',
                    'message' => 'Bidding Rule is not Update into DB.'
                );
            }
        } elseif ($request->input('formType') == 'delete') {
            $id = $request->input('id');
            $type = $request->input('formType');
            $res = BiddingRule::storeBiddingRule($id, $type);
            if ($res) {
                $response = array(
                    'status' => 'Success',
                    'title' => 'Success',
                    'message' => 'Bidding Rule Deleted Succussfully.',
                    "tableData" => $this->GetbiddingRuleListForResponse()
                );
            } else {
                $response = array(
                    'status' => 'fail',
                    'title' => 'fail',
                    'message' => 'Bidding Rule is not deleted.'
                );
            }
        }
        return response()->json($response);
    }

    /**
     * This function is used to store brand into Session
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function changeBrand(Request $request)
    {
        session()->forget(['accountId', 'brandId', 'brandName']);
        $brandSwitcherValue = $request->input('brand_id');
        $separateBrandValue = explode('^', $brandSwitcherValue);
        session(['accountId' => $separateBrandValue[0]]);
        session(['brandId' => $separateBrandValue[1]]);
        session(['brandName' => $separateBrandValue[2]]);
        if (1) {
            $response = array(
                'status' => 'Success',
                'title' => 'Success',
                'message' => 'Successfully Brand Switched.'
            );
        } else {
            $response = array(
                'status' => 'fail',
                'title' => 'fail',
                'message' => 'Bidding Rule is not deleted.'
            );
        }
        return response()->json($response);
    }

    /**
     * This function is used to store Value as a Rule
     * @param Request $request
     * @return array
     */
    public function onlyStoreRules(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'ruleName' => 'required|unique:tbl_ams_bidding_rule_preset,presetName|max:255',
        ]);
        if ($validator->fails()) {
            return $response = array(
                'status' => false,
                'title' => 'Fail',
                'message' => $validator->messages()->all()
            );
        }
        $rule_name = $request->input('ruleName');
        $metric = $request->input('metric');
        $condition = $request->input('condition');
        $integerValues = $request->input('integerValues');
        $thenClause = $request->input('thenClause');
        $bidBy = $request->input('bidBy');
        $andOr = $request->input('andOr');
        $frequency = $request->input('frequency');
        $lookBackPeriod = $request->input('lookBackPeriod');
        $lookBackPeriodValue = $this->ValueConversion('lookBackPeriod', $lookBackPeriod);
        // make data array structure
        $dataArray = array(
            'presetName' => $rule_name,
            'metric' => implode(',', $metric),
            'condition' => implode(',', $condition),
            'integerValues' => implode(',', $integerValues),
            'thenClause' => $thenClause,
            'bidBy' => $bidBy,
            'andOr' => isset($andOr) ?  $andOr : 'NA',
            'lookBackPeriod' => $lookBackPeriod,
            'lookBackPeriodDays' => $lookBackPeriodValue,
            'frequency' => $frequency,
            'createdAt' => date('Y-m-d H:i:s'),
            'updatedAt' => date('Y-m-d H:i:s'),
        );
        $response = BiddingRule::storePresetRule($dataArray);
        if ($response) {
            return $response = array(
                'status' => TRUE,
                'title' => 'Success',
                'message' => 'Pre Set Rules Added Succussfully',
                "rules" =>$this->presetRuleList()
            );
        } else {
            return $response = array(
                'status' => FALSE,
                'title' => 'Fail',
                'message' => 'Bid is not added into DB.'
            );
        }
    }

    /**
     * This function is used to get campaign and portfolio list of select sponsored Type
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getCampaignPortfolioList(Request $request)
    {
        $requestArray = $request;
        $responseData = array();
        $responseData["data"] = [];
        if ($requestArray['portfolio_campaign_type'] && $requestArray['sponsored_type']) { // validate that these
            // parameter
            $profileIdArray = explode("|", $requestArray['profile_fk_id']);
            $profileFkId = $profileIdArray[0];
            $portfolioCampaignType = $requestArray['portfolio_campaign_type'];
            $sponsoredType = $requestArray['sponsored_type'];
            if (!empty($portfolioCampaignType)) {
                switch ($portfolioCampaignType) {
                    case "Campaign":
                    {
                        $fetchCampaignRecord = array(
                            'fkProfileId' => $profileFkId,
                            'sponsored_type' => $sponsoredType
                        );
                        $allCampaigns = PortfolioAllCampaignList::getCampaignListOfSpecificProfile($fetchCampaignRecord);
                        $responseData['data'] = $allCampaigns->map(function($item,$value){
                            return [
                                "id"=>"$item->id",
                                "name"=>"$item->name",
                            ];
                        });
                        break;
                    }
                    case "Portfolio":
                    {
                        $fetchPortfolioRecord = array(
                            'fkProfileId' => $profileFkId,
                        );
                        $allPortfolios = Portfolios::getPortfoliosList($fetchPortfolioRecord);
                        $responseData['data'] = $allPortfolios->map(function($item,$value){
                            return [
                                "id"=>"$item->id",
                                "name"=>"$item->name",
                            ];
                        });
                        break;
                    }
                }
            }
        };
        return response()->json($responseData);
    }

    public function presetRule(Request $request)
    {
        $responseData = array();
        if ($request->ajax()) { // Check its ajax call or Not
            if ($request->has('id')) { // validate that these
                // parameter
                $id = $request->input('id');
                if (!empty($id)) {
                    $presetData = BiddingRule::getPresetRule($id);
                    if ($presetData != false) {
                        $responseData = array('data' => $presetData, 'status' => true);
                    } else {
                        $responseData = array('status' => false);
                    }
                } else {
                    $responseData = array('status' => false);
                }
            }
            return response()->json($responseData);
        }
    }

    /**
     * This function is used to convert values in require output
     *
     * @param $type
     * @param $value
     * @return array|float|int|string
     */
    private function ValueConversion($type, $value)
    {
        if ($type == 'lookBackPeriod') {
            if ($value == '7d') {
                return '7';
            } else if ($value == '14d') {
                return '14';
            } else if ($value == '21d') {
                return '21';
            } else if ($value == '1m') {
                return '30';
            }
        } else if ($type == 'integerValues') {
            $returnArray = array();
            foreach ($value as $single) {
                $returnArray[] = $single;
            }
            return $returnArray;
        } elseif ($type == 'bidByValues') {
            return $value / 100;
        }
    }

    /**
     * @return \Illuminate\Http\JsonResponse|mixed
     * @throws \Exception
     */
    public function getbiddingRuleList()
    {
        $response = BiddingRule::getBiddingRulesListSpecificUser();

        if (!empty($response)) {
            foreach ($response as $rule) {
                // get Campaign/Portfolio List of Rules
                $responseType = BiddingRule::getBiddingRulesDatatableList($rule->type, $rule->id);
                $Names = array();
                foreach ($responseType as $single) {
                    $Names[] = $single->typeName;
                }
                //Frequency text
                if ($rule->frequency == 'every_day') {
                    $rule->frequency = 'Every Other Day';
                } else if ($rule->frequency == 'w') {
                    $rule->frequency = 'Once Per Week';
                } else if ($rule->frequency == 'm') {
                    $rule->frequency = 'Once Per Month';
                } else if ($rule->frequency == 'once_per_day') {
                    $rule->frequency = 'Once Per Day';
                }
                //Statement Section
                $rule->list = $Names; // assign list of (campaign,portfolio)
                $returnText = ''; // declare the varaible for statement text
                $metricList = explode(',', $rule->metric);
                $conditionList = explode(',', $rule->condition);
                $integerValuesList = explode(',', $rule->integerValues);
                for ($i = 0; $i < count($metricList); $i++) {
                    $metricValue = $metricList[$i];
                    if ($metricValue == 'cost') {
                        $metricValue = 'spend';
                    }
                    if ($metricValue == 'revenue') {
                        $metricValue = 'sales';
                    }
                    $and = '';
                    if ($rule->andOr != 'NA') {
                        if ($rule->andOr == 'and') {
                            $and = 'AND';
                        } else if ($rule->andOr == 'or') {
                            $and = 'OR';
                        }
                    }
                    $returnText .= ' if ' . $metricValue . ' ' . str_replace('lesser', 'less', $conditionList[$i]) . ' than ' . $integerValuesList[$i] . ' ' . (($i == 1) ? '' : $and);
                }
                $returnText .= ' then ' . $rule->thenClause . ' Bid By ' . $rule->bidBy . ' % ';
                $rule->statement = $returnText;
            }
            return response()->json($response,200);
        }
        return response()->json(false,200);
    }
    private function GetbiddingRuleListForResponse(){
        $response = BiddingRule::getBiddingRulesListSpecificUser();
        if (!empty($response)){
            foreach ($response as $rule) {
                // get Campaign/Portfolio List of Rules
                $responseType = BiddingRule::getBiddingRulesDatatableList($rule->type, $rule->id);
                $Names = array();
                foreach ($responseType as $single) {
                    $Names[] = $single->typeName;
                }
                //Frequency text
                if ($rule->frequency == 'every_day') {
                    $rule->frequency = 'Every Other Day';
                } else if ($rule->frequency == 'w') {
                    $rule->frequency = 'Once Per Week';
                } else if ($rule->frequency == 'm') {
                    $rule->frequency = 'Once Per Month';
                } else if ($rule->frequency == 'once_per_day') {
                    $rule->frequency = 'Once Per Day';
                }
                //Statement Section
                $rule->list = $Names; // assign list of (campaign,portfolio)
                $returnText = ''; // declare the varaible for statement text
                $metricList = explode(',', $rule->metric);
                $conditionList = explode(',', $rule->condition);
                $integerValuesList = explode(',', $rule->integerValues);
                for ($i = 0; $i < count($metricList); $i++) {
                    $metricValue = $metricList[$i];
                    if ($metricValue == 'cost') {
                        $metricValue = 'spend';
                    }
                    if ($metricValue == 'revenue') {
                        $metricValue = 'sales';
                    }
                    $and = '';
                    if ($rule->andOr != 'NA') {
                        if ($rule->andOr == 'and') {
                            $and = 'AND';
                        } else if ($rule->andOr == 'or') {
                            $and = 'OR';
                        }
                    }
                    $returnText .= ' if ' . $metricValue . ' ' . str_replace('lesser', 'less', $conditionList[$i]) . ' than ' . $integerValuesList[$i] . ' ' . (($i == 1) ? '' : $and);
                }
                $returnText .= ' then ' . $rule->thenClause . ' Bid By ' . $rule->bidBy . ' % ';
                $rule->statement = $returnText;
            }
        }
        return $response;
    }
}