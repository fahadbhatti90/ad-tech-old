<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\User;
use Illuminate\Http\Request;
use App\Models\ClientModels\ClientModel;
use App\Models\ams\scheduleEmail\sponsordTypes;
use App\Models\ams\scheduleEmail\sponsordReports;
use App\Models\ams\scheduleEmail\scheduleAdvertisingReports;
use App\Models\ams\scheduleEmail\amsReportsMetrics;
use App\Models\ams\scheduleEmail\scheduledEmailAdvertisingReportsMetrics;
use App\Models\AMSModel;
use App\Models\AccountModels\AccountModel;
use Auth;
use Illuminate\Database\Eloquent\softDeletes;
use App\Models\ams\scheduleEmail\amsScheduleSelectedEmailSponsordTypes;
use App\Models\ams\scheduleEmail\amsScheduleSelectedEmailReports;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Rap2hpoutre\FastExcel\FastExcel;
use Illuminate\Support\Facades\Artisan;

class advertisingReportsEmail extends Controller
{
    public function __construct()
    {
        $this->middleware('auth.manager');
    }//end constructor
    /*********************************************************************************************************/
    /**                            Advertising Schedule To Email MODULE                                     **/
    /*********************************************************************************************************/
    /**
     * @return view
     */
    public function view()
    {
        /*$test = AccountModel::with("brand_alias")->get();
        dd($test);*/
        $getGlobalBrandId = getBrandId();
        $data = [];
        $managerId = Auth::user()->id;
        $accounts = AccountModel::where("fkBrandId", getBrandId())
            ->select("id", "fkId")
            /*->where("fkBrandId",getBrandId())*/
            ->where("fkAccountType", 1)
            ->get()
            ->map(function ($item, $value) {
                return $item->fkId;
            });
        $data["scheduledEmails"] = scheduleAdvertisingReports::with('selectedSponsoredTypes')->with('selectedReportTypes')->with('selectedReportsMetrics')->whereIn("fkProfileId", $accounts)->orderBy('id', 'desc')->get();
        // $data["amsProfiles"] =  AccountModel::with("ams")->with("brand_alias")->where('fkAccountType', 1)->where('fkBrandId', $getGlobalBrandId)->get();
        // $data["sponsordTypes"] = sponsordTypes::all();
        // $data["sponsordReports"] = sponsordReports::where('isActive', 0)->get();
        // $data["amsCampaignReportsMetrics"] = amsReportsMetrics::where('fkParameterType', 1)->get();
        // $data["amsAdGroupReportsMetrics"] = amsReportsMetrics::where('fkParameterType', 2)->get();
        // $data["amsProductAdsReportsMetrics"] = amsReportsMetrics::where('fkParameterType', 3)->get();
        // $data["amsKeywordReportsMetrics"] = amsReportsMetrics::where('fkParameterType', 4)->get();
        // $data["amsAsinReportsMetrics"] = amsReportsMetrics::where('fkParameterType', 5)->get();
        return $data;
    }//end functon

    /**
     * @return view
     */
    public function getPopUpData()
    {
        $getGlobalBrandId = getBrandId();
        $data = [];
        $managerId = Auth::user()->id;
        $accounts = AccountModel::where("fkBrandId", getBrandId())
            ->select("id", "fkId")
            /*->where("fkBrandId",getBrandId())*/
            ->where("fkAccountType", 1)
            ->get()
            ->map(function ($item, $value) {
                return $item->fkId;
            });
        $data["amsProfiles"] = AccountModel::with("ams")->with("brand_alias")->where('fkAccountType', 1)->where('fkBrandId', $getGlobalBrandId)->get();
        $data["sponsordTypes"] = sponsordTypes::all();
        // $data["sponsordReports"] = sponsordReports::where('isActive', 0)->get();
        // $data["amsCampaignReportsMetrics"] = amsReportsMetrics::where('fkParameterType', 1)->get();
        // $data["amsAdGroupReportsMetrics"] = amsReportsMetrics::where('fkParameterType', 2)->get();
        // $data["amsProductAdsReportsMetrics"] = amsReportsMetrics::where('fkParameterType', 3)->get();
        // $data["amsKeywordReportsMetrics"] = amsReportsMetrics::where('fkParameterType', 4)->get();
        // $data["amsAsinReportsMetrics"] = amsReportsMetrics::where('fkParameterType', 5)->get();
        return $data;
    }//end functon

    /**
     * @param $request
     * @return function
     */
    public function manageEmailSchedule(Request $request)
    {
        switch ($request->opType) {
            case 2:
                return $this->emailScheduleUpdate($request);
                break;
            default:
                return $this->emailScheduleAdd($request);
                break;
        }//end switch
    }
    /********* Private funcitons to add schedule ********/
    /**
     * @param $request
     * @return array
     */
    private function emailScheduleAdd(Request $request)
    {
        $brand = $request->brand;
        $reportName = trim($request->reportName);
        $countReportName = scheduleAdvertisingReports::where('reportName', $reportName)->count();

        if ($countReportName > 0) {
            return $response = array(
                'status' => false,
                'title' => "Error",
                'message' => ["This Report Name Has Already Been Taken."],
            );
        }//end if
        $reportType = $request->reportType;
        $reportTypeArray = (explode(",", $reportType));
        /*********** Get distinct report type parameters *************/
        $reportsMetricsParameterTypes = sponsordReports::distinct()->whereIn('id', $reportTypeArray)->get(['fkParameterType']);
        $reportsMetricsParameterTypesArray = array();
        foreach ($reportsMetricsParameterTypes as $key => $value) {
            $reportsMetricsParameterTypesArray[] = $value->fkParameterType;
        } //end foreach
        /*********** Get  report  metrics type parameters *************/
        $selectedcampaignMetricsCheckBox = trim($request->selectedcampaignMetricsCheckBox);
        $selectedcampaignMetricsCheckBoxArray = empty($selectedcampaignMetricsCheckBox) ? array() : (explode(",", $selectedcampaignMetricsCheckBox));
        $selectedadGroupMetricsCheckBox = trim($request->selectedadGroupMetricsCheckBox);
        $selectedadGroupMetricsCheckBoxArray = empty($selectedadGroupMetricsCheckBox) ? array() : (explode(",", $selectedadGroupMetricsCheckBox));
        $selectedProductAdsMetricsCheckBox = trim($request->selectedProductAdsMetricsCheckBox);
        $selectedProductAdsMetricsCheckBoxArray = empty($selectedProductAdsMetricsCheckBox) ? array() : (explode(",", $selectedProductAdsMetricsCheckBox));
        $selectedkeywordMetricsCheckBox = trim($request->selectedkeywordMetricsCheckBox);
        $selectedkeywordMetricsCheckBoxArray = empty($selectedkeywordMetricsCheckBox) ? array() : (explode(",", $selectedkeywordMetricsCheckBox));
        $selectedAsinMetricsCheckBox = trim($request->selectedAsinMetricsCheckBox);
        $selectedAsinMetricsCheckBoxArray = empty($selectedAsinMetricsCheckBox) ? array() : (explode(",", $selectedAsinMetricsCheckBox));
        if (in_array(1, $reportsMetricsParameterTypesArray)) {
            if (empty($selectedcampaignMetricsCheckBoxArray)) {
                //echo 'no metircs selected against campaign';
                return $response = array(
                    'status' => false,
                    'title' => "Error",
                    'message' => ["Please select metrics agaisnt campaign report."],
                );
            }
        }//end if
        if (in_array(2, $reportsMetricsParameterTypesArray)) {
            if (empty($selectedadGroupMetricsCheckBoxArray)) {
                return $response = array(
                    'status' => false,
                    'title' => "Error",
                    'message' => ["Please select metrics agaisnt ad group report."],
                );
            }//end if
        }//end if
        if (in_array(3, $reportsMetricsParameterTypesArray)) {
            if (empty($selectedProductAdsMetricsCheckBoxArray)) {
                return $response = array(
                    'status' => false,
                    'title' => "Error",
                    'message' => ["Please select metrics agaisnt product ads report."],
                );
            }//end if
        }//end if
        if (in_array(4, $reportsMetricsParameterTypesArray)) {
            if (empty($selectedkeywordMetricsCheckBoxArray)) {
                return $response = array(
                    'status' => false,
                    'title' => "Error",
                    'message' => ["Please select metrics agaisnt keyword report."],
                );
            }//end if
        }//end if
        if (in_array(5, $reportsMetricsParameterTypesArray)) {
            if (empty($selectedAsinMetricsCheckBoxArray)) {
                return $response = array(
                    'status' => false,
                    'title' => "Error",
                    'message' => ["Please select metrics agaisnt aisns report."],
                );
            }//end if
        }//end if


        $monday = ($request->M) ? 1 : 0;
        $tuesday = ($request->T) ? 1 : 0;
        $wednesday = ($request->W) ? 1 : 0;
        $thursday = ($request->TH) ? 1 : 0;
        $firday = ($request->F) ? 1 : 0;
        $saturday = ($request->SA) ? 1 : 0;
        $sunday = ($request->SU) ? 1 : 0;
        $isAllMetrics = ($request->selectAllMetrics) ? 1 : 0;
        $sponsordType = $request->sponsordType;
        $sponsordTypeArray = [];
        if (!empty(trim($sponsordType))) {
            $sponsordTypeArray = (explode(",", $sponsordType));
        }
        $granularity = $request->granularity;
        //$ccEmails = $request->ccEmails;
        /*$time = $request->time;*/
        $time = date("H:i", strtotime($request->time));
        $timeFrame = $request->timeFrame;
        $getAmsProfiles = AMSModel::where('id', $brand)->first();
        $amsProfileId = $getAmsProfiles->profileId;
        $ccEmails = ($request->has('ccEmails')) && !is_null($request->input('ccEmails')) ? implode(",", $request->input('ccEmails')) : 'NA';
        /*add schedule columns starts*/
        $data = [
            'reportName' => $reportName,
            'amsProfileId' => $amsProfileId,
            'fkProfileId' => $brand,
            'granularity' => $granularity,
            'allMetricsCheck' => $isAllMetrics,
            'timeFrame' => $timeFrame,
            'time' => $time,
            'addCC' => $ccEmails,
            //'scheduleDate' => '2020-03-02',
            'mon' => $monday,
            'tue' => $tuesday,
            'wed' => $wednesday,
            'thu' => $thursday,
            'fri' => $firday,
            'sat' => $saturday,
            'sun' => $sunday,
            'createdBy' => auth()->user()->id
        ];

        $addSchedule = scheduleAdvertisingReports::create($data);

        //get last inserted schedule id
        $lastInsertedSchedule = $addSchedule->id;
        /*add schedule columns ends*/
        /*insert selected sponsord types starts*/
        if (!empty($sponsordTypeArray)) {
            foreach ($sponsordTypeArray as $sponsordTypeValue) {
                $addSponsordType = amsScheduleSelectedEmailSponsordTypes::create([
                    'fkReportScheduleId' => $lastInsertedSchedule,
                    'fkSponsordTypeId' => $sponsordTypeValue
                ]);
            }//end foreach
        }//end if
        /*insert selected sponsord types ends*/
        /*insert selected report types starts*/
        foreach ($reportTypeArray as $reportTypeValue) {
            $GetReportTypeData = sponsordReports::where('id', $reportTypeValue)->first();
            $fkSponsordTypeId = $GetReportTypeData->fkSponsordTypeId;
            $fkReportTypeId = $GetReportTypeData->id;
            //$fkReportId = $GetReportTypeData->fkReportId;
            $fkParameterType = $GetReportTypeData->fkParameterType;

            $addReportType = amsScheduleSelectedEmailReports::create([
                'fkReportScheduleId' => $lastInsertedSchedule,
                'fkSponsordTypeId' => $fkSponsordTypeId,
                'fkReportId' => $fkReportTypeId,
                'fkParameterType' => $fkParameterType
            ]);
        }      //end foreach
        /*insert selected report types ends*/
        /*insert selected metrics starts*/
        if (!empty($selectedcampaignMetricsCheckBoxArray)) {
            foreach ($selectedcampaignMetricsCheckBoxArray as $fkSelectedMetricsId) {

                $GetMetricData = amsReportsMetrics::where('id', $fkSelectedMetricsId)->first();
                $fkReportMetricId = $GetMetricData->id;
                $fkSponsordTypeId = $GetMetricData->fkSponsordTypeId;
                $fkMetricParameterType = $GetMetricData->fkParameterType;

                $addMetrics = scheduledEmailAdvertisingReportsMetrics::create([

                    'fkReportScheduleId' => $lastInsertedSchedule,
                    'fkReportMetricId' => $fkReportMetricId,
                    'fkParameterType' => $fkMetricParameterType
                ]);
            }//end foreach
        }//end if
        if (!empty($selectedadGroupMetricsCheckBoxArray)) {
            foreach ($selectedadGroupMetricsCheckBoxArray as $fkSelectedMetricsId) {

                $GetMetricData = amsReportsMetrics::where('id', $fkSelectedMetricsId)->first();
                $fkReportMetricId = $GetMetricData->id;
                $fkSponsordTypeId = $GetMetricData->fkSponsordTypeId;
                $fkMetricParameterType = $GetMetricData->fkParameterType;

                $addMetrics = scheduledEmailAdvertisingReportsMetrics::create([

                    'fkReportScheduleId' => $lastInsertedSchedule,
                    'fkReportMetricId' => $fkReportMetricId,
                    'fkParameterType' => $fkMetricParameterType
                ]);
            }//end foreach
        }//end if
        if (!empty($selectedProductAdsMetricsCheckBoxArray)) {
            foreach ($selectedProductAdsMetricsCheckBoxArray as $fkSelectedMetricsId) {

                $GetMetricData = amsReportsMetrics::where('id', $fkSelectedMetricsId)->first();
                $fkReportMetricId = $GetMetricData->id;
                $fkSponsordTypeId = $GetMetricData->fkSponsordTypeId;
                $fkMetricParameterType = $GetMetricData->fkParameterType;

                $addMetrics = scheduledEmailAdvertisingReportsMetrics::create([

                    'fkReportScheduleId' => $lastInsertedSchedule,
                    'fkReportMetricId' => $fkReportMetricId,
                    'fkParameterType' => $fkMetricParameterType
                ]);
            }//end foreach
        }//end if
        if (!empty($selectedkeywordMetricsCheckBoxArray)) {
            foreach ($selectedkeywordMetricsCheckBoxArray as $fkSelectedMetricsId) {

                $GetMetricData = amsReportsMetrics::where('id', $fkSelectedMetricsId)->first();
                $fkReportMetricId = $GetMetricData->id;
                $fkSponsordTypeId = $GetMetricData->fkSponsordTypeId;
                $fkMetricParameterType = $GetMetricData->fkParameterType;

                $addMetrics = scheduledEmailAdvertisingReportsMetrics::create([

                    'fkReportScheduleId' => $lastInsertedSchedule,
                    'fkReportMetricId' => $fkReportMetricId,
                    'fkParameterType' => $fkMetricParameterType
                ]);
            }//end foreach
        }//end if
        if (!empty($selectedAsinMetricsCheckBoxArray)) {
            foreach ($selectedAsinMetricsCheckBoxArray as $fkSelectedMetricsId) {
                $GetMetricData = amsReportsMetrics::where('id', $fkSelectedMetricsId)->first();
                $fkReportMetricId = $GetMetricData->id;
                $fkSponsordTypeId = $GetMetricData->fkSponsordTypeId;
                $fkMetricParameterType = $GetMetricData->fkParameterType;
                $addMetrics = scheduledEmailAdvertisingReportsMetrics::create([
                    'fkReportScheduleId' => $lastInsertedSchedule,
                    'fkReportMetricId' => $fkReportMetricId,
                    'fkParameterType' => $fkMetricParameterType
                ]);
            }//end foreach
        }//end if
        /*insert selected metrics ends*/
        return $response = array(
            'status' => true,
            'title' => "Success",
            'message' => "Email Schedule Added Successfully",
        );
    }//end function
    /********* Private funcitons to add schedule ********/
    /**
     * @param $request
     * @return array
     */
    private function emailScheduleUpdate(Request $request)
    {
        
        $id = $request->id;
        $brand = $request->brand;
        $reportName = trim($request->reportName);
        $countReportName = scheduleAdvertisingReports::where('reportName', $reportName)->where('id', '!=', $id)->count();

        if ($countReportName > 0) {
            return $response = array(
                'status' => false,
                'title' => "Error",
                'message' => ["This Report Name Has Already Been Taken."],
            );
        } else {
            $reportType = $request->reportType;
            $reportTypeArray = (explode(",", $reportType));
            /*********** Get distinct report type parameters *************/
            $reportsMetricsParameterTypes = sponsordReports::distinct()->whereIn('id', $reportTypeArray)->get(['fkParameterType']);

            $reportsMetricsParameterTypesArray = array();
            foreach ($reportsMetricsParameterTypes as $key => $value) {
                $reportsMetricsParameterTypesArray[] = $value->fkParameterType;
            } //end foreach
            /*********** Get  report  metrics type parameters *************/
            $selectedcampaignMetricsCheckBox = trim($request->selectedcampaignMetricsCheckBox);

            //$selectedcampaignMetricsCheckBoxArray = array();
            $selectedcampaignMetricsCheckBoxArray = empty($selectedcampaignMetricsCheckBox) ? array() : (explode(",", $selectedcampaignMetricsCheckBox));
            $selectedadGroupMetricsCheckBox = trim($request->selectedadGroupMetricsCheckBox);
            $selectedadGroupMetricsCheckBoxArray = empty($selectedadGroupMetricsCheckBox) ? array() : (explode(",", $selectedadGroupMetricsCheckBox));
            $selectedProductAdsMetricsCheckBox = trim($request->selectedProductAdsMetricsCheckBox);
            $selectedProductAdsMetricsCheckBoxArray = empty($selectedProductAdsMetricsCheckBox) ? array() : (explode(",", $selectedProductAdsMetricsCheckBox));
            $selectedkeywordMetricsCheckBox = trim($request->selectedkeywordMetricsCheckBox);
            $selectedkeywordMetricsCheckBoxArray = empty($selectedkeywordMetricsCheckBox) ? array() : (explode(",", $selectedkeywordMetricsCheckBox));
            $selectedAsinMetricsCheckBox = trim($request->selectedAsinMetricsCheckBox);
            $selectedAsinMetricsCheckBoxArray = empty($selectedAsinMetricsCheckBox) ? array() : (explode(",", $selectedAsinMetricsCheckBox));

            if (in_array(1, $reportsMetricsParameterTypesArray)) {
                if (empty($selectedcampaignMetricsCheckBoxArray)) {
                    //echo 'no metircs selected against campaign';
                    return $response = array(
                        'status' => false,
                        'title' => "Error",
                        'message' => ["Please select metrics agaisnt campaign report."],
                    );
                }
            }//end if
            if (in_array(2, $reportsMetricsParameterTypesArray)) {
                if (empty($selectedadGroupMetricsCheckBoxArray)) {
                    return $response = array(
                        'status' => false,
                        'title' => "Error",
                        'message' => ["Please select metrics agaisnt ad group report."],
                    );
                }//end if
            }//end if
            if (in_array(3, $reportsMetricsParameterTypesArray)) {
                if (empty($selectedProductAdsMetricsCheckBoxArray)) {
                    return $response = array(
                        'status' => false,
                        'title' => "Error",
                        'message' => ["Please select metrics agaisnt product ads report."],
                    );
                }//end if
            }//end if
            if (in_array(4, $reportsMetricsParameterTypesArray)) {
                if (empty($selectedkeywordMetricsCheckBoxArray)) {
                    return $response = array(
                        'status' => false,
                        'title' => "Error",
                        'message' => ["Please select metrics agaisnt keyword report."],
                    );
                }//end if
            }//end if
            if (in_array(5, $reportsMetricsParameterTypesArray)) {
                if (empty($selectedAsinMetricsCheckBoxArray)) {
                    return $response = array(
                        'status' => false,
                        'title' => "Error",
                        'message' => ["Please select metrics agaisnt aisns report."],
                    );
                }//end if
            }//end if
            $selectedDays = (explode(",", $request->selectedDays));

            $monday = ($request->M) ? 1 : 0;
            $tuesday = ($request->T) ? 1 : 0;
            $wednesday = ($request->W) ? 1 : 0;
            $thursday = ($request->TH) ? 1 : 0;
            $firday = ($request->F) ? 1 : 0;
            $saturday = ($request->SA) ? 1 : 0;
            $sunday = ($request->SU) ? 1 : 0;
            $sponsordType = $request->sponsordType;
            $sponsordTypeArray = [];
            if (!empty(trim($sponsordType))) {
                $sponsordTypeArray = (explode(",", $sponsordType));
            }
            $granularity = $request->granularity;
            //$ccEmails = $request->ccEmails;
            /*$time = $request->time;*/
            $time = date("H:i", strtotime($request->time));
            $timeFrame = $request->timeFrame;
            $getAmsProfiles = AMSModel::where('id', $brand)->first();
            $amsProfileId = $getAmsProfiles->profileId;
            $ccEmails = ($request->has('ccEmails')) && !is_null($request->input('ccEmails')) ? implode(",", $request->input('ccEmails')) : 'NA';
            $isAllMetrics = ($request->selectAllMetrics) ? 1 : 0;
            /*Delete old selected options before update starts*/
            $deleteSponsordTypes = amsScheduleSelectedEmailSponsordTypes::where('fkReportScheduleId', $id)->delete();
            $deleteSelectedEmailReports = amsScheduleSelectedEmailReports::where('fkReportScheduleId', $id)->delete();
            $deleteReportsMetrics = scheduledEmailAdvertisingReportsMetrics::where('fkReportScheduleId', $id)->delete();
            /*Delete old selected options before update ends*/
            /*add schedule columns starts*/

            $data = [
                'reportName' => $reportName,
                'amsProfileId' => $amsProfileId,
                'fkProfileId' => $brand,
                'granularity' => $granularity,
                'allMetricsCheck' => $isAllMetrics,
                'timeFrame' => $timeFrame,
                'time' => $time,
                'addCC' => $ccEmails,
                //'scheduleDate' => '2020-03-02',
                'mon' => $monday,
                'tue' => $tuesday,
                'wed' => $wednesday,
                'thu' => $thursday,
                'fri' => $firday,
                'sat' => $saturday,
                'sun' => $sunday,
                'createdBy' => auth()->user()->id
            ];



            $addSchedule = scheduleAdvertisingReports::where('id', $id)->update($data);

            //get last inserted schedule id
            $lastInsertedSchedule = $id;

            /*add schedule columns ends*/
            /*insert selected sponsord types starts*/
            if (!empty($sponsordTypeArray)) {
                foreach ($sponsordTypeArray as $sponsordTypeValue) {
                    $addSponsordType = amsScheduleSelectedEmailSponsordTypes::create([
                        'fkReportScheduleId' => $lastInsertedSchedule,
                        'fkSponsordTypeId' => $sponsordTypeValue
                    ]);
                }//end foreach
            }//end if
            /*insert selected sponsord types ends*/
            /*insert selected report types starts*/
            foreach ($reportTypeArray as $reportTypeValue) {
                $GetReportTypeData = sponsordReports::where('id', $reportTypeValue)->first();
                $fkSponsordTypeId = $GetReportTypeData->fkSponsordTypeId;
                $fkReportTypeId = $GetReportTypeData->id;
                //$fkReportId = $GetReportTypeData->fkReportId;
                $fkParameterType = $GetReportTypeData->fkParameterType;

                $addReportType = amsScheduleSelectedEmailReports::create([
                    'fkReportScheduleId' => $lastInsertedSchedule,
                    'fkSponsordTypeId' => $fkSponsordTypeId,
                    'fkReportId' => $fkReportTypeId,
                    'fkParameterType' => $fkParameterType
                ]);
            }      //end foreach
            /*insert selected report types ends*/
            /*insert selected metrics starts*/
            if (!empty($selectedcampaignMetricsCheckBoxArray)) {
                foreach ($selectedcampaignMetricsCheckBoxArray as $fkSelectedMetricsId) {

                    $GetMetricData = amsReportsMetrics::where('id', $fkSelectedMetricsId)->first();
                    $fkReportMetricId = $GetMetricData->id;
                    $fkSponsordTypeId = $GetMetricData->fkSponsordTypeId;
                    $fkMetricParameterType = $GetMetricData->fkParameterType;

                    $addMetrics = scheduledEmailAdvertisingReportsMetrics::create([

                        'fkReportScheduleId' => $lastInsertedSchedule,
                        'fkReportMetricId' => $fkReportMetricId,
                        'fkParameterType' => $fkMetricParameterType
                    ]);
                }//end foreach
            }//end if
            if (!empty($selectedadGroupMetricsCheckBoxArray)) {
                foreach ($selectedadGroupMetricsCheckBoxArray as $fkSelectedMetricsId) {

                    $GetMetricData = amsReportsMetrics::where('id', $fkSelectedMetricsId)->first();
                    $fkReportMetricId = $GetMetricData->id;
                    $fkSponsordTypeId = $GetMetricData->fkSponsordTypeId;
                    $fkMetricParameterType = $GetMetricData->fkParameterType;

                    $addMetrics = scheduledEmailAdvertisingReportsMetrics::create([

                        'fkReportScheduleId' => $lastInsertedSchedule,
                        'fkReportMetricId' => $fkReportMetricId,
                        'fkParameterType' => $fkMetricParameterType
                    ]);
                }//end foreach
            }//end if
            if (!empty($selectedProductAdsMetricsCheckBoxArray)) {
                foreach ($selectedProductAdsMetricsCheckBoxArray as $fkSelectedMetricsId) {

                    $GetMetricData = amsReportsMetrics::where('id', $fkSelectedMetricsId)->first();
                    $fkReportMetricId = $GetMetricData->id;
                    $fkSponsordTypeId = $GetMetricData->fkSponsordTypeId;
                    $fkMetricParameterType = $GetMetricData->fkParameterType;

                    $addMetrics = scheduledEmailAdvertisingReportsMetrics::create([

                        'fkReportScheduleId' => $lastInsertedSchedule,
                        'fkReportMetricId' => $fkReportMetricId,
                        'fkParameterType' => $fkMetricParameterType
                    ]);
                }//end foreach
            }//end if
            if (!empty($selectedkeywordMetricsCheckBoxArray)) {
                foreach ($selectedkeywordMetricsCheckBoxArray as $fkSelectedMetricsId) {

                    $GetMetricData = amsReportsMetrics::where('id', $fkSelectedMetricsId)->first();
                    $fkReportMetricId = $GetMetricData->id;
                    $fkSponsordTypeId = $GetMetricData->fkSponsordTypeId;
                    $fkMetricParameterType = $GetMetricData->fkParameterType;

                    $addMetrics = scheduledEmailAdvertisingReportsMetrics::create([

                        'fkReportScheduleId' => $lastInsertedSchedule,
                        'fkReportMetricId' => $fkReportMetricId,
                        'fkParameterType' => $fkMetricParameterType
                    ]);
                }//end foreach
            }//end if
            if (!empty($selectedAsinMetricsCheckBoxArray)) {
                foreach ($selectedAsinMetricsCheckBoxArray as $fkSelectedMetricsId) {
                    $GetMetricData = amsReportsMetrics::where('id', $fkSelectedMetricsId)->first();
                    $fkReportMetricId = $GetMetricData->id;
                    $fkSponsordTypeId = $GetMetricData->fkSponsordTypeId;
                    $fkMetricParameterType = $GetMetricData->fkParameterType;
                    $addMetrics = scheduledEmailAdvertisingReportsMetrics::create([
                        'fkReportScheduleId' => $lastInsertedSchedule,
                        'fkReportMetricId' => $fkReportMetricId,
                        'fkParameterType' => $fkMetricParameterType
                    ]);
                }//end foreach
            }//end if
            /*insert selected metrics ends*/
            return $response = array(
                'status' => true,
                'title' => "Success",
                'message' => "Email Schedule Updated Successfully",
            );
        }//end if
    }

    /**
     * deleteScheudle
     *
     * @param scheduleAdvertisingReports scheduleId
     * @return void
     */
    public function deleteSchedule($scheduleId)
    {
        $res = scheduleAdvertisingReports::where('id', $scheduleId)->delete();
        return array(
            'status' => true,
            'message' => "Record Deleted Successfully"
        );
    }//end function

    /**
     * get report types
     *
     * @param scheduleAdvertisingReports scheduleId
     * @return report type array
     */
    public function getReportTypes(Request $request)
    {
        $sponsordTypeValue = $request->sponsordTypeValue;

        $sponsordTypeValueArray = explode(",", $sponsordTypeValue);

        $reportsValues = sponsordReports::select("id", "reportName")->whereIn('fkSponsordTypeId', $sponsordTypeValueArray)->where('isActive', 0)->get();


        return $reportsValues;
        $reportsTypesArray = array();
        // foreach ($reportsValues as $key => $value) {
        //     $reportsTypesArray[$value->id] = $value->reportName;
        // }

        // return $reportsTypesArray;

        return $response = array(
            'reportsTypesArray' => $reportsTypesArray,
            'status' => false,
            'title' => "Error Invalid Data",
            'message' => ["The Given Dropdown dosen't found"],
        );

    }

    /**
     * get report types
     *
     * @param scheduleAdvertisingReports scheduleId
     * @return report metric and parameter type array
     */
    public function getReportMetrics(Request $request)
    {

        $reportTypeValue = $request->reportTypeValue;

        // if (empty(trim($reportTypeValue))) {
        //     $reportsMetricsValues = amsReportsMetrics::all();
        //     $reportsMetricsArray = array();
        //     $reportsParameterTypesArray = array();
        //     //$reportsParameterTypesArray = array("1","4");
        // }
        // else
        //     {
        $reportsMetricsValueArray = explode(",", $reportTypeValue);
        /*********** Get distinct report type parameters *************/
        $reportsParameterTypes = sponsordReports::distinct()->whereIn('id', $reportsMetricsValueArray)->get(['fkParameterType']);

        $reportsParameterTypesArray = array();
        foreach ($reportsParameterTypes as $key => $value) {
            $reportsParameterTypesArray[] = $value->fkParameterType;
        } //end foreach
        /*********** Get  report  metrics type parameters *************/
        $reportsMetricsValueArray = explode(",", $reportTypeValue);
        $reportsMetricsValues = amsReportsMetrics::whereIn('fkParameterType', $reportsParameterTypesArray)->get();
        //}//end if

        $reportsMetricsArray = array();
        foreach ($reportsMetricsValues as $key => $value) {
            $reportsMetricsArray[$value->id] = $value->metricName;
        }
        $reportsMetricsArray = array();
        foreach ($reportsMetricsValues as $key => $value) {
            $reportsMetricsArray[$value->id] = $value->metricName;
        }
        $data["reportsParameterTypes"] = $reportsParameterTypesArray;
        if (in_array(1, $reportsParameterTypesArray)) {
            $data["Campaign"] = amsReportsMetrics::where('fkParameterType', 1)->get();
        }
        if (in_array(2, $reportsParameterTypesArray)) {
            $data["Ad Group"] = amsReportsMetrics::where('fkParameterType', 2)->get();
        }
        if (in_array(3, $reportsParameterTypesArray)) {
            $data["Product Ads"] = amsReportsMetrics::where('fkParameterType', 3)->get();
        }
        if (in_array(4, $reportsParameterTypesArray)) {
            $data["Keyword"] = amsReportsMetrics::where('fkParameterType', 4)->get();
        }
        if (in_array(5, $reportsParameterTypesArray)) {
            $data["ASINS"] = amsReportsMetrics::where('fkParameterType', 5)->get();
        }
        return $data;
    }

    /**
     * @param $selectedReportsMetricsArray
     * @param $parameterType
     * Type Array
     * @return array
     */
    private function _getMetricsArray($selectedReportsMetricsArray, $parameterType)
    {
        $metrics = amsReportsMetrics::where('fkParameterType', $parameterType)->get();
        $metricsArray = [];
        foreach ($metrics as $values) {
            $id = $values->id;
            $metricName = $values->metricName;
            $isChecked = in_array($id, $selectedReportsMetricsArray) ? 'true' : 'false';
            $metricsArray[] = [
                'id' => $id,
                'metricName' => $metricName,
                'isChecked' => $isChecked,
            ];
        }

        return $data = $metricsArray;
    }

    /**
     * get report types
     * @param request
     * @return array
     */
    public function getEditFormData(Request $request)
    {

        $data = [];
        Artisan::call('cache:clear');
        $scheduleId = $request->scheduleId;
        $selectSchedule = scheduleAdvertisingReports::find($scheduleId);
        $sponsordTypeValueArray = [];
        $selectedSponsoredTypes = $selectSchedule->selectedSponsoredTypes;
        //dd($selectSchedule);
        foreach ($selectedSponsoredTypes as $selectedSponsoredType) {
            $sponsordTypeValueArray[] = $selectedSponsoredType->id;
        }
        if (empty($sponsordTypeValueArray)) {
            $reportsValues = sponsordReports::where('isActive', 0)->get();
        } else {
            $reportsValues = sponsordReports::whereIn('fkSponsordTypeId', $sponsordTypeValueArray)->where('isActive', 0)->get();
        }

        $reportsTypesArray = array();
        foreach ($reportsValues as $key => $value) {
            $reportsTypesArray[$value->id] = htmlspecialchars($value->reportName);
        }
        $selectedReportTypes = $selectSchedule->selectedReportTypes;
        $selectedReportTypesArray = array();
        foreach ($selectedReportTypes as $selectedReportType) {
            $selectedReportTypesArray[] = $selectedReportType->id;
        }


        $selectedReportsMetrics = $selectSchedule->selectedReportsMetrics;
        $selectedReportsMetricsArray = array();
        foreach ($selectedReportsMetrics as $selectedReportsMetric) {
            $selectedReportsMetricsArray[] = $selectedReportsMetric->id;
        }
        /**************get selected metrics ends***************/
        $data['reportName'] = $selectSchedule->reportName;
        $data['fkProfileId'] = $selectSchedule->fkProfileId;
        $data['granularity'] = $selectSchedule->granularity;
        $data['timeFrame'] = $selectSchedule->timeFrame;
        $data['allMetricsCheck'] = $selectSchedule->allMetricsCheck;
        $data['addCC'] = $selectSchedule->addCC;
        $data['mon'] = $selectSchedule->mon;
        $data['tue'] = $selectSchedule->tue;
        $data['wed'] = $selectSchedule->wed;
        $data['thu'] = $selectSchedule->thu;
        $data['fri'] = $selectSchedule->fri;
        $data['sat'] = $selectSchedule->sat;
        $data['sun'] = $selectSchedule->sun;
        /*$time = $selectSchedule->time;*/
        $data['time'] = date('h:i A', strtotime($selectSchedule->time));

        $data['selectedSponsordTypeValueArray'] = $sponsordTypeValueArray;
        $data['selectedReportTypesArray'] = $selectedReportTypesArray;
        /*get selected report metrics array started*/
        /*********** Get distinct report type parameters *************/
        $reportsParameterTypes = sponsordReports::distinct()->whereIn('id', $selectedReportTypesArray)->get(['fkParameterType']);

        $reportsParameterTypesArray = array();
        foreach ($reportsParameterTypes as $key => $value) {
            $reportsParameterTypesArray[] = $value->fkParameterType;
        } //end foreach
        /*********** Get  report  metrics type parameters *************/
        $reportsMetricsValues = amsReportsMetrics::whereIn('fkParameterType', $reportsParameterTypesArray)->get();
        //}//end if
        $reportsMetricsArray = array();
        foreach ($reportsMetricsValues as $key => $value) {
            $reportsMetricsArray[$value->id] = $value->metricName;
        }
        $reportsMetricsArray = array();
        foreach ($reportsMetricsValues as $key => $value) {
            $reportsMetricsArray[$value->id] = $value->metricName;
        }
        $data["reportsParameterTypes"] = $reportsParameterTypesArray;

        if (in_array(1, $reportsParameterTypesArray)) {
            $data["Campaign"] = $this->_getMetricsArray($selectedReportsMetricsArray, 1);
        }
        if (in_array(2, $reportsParameterTypesArray)) {
            $data["Ad Group"] = $this->_getMetricsArray($selectedReportsMetricsArray, 2);
        }
        if (in_array(3, $reportsParameterTypesArray)) {
            $data["Product Ads"] = $this->_getMetricsArray($selectedReportsMetricsArray, 3);
        }
        if (in_array(4, $reportsParameterTypesArray)) {
            $data["Keyword"] = $this->_getMetricsArray($selectedReportsMetricsArray, 4);
        }
        if (in_array(5, $reportsParameterTypesArray)) {
            $data["ASINS"] = $this->_getMetricsArray($selectedReportsMetricsArray, 5);
        }

//        if (in_array(1, $reportsParameterTypesArray)) {
//            $data["amsCampaignReportsMetrics"] = $this->_getMetricsArray($selectedReportsMetricsArray, 1);
//        }
//        if (in_array(2, $reportsParameterTypesArray)) {
//            $data["amsAdGroupReportsMetrics"] = $this->_getMetricsArray($selectedReportsMetricsArray, 2);
//        }
//        if (in_array(3, $reportsParameterTypesArray)) {
//            $data["amsProductAdsReportsMetrics"] = $this->_getMetricsArray($selectedReportsMetricsArray, 3);
//        }
//        if (in_array(4, $reportsParameterTypesArray)) {
//            $data["amsKeywordReportsMetrics"] = $this->_getMetricsArray($selectedReportsMetricsArray, 4);
//        }
//        if (in_array(5, $reportsParameterTypesArray)) {
//            $data["amsAsinReportsMetrics"] = $this->_getMetricsArray($selectedReportsMetricsArray, 5);
//        }

        $data["sponsordTypeValueArray"] = $this->_getSponsoredTypeArray($selectedSponsoredTypes);
        $data["reportsTypesArray"] =   $this->_getReportTypeArray($reportsValues);
        return $data;
        /*get selected report metrics array ends*/

    }

    /**
     * @param $selectedSponsoredTypes
     * Type Array
     * @return array
     */
    private function _getSponsoredTypeArray($selectedSponsoredTypes)
    {
        $sponsoredTypesArray = [];
        foreach ($selectedSponsoredTypes as $values) {
            $id = $values->id;
            $sponsoredTypesName = $values->sponsordTypenName;
            $sponsoredTypesArray[] = [
                'label' => $sponsoredTypesName,
                'value' => $id
            ];
        }
        return $data = $sponsoredTypesArray;
    }

    /**
     * @param $selectedSponsoredTypes
     * Type Array
     * @return array
     */
    private function _getReportTypeArray($reportsValues)
    {
        $reportTypesArray = [];
        foreach ($reportsValues as $values) {
            $id = $values->id;
            $reportTypesName = $values->reportName;
            $reportTypesArray[] = [
                'label' => $reportTypesName,
                'value' => $id
            ];
        }
        return $data = $reportTypesArray;
    }

    /**
     * get report types
     *
     * @param request
     * @return array
     */
    public function getMetricsPopupData(Request $request)
    {
        $scheduleId = $request->scheduleId;
        $getSelectedParameterTypes = amsScheduleSelectedEmailReports::distinct()->where('fkReportScheduleId', $scheduleId)->get(['fkParameterType']);
        $getSelectedParameterTypesArray = array();
        foreach ($getSelectedParameterTypes as $getSelectedParameterType) {
            $getSelectedParameterTypesArray[] = $getSelectedParameterType->fkParameterType;
        } //end foreach
        /*Get campaign metrics starts*/
        $selectCompaignReportsSelectedMetrics = scheduledEmailAdvertisingReportsMetrics::where("fkReportScheduleId", $scheduleId)->where("fkParameterType", 1)->get();
        $compaignReportsSelectedMetricsCount = $selectCompaignReportsSelectedMetrics->count();
        $campaingMetricsArray = array();
        $campaingMetricsString = '';
        if ($compaignReportsSelectedMetricsCount > 0) {
            foreach ($selectCompaignReportsSelectedMetrics as $selectCompaignReportsSelectedMetric) {

                $currentComapaignMetricId = $selectCompaignReportsSelectedMetric->fkReportMetricId;

                /*************** Get column name against metric id starts ***********/
                $GetCompaignReportMetricsNames = amsReportsMetrics::where('id', $currentComapaignMetricId)->first();
                $GetCompaignReportMetricsNamesCount = $GetCompaignReportMetricsNames->count();
                if ($GetCompaignReportMetricsNamesCount > 0) {
                    $tblCompaignColumnName = $GetCompaignReportMetricsNames->metricName;
                    $campaingMetricsArray[] = trim($tblCompaignColumnName);
                }//end if
            }//end foreach
            $campaingMetricsString = implode(' , ', $campaingMetricsArray);
        }
        /*Get campaign metrics ends*/
        /*Get adGroup metrics starts*/
        $selectAdGroupReportsSelectedMetrics = scheduledEmailAdvertisingReportsMetrics::where("fkReportScheduleId", $scheduleId)->where("fkParameterType", 2)->get();
        $adGroupReportsSelectedMetricsCount = $selectAdGroupReportsSelectedMetrics->count();
        $adGroupMetricsArray = array();
        $adGroupMetricsString = '';
        if ($adGroupReportsSelectedMetricsCount > 0) {
            foreach ($selectAdGroupReportsSelectedMetrics as $selectAdGroupReportsSelectedMetric) {
                $currentAdGroupMetricId = $selectAdGroupReportsSelectedMetric->fkReportMetricId;
                /*************** Get column name against metric id starts ***********/
                $GetAdGroupReportMetricsNames = amsReportsMetrics::where('id', $currentAdGroupMetricId)->first();
                $GetAdGroupReportMetricsNamesCount = $GetAdGroupReportMetricsNames->count();
                if ($GetAdGroupReportMetricsNamesCount > 0) {
                    $tblAdGroupColumnName = $GetAdGroupReportMetricsNames->metricName;
                    $adGroupMetricsArray[] = trim($tblAdGroupColumnName);
                }//end if
            }//end foreach
            $adGroupMetricsString = implode(' , ', $adGroupMetricsArray);
        }
        /*Get adGroup metrics ends*/
        /*Get productAds metrics starts*/
        $selectProductAdsReportsSelectedMetrics = scheduledEmailAdvertisingReportsMetrics::where("fkReportScheduleId", $scheduleId)->where("fkParameterType", 3)->get();
        $productAdsReportsSelectedMetricsCount = $selectProductAdsReportsSelectedMetrics->count();
        $productAdsMetricsArray = array();
        $productAdsMetricsString = '';
        if ($productAdsReportsSelectedMetricsCount > 0) {
            foreach ($selectProductAdsReportsSelectedMetrics as $selectProductAdsReportsSelectedMetric) {
                $currentProductAdsMetricId = $selectProductAdsReportsSelectedMetric->fkReportMetricId;
                /*************** Get column name against metric id starts ***********/
                $GetProductAdsReportMetricsNames = amsReportsMetrics::where('id', $currentProductAdsMetricId)->first();
                $GetProductAdsReportMetricsNamesCount = $GetProductAdsReportMetricsNames->count();
                if ($GetProductAdsReportMetricsNamesCount > 0) {
                    $tblProductAdsColumnName = $GetProductAdsReportMetricsNames->metricName;
                    $productAdsMetricsArray[] = trim($tblProductAdsColumnName);
                }//end if
            }//end foreach
            $productAdsMetricsString = implode(' , ', $productAdsMetricsArray);
        }
        /*Get productAds metrics ends*/
        /*Get keyword metrics starts*/
        $selectKeywordReportsSelectedMetrics = scheduledEmailAdvertisingReportsMetrics::where("fkReportScheduleId", $scheduleId)->where("fkParameterType", 4)->get();
        $keywordReportsSelectedMetricsCount = $selectKeywordReportsSelectedMetrics->count();
        $keywordMetricsArray = array();
        $keywordMetricsString = '';
        if ($keywordReportsSelectedMetricsCount > 0) {
            foreach ($selectKeywordReportsSelectedMetrics as $selectKeywordReportsSelectedMetric) {
                $currentKeywordMetricId = $selectKeywordReportsSelectedMetric->fkReportMetricId;
                /*************** Get column name against metric id starts ***********/
                $getKeywordReportMetricsNames = amsReportsMetrics::where('id', $currentKeywordMetricId)->first();
                $getKeyReportMetricsNamesCount = $getKeywordReportMetricsNames->count();
                if ($getKeyReportMetricsNamesCount > 0) {
                    $tblKeywordColumnName = $getKeywordReportMetricsNames->metricName;
                    $keywordMetricsArray[] = trim($tblKeywordColumnName);
                }//end if
            }//end foreach
            $keywordMetricsString = implode(' , ', $keywordMetricsArray);
        }
        /*Get keyword metrics ends*/
        /*Get Asins metrics starts*/
        $selectAsinsReportsSelectedMetrics = scheduledEmailAdvertisingReportsMetrics::where("fkReportScheduleId", $scheduleId)->where("fkParameterType", 5)->get();
        $selectAsinsReportsSelectedMetricsCount = $selectAsinsReportsSelectedMetrics->count();
        $asinsMetricsArray = array();
        $asinsMetricsString = '';
        if ($selectAsinsReportsSelectedMetricsCount > 0) {
            foreach ($selectAsinsReportsSelectedMetrics as $selectAsinsReportsSelectedMetric) {
                $currentAsinMetricId = $selectAsinsReportsSelectedMetric->fkReportMetricId;
                /*************** Get column name against metric id starts ***********/
                $getAsinsReportMetricsNames = amsReportsMetrics::where('id', $currentAsinMetricId)->first();
                $getAsinsReportMetricsNamesCount = $getAsinsReportMetricsNames->count();
                if ($getAsinsReportMetricsNamesCount > 0) {
                    $tblAsinsColumnName = $getAsinsReportMetricsNames->metricName;
                    $asinsMetricsArray[] = trim($tblAsinsColumnName);
                }//end if
            }//end foreach
            $asinsMetricsString = implode(' , ', $asinsMetricsArray);
        }
        /*Get Asins metrics ends*/
        return $response = array(
            'getSelectedParameterTypesArray' => $getSelectedParameterTypesArray,
            'campaingMetricsString' => $campaingMetricsString,
            'adGroupMetricsString' => $adGroupMetricsString,
            'productAdsMetricsString' => $productAdsMetricsString,
            'keywordMetricsString' => $keywordMetricsString,
            'asinsMetricsString' => $asinsMetricsString,
            'status' => true,
            //'title' => "Error Invalid Data",
            //'message' => ["The Given Dropdown dosen't found"],
        );
        /*if (in_array(1, $getSelectedSponsorTypesArray)) {

        }
        if (in_array(2, $getSelectedSponsorTypesArray)) {

        }
        if (in_array(3, $getSelectedSponsorTypesArray)) {

        }*/

    }
    /*Advertising schedule ends*/
}
