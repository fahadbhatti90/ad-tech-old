<?php

namespace App\Console\Commands\Ams\AmsAdvertisingEmailSchedule;


use App\Models\ClientModels\ClientModel;
use Illuminate\Console\Command;
use App\Models\ams\scheduleEmail\scheduleAdvertisingReports;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\File;
use Rap2hpoutre\FastExcel\FastExcel;
use App\Mail\BuyBoxEmailAlertMarkdown;
use Symfony\Component\Process\Process;
use App\Notifications\BuyBoxEmailAlert;
use App\Models\ams\scheduleEmail\amsScheduleSelectedEmailReports;
use App\Models\ams\scheduleEmail\scheduledEmailAdvertisingReportsMetrics;
use App\Models\ams\scheduleEmail\amsReportsMetrics;
use App\User;
use App\Models\AccountModels\AccountModel;
use App\Models\ams\scheduleEmail\amsAdvertisingScheduleFiles;

class runAmsAdvertisingEmailSchedule extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'runAmsAdvertisingEmailSchedule:cron';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }
    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
      $devServerLink = getHostForNoti().'/public/files/advertisingEmail/';
      $apiServerLink = asset('public/files/advertisingEmail/').'/';
        /*************** Get day,date and time start **************************/
        scSetMemoryLimitAndExeTime();
        Log::info("filePath:app\Console\Commands\Ams\AmsAdvertisingEmailSchedule, Module Name: Advertising Reports to e-mail. Start Cron.");
//Print out the day that our date fell on.
        $date=date('Y-m-d');
        $currentTime=date("H:i", time());
        //Get the day of the week using PHP's date function.
        $dayOfWeek = date("l", strtotime($date));
        echo  $date . ' fell on a ' . $dayOfWeek .' time :'.$currentTime;
        switch ($dayOfWeek) {

            case 'Monday':
                $dayColumnName='mon';
                break;
            case 'Tuesday':
                $dayColumnName='tue';
                break;
            case 'Wednesday':
                $dayColumnName='wed';
                break;
            case 'Thursday':
                $dayColumnName='thu';
                break;
            case 'Friday':
                $dayColumnName='fri';
                break;
            case 'Saturday':
                $dayColumnName='sat';
                break;
            case 'Sunday':
                $dayColumnName='sun';
                break;
        }
        /*************** Get day,date and time ends **************************/
        /*************** Get schedules to run start **************************/
        $scheduledReports = scheduleAdvertisingReports::where("status", 0)->where($dayColumnName, 1)->where('time', $currentTime)->get();
        //dd($scheduledReports);
        //$scheduledReports = scheduleAdvertisingReports::where('id', 13)->get();
        //$scheduledReports = scheduleAdvertisingReports::All();
        $scheduleCount = $scheduledReports->count();
        if ($scheduleCount > 0) {
            Log::info("filePath:app\Console\Commands\Ams\AmsAdvertisingEmailSchedule, Module Name: Advertising Reports to e-mail. Schedule Found.");
            foreach ($scheduledReports as $key => $value) {
                /*************** update status for running schedule ********/
                $scheduleId = $value->id;
                $reportName = $value->reportName;
                $amsProfileId = $value->amsProfileId;
                $fkProfileId = $value->fkProfileId;
                $granularity = $value->granularity;
                $addCC = [];
                $ccstring = trim($value->addCC);
                if (!empty($ccstring)) {
                    $addCC = explode(',', $value->addCC);
                }
                $createdBy = $value->createdBy;
                $GetManagerId = AccountModel::where('fkId', $fkProfileId)->where('fkAccountType', 1)->first();

                if (!empty($GetManagerId)) {
                    //$managerId = $GetManagerId->fkManagerId;
                    $brandId = $GetManagerId->fkBrandId;

                } else {
                    $brandId = '';
                }
                if (!empty($brandId)) {
                    $getBrandAssignedUsers = ClientModel::with("brandAssignedUsers")->find($brandId);
                    $managerEmailArray = [];
                    foreach ($getBrandAssignedUsers->brandAssignedUsers as $getBrandAssignedUser) {
                        $brandAssignedUserId = $getBrandAssignedUser->pivot->fkManagerId;
                        $GetManagerEmail = User::where('id', $brandAssignedUserId)->first();
                        $managerEmailArray[] = $GetManagerEmail->email;
                    }
                } else {
                    $brandId = '';
                    $managerEmailArray = [];
                }
                if (!empty($managerEmailArray)){
                $timeFrame = $value->timeFrame;
                $updateRunningScheduleStatus = scheduleAdvertisingReports::where('id', $scheduleId)->update(['status' => 1, 'cronLastRunDate' => $date, 'cronLastRunTime' => $currentTime]);
                /*** User Email Credentials Starts ***/
                $cronData = [];
                $cronData['email'] = $managerEmailArray;
                $cronData['addCC'] = $addCC;
                $cronData['reportName'] = $reportName;
                $cronData['granularity'] = $granularity;
                $fileNames = [];
                $finalDataArray = [];
                /*** User Email Credentials Ends ***/
                /*** Get selected metrics against campaign parameter type  starts ***/
                $selectCompaignReportsSelectedMetrics = scheduledEmailAdvertisingReportsMetrics::where("fkReportScheduleId", $scheduleId)->where("fkParameterType", 1)->get();
                $compaignReportsSelectedMetricsCount = $selectCompaignReportsSelectedMetrics->count();
                if ($compaignReportsSelectedMetricsCount > 0) {
                    /*** Get selected metrics against parameter type  starts ***/
                    $getSelectedSponsordTypes = amsScheduleSelectedEmailReports::distinct()->where('fkReportScheduleId', $scheduleId)->where('fkParameterType', 1)->get(['fkSponsordTypeId']);
                    $getSelectedSponsorTypesArray = array();
                    foreach ($getSelectedSponsordTypes as $getSelectedSponsordType) {
                        $getSelectedSponsorTypesArray[] = $getSelectedSponsordType->fkSponsordTypeId;
                    } //end foreach
                    $commaSeprateSponsordTypeArray = [];
                    $commaSeprateSponsordType = '';
                    if (in_array(1, $getSelectedSponsorTypesArray)) {
                        $commaSeprateSponsordTypeArray[] = 'SD';
                    }
                    if (in_array(2, $getSelectedSponsorTypesArray)) {
                        $commaSeprateSponsordTypeArray[] = 'SP';
                    }
                    if (in_array(3, $getSelectedSponsorTypesArray)) {
                        $commaSeprateSponsordTypeArray[] = 'SB';
                    }
                    if (empty($commaSeprateSponsordTypeArray)) {
                        $commaSeprateSponsordType = 'SP,SD,SB';
                    } else {
                        $commaSeprateSponsordTypeImplode = implode(",", $commaSeprateSponsordTypeArray);
                        //$commaSeprateSponsordType = "'$commaSeprateSponsordTypeImplode'";
                        $commaSeprateSponsordType = $commaSeprateSponsordTypeImplode;
                    }
                    /*** Get selected metrics against parameter type  ends ***/
                    //dd($selectCompaignReportsSelectedMetrics);
                    /******** Get column name against metric id starts ***********/
                    /******** Metrics that will send in all emails starts ***********/
                    $compaignMetricsArray = [];
                    switch ($granularity) {
                        case 'Daily':
                            $compaignMetricsArray = [
                                'brand_name',
                                'account_name',
                                'date_key',
                            ];
                            break;
                        default:
                            $compaignMetricsArray = [
                                'brand_name',
                                'account_name',
                                'year_',
                                'start_date',
                                'end_date'
                            ];
                            break;
                    }//end switch
                    /******** Metrics that will send in all emails ends ***********/
                    /******** Make refined metrics array starts ***********/
                    foreach ($selectCompaignReportsSelectedMetrics as $selectCompaignReportsSelectedMetric) {

                        $currentComapaignMetricId = $selectCompaignReportsSelectedMetric->fkReportMetricId;

                        /*************** Get column name against metric id starts ***********/
                        $GetCompaignReportMetricsNames = amsReportsMetrics::where('id', $currentComapaignMetricId)->first();
                        $GetCompaignReportMetricsNamesCount = $GetCompaignReportMetricsNames->count();
                        if ($GetCompaignReportMetricsNamesCount > 0) {
                            $tblCompaignColumnName = $GetCompaignReportMetricsNames->tblColumnName;
                            $compaignMetricsArray[] = trim($tblCompaignColumnName);
                        }//end if
                    }//end foreach
                    /******** Make refined metrics array ends ***********/
                    /******** Make arryas and  csv with granularity starts***********/
                    switch ($granularity) {
                        case 'Daily':
                            echo 'Daily';
                            $DB2 = 'mysqlDb2';
                            $parameters = array($amsProfileId, $commaSeprateSponsordType, $timeFrame);
                            $storedProceduresData = \DB::connection($DB2)->select('call spCalculateDailyCampaignSchedulingReport(?,?,?)', $parameters);
                            \DB::disconnect($DB2);
                            if (!empty($storedProceduresData)) {
                                Log::info("filePath:app\Console\Commands\Ams\AmsAdvertisingEmailSchedule, Module Name: Advertising Reports to e-mail. Message :Data found against this schedule.");
                                $storeNew = [];
                                $matchesColumns = [];
                                foreach ($storedProceduresData as $key => $value) {
                                    foreach ($compaignMetricsArray as $key1) {

                                        if (array_key_exists($key1, $value)) {
                                            //$this->info($key1);
                                            $matchesColumns[$key1] = $value->$key1;

                                        }
                                    }
                                    array_push($storeNew, $matchesColumns);
                                }
                                $campaignFinalMetrics = array();
                                $campaignFinalMetricsArray = array();
                                foreach ($storeNew as $campaignFinalMetricKey => $campaignFinalMetricValue) {
                                    //dd($campaignFinalMetricValue);
                                    if (isset($campaignFinalMetricValue['date_key'])) {
                                        $campaignFinalMetrics['Date'] = $campaignFinalMetricValue['date_key'];
                                    }
                                    //if(isset($campaignFinalMetricValue['brand_name'])){ $campaignFinalMetrics['Brand Name'] = $campaignFinalMetricValue['brand_name']; }
                                    //if(isset($campaignFinalMetricValue['account_name'])){ $campaignFinalMetrics['Account Name'] = $campaignFinalMetricValue['account_name']; }
                                    //if(isset($campaignFinalMetricValue['campaign_id'])){ $campaignFinalMetrics['Campaign Id'] = $campaignFinalMetricValue['campaign_id']; }
                                    if (isset($campaignFinalMetricValue['campaign_name'])) {
                                        $campaignFinalMetrics['Campaign Name'] = $campaignFinalMetricValue['campaign_name'];
                                    }
                                    if (isset($campaignFinalMetricValue['campaign_type'])) {
                                        $campaignFinalMetrics['Campaign Type'] = $campaignFinalMetricValue['campaign_type'];
                                    }
                                    if (isset($campaignFinalMetricValue['campaign_budget'])) {
                                        $campaignFinalMetrics['Campaign Budget'] = $this->roundValue($campaignFinalMetricValue['campaign_budget']);
                                    }
                                    if (isset($campaignFinalMetricValue['impressions'])) {
                                        $campaignFinalMetrics['Impressions'] = $campaignFinalMetricValue['impressions'];
                                    }
                                    if (isset($campaignFinalMetricValue['clicks'])) {
                                        $campaignFinalMetrics['Clicks'] = $campaignFinalMetricValue['clicks'];
                                    }
                                    if (isset($campaignFinalMetricValue['cost'])) {
                                        $campaignFinalMetrics['Cost'] = $this->roundValue($campaignFinalMetricValue['cost']);
                                    }
                                    if (isset($campaignFinalMetricValue['revenue'])) {
                                        $campaignFinalMetrics['Revenue'] = $this->roundValue($campaignFinalMetricValue['revenue']);
                                    }
                                    if (isset($campaignFinalMetricValue['order_conversion'])) {
                                        $campaignFinalMetrics['Order Conversion'] = $campaignFinalMetricValue['order_conversion'];
                                    }
                                    if (isset($campaignFinalMetricValue['ctr'])) {
                                        $campaignFinalMetrics['Click-Thru Rate (CTR)'] = $this->roundValue($campaignFinalMetricValue['ctr']);
                                    }
                                    if (isset($campaignFinalMetricValue['cpc'])) {
                                        $campaignFinalMetrics['Cost Per Click (CPC)'] = $this->roundValue($campaignFinalMetricValue['cpc']);
                                    }
                                    if (isset($campaignFinalMetricValue['acos'])) {
                                        $campaignFinalMetrics['Total Advertising Cost of Sales (ACoS)'] = $this->roundValue($campaignFinalMetricValue['acos']);
                                    }
                                    if (isset($campaignFinalMetricValue['roas'])) {
                                        $campaignFinalMetrics['Total Return on Advertising Spend (RoAS)'] = $this->roundValue($campaignFinalMetricValue['roas']);
                                    }
                                    if (isset($campaignFinalMetricValue['cpa'])) {
                                        $campaignFinalMetrics['CPA'] = $this->roundValue($campaignFinalMetricValue['cpa']);
                                    }
                                    array_push($campaignFinalMetricsArray, $campaignFinalMetrics);
                                    //isset()
                                    //dd($campaignFinalMetrics);
                                }
                                $fileName = 'Campaign-Daily-' . date('YmdHis') . '-' . $scheduleId . '.xlsx';
                                $fileTempPath = public_path('files/advertisingEmail/Campaign-Daily-' . date('YmdHis') . '-' . $scheduleId . '.xlsx');
                                $this->_generateSoldByCSV($campaignFinalMetricsArray, $fileTempPath);
                                /*$fileNames[] =array('path'=>$fileTempPath,
                                    'name'=>$fileName
                                ) ;*/
                                //$finalDataArray['Campaign'] = $campaignFinalMetricsArray;
                                //$cronData['campaignReportStatus'] = '<p>Campaign Report : Successfully generated.<a href="http://localhost:8080/dev.helotek1/public/files/advertisingEmail/Product-Ads-Monthly-20200412215228-9.xlsx" download=""> Click Here To Download </a></p>';
                                $completeFilePath = $apiServerLink . $fileName;
                                $cronData['campaignReportStatus'] = '<p>Campaign Report : <a href="' . $apiServerLink . $fileName . '" download=""> Click Here To Download </a></p>';
                                $fileEntryArray = [];
                                $fileEntryArray = array('fkScheduleId' => $scheduleId, 'fkParameterTypeId' => 1, 'parameterTypeName' => 'Campaign', 'time' => $currentTime, 'date' => $date,
                                    'fileName' => $fileName,
                                    'filePath' => $fileTempPath,
                                    'completeFilePath' => $completeFilePath,
                                    'devServerLink' => $devServerLink,
                                    'apiServerLink' => $apiServerLink,
                                    'isDataFound' => '1',
                                    'isFileDeleted' => '0');
                                $this->_fileDbLogs($fileEntryArray);

                            } else {
                                $fileEntryArray = [];
                                $fileEntryArray = array('fkScheduleId' => $scheduleId, 'fkParameterTypeId' => 1, 'parameterTypeName' => 'Campaign', 'time' => $currentTime, 'date' => $date,
                                    'devServerLink' => $devServerLink,
                                    'apiServerLink' => $apiServerLink,
                                    'isDataFound' => '0',
                                    'isFileDeleted' => '0');
                                $this->_fileDbLogs($fileEntryArray);
                                $cronData['campaignReportStatus'] = '<p>Campaign Report : Data not found.</p>';
                                Log::info("filePath:app\Console\Commands\Ams\AmsAdvertisingEmailSchedule, Module Name: Advertising Reports to e-mail. Message :No data found against this schedule.");
                            }
                            break;
                        //weekely start
                        case 'Weekly':
                            echo 'Weekly';
                            $DB2 = 'mysqlDb2';
                            $parameters = array($amsProfileId, $commaSeprateSponsordType, $timeFrame);
                            $storedProceduresData = \DB::connection($DB2)->select('call spCalculateWeeklyCampaignSchedulingReport(?,?,?)', $parameters);
                            \DB::disconnect($DB2);
                            if (!empty($storedProceduresData)) {
                                $filterDataArray = [];
                                $filterData = [];
                                foreach ($storedProceduresData as $orgnalKey => $orgnalvalue) {
                                    $filterKey = strtolower($orgnalKey);
                                    $filterDataArray['brand_id'] = $orgnalvalue->brand_id;
                                    $filterDataArray['brand_name'] = $orgnalvalue->brand_name;
                                    //$filterDataArray['accouint_id'] = $orgnalvalue->accouint_id;
                                    $filterDataArray['account_name'] = $orgnalvalue->account_name;
                                    $filterDataArray['profile_id'] = $orgnalvalue->profile_id;
                                    $filterDataArray['profile_name'] = $orgnalvalue->profile_name;
                                    $filterDataArray['year_'] = $orgnalvalue->year_;
                                    $filterDataArray['start_date'] = $orgnalvalue->start_date;
                                    $filterDataArray['end_date'] = $orgnalvalue->end_date;
                                    $filterDataArray['campaign_id'] = $orgnalvalue->campaign_id;
                                    $filterDataArray['campaign_name'] = $orgnalvalue->campaign_name;
                                    $filterDataArray['campaign_type'] = $orgnalvalue->campaign_type;
                                    $filterDataArray['campaign_budget'] = $orgnalvalue->campaign_budget;
                                    $filterDataArray['impressions'] = $orgnalvalue->impressions;
                                    $filterDataArray['clicks'] = $orgnalvalue->clicks;
                                    $filterDataArray['cost'] = $orgnalvalue->cost;
                                    $filterDataArray['revenue'] = $orgnalvalue->revenue;
                                    $filterDataArray['order_conversion'] = $orgnalvalue->order_conversion;
                                    $filterDataArray['acos'] = $orgnalvalue->acos_;
                                    $filterDataArray['CPC'] = $orgnalvalue->CPC;
                                    $filterDataArray['CTR'] = $orgnalvalue->CTR;
                                    $filterDataArray['CPA'] = $orgnalvalue->CPA;
                                    $filterDataArray['ROAS'] = $orgnalvalue->ROAS;
                                    $filterData[] = array_change_key_case($filterDataArray, CASE_LOWER);
                                }
                                $storeNew = [];
                                $matchesColumns = [];
                                foreach ($filterData as $key => $value) {
                                    foreach ($compaignMetricsArray as $key1) {
                                        if (array_key_exists($key1, $value)) {
                                            $matchesColumns[$key1] = $value[$key1];
                                        }
                                        //$matchesColumns[$key1];
                                    }
                                    array_push($storeNew, $matchesColumns);
                                }

                                $campaignFinalMetrics = array();
                                $campaignFinalMetricsArray = array();
                                foreach ($storeNew as $campaignFinalMetricKey => $campaignFinalMetricValue) {
                                    if (isset($campaignFinalMetricValue['year_'])) {
                                        $campaignFinalMetrics['Year_'] = $campaignFinalMetricValue['year_'];
                                    }
                                    if (isset($campaignFinalMetricValue['start_date'])) {
                                        $campaignFinalMetrics['Start Date'] = $campaignFinalMetricValue['start_date'];
                                    }
                                    if (isset($campaignFinalMetricValue['end_date'])) {
                                        $campaignFinalMetrics['End Date'] = $campaignFinalMetricValue['end_date'];
                                    }
                                    //if(isset($campaignFinalMetricValue['brand_name'])){ $campaignFinalMetrics['Brand Name'] = $campaignFinalMetricValue['brand_name']; }
                                    //if(isset($campaignFinalMetricValue['account_name'])){ $campaignFinalMetrics['Account Name'] = $campaignFinalMetricValue['account_name']; }
                                    //if(isset($campaignFinalMetricValue['campaign_id'])){ $campaignFinalMetrics['Campaign Id'] = $campaignFinalMetricValue['campaign_id']; }
                                    if (isset($campaignFinalMetricValue['campaign_name'])) {
                                        $campaignFinalMetrics['Campaign Name'] = $campaignFinalMetricValue['campaign_name'];
                                    }
                                    if (isset($campaignFinalMetricValue['campaign_type'])) {
                                        $campaignFinalMetrics['Campaign Type'] = $campaignFinalMetricValue['campaign_type'];
                                    }
                                    if (isset($campaignFinalMetricValue['campaign_budget'])) {
                                        $campaignFinalMetrics['Campaign Budget'] = $this->roundValue($campaignFinalMetricValue['campaign_budget']);
                                    }
                                    if (isset($campaignFinalMetricValue['impressions'])) {
                                        $campaignFinalMetrics['Impressions'] = $campaignFinalMetricValue['impressions'];
                                    }
                                    if (isset($campaignFinalMetricValue['clicks'])) {
                                        $campaignFinalMetrics['Clicks'] = $campaignFinalMetricValue['clicks'];
                                    }
                                    if (isset($campaignFinalMetricValue['cost'])) {
                                        $campaignFinalMetrics['Cost'] = $this->roundValue($campaignFinalMetricValue['cost']);
                                    }
                                    if (isset($campaignFinalMetricValue['revenue'])) {
                                        $campaignFinalMetrics['Revenue'] = $this->roundValue($campaignFinalMetricValue['revenue']);
                                    }
                                    if (isset($campaignFinalMetricValue['order_conversion'])) {
                                        $campaignFinalMetrics['Order Conversion'] = $campaignFinalMetricValue['order_conversion'];
                                    }
                                    if (isset($campaignFinalMetricValue['ctr'])) {
                                        $campaignFinalMetrics['Click-Thru Rate (CTR)'] = $this->roundValue($campaignFinalMetricValue['ctr']);
                                    }
                                    if (isset($campaignFinalMetricValue['cpc'])) {
                                        $campaignFinalMetrics['Cost Per Click (CPC)'] = $this->roundValue($campaignFinalMetricValue['cpc']);
                                    }
                                    if (isset($campaignFinalMetricValue['acos'])) {
                                        $campaignFinalMetrics['Total Advertising Cost of Sales (ACoS)'] = $this->roundValue($campaignFinalMetricValue['acos']);
                                    }
                                    if (isset($campaignFinalMetricValue['roas'])) {
                                        $campaignFinalMetrics['Total Return on Advertising Spend (RoAS)'] = $this->roundValue($campaignFinalMetricValue['roas']);
                                    }
                                    if (isset($campaignFinalMetricValue['cpa'])) {
                                        $campaignFinalMetrics['CPA'] = $this->roundValue($campaignFinalMetricValue['cpa']);
                                    }
                                    array_push($campaignFinalMetricsArray, $campaignFinalMetrics);
                                }
                                $fileName = 'Campaign-Weekly-' . date('YmdHis') . '-' . $scheduleId . '.xlsx';
                                $fileTempPath = public_path('files/advertisingEmail/Campaign-Weekly-' . date('YmdHis') . '-' . $scheduleId . '.xlsx');
                                $this->_generateSoldByCSV($campaignFinalMetricsArray, $fileTempPath);
                                /* $fileNames[] =array('path'=>$fileTempPath,
                                     'name'=>$fileName
                                 ) ;*/
                                //$finalDataArray['Campaign'] = $campaignFinalMetricsArray;
                                //$cronData['campaignReportStatus'] = '<p>Campaign Report : Successfully generated.Check the attachment.</p>';
                                $cronData['campaignReportStatus'] = '<p>Campaign Report : <a href="' . $apiServerLink . $fileName . '" download=""> Click Here To Download </a></p>';
                                $completeFilePath = $apiServerLink . $fileName;
                                $fileEntryArray = [];
                                $fileEntryArray = array('fkScheduleId' => $scheduleId, 'fkParameterTypeId' => 1, 'parameterTypeName' => 'Campaign', 'time' => $currentTime, 'date' => $date,
                                    'fileName' => $fileName,
                                    'filePath' => $fileTempPath,
                                    'completeFilePath' => $completeFilePath,
                                    'devServerLink' => $devServerLink,
                                    'apiServerLink' => $apiServerLink,
                                    'isDataFound' => '1',
                                    'isFileDeleted' => '0');
                                $this->_fileDbLogs($fileEntryArray);

                            } else {
                                $fileEntryArray = [];
                                $fileEntryArray = array('fkScheduleId' => $scheduleId, 'fkParameterTypeId' => 1, 'parameterTypeName' => 'Campaign', 'time' => $currentTime, 'date' => $date,
                                    'devServerLink' => $devServerLink,
                                    'apiServerLink' => $apiServerLink,
                                    'isDataFound' => '0',
                                    'isFileDeleted' => '0');
                                $this->_fileDbLogs($fileEntryArray);
                                $cronData['campaignReportStatus'] = '<p>Campaign Report : Data not found.</p>';
                                Log::info("filePath:app\Console\Commands\Ams\AmsAdvertisingEmailSchedule, Module Name: Advertising Reports to e-mail. Message :No data found against this schedule.");
                            }
                            break;
                        //monthly start
                        case 'Monthly':
                            echo 'Monthly';
                            $DB2 = 'mysqlDb2';
                            $parameters = array($amsProfileId, $commaSeprateSponsordType, $timeFrame);
                            $storedProceduresData = \DB::connection($DB2)->select('call spCalculateMonthlyCampaignSchedulingReport(?,?,?)', $parameters);
                            \DB::disconnect($DB2);
                            if (!empty($storedProceduresData)) {
                                $filterDataArray = [];
                                $filterData = [];
                                foreach ($storedProceduresData as $orgnalKey => $orgnalvalue) {

                                    $filterKey = strtolower($orgnalKey);
                                    $filterDataArray['brand_id'] = $orgnalvalue->brand_id;
                                    $filterDataArray['brand_name'] = $orgnalvalue->brand_name;
                                    //$filterDataArray['accouint_id'] = $orgnalvalue->accouint_id;
                                    $filterDataArray['account_name'] = $orgnalvalue->account_name;
                                    $filterDataArray['profile_id'] = $orgnalvalue->profile_id;
                                    $filterDataArray['profile_name'] = $orgnalvalue->profile_name;
                                    $filterDataArray['year_'] = $orgnalvalue->year_;
                                    $filterDataArray['start_date'] = $orgnalvalue->start_date;
                                    $filterDataArray['end_date'] = $orgnalvalue->end_date;
                                    $filterDataArray['campaign_id'] = $orgnalvalue->campaign_id;
                                    $filterDataArray['campaign_name'] = $orgnalvalue->campaign_name;
                                    $filterDataArray['campaign_type'] = $orgnalvalue->campaign_type;
                                    $filterDataArray['campaign_budget'] = $orgnalvalue->campaign_budget;
                                    $filterDataArray['impressions'] = $orgnalvalue->impressions;
                                    $filterDataArray['clicks'] = $orgnalvalue->clicks;
                                    $filterDataArray['cost'] = $orgnalvalue->cost;
                                    $filterDataArray['revenue'] = $orgnalvalue->revenue;
                                    $filterDataArray['order_conversion'] = $orgnalvalue->order_conversion;
                                    $filterDataArray['acos'] = $orgnalvalue->acos_;
                                    $filterDataArray['CPC'] = $orgnalvalue->CPC;
                                    $filterDataArray['CTR'] = $orgnalvalue->CTR;
                                    $filterDataArray['CPA'] = $orgnalvalue->CPA;
                                    $filterDataArray['ROAS'] = $orgnalvalue->ROAS;
                                    $filterData[] = array_change_key_case($filterDataArray, CASE_LOWER);
                                }
                                $storeNew = [];
                                $matchesColumns = [];
                                foreach ($filterData as $key => $value) {

                                    foreach ($compaignMetricsArray as $key1) {

                                        if (array_key_exists($key1, $value)) {
                                            $matchesColumns[$key1] = $value[$key1];

                                        }
                                    }
                                    array_push($storeNew, $matchesColumns);
                                }
                                $campaignFinalMetrics = array();
                                $campaignFinalMetricsArray = array();
                                foreach ($storeNew as $campaignFinalMetricKey => $campaignFinalMetricValue) {
                                    if (isset($campaignFinalMetricValue['year_'])) {
                                        $campaignFinalMetrics['Year_'] = $campaignFinalMetricValue['year_'];
                                    }
                                    if (isset($campaignFinalMetricValue['start_date'])) {
                                        $campaignFinalMetrics['Start Date'] = $campaignFinalMetricValue['start_date'];
                                    }
                                    if (isset($campaignFinalMetricValue['end_date'])) {
                                        $campaignFinalMetrics['End Date'] = $campaignFinalMetricValue['end_date'];
                                    }
                                    //if(isset($campaignFinalMetricValue['brand_name'])){ $campaignFinalMetrics['Brand Name'] = $campaignFinalMetricValue['brand_name']; }
                                    //if(isset($campaignFinalMetricValue['account_name'])){ $campaignFinalMetrics['Account Name'] = $campaignFinalMetricValue['account_name']; }
                                    //if(isset($campaignFinalMetricValue['campaign_id'])){ $campaignFinalMetrics['Campaign Id'] = $campaignFinalMetricValue['campaign_id']; }
                                    if (isset($campaignFinalMetricValue['campaign_name'])) {
                                        $campaignFinalMetrics['Campaign Name'] = $campaignFinalMetricValue['campaign_name'];
                                    }
                                    if (isset($campaignFinalMetricValue['campaign_type'])) {
                                        $campaignFinalMetrics['Campaign Type'] = $campaignFinalMetricValue['campaign_type'];
                                    }
                                    if (isset($campaignFinalMetricValue['campaign_budget'])) {
                                        $campaignFinalMetrics['Campaign Budget'] = $this->roundValue($campaignFinalMetricValue['campaign_budget']);
                                    }
                                    if (isset($campaignFinalMetricValue['impressions'])) {
                                        $campaignFinalMetrics['Impressions'] = $campaignFinalMetricValue['impressions'];
                                    }
                                    if (isset($campaignFinalMetricValue['clicks'])) {
                                        $campaignFinalMetrics['Clicks'] = $campaignFinalMetricValue['clicks'];
                                    }
                                    if (isset($campaignFinalMetricValue['cost'])) {
                                        $campaignFinalMetrics['Cost'] = $this->roundValue($campaignFinalMetricValue['cost']);
                                    }
                                    if (isset($campaignFinalMetricValue['revenue'])) {
                                        $campaignFinalMetrics['Revenue'] = $this->roundValue($campaignFinalMetricValue['revenue']);
                                    }
                                    if (isset($campaignFinalMetricValue['order_conversion'])) {
                                        $campaignFinalMetrics['Order Conversion'] = $campaignFinalMetricValue['order_conversion'];
                                    }
                                    if (isset($campaignFinalMetricValue['ctr'])) {
                                        $campaignFinalMetrics['Click-Thru Rate (CTR)'] = $this->roundValue($campaignFinalMetricValue['ctr']);
                                    }
                                    if (isset($campaignFinalMetricValue['cpc'])) {
                                        $campaignFinalMetrics['Cost Per Click (CPC)'] = $this->roundValue($campaignFinalMetricValue['cpc']);
                                    }
                                    if (isset($campaignFinalMetricValue['acos'])) {
                                        $campaignFinalMetrics['Total Advertising Cost of Sales (ACoS)'] = $this->roundValue($campaignFinalMetricValue['acos']);
                                    }
                                    if (isset($campaignFinalMetricValue['roas'])) {
                                        $campaignFinalMetrics['Total Return on Advertising Spend (RoAS)'] = $this->roundValue($campaignFinalMetricValue['roas']);
                                    }
                                    if (isset($campaignFinalMetricValue['cpa'])) {
                                        $campaignFinalMetrics['CPA'] = $this->roundValue($campaignFinalMetricValue['cpa']);
                                    }
                                    array_push($campaignFinalMetricsArray, $campaignFinalMetrics);
                                }
                                //dd($campaignFinalMetricsArray);
                                $fileName = 'Campaign-Monthly-' . date('YmdHis') . '-' . $scheduleId . '.xlsx';
                                $fileTempPath = public_path('files/advertisingEmail/Campaign-Monthly-' . date('YmdHis') . '-' . $scheduleId . '.xlsx');
                                $this->_generateSoldByCSV($campaignFinalMetricsArray, $fileTempPath);
                                /*$fileNames[] =array('path'=>$fileTempPath,
                                    'name'=>$fileName
                                ) ;*/
                                //$finalDataArray['Campaign'] = $campaignFinalMetricsArray;
                                //$cronData['campaignReportStatus'] = '<p>Campaign Report : Successfully generated.Check the attachment.</p>';
                                $cronData['campaignReportStatus'] = '<p>Campaign Report : <a href="' . $apiServerLink . $fileName . '" download=""> Click Here To Download </a></p>';
                                $completeFilePath = $apiServerLink . $fileName;
                                $fileEntryArray = [];
                                $fileEntryArray = array('fkScheduleId' => $scheduleId, 'fkParameterTypeId' => 1, 'parameterTypeName' => 'Campaign', 'time' => $currentTime, 'date' => $date,
                                    'fileName' => $fileName,
                                    'filePath' => $fileTempPath,
                                    'completeFilePath' => $completeFilePath,
                                    'devServerLink' => $devServerLink,
                                    'apiServerLink' => $apiServerLink,
                                    'isDataFound' => '1',
                                    'isFileDeleted' => '0');
                                $this->_fileDbLogs($fileEntryArray);

                            } else {
                                $fileEntryArray = [];
                                $fileEntryArray = array('fkScheduleId' => $scheduleId, 'fkParameterTypeId' => 1, 'parameterTypeName' => 'Campaign', 'time' => $currentTime, 'date' => $date,
                                    'devServerLink' => $devServerLink,
                                    'apiServerLink' => $apiServerLink,
                                    'isDataFound' => '0',
                                    'isFileDeleted' => '0');
                                $this->_fileDbLogs($fileEntryArray);
                                $cronData['campaignReportStatus'] = '<p>Campaign Report : Data not found.</p>';
                                //$noDATAEamil = $this->_noDataEmailAlert($cronData);
                                //Log::info("filePath:app\Console\Commands\Ams\AmsAdvertisingEmailSchedule, Module Name: Advertising Reports to e-mail. Message :No data found against this schedule.");
                            }
                            break;
                    }
                    /******** Make arryas and  csv with granularity ends***********/
                } else {
                    echo 'Not metrics found for campaign report';
                    Log::info("filePath:app\Console\Commands\Ams\AmsAdvertisingEmailSchedule, Module Name: Advertising Reports to e-mail. Message :Not metrics found for Keyword report.");
                }//end if
                /*** Get selected metrics against campaign parameter type  ends ***/
                /*** Get selected metrics against adGroup parameter type  starts ***/
                $selectAdGroupReportsSelectedMetrics = scheduledEmailAdvertisingReportsMetrics::where("fkReportScheduleId", $scheduleId)->where("fkParameterType", 2)->get();
                $adGroupReportsSelectedMetricsCount = $selectAdGroupReportsSelectedMetrics->count();
                if ($adGroupReportsSelectedMetricsCount > 0) {
                    /*** Get selected metrics against parameter type  starts ***/
                    $getSelectedSponsordTypes = amsScheduleSelectedEmailReports::distinct()->where('fkReportScheduleId', $scheduleId)->where('fkParameterType', 2)->get(['fkSponsordTypeId']);
                    $getSelectedSponsorTypesArray = array();
                    foreach ($getSelectedSponsordTypes as $getSelectedSponsordType) {
                        $getSelectedSponsorTypesArray[] = $getSelectedSponsordType->fkSponsordTypeId;
                    } //end foreach
                    $commaSeprateSponsordTypeArray = [];
                    $commaSeprateSponsordType = '';
                    if (in_array(1, $getSelectedSponsorTypesArray)) {
                        $commaSeprateSponsordTypeArray[] = 'SD';
                    }
                    if (in_array(2, $getSelectedSponsorTypesArray)) {
                        $commaSeprateSponsordTypeArray[] = 'SP';
                    }
                    if (in_array(3, $getSelectedSponsorTypesArray)) {
                        $commaSeprateSponsordTypeArray[] = 'SB';
                    }
                    if (empty($commaSeprateSponsordTypeArray)) {
                        $commaSeprateSponsordType = 'SP,SD,SB';
                    } else {
                        $commaSeprateSponsordTypeImplode = implode(",", $commaSeprateSponsordTypeArray);
                        //$commaSeprateSponsordType = "'$commaSeprateSponsordTypeImplode'";
                        $commaSeprateSponsordType = $commaSeprateSponsordTypeImplode;
                    }
                    /*** Get selected metrics against parameter type  ends ***/
                    /******** Get column name against metric id starts ***********/
                    /******** Metrics that will send in all emails starts ***********/
                    $adGroupMetricsArray = [];
                    switch ($granularity) {
                        case 'Daily':
                            $adGroupMetricsArray = [
                                'brand_name',
                                'account_name',
                                'date_key',
                            ];
                            break;
                        default:
                            $adGroupMetricsArray = [
                                'brand_name',
                                'account_name',
                                'year_',
                                'start_date',
                                'end_date'
                            ];
                            break;
                    }//end switch
                    /******** Metrics that will send in all emails ends ***********/
                    /******** Make refined metrics array starts ***********/
                    foreach ($selectAdGroupReportsSelectedMetrics as $selectAdGroupReportsSelectedMetric) {

                        $currentAdGroupMetricId = $selectAdGroupReportsSelectedMetric->fkReportMetricId;

                        /*************** Get column name against metric id starts ***********/
                        $GetAdGroupReportMetricsNames = amsReportsMetrics::where('id', $currentAdGroupMetricId)->first();
                        $GetAdGroupReportMetricsNamesCount = $GetAdGroupReportMetricsNames->count();
                        if ($GetAdGroupReportMetricsNamesCount > 0) {
                            $tblAdGroupColumnName = $GetAdGroupReportMetricsNames->tblColumnName;
                            $adGroupMetricsArray[] = strtolower(trim($tblAdGroupColumnName));
                        }//end if
                    }//end foreach
                    /******** Make refined metrics array ends ***********/
                    /******** Make arryas and  csv with granularity starts***********/
                    switch ($granularity) {
                        case 'Daily':
                            echo 'Daily';
                            $DB2 = 'mysqlDb2';
                            $parameters = array($amsProfileId, $commaSeprateSponsordType, $timeFrame);
                            $storedProceduresData = \DB::connection($DB2)->select('call spCalculateDailyAdgroupSchedulingReport(?,?,?)', $parameters);
                            \DB::disconnect($DB2);
                            if (!empty($storedProceduresData)) {
                                Log::info("filePath:app\Console\Commands\Ams\AmsAdvertisingEmailSchedule, Module Name: Advertising Reports to e-mail. Message :Data found against this schedule.");
                                $filterDataArray = [];
                                $filterData = [];
                                //dd($storedProceduresData);
                                foreach ($storedProceduresData as $orgnalKey => $orgnalvalue) {
                                    $filterKey = strtolower($orgnalKey);
                                    $filterDataArray['brand_id'] = $orgnalvalue->brand_id;
                                    $filterDataArray['brand_name'] = $orgnalvalue->brand_name;
                                    //$filterDataArray['accouint_id'] = $orgnalvalue->accouint_id;
                                    $filterDataArray['account_name'] = $orgnalvalue->account_name;
                                    $filterDataArray['profile_id'] = $orgnalvalue->profile_id;
                                    $filterDataArray['profile_name'] = $orgnalvalue->profile_name;
                                    $filterDataArray['date_key'] = $orgnalvalue->date_key;
                                    $filterDataArray['campaign_id'] = $orgnalvalue->campaign_id;
                                    $filterDataArray['campaign_name'] = $orgnalvalue->campaign_name;
                                    $filterDataArray['campaign_type'] = $orgnalvalue->campaign_type;
                                    $filterDataArray['adGroupId'] = $orgnalvalue->adGroupId;
                                    $filterDataArray['adGroupName'] = $orgnalvalue->adGroupName;
                                    $filterDataArray['impressions'] = $orgnalvalue->impressions;
                                    $filterDataArray['clicks'] = $orgnalvalue->clicks;
                                    $filterDataArray['cost'] = $orgnalvalue->cost;
                                    $filterDataArray['revenue'] = $orgnalvalue->revenue;
                                    $filterDataArray['order_conversion'] = $orgnalvalue->order_conversion;
                                    $filterDataArray['acos'] = $orgnalvalue->acos;
                                    $filterDataArray['cpc'] = $orgnalvalue->cpc;
                                    $filterDataArray['ctr'] = $orgnalvalue->ctr;
                                    $filterDataArray['cpa'] = $orgnalvalue->cpa;
                                    $filterDataArray['roas'] = $orgnalvalue->roas;
                                    $filterData[] = array_change_key_case($filterDataArray, CASE_LOWER);
                                }
                                //echo 'test';
                                //dd($filterData);
                                $storeNew = [];
                                $matchesColumns = [];
                                foreach ($filterData as $key => $value) {
                                    foreach ($adGroupMetricsArray as $key1) {
                                        if (array_key_exists($key1, $value)) {
                                            $matchesColumns[$key1] = $value[$key1];
                                        }
                                        //$matchesColumns[$key1];
                                    }
                                    array_push($storeNew, $matchesColumns);
                                }
                                //dd($storeNew);
                                $adGroupFinalMetrics = array();
                                $adGroupFinalMetricsArray = array();
                                foreach ($storeNew as $adGroupFinalMetricKey => $adGroupFinalMetricValue) {
                                    //dd($campaignFinalMetricValue);
                                    if (isset($adGroupFinalMetricValue['date_key'])) {
                                        $adGroupFinalMetrics['Date'] = $adGroupFinalMetricValue['date_key'];
                                    }
                                    //if(isset($adGroupFinalMetricValue['brand_name'])){ $adGroupFinalMetrics['Brand Name'] = $adGroupFinalMetricValue['brand_name']; }
                                    //if(isset($adGroupFinalMetricValue['account_name'])){ $adGroupFinalMetrics['Account Name'] = $adGroupFinalMetricValue['account_name']; }
                                    //if(isset($adGroupFinalMetricValue['campaign_id'])){ $adGroupFinalMetrics['Campaign Id'] = $adGroupFinalMetricValue['campaign_id']; }
                                    if (isset($adGroupFinalMetricValue['campaign_name'])) {
                                        $adGroupFinalMetrics['Campaign Name'] = $adGroupFinalMetricValue['campaign_name'];
                                    }
                                    if (isset($adGroupFinalMetricValue['campaign_type'])) {
                                        $adGroupFinalMetrics['Campaign Type'] = $adGroupFinalMetricValue['campaign_type'];
                                    }
                                    if (isset($adGroupFinalMetricValue['adgroupid'])) {
                                        $adGroupFinalMetrics['Ad Group Id'] = $adGroupFinalMetricValue['adgroupid'];
                                    }
                                    if (isset($adGroupFinalMetricValue['adgroupname'])) {
                                        $adGroupFinalMetrics['Ad Group Name'] = $adGroupFinalMetricValue['adgroupname'];
                                    }
                                    if (isset($adGroupFinalMetricValue['impressions'])) {
                                        $adGroupFinalMetrics['Impressions'] = $adGroupFinalMetricValue['impressions'];
                                    }
                                    if (isset($adGroupFinalMetricValue['clicks'])) {
                                        $adGroupFinalMetrics['Clicks'] = $adGroupFinalMetricValue['clicks'];
                                    }
                                    if (isset($adGroupFinalMetricValue['cost'])) {
                                        $adGroupFinalMetrics['Cost'] = $this->roundValue($adGroupFinalMetricValue['cost']);
                                    }
                                    if (isset($adGroupFinalMetricValue['revenue'])) {
                                        $adGroupFinalMetrics['Revenue'] = $this->roundValue($adGroupFinalMetricValue['revenue']);
                                    }
                                    if (isset($adGroupFinalMetricValue['order_conversion'])) {
                                        $adGroupFinalMetrics['Order Conversion'] = $adGroupFinalMetricValue['order_conversion'];
                                    }
                                    if (isset($adGroupFinalMetricValue['ctr'])) {
                                        $adGroupFinalMetrics['Click-Thru Rate (CTR)'] = $this->roundValue($adGroupFinalMetricValue['ctr']);
                                    }
                                    if (isset($adGroupFinalMetricValue['cpc'])) {
                                        $adGroupFinalMetrics['Cost Per Click (CPC)'] = $this->roundValue($adGroupFinalMetricValue['cpc']);
                                    }
                                    if (isset($adGroupFinalMetricValue['acos'])) {
                                        $adGroupFinalMetrics['Total Advertising Cost of Sales (ACoS)'] = $this->roundValue($adGroupFinalMetricValue['acos']);
                                    }
                                    if (isset($adGroupFinalMetricValue['roas'])) {
                                        $adGroupFinalMetrics['Total Return on Advertising Spend (RoAS)'] = $this->roundValue($adGroupFinalMetricValue['roas']);
                                    }
                                    if (isset($adGroupFinalMetricValue['cpa'])) {
                                        $adGroupFinalMetrics['CPA'] = $this->roundValue($adGroupFinalMetricValue['cpa']);
                                    }
                                    array_push($adGroupFinalMetricsArray, $adGroupFinalMetrics);
                                    //isset()
                                    //dd($campaignFinalMetrics);
                                }
                                //dd($adGroupFinalMetricsArray);
                                // dd($storeNew);

                                $fileName = 'Ad-Group-Daily-' . date('YmdHis') . '-' . $scheduleId . '.xlsx';
                                $fileTempPath = public_path('files/advertisingEmail/Ad-Group-Daily-' . date('YmdHis') . '-' . $scheduleId . '.xlsx');
                                $this->_generateSoldByCSV($adGroupFinalMetricsArray, $fileTempPath);
                                /*$fileNames[] =array('path'=>$fileTempPath,
                                    'name'=>$fileName
                                ) ;*/
                                //$finalDataArray['Ad-Group'] = $adGroupFinalMetricsArray;
                                //$cronData['adGroupReportStatus'] = '<p>Ad Group Report : Successfully generated.Check the attachment.</p>';
                                $cronData['adGroupReportStatus'] = '<p>Ad Group Report : <a href="' . $apiServerLink . $fileName . '" download=""> Click Here To Download </a></p>';
                                $completeFilePath = $apiServerLink . $fileName;
                                $fileEntryArray = [];
                                $fileEntryArray = array('fkScheduleId' => $scheduleId, 'fkParameterTypeId' => 2, 'parameterTypeName' => 'Ad Group', 'time' => $currentTime, 'date' => $date,
                                    'fileName' => $fileName,
                                    'filePath' => $fileTempPath,
                                    'completeFilePath' => $completeFilePath,
                                    'devServerLink' => $devServerLink,
                                    'apiServerLink' => $apiServerLink,
                                    'isDataFound' => '1',
                                    'isFileDeleted' => '0');
                                $this->_fileDbLogs($fileEntryArray);

                            } else {
                                $fileEntryArray = [];
                                $fileEntryArray = array('fkScheduleId' => $scheduleId, 'fkParameterTypeId' => 2, 'parameterTypeName' => 'Ad Group', 'time' => $currentTime, 'date' => $date,
                                    'devServerLink' => $devServerLink,
                                    'apiServerLink' => $apiServerLink,
                                    'isDataFound' => '0',
                                    'isFileDeleted' => '0');
                                $this->_fileDbLogs($fileEntryArray);
                                $cronData['adGroupReportStatus'] = '<p>Ad Group Report : Data not found.</p>';
                                Log::info("filePath:app\Console\Commands\Ams\AmsAdvertisingEmailSchedule, Module Name: Advertising Reports to e-mail. Message :No data found against this schedule.");
                            }
                            break;
                        //weekely start
                        case 'Weekly':
                            echo 'Weekly';
                            $DB2 = 'mysqlDb2';
                            $parameters = array($amsProfileId, $commaSeprateSponsordType, $timeFrame);
                            $storedProceduresData = \DB::connection($DB2)->select('call `spCalculateWeeklyAdgroupSchedulingReport` (?,?,?)', $parameters);
                            \DB::disconnect($DB2);
                            if (!empty($storedProceduresData)) {
                                $filterDataArray = [];
                                $filterData = [];
                                //dd($storedProceduresData);
                                foreach ($storedProceduresData as $orgnalKey => $orgnalvalue) {
                                    $filterKey = strtolower($orgnalKey);
                                    $filterDataArray['brand_id'] = $orgnalvalue->brand_id;
                                    $filterDataArray['brand_name'] = $orgnalvalue->brand_name;
                                    //$filterDataArray['accouint_id'] = $orgnalvalue->accouint_id;
                                    $filterDataArray['account_name'] = $orgnalvalue->account_name;
                                    $filterDataArray['profile_id'] = $orgnalvalue->profile_id;
                                    $filterDataArray['profile_name'] = $orgnalvalue->profile_name;
                                    $filterDataArray['year_'] = $orgnalvalue->year_;
                                    $filterDataArray['start_date'] = $orgnalvalue->start_date;
                                    $filterDataArray['end_date'] = $orgnalvalue->end_Date;
                                    $filterDataArray['campaign_id'] = $orgnalvalue->campaign_id;
                                    $filterDataArray['campaign_name'] = $orgnalvalue->campaign_name;
                                    $filterDataArray['campaign_type'] = $orgnalvalue->campaign_type;
                                    $filterDataArray['adGroupId'] = $orgnalvalue->adGroupId;
                                    $filterDataArray['adGroupName'] = $orgnalvalue->adGroupName;
                                    $filterDataArray['impressions'] = $orgnalvalue->impressions;
                                    $filterDataArray['clicks'] = $orgnalvalue->clicks;
                                    $filterDataArray['cost'] = $orgnalvalue->cost;
                                    $filterDataArray['revenue'] = $orgnalvalue->revenue;
                                    $filterDataArray['order_conversion'] = $orgnalvalue->order_conversion;
                                    $filterDataArray['acos'] = $orgnalvalue->acos_;
                                    $filterDataArray['CPC'] = $orgnalvalue->CPC;
                                    $filterDataArray['CTR'] = $orgnalvalue->CTR;
                                    $filterDataArray['CPA'] = $orgnalvalue->CPA;
                                    $filterDataArray['ROAS'] = $orgnalvalue->ROAS;
                                    $filterData[] = array_change_key_case($filterDataArray, CASE_LOWER);
                                }

                                $storeNew = [];
                                $matchesColumns = [];
                                foreach ($filterData as $key => $value) {
                                    foreach ($adGroupMetricsArray as $key1) {
                                        if (array_key_exists($key1, $value)) {
                                            $matchesColumns[$key1] = $value[$key1];
                                        }
                                        //$matchesColumns[$key1];
                                    }
                                    array_push($storeNew, $matchesColumns);
                                }
                                $adGroupFinalMetrics = array();
                                $adGroupFinalMetricsArray = array();
                                foreach ($storeNew as $adGroupFinalMetricKey => $adGroupFinalMetricValue) {
                                    if (isset($adGroupFinalMetricValue['year_'])) {
                                        $adGroupFinalMetrics['Year_'] = $adGroupFinalMetricValue['year_'];
                                    }
                                    if (isset($adGroupFinalMetricValue['start_date'])) {
                                        $adGroupFinalMetrics['Start Date'] = $adGroupFinalMetricValue['start_date'];
                                    }
                                    if (isset($adGroupFinalMetricValue['end_date'])) {
                                        $adGroupFinalMetrics['End Date'] = $adGroupFinalMetricValue['end_date'];
                                    }
                                    //if(isset($adGroupFinalMetricValue['brand_name'])){ $adGroupFinalMetrics['Brand Name'] = $adGroupFinalMetricValue['brand_name']; }
                                    //if(isset($adGroupFinalMetricValue['account_name'])){ $adGroupFinalMetrics['Account Name'] = $adGroupFinalMetricValue['account_name']; }
                                    //if(isset($adGroupFinalMetricValue['campaign_id'])){ $adGroupFinalMetrics['Campaign Id'] = $adGroupFinalMetricValue['campaign_id']; }
                                    if (isset($adGroupFinalMetricValue['campaign_name'])) {
                                        $adGroupFinalMetrics['Campaign Name'] = $adGroupFinalMetricValue['campaign_name'];
                                    }
                                    if (isset($adGroupFinalMetricValue['campaign_type'])) {
                                        $adGroupFinalMetrics['Campaign Type'] = $adGroupFinalMetricValue['campaign_type'];
                                    }
                                    if (isset($adGroupFinalMetricValue['adgroupid'])) {
                                        $adGroupFinalMetrics['Ad Group Id'] = $adGroupFinalMetricValue['adgroupid'];
                                    }
                                    if (isset($adGroupFinalMetricValue['adgroupname'])) {
                                        $adGroupFinalMetrics['Ad Group Name'] = $adGroupFinalMetricValue['adgroupname'];
                                    }
                                    if (isset($adGroupFinalMetricValue['impressions'])) {
                                        $adGroupFinalMetrics['Impressions'] = $adGroupFinalMetricValue['impressions'];
                                    }
                                    if (isset($adGroupFinalMetricValue['clicks'])) {
                                        $adGroupFinalMetrics['Clicks'] = $adGroupFinalMetricValue['clicks'];
                                    }
                                    if (isset($adGroupFinalMetricValue['cost'])) {
                                        $adGroupFinalMetrics['Cost'] = $this->roundValue($adGroupFinalMetricValue['cost']);
                                    }
                                    if (isset($adGroupFinalMetricValue['revenue'])) {
                                        $adGroupFinalMetrics['Revenue'] = $this->roundValue($adGroupFinalMetricValue['revenue']);
                                    }
                                    if (isset($adGroupFinalMetricValue['order_conversion'])) {
                                        $adGroupFinalMetrics['Order Conversion'] = $adGroupFinalMetricValue['order_conversion'];
                                    }
                                    if (isset($adGroupFinalMetricValue['ctr'])) {
                                        $adGroupFinalMetrics['Click-Thru Rate (CTR)'] = $this->roundValue($adGroupFinalMetricValue['ctr']);
                                    }
                                    if (isset($adGroupFinalMetricValue['cpc'])) {
                                        $adGroupFinalMetrics['Cost Per Click (CPC)'] = $this->roundValue($adGroupFinalMetricValue['cpc']);
                                    }
                                    if (isset($adGroupFinalMetricValue['acos'])) {
                                        $adGroupFinalMetrics['Total Advertising Cost of Sales (ACoS)'] = $this->roundValue($adGroupFinalMetricValue['acos']);
                                    }
                                    if (isset($adGroupFinalMetricValue['roas'])) {
                                        $adGroupFinalMetrics['Total Return on Advertising Spend (RoAS)'] = $this->roundValue($adGroupFinalMetricValue['roas']);
                                    }
                                    if (isset($adGroupFinalMetricValue['cpa'])) {
                                        $adGroupFinalMetrics['CPA'] = $this->roundValue($adGroupFinalMetricValue['cpa']);
                                    }
                                    array_push($adGroupFinalMetricsArray, $adGroupFinalMetrics);
                                }
                                $fileName = 'Ad-Group-Weekly-' . date('YmdHis') . '-' . $scheduleId . '.xlsx';
                                $fileTempPath = public_path('files/advertisingEmail/Ad-Group-Weekly-' . date('YmdHis') . '-' . $scheduleId . '.xlsx');
                                $this->_generateSoldByCSV($adGroupFinalMetricsArray, $fileTempPath);
                                /* $fileNames[] =array('path'=>$fileTempPath,
                                     'name'=>$fileName
                                 ) ;*/
                                //$finalDataArray['Ad-Group'] = $adGroupFinalMetricsArray;
                                //$cronData['adGroupReportStatus'] = '<p>Ad Group Report : Successfully generated.Check the attachment.</p>';
                                $cronData['adGroupReportStatus'] = '<p>Ad Group Report : <a href="' . $apiServerLink . $fileName . '" download=""> Click Here To Download </a></p>';
                                $completeFilePath = $apiServerLink . $fileName;
                                $fileEntryArray = [];
                                $fileEntryArray = array('fkScheduleId' => $scheduleId, 'fkParameterTypeId' => 2, 'parameterTypeName' => 'Ad Group', 'time' => $currentTime, 'date' => $date,
                                    'fileName' => $fileName,
                                    'filePath' => $fileTempPath,
                                    'completeFilePath' => $completeFilePath,
                                    'devServerLink' => $devServerLink,
                                    'apiServerLink' => $apiServerLink,
                                    'isDataFound' => '1',
                                    'isFileDeleted' => '0');
                                $this->_fileDbLogs($fileEntryArray);

                            } else {
                                $fileEntryArray = [];
                                $fileEntryArray = array('fkScheduleId' => $scheduleId, 'fkParameterTypeId' => 2, 'parameterTypeName' => 'Ad Group', 'time' => $currentTime, 'date' => $date,
                                    'devServerLink' => $devServerLink,
                                    'apiServerLink' => $apiServerLink,
                                    'isDataFound' => '0',
                                    'isFileDeleted' => '0');
                                $this->_fileDbLogs($fileEntryArray);
                                $cronData['adGroupReportStatus'] = '<p>Ad Group Report : Data not found.</p>';
                                Log::info("filePath:app\Console\Commands\Ams\AmsAdvertisingEmailSchedule, Module Name: Advertising Reports to e-mail. Message :No data found against this schedule.");
                            }
                            break;
                        //monthly start
                        case 'Monthly':
                            echo 'Monthly';
                            $DB2 = 'mysqlDb2';
                            $parameters = array($amsProfileId, $commaSeprateSponsordType, $timeFrame);
                            $storedProceduresData = \DB::connection($DB2)->select('call `spCalculateMonthlyAdgroupSchedulingReport` (?,?,?)', $parameters);
                            \DB::disconnect($DB2);
                            if (!empty($storedProceduresData)) {
                                $filterDataArray = [];
                                $filterData = [];
                                //dd($storedProceduresData);
                                foreach ($storedProceduresData as $orgnalKey => $orgnalvalue) {

                                    $filterKey = strtolower($orgnalKey);
                                    $filterDataArray['brand_id'] = $orgnalvalue->brand_id;
                                    $filterDataArray['brand_name'] = $orgnalvalue->brand_name;
                                    //$filterDataArray['accouint_id'] = $orgnalvalue->accouint_id;
                                    $filterDataArray['account_name'] = $orgnalvalue->account_name;
                                    $filterDataArray['profile_id'] = $orgnalvalue->profile_id;
                                    $filterDataArray['profile_name'] = $orgnalvalue->profile_name;
                                    $filterDataArray['year_'] = $orgnalvalue->year_;
                                    $filterDataArray['start_date'] = $orgnalvalue->start_date;
                                    $filterDataArray['end_date'] = $orgnalvalue->end_Date;
                                    $filterDataArray['campaign_id'] = $orgnalvalue->campaign_id;
                                    $filterDataArray['campaign_name'] = $orgnalvalue->campaign_name;
                                    $filterDataArray['campaign_type'] = $orgnalvalue->campaign_type;
                                    $filterDataArray['adGroupId'] = $orgnalvalue->adGroupId;
                                    $filterDataArray['adGroupName'] = $orgnalvalue->adGroupName;
                                    $filterDataArray['impressions'] = $orgnalvalue->impressions;
                                    $filterDataArray['clicks'] = $orgnalvalue->clicks;
                                    $filterDataArray['cost'] = $orgnalvalue->cost;
                                    $filterDataArray['revenue'] = $orgnalvalue->revenue;
                                    $filterDataArray['order_conversion'] = $orgnalvalue->order_conversion;
                                    $filterDataArray['acos'] = $orgnalvalue->acos_;
                                    $filterDataArray['CPC'] = $orgnalvalue->CPC;
                                    $filterDataArray['CTR'] = $orgnalvalue->CTR;
                                    $filterDataArray['CPA'] = $orgnalvalue->CPA;
                                    $filterDataArray['ROAS'] = $orgnalvalue->ROAS;
                                    $filterData[] = array_change_key_case($filterDataArray, CASE_LOWER);
                                }
                                $storeNew = [];
                                $matchesColumns = [];
                                foreach ($filterData as $key => $value) {

                                    foreach ($adGroupMetricsArray as $key1) {

                                        if (array_key_exists($key1, $value)) {
                                            $matchesColumns[$key1] = $value[$key1];

                                        }
                                    }
                                    array_push($storeNew, $matchesColumns);
                                }
                                $adGroupFinalMetrics = array();
                                $adGroupFinalMetricsArray = array();
                                foreach ($storeNew as $adGroupFinalMetricKey => $adGroupFinalMetricValue) {
                                    if (isset($adGroupFinalMetricValue['year_'])) {
                                        $adGroupFinalMetrics['Year_'] = $adGroupFinalMetricValue['year_'];
                                    }
                                    if (isset($adGroupFinalMetricValue['start_date'])) {
                                        $adGroupFinalMetrics['Start Date'] = $adGroupFinalMetricValue['start_date'];
                                    }
                                    if (isset($adGroupFinalMetricValue['end_date'])) {
                                        $adGroupFinalMetrics['End Date'] = $adGroupFinalMetricValue['end_date'];
                                    }
                                    //if(isset($adGroupFinalMetricValue['brand_name'])){ $adGroupFinalMetrics['Brand Name'] = $adGroupFinalMetricValue['brand_name']; }
                                    //if(isset($adGroupFinalMetricValue['account_name'])){ $adGroupFinalMetrics['Account Name'] = $adGroupFinalMetricValue['account_name']; }
                                    //if(isset($adGroupFinalMetricValue['campaign_id'])){ $adGroupFinalMetrics['Campaign Id'] = $adGroupFinalMetricValue['campaign_id']; }
                                    if (isset($adGroupFinalMetricValue['campaign_name'])) {
                                        $adGroupFinalMetrics['Campaign Name'] = $adGroupFinalMetricValue['campaign_name'];
                                    }
                                    if (isset($adGroupFinalMetricValue['campaign_type'])) {
                                        $adGroupFinalMetrics['Campaign Type'] = $adGroupFinalMetricValue['campaign_type'];
                                    }
                                    if (isset($adGroupFinalMetricValue['adgroupid'])) {
                                        $adGroupFinalMetrics['Ad Group Id'] = $adGroupFinalMetricValue['adgroupid'];
                                    }
                                    if (isset($adGroupFinalMetricValue['adgroupname'])) {
                                        $adGroupFinalMetrics['Ad Group Name'] = $adGroupFinalMetricValue['adgroupname'];
                                    }
                                    if (isset($adGroupFinalMetricValue['impressions'])) {
                                        $adGroupFinalMetrics['Impressions'] = $adGroupFinalMetricValue['impressions'];
                                    }
                                    if (isset($adGroupFinalMetricValue['clicks'])) {
                                        $adGroupFinalMetrics['Clicks'] = $adGroupFinalMetricValue['clicks'];
                                    }
                                    if (isset($adGroupFinalMetricValue['cost'])) {
                                        $adGroupFinalMetrics['Cost'] = $this->roundValue($adGroupFinalMetricValue['cost']);
                                    }
                                    if (isset($adGroupFinalMetricValue['revenue'])) {
                                        $adGroupFinalMetrics['Revenue'] = $this->roundValue($adGroupFinalMetricValue['revenue']);
                                    }
                                    if (isset($adGroupFinalMetricValue['order_conversion'])) {
                                        $adGroupFinalMetrics['Order Conversion'] = $adGroupFinalMetricValue['order_conversion'];
                                    }
                                    if (isset($adGroupFinalMetricValue['ctr'])) {
                                        $adGroupFinalMetrics['Click-Thru Rate (CTR)'] = $this->roundValue($adGroupFinalMetricValue['ctr']);
                                    }
                                    if (isset($adGroupFinalMetricValue['cpc'])) {
                                        $adGroupFinalMetrics['Cost Per Click (CPC)'] = $this->roundValue($adGroupFinalMetricValue['cpc']);
                                    }
                                    if (isset($adGroupFinalMetricValue['acos'])) {
                                        $adGroupFinalMetrics['Total Advertising Cost of Sales (ACoS)'] = $this->roundValue($adGroupFinalMetricValue['acos']);
                                    }
                                    if (isset($adGroupFinalMetricValue['roas'])) {
                                        $adGroupFinalMetrics['Total Return on Advertising Spend (RoAS)'] = $this->roundValue($adGroupFinalMetricValue['roas']);
                                    }
                                    if (isset($adGroupFinalMetricValue['cpa'])) {
                                        $adGroupFinalMetrics['CPA'] = $this->roundValue($adGroupFinalMetricValue['cpa']);
                                    }
                                    array_push($adGroupFinalMetricsArray, $adGroupFinalMetrics);
                                }
                                $fileName = 'Ad-Group-Monthly-' . date('YmdHis') . '-' . $scheduleId . '.xlsx';
                                $fileTempPath = public_path('files/advertisingEmail/Ad-Group-Monthly-' . date('YmdHis') . '-' . $scheduleId . '.xlsx');
                                $this->_generateSoldByCSV($adGroupFinalMetricsArray, $fileTempPath);
                                /* $fileNames[] =array('path'=>$fileTempPath,
                                     'name'=>$fileName
                                 ) ;*/
                                //$finalDataArray['Ad-Group'] = $adGroupFinalMetricsArray;
                                //$cronData['adGroupReportStatus'] = '<p>Ad Group Report : Successfully generated.Check the attachment.</p>';
                                $cronData['adGroupReportStatus'] = '<p>Ad Group Report : <a href="' . $apiServerLink . $fileName . '" download=""> Click Here To Download </a></p>';
                                $completeFilePath = $apiServerLink . $fileName;
                                $fileEntryArray = [];
                                $fileEntryArray = array('fkScheduleId' => $scheduleId, 'fkParameterTypeId' => 2, 'parameterTypeName' => 'Ad Group', 'time' => $currentTime, 'date' => $date,
                                    'fileName' => $fileName,
                                    'filePath' => $fileTempPath,
                                    'completeFilePath' => $completeFilePath,
                                    'devServerLink' => $devServerLink,
                                    'apiServerLink' => $apiServerLink,
                                    'isDataFound' => '1',
                                    'isFileDeleted' => '0');
                                $this->_fileDbLogs($fileEntryArray);

                            } else {
                                $fileEntryArray = [];
                                $fileEntryArray = array('fkScheduleId' => $scheduleId, 'fkParameterTypeId' => 2, 'parameterTypeName' => 'Ad Group', 'time' => $currentTime, 'date' => $date,
                                    'devServerLink' => $devServerLink,
                                    'apiServerLink' => $apiServerLink,
                                    'isDataFound' => '0',
                                    'isFileDeleted' => '0');
                                $this->_fileDbLogs($fileEntryArray);
                                $cronData['adGroupReportStatus'] = '<p>Ad Group Report : Data not found.</p>';
                                //$noDATAEamil = $this->_noDataEmailAlert($cronData);
                                //Log::info("filePath:app\Console\Commands\Ams\AmsAdvertisingEmailSchedule, Module Name: Advertising Reports to e-mail. Message :No data found against this schedule.");
                            }
                            break;
                    }
                    /******** Make arryas and  csv with granularity ends***********/
                } else {
                    echo 'No metrics found for adgroup report';
                    Log::info("filePath:app\Console\Commands\Ams\AmsAdvertisingEmailSchedule, Module Name: Advertising Reports to e-mail. Message :No metrics found for adgroup report.");
                }//end if
                /*** Get selected metrics against adGroup parameter type  ends ***/
                /*** Get selected metrics against adGroup parameter type  starts ***/
                $selectProductAdsReportsSelectedMetrics = scheduledEmailAdvertisingReportsMetrics::where("fkReportScheduleId", $scheduleId)->where("fkParameterType", 3)->get();
                $productAdsReportsSelectedMetricsCount = $selectProductAdsReportsSelectedMetrics->count();
                if ($productAdsReportsSelectedMetricsCount > 0) {
                    /*** Get selected metrics against parameter type  starts ***/
                    $getSelectedSponsordTypes = amsScheduleSelectedEmailReports::distinct()->where('fkReportScheduleId', $scheduleId)->where('fkParameterType', 3)->get(['fkSponsordTypeId']);
                    $getSelectedSponsorTypesArray = array();
                    foreach ($getSelectedSponsordTypes as $getSelectedSponsordType) {
                        $getSelectedSponsorTypesArray[] = $getSelectedSponsordType->fkSponsordTypeId;
                    } //end foreach
                    $commaSeprateSponsordTypeArray = [];
                    $commaSeprateSponsordType = '';
                    if (in_array(1, $getSelectedSponsorTypesArray)) {
                        $commaSeprateSponsordTypeArray[] = 'SD';
                    }
                    if (in_array(2, $getSelectedSponsorTypesArray)) {
                        $commaSeprateSponsordTypeArray[] = 'SP';
                    }
                    if (in_array(3, $getSelectedSponsorTypesArray)) {
                        $commaSeprateSponsordTypeArray[] = 'SB';
                    }
                    if (empty($commaSeprateSponsordTypeArray)) {
                        $commaSeprateSponsordType = 'SP,SD,SB';
                    } else {
                        $commaSeprateSponsordTypeImplode = implode(",", $commaSeprateSponsordTypeArray);
                        //$commaSeprateSponsordType = "'$commaSeprateSponsordTypeImplode'";
                        $commaSeprateSponsordType = $commaSeprateSponsordTypeImplode;
                    }
                    /*** Get selected metrics against parameter type  ends ***/
                    /******** Get column name against metric id starts ***********/
                    /******** Metrics that will send in all emails starts ***********/
                    $productAdsMetricsArray = [];
                    switch ($granularity) {
                        case 'Daily':
                            $productAdsMetricsArray = [
                                'brand_name',
                                'account_name',
                                'date_key',
                            ];
                            break;
                        default:
                            $productAdsMetricsArray = [
                                'brand_name',
                                'account_name',
                                'year_',
                                'start_date',
                                'end_date'
                            ];
                            break;
                    }//end switch
                    /******** Metrics that will send in all emails ends ***********/
                    /******** Make refined metrics array starts ***********/
                    foreach ($selectProductAdsReportsSelectedMetrics as $selectProductAdsReportsSelectedMetric) {

                        $currentProductAdsMetricId = $selectProductAdsReportsSelectedMetric->fkReportMetricId;

                        /*************** Get column name against metric id starts ***********/
                        $GetProductAdsReportMetricsNames = amsReportsMetrics::where('id', $currentProductAdsMetricId)->first();
                        $GetProductAdsReportMetricsNamesCount = $GetProductAdsReportMetricsNames->count();
                        if ($GetProductAdsReportMetricsNamesCount > 0) {
                            $tblProductAdsColumnName = $GetProductAdsReportMetricsNames->tblColumnName;
                            $productAdsMetricsArray[] = strtolower(trim($tblProductAdsColumnName));
                        }//end if
                    }//end foreach
                    /******** Make refined metrics array ends ***********/
                    /******** Make arryas and  csv with granularity starts***********/
                    switch ($granularity) {
                        case 'Daily':
                            echo 'Daily';
                            $DB2 = 'mysqlDb2';
                            $parameters = array($amsProfileId, $commaSeprateSponsordType, $timeFrame);
                            $storedProceduresData = \DB::connection($DB2)->select('call spCalculateDailyProductAdsSchedulingReport (?,?,?)', $parameters);
                            \DB::disconnect($DB2);
                            if (!empty($storedProceduresData)) {
                                Log::info("filePath:app\Console\Commands\Ams\AmsAdvertisingEmailSchedule, Module Name: Advertising Reports to e-mail. Message :Data found against this schedule.");
                                $filterDataArray = [];
                                $filterData = [];
                                //dd($storedProceduresData);
                                foreach ($storedProceduresData as $orgnalKey => $orgnalvalue) {
                                    $filterKey = strtolower($orgnalKey);
                                    $filterDataArray['brand_id'] = $orgnalvalue->brand_id;
                                    $filterDataArray['brand_name'] = $orgnalvalue->brand_name;
                                    //$filterDataArray['accouint_id'] = $orgnalvalue->accouint_id;
                                    $filterDataArray['account_name'] = $orgnalvalue->account_name;
                                    $filterDataArray['profile_id'] = $orgnalvalue->profile_id;
                                    $filterDataArray['profile_name'] = $orgnalvalue->profile_name;
                                    $filterDataArray['date_key'] = $orgnalvalue->date_key;
                                    $filterDataArray['campaign_id'] = $orgnalvalue->campaign_id;
                                    $filterDataArray['campaign_name'] = $orgnalvalue->campaign_name;
                                    $filterDataArray['campaign_type'] = $orgnalvalue->campaign_type;
                                    $filterDataArray['adGroupId'] = $orgnalvalue->adGroupId;
                                    $filterDataArray['adGroupName'] = $orgnalvalue->adGroupName;
                                    $filterDataArray['ad_id'] = $orgnalvalue->ad_id;
                                    $filterDataArray['asin'] = $orgnalvalue->asin_;
                                    $filterDataArray['sku'] = $orgnalvalue->sku;
                                    $filterDataArray['impressions'] = $orgnalvalue->impressions;
                                    $filterDataArray['clicks'] = $orgnalvalue->clicks;
                                    $filterDataArray['cost'] = $orgnalvalue->cost;
                                    $filterDataArray['revenue'] = $orgnalvalue->revenue;
                                    $filterDataArray['order_conversion'] = $orgnalvalue->order_conversion;
                                    $filterDataArray['acos'] = $orgnalvalue->acos;
                                    $filterDataArray['cpc'] = $orgnalvalue->cpc;
                                    $filterDataArray['ctr'] = $orgnalvalue->ctr;
                                    $filterDataArray['cpa'] = $orgnalvalue->cpa;
                                    $filterDataArray['roas'] = $orgnalvalue->roas;
                                    $filterData[] = array_change_key_case($filterDataArray, CASE_LOWER);
                                }
                                //echo 'test';
                                //dd($filterData);
                                $storeNew = [];
                                $matchesColumns = [];
                                foreach ($filterData as $key => $value) {
                                    foreach ($productAdsMetricsArray as $key1) {
                                        if (array_key_exists($key1, $value)) {
                                            $matchesColumns[$key1] = $value[$key1];
                                        }
                                        //$matchesColumns[$key1];
                                    }
                                    array_push($storeNew, $matchesColumns);
                                }
                                //dd($storeNew);
                                $productAdsFinalMetrics = array();
                                $productAdsFinalMetricsArray = array();
                                foreach ($storeNew as $productAdsFinalMetricKey => $productAdsFinalMetricValue) {
                                    //dd($campaignFinalMetricValue);
                                    if (isset($productAdsFinalMetricValue['date_key'])) {
                                        $productAdsFinalMetrics['Date'] = $productAdsFinalMetricValue['date_key'];
                                    }
                                    //if(isset($productAdsFinalMetricValue['brand_name'])){ $productAdsFinalMetrics['Brand Name'] = $productAdsFinalMetricValue['brand_name']; }
                                    //if(isset($productAdsFinalMetricValue['account_name'])){ $productAdsFinalMetrics['Account Name'] = $productAdsFinalMetricValue['account_name']; }
                                    //if(isset($productAdsFinalMetricValue['campaign_id'])){ $productAdsFinalMetrics['Campaign Id'] = $productAdsFinalMetricValue['campaign_id']; }
                                    if (isset($productAdsFinalMetricValue['campaign_name'])) {
                                        $productAdsFinalMetrics['Campaign Name'] = $productAdsFinalMetricValue['campaign_name'];
                                    }
                                    if (isset($productAdsFinalMetricValue['campaign_type'])) {
                                        $productAdsFinalMetrics['Campaign Type'] = $productAdsFinalMetricValue['campaign_type'];
                                    }
                                    if (isset($productAdsFinalMetricValue['adgroupid'])) {
                                        $productAdsFinalMetrics['Ad Group Id'] = $productAdsFinalMetricValue['adgroupid'];
                                    }
                                    if (isset($productAdsFinalMetricValue['adgroupname'])) {
                                        $productAdsFinalMetrics['Ad Group Name'] = $productAdsFinalMetricValue['adgroupname'];
                                    }
                                    if (isset($productAdsFinalMetricValue['ad_id'])) {
                                        $productAdsFinalMetrics['Ad Id'] = $productAdsFinalMetricValue['ad_id'];
                                    }
                                    if (isset($productAdsFinalMetricValue['asin'])) {
                                        $productAdsFinalMetrics['Asin'] = $productAdsFinalMetricValue['asin'];
                                    }
                                    if (isset($productAdsFinalMetricValue['sku'])) {
                                        $productAdsFinalMetrics['Sku'] = $productAdsFinalMetricValue['sku'];
                                    }
                                    if (isset($productAdsFinalMetricValue['impressions'])) {
                                        $productAdsFinalMetrics['Impressions'] = $productAdsFinalMetricValue['impressions'];
                                    }
                                    if (isset($productAdsFinalMetricValue['clicks'])) {
                                        $productAdsFinalMetrics['Clicks'] = $productAdsFinalMetricValue['clicks'];
                                    }
                                    if (isset($productAdsFinalMetricValue['cost'])) {
                                        $productAdsFinalMetrics['Cost'] = $this->roundValue($productAdsFinalMetricValue['cost']);
                                    }
                                    if (isset($productAdsFinalMetricValue['revenue'])) {
                                        $productAdsFinalMetrics['Revenue'] = $this->roundValue($productAdsFinalMetricValue['revenue']);
                                    }
                                    if (isset($productAdsFinalMetricValue['order_conversion'])) {
                                        $productAdsFinalMetrics['Order Conversion'] = $productAdsFinalMetricValue['order_conversion'];
                                    }
                                    if (isset($productAdsFinalMetricValue['ctr'])) {
                                        $productAdsFinalMetrics['Click-Thru Rate (CTR)'] = $this->roundValue($productAdsFinalMetricValue['ctr']);
                                    }
                                    if (isset($productAdsFinalMetricValue['cpc'])) {
                                        $productAdsFinalMetrics['Cost Per Click (CPC)'] = $this->roundValue($productAdsFinalMetricValue['cpc']);
                                    }
                                    if (isset($productAdsFinalMetricValue['acos'])) {
                                        $productAdsFinalMetrics['Total Advertising Cost of Sales (ACoS)'] = $this->roundValue($productAdsFinalMetricValue['acos']);
                                    }
                                    if (isset($productAdsFinalMetricValue['roas'])) {
                                        $productAdsFinalMetrics['Total Return on Advertising Spend (RoAS)'] = $this->roundValue($productAdsFinalMetricValue['roas']);
                                    }
                                    if (isset($productAdsFinalMetricValue['cpa'])) {
                                        $productAdsFinalMetrics['CPA'] = $this->roundValue($productAdsFinalMetricValue['cpa']);
                                    }
                                    array_push($productAdsFinalMetricsArray, $productAdsFinalMetrics);
                                    //isset()
                                    //dd($campaignFinalMetrics);
                                }
                                //dd($adGroupFinalMetricsArray);
                                // dd($storeNew);

                                $fileName = 'Product-Ads-Daily-' . date('YmdHis') . '-' . $scheduleId . '.xlsx';
                                $fileTempPath = public_path('files/advertisingEmail/Product-Ads-Daily-' . date('YmdHis') . '-' . $scheduleId . '.xlsx');
                                $this->_generateSoldByCSV($productAdsFinalMetricsArray, $fileTempPath);
                                /*$fileNames[] =array('path'=>$fileTempPath,
                                    'name'=>$fileName
                                ) ;*/
                                //$finalDataArray['Product-Ads'] = $productAdsFinalMetricsArray;
                                //$cronData['productAdsReportStatus'] = '<p>Product Ads Report : Successfully generated.Check the attachment.</p>';
                                $cronData['productAdsReportStatus'] = '<p>Product Ads Report : <a href="' . $apiServerLink . $fileName . '" download=""> Click Here To Download </a></p>';
                                $completeFilePath = $apiServerLink . $fileName;
                                $fileEntryArray = [];
                                $fileEntryArray = array('fkScheduleId' => $scheduleId, 'fkParameterTypeId' => 3, 'parameterTypeName' => 'Product Ads', 'time' => $currentTime, 'date' => $date,
                                    'fileName' => $fileName,
                                    'filePath' => $fileTempPath,
                                    'completeFilePath' => $completeFilePath,
                                    'devServerLink' => $devServerLink,
                                    'apiServerLink' => $apiServerLink,
                                    'isDataFound' => '1',
                                    'isFileDeleted' => '0');
                                $this->_fileDbLogs($fileEntryArray);

                            } else {
                                $fileEntryArray = [];
                                $fileEntryArray = array('fkScheduleId' => $scheduleId, 'fkParameterTypeId' => 3, 'parameterTypeName' => 'Product Ads', 'time' => $currentTime, 'date' => $date,
                                    'devServerLink' => $devServerLink,
                                    'apiServerLink' => $apiServerLink,
                                    'isDataFound' => '0',
                                    'isFileDeleted' => '0');
                                $this->_fileDbLogs($fileEntryArray);
                                $cronData['productAdsReportStatus'] = '<p>Product Ads Report : Data not found.</p>';
                                Log::info("filePath:app\Console\Commands\Ams\AmsAdvertisingEmailSchedule, Module Name: Advertising Reports to e-mail. Message :No data found against this schedule.");
                            }
                            break;
                        //weekely start
                        case 'Weekly':
                            echo 'Weekly';
                            $DB2 = 'mysqlDb2';
                            $parameters = array($amsProfileId, $commaSeprateSponsordType, $timeFrame);
                            $storedProceduresData = \DB::connection($DB2)->select('call `spCalculateWeeklyProductAdsSchedulingReport` (?,?,?)', $parameters);
                            \DB::disconnect($DB2);
                            if (!empty($storedProceduresData)) {
                                $filterDataArray = [];
                                $filterData = [];
                                //dd($storedProceduresData);
                                foreach ($storedProceduresData as $orgnalKey => $orgnalvalue) {
                                    $filterKey = strtolower($orgnalKey);
                                    $filterDataArray['brand_id'] = $orgnalvalue->brand_id;
                                    $filterDataArray['brand_name'] = $orgnalvalue->brand_name;
                                    //$filterDataArray['accouint_id'] = $orgnalvalue->accouint_id;
                                    $filterDataArray['account_name'] = $orgnalvalue->account_name;
                                    $filterDataArray['profile_id'] = $orgnalvalue->profile_id;
                                    $filterDataArray['profile_name'] = $orgnalvalue->profile_name;
                                    $filterDataArray['year_'] = $orgnalvalue->year_;
                                    $filterDataArray['start_date'] = $orgnalvalue->start_date;
                                    $filterDataArray['end_date'] = $orgnalvalue->end_Date;
                                    $filterDataArray['campaign_id'] = $orgnalvalue->campaign_id;
                                    $filterDataArray['campaign_name'] = $orgnalvalue->campaign_name;
                                    $filterDataArray['campaign_type'] = $orgnalvalue->campaign_type;
                                    $filterDataArray['adGroupId'] = $orgnalvalue->adGroupId;
                                    $filterDataArray['adGroupName'] = $orgnalvalue->adGroupName;
                                    $filterDataArray['ad_id'] = $orgnalvalue->ad_id;
                                    $filterDataArray['asin'] = $orgnalvalue->asin_;
                                    $filterDataArray['sku'] = $orgnalvalue->sku;
                                    $filterDataArray['impressions'] = $orgnalvalue->impressions;
                                    $filterDataArray['clicks'] = $orgnalvalue->clicks;
                                    $filterDataArray['cost'] = $orgnalvalue->cost;
                                    $filterDataArray['revenue'] = $orgnalvalue->revenue;
                                    $filterDataArray['order_conversion'] = $orgnalvalue->order_conversion;
                                    $filterDataArray['acos'] = $orgnalvalue->acos_;
                                    $filterDataArray['CPC'] = $orgnalvalue->CPC;
                                    $filterDataArray['CTR'] = $orgnalvalue->CTR;
                                    $filterDataArray['CPA'] = $orgnalvalue->CPA;
                                    $filterDataArray['ROAS'] = $orgnalvalue->ROAS;
                                    $filterData[] = array_change_key_case($filterDataArray, CASE_LOWER);
                                }

                                $storeNew = [];
                                $matchesColumns = [];
                                foreach ($filterData as $key => $value) {
                                    foreach ($productAdsMetricsArray as $key1) {
                                        if (array_key_exists($key1, $value)) {
                                            $matchesColumns[$key1] = $value[$key1];
                                        }
                                        //$matchesColumns[$key1];
                                    }
                                    array_push($storeNew, $matchesColumns);
                                }
                                $productAdsFinalMetrics = array();
                                $productAdsFinalMetricsArray = array();
                                foreach ($storeNew as $productAdsFinalMetricKey => $productAdsFinalMetricValue) {
                                    if (isset($productAdsFinalMetricValue['year_'])) {
                                        $productAdsFinalMetrics['Year_'] = $productAdsFinalMetricValue['year_'];
                                    }
                                    if (isset($productAdsFinalMetricValue['start_date'])) {
                                        $productAdsFinalMetrics['Start Date'] = $productAdsFinalMetricValue['start_date'];
                                    }
                                    if (isset($productAdsFinalMetricValue['end_date'])) {
                                        $productAdsFinalMetrics['End Date'] = $productAdsFinalMetricValue['end_date'];
                                    }
                                    //if(isset($productAdsFinalMetricValue['brand_name'])){ $productAdsFinalMetrics['Brand Name'] = $productAdsFinalMetricValue['brand_name']; }
                                    //if(isset($productAdsFinalMetricValue['account_name'])){ $productAdsFinalMetrics['Account Name'] = $productAdsFinalMetricValue['account_name']; }
                                    //if(isset($productAdsFinalMetricValue['campaign_id'])){ $productAdsFinalMetrics['Campaign Id'] = $productAdsFinalMetricValue['campaign_id']; }
                                    if (isset($productAdsFinalMetricValue['campaign_name'])) {
                                        $productAdsFinalMetrics['Campaign Name'] = $productAdsFinalMetricValue['campaign_name'];
                                    }
                                    if (isset($productAdsFinalMetricValue['campaign_type'])) {
                                        $productAdsFinalMetrics['Campaign Type'] = $productAdsFinalMetricValue['campaign_type'];
                                    }
                                    if (isset($productAdsFinalMetricValue['adgroupid'])) {
                                        $productAdsFinalMetrics['Ad Group Id'] = $productAdsFinalMetricValue['adgroupid'];
                                    }
                                    if (isset($productAdsFinalMetricValue['adgroupname'])) {
                                        $productAdsFinalMetrics['Ad Group Name'] = $productAdsFinalMetricValue['adgroupname'];
                                    }
                                    if (isset($productAdsFinalMetricValue['ad_id'])) {
                                        $productAdsFinalMetrics['Ad Id'] = $productAdsFinalMetricValue['ad_id'];
                                    }
                                    if (isset($productAdsFinalMetricValue['asin'])) {
                                        $productAdsFinalMetrics['Asin'] = $productAdsFinalMetricValue['asin'];
                                    }
                                    if (isset($productAdsFinalMetricValue['sku'])) {
                                        $productAdsFinalMetrics['sku'] = $productAdsFinalMetricValue['sku'];
                                    }
                                    if (isset($productAdsFinalMetricValue['impressions'])) {
                                        $productAdsFinalMetrics['Impressions'] = $productAdsFinalMetricValue['impressions'];
                                    }
                                    if (isset($productAdsFinalMetricValue['clicks'])) {
                                        $productAdsFinalMetrics['Clicks'] = $productAdsFinalMetricValue['clicks'];
                                    }
                                    if (isset($productAdsFinalMetricValue['cost'])) {
                                        $productAdsFinalMetrics['Cost'] = $this->roundValue($productAdsFinalMetricValue['cost']);
                                    }
                                    if (isset($productAdsFinalMetricValue['revenue'])) {
                                        $productAdsFinalMetrics['Revenue'] = $this->roundValue($productAdsFinalMetricValue['revenue']);
                                    }
                                    if (isset($productAdsFinalMetricValue['order_conversion'])) {
                                        $productAdsFinalMetrics['Order Conversion'] = $productAdsFinalMetricValue['order_conversion'];
                                    }
                                    if (isset($productAdsFinalMetricValue['ctr'])) {
                                        $productAdsFinalMetrics['Click-Thru Rate (CTR)'] = $this->roundValue($productAdsFinalMetricValue['ctr']);
                                    }
                                    if (isset($productAdsFinalMetricValue['cpc'])) {
                                        $productAdsFinalMetrics['Cost Per Click (CPC)'] = $this->roundValue($productAdsFinalMetricValue['cpc']);
                                    }
                                    if (isset($productAdsFinalMetricValue['acos'])) {
                                        $productAdsFinalMetrics['Total Advertising Cost of Sales (ACoS)'] = $this->roundValue($productAdsFinalMetricValue['acos']);
                                    }
                                    if (isset($productAdsFinalMetricValue['roas'])) {
                                        $productAdsFinalMetrics['Total Return on Advertising Spend (RoAS)'] = $this->roundValue($productAdsFinalMetricValue['roas']);
                                    }
                                    if (isset($productAdsFinalMetricValue['cpa'])) {
                                        $productAdsFinalMetrics['CPA'] = $this->roundValue($productAdsFinalMetricValue['cpa']);
                                    }
                                    array_push($productAdsFinalMetricsArray, $productAdsFinalMetrics);
                                }
                                $fileName = 'Product-Ads-Weekly-' . date('YmdHis') . '-' . $scheduleId . '.xlsx';
                                $fileTempPath = public_path('files/advertisingEmail/Product-Ads-Weekly-' . date('YmdHis') . '-' . $scheduleId . '.xlsx');
                                $this->_generateSoldByCSV($productAdsFinalMetricsArray, $fileTempPath);
                                /*$fileNames[] =array('path'=>$fileTempPath,
                                    'name'=>$fileName
                                ) ;*/
                                //$finalDataArray['Product-Ads'] = $productAdsFinalMetricsArray;
                                //$cronData['productAdsReportStatus'] = '<p>Product Ads Report : Successfully generated.Check the attachment.</p>';
                                $cronData['productAdsReportStatus'] = '<p>Product Ads Report : <a href="' . $apiServerLink . $fileName . '" download=""> Click Here To Download </a></p>';
                                $completeFilePath = $apiServerLink . $fileName;
                                $fileEntryArray = [];
                                $fileEntryArray = array('fkScheduleId' => $scheduleId, 'fkParameterTypeId' => 3, 'parameterTypeName' => 'Product Ads', 'time' => $currentTime, 'date' => $date,
                                    'fileName' => $fileName,
                                    'filePath' => $fileTempPath,
                                    'completeFilePath' => $completeFilePath,
                                    'devServerLink' => $devServerLink,
                                    'apiServerLink' => $apiServerLink,
                                    'isDataFound' => '1',
                                    'isFileDeleted' => '0');
                                $this->_fileDbLogs($fileEntryArray);

                            } else {
                                $fileEntryArray = [];
                                $fileEntryArray = array('fkScheduleId' => $scheduleId, 'fkParameterTypeId' => 3, 'parameterTypeName' => 'Product Ads', 'time' => $currentTime, 'date' => $date,
                                    'devServerLink' => $devServerLink,
                                    'apiServerLink' => $apiServerLink,
                                    'isDataFound' => '0',
                                    'isFileDeleted' => '0');
                                $this->_fileDbLogs($fileEntryArray);
                                $cronData['productAdsReportStatus'] = '<p>Product Ads Report : Data not found.</p>';
                                Log::info("filePath:app\Console\Commands\Ams\AmsAdvertisingEmailSchedule, Module Name: Advertising Reports to e-mail. Message :No data found against this schedule.");
                            }
                            break;
                        //monthly start
                        case 'Monthly':
                            echo 'Monthly';
                            $DB2 = 'mysqlDb2';
                            $parameters = array($amsProfileId, $commaSeprateSponsordType, $timeFrame);
                            $storedProceduresData = \DB::connection($DB2)->select('call `spCalculateMonthlyProductAdsSchedulingReport` (?,?,?)', $parameters);
                            \DB::disconnect($DB2);
                            if (!empty($storedProceduresData)) {
                                $filterDataArray = [];
                                $filterData = [];
                                //dd($storedProceduresData);
                                foreach ($storedProceduresData as $orgnalKey => $orgnalvalue) {

                                    $filterKey = strtolower($orgnalKey);
                                    $filterDataArray['brand_id'] = $orgnalvalue->brand_id;
                                    $filterDataArray['brand_name'] = $orgnalvalue->brand_name;
                                    //$filterDataArray['accouint_id'] = $orgnalvalue->accouint_id;
                                    $filterDataArray['account_name'] = $orgnalvalue->account_name;
                                    $filterDataArray['profile_id'] = $orgnalvalue->profile_id;
                                    $filterDataArray['profile_name'] = $orgnalvalue->profile_name;
                                    $filterDataArray['year_'] = $orgnalvalue->year_;
                                    $filterDataArray['start_date'] = $orgnalvalue->start_date;
                                    $filterDataArray['end_date'] = $orgnalvalue->end_Date;
                                    $filterDataArray['campaign_id'] = $orgnalvalue->campaign_id;
                                    $filterDataArray['campaign_name'] = $orgnalvalue->campaign_name;
                                    $filterDataArray['campaign_type'] = $orgnalvalue->campaign_type;
                                    $filterDataArray['adGroupId'] = $orgnalvalue->adGroupId;
                                    $filterDataArray['adGroupName'] = $orgnalvalue->adGroupName;
                                    $filterDataArray['ad_id'] = $orgnalvalue->ad_id;
                                    $filterDataArray['asin'] = $orgnalvalue->asin_;
                                    $filterDataArray['sku'] = $orgnalvalue->sku;
                                    $filterDataArray['impressions'] = $orgnalvalue->impressions;
                                    $filterDataArray['clicks'] = $orgnalvalue->clicks;
                                    $filterDataArray['cost'] = $orgnalvalue->cost;
                                    $filterDataArray['revenue'] = $orgnalvalue->revenue;
                                    $filterDataArray['order_conversion'] = $orgnalvalue->order_conversion;
                                    $filterDataArray['acos'] = $orgnalvalue->acos_;
                                    $filterDataArray['CPC'] = $orgnalvalue->CPC;
                                    $filterDataArray['CTR'] = $orgnalvalue->CTR;
                                    $filterDataArray['CPA'] = $orgnalvalue->CPA;
                                    $filterDataArray['ROAS'] = $orgnalvalue->ROAS;
                                    $filterData[] = array_change_key_case($filterDataArray, CASE_LOWER);
                                }
                                $storeNew = [];
                                $matchesColumns = [];
                                foreach ($filterData as $key => $value) {

                                    foreach ($productAdsMetricsArray as $key1) {

                                        if (array_key_exists($key1, $value)) {
                                            $matchesColumns[$key1] = $value[$key1];

                                        }
                                    }
                                    array_push($storeNew, $matchesColumns);
                                }
                                $productAdsFinalMetrics = array();
                                $productAdsFinalMetricsArray = array();
                                foreach ($storeNew as $productAdsFinalMetricKey => $productAdsFinalMetricValue) {
                                    if (isset($productAdsFinalMetricValue['year_'])) {
                                        $productAdsFinalMetrics['Year_'] = $productAdsFinalMetricValue['year_'];
                                    }
                                    if (isset($productAdsFinalMetricValue['start_date'])) {
                                        $productAdsFinalMetrics['Start Date'] = $productAdsFinalMetricValue['start_date'];
                                    }
                                    if (isset($productAdsFinalMetricValue['end_date'])) {
                                        $productAdsFinalMetrics['End Date'] = $productAdsFinalMetricValue['end_date'];
                                    }
                                    //if(isset($productAdsFinalMetricValue['brand_name'])){ $productAdsFinalMetrics['Brand Name'] = $productAdsFinalMetricValue['brand_name']; }
                                    //if(isset($productAdsFinalMetricValue['account_name'])){ $productAdsFinalMetrics['Account Name'] = $productAdsFinalMetricValue['account_name']; }
                                    //if(isset($productAdsFinalMetricValue['campaign_id'])){ $productAdsFinalMetrics['Campaign Id'] = $productAdsFinalMetricValue['campaign_id']; }
                                    if (isset($productAdsFinalMetricValue['campaign_name'])) {
                                        $productAdsFinalMetrics['Campaign Name'] = $productAdsFinalMetricValue['campaign_name'];
                                    }
                                    if (isset($productAdsFinalMetricValue['campaign_type'])) {
                                        $productAdsFinalMetrics['Campaign Type'] = $productAdsFinalMetricValue['campaign_type'];
                                    }
                                    if (isset($productAdsFinalMetricValue['adgroupid'])) {
                                        $productAdsFinalMetrics['Ad Group Id'] = $productAdsFinalMetricValue['adgroupid'];
                                    }
                                    if (isset($productAdsFinalMetricValue['adgroupname'])) {
                                        $productAdsFinalMetrics['Ad Group Name'] = $productAdsFinalMetricValue['adgroupname'];
                                    }
                                    if (isset($productAdsFinalMetricValue['ad_id'])) {
                                        $productAdsFinalMetrics['Ad Id'] = $productAdsFinalMetricValue['ad_id'];
                                    }
                                    if (isset($productAdsFinalMetricValue['asin'])) {
                                        $productAdsFinalMetrics['asin'] = $productAdsFinalMetricValue['asin'];
                                    }
                                    if (isset($productAdsFinalMetricValue['sku'])) {
                                        $productAdsFinalMetrics['sku'] = $productAdsFinalMetricValue['sku'];
                                    }
                                    if (isset($productAdsFinalMetricValue['impressions'])) {
                                        $productAdsFinalMetrics['Impressions'] = $productAdsFinalMetricValue['impressions'];
                                    }
                                    if (isset($productAdsFinalMetricValue['clicks'])) {
                                        $productAdsFinalMetrics['Clicks'] = $productAdsFinalMetricValue['clicks'];
                                    }
                                    if (isset($productAdsFinalMetricValue['cost'])) {
                                        $productAdsFinalMetrics['Cost'] = $this->roundValue($productAdsFinalMetricValue['cost']);
                                    }
                                    if (isset($productAdsFinalMetricValue['revenue'])) {
                                        $productAdsFinalMetrics['Revenue'] = $this->roundValue($productAdsFinalMetricValue['revenue']);
                                    }
                                    if (isset($productAdsFinalMetricValue['order_conversion'])) {
                                        $productAdsFinalMetrics['Order Conversion'] = $productAdsFinalMetricValue['order_conversion'];
                                    }
                                    if (isset($productAdsFinalMetricValue['ctr'])) {
                                        $productAdsFinalMetrics['Click-Thru Rate (CTR)'] = $this->roundValue($productAdsFinalMetricValue['ctr']);
                                    }
                                    if (isset($productAdsFinalMetricValue['cpc'])) {
                                        $productAdsFinalMetrics['Cost Per Click (CPC)'] = $this->roundValue($productAdsFinalMetricValue['cpc']);
                                    }
                                    if (isset($productAdsFinalMetricValue['acos'])) {
                                        $productAdsFinalMetrics['Total Advertising Cost of Sales (ACoS)'] = $this->roundValue($productAdsFinalMetricValue['acos']);
                                    }
                                    if (isset($productAdsFinalMetricValue['roas'])) {
                                        $productAdsFinalMetrics['Total Return on Advertising Spend (RoAS)'] = $this->roundValue($productAdsFinalMetricValue['roas']);
                                    }
                                    if (isset($productAdsFinalMetricValue['cpa'])) {
                                        $productAdsFinalMetrics['CPA'] = $this->roundValue($productAdsFinalMetricValue['cpa']);
                                    }
                                    array_push($productAdsFinalMetricsArray, $productAdsFinalMetrics);
                                }
                                $fileName = 'Product-Ads-Monthly-' . date('YmdHis') . '-' . $scheduleId . '.xlsx';
                                $fileTempPath = public_path('files/advertisingEmail/Product-Ads-Monthly-' . date('YmdHis') . '-' . $scheduleId . '.xlsx');
                                $this->_generateSoldByCSV($productAdsFinalMetricsArray, $fileTempPath);
                                /*$fileNames[] =array('path'=>$fileTempPath,
                                    'name'=>$fileName
                                ) ;*/
                                //$finalDataArray['Product-Ads'] = $productAdsFinalMetricsArray;
                                //$cronData['productAdsReportStatus'] = '<p>Product Ads Report : Successfully generated.Check the attachment.</p>';
                                $cronData['productAdsReportStatus'] = '<p>Product Ads Report : <a href="' . $apiServerLink . $fileName . '" download=""> Click Here To Download </a></p>';
                                $completeFilePath = $apiServerLink . $fileName;
                                $fileEntryArray = [];
                                $fileEntryArray = array('fkScheduleId' => $scheduleId, 'fkParameterTypeId' => 3, 'parameterTypeName' => 'Product Ads', 'time' => $currentTime, 'date' => $date,
                                    'fileName' => $fileName,
                                    'filePath' => $fileTempPath,
                                    'completeFilePath' => $completeFilePath,
                                    'devServerLink' => $devServerLink,
                                    'apiServerLink' => $apiServerLink,
                                    'isDataFound' => '1',
                                    'isFileDeleted' => '0');
                                $this->_fileDbLogs($fileEntryArray);

                            } else {
                                $fileEntryArray = [];
                                $fileEntryArray = array('fkScheduleId' => $scheduleId, 'fkParameterTypeId' => 3, 'parameterTypeName' => 'Product Ads', 'time' => $currentTime, 'date' => $date,
                                    'devServerLink' => $devServerLink,
                                    'apiServerLink' => $apiServerLink,
                                    'isDataFound' => '0',
                                    'isFileDeleted' => '0');
                                $this->_fileDbLogs($fileEntryArray);
                                $cronData['productAdsReportStatus'] = '<p>Product Ads Report : Data not found.</p>';
                                //$noDATAEamil = $this->_noDataEmailAlert($cronData);
                                //Log::info("filePath:app\Console\Commands\Ams\AmsAdvertisingEmailSchedule, Module Name: Advertising Reports to e-mail. Message :No data found against this schedule.");
                            }
                            break;
                    }
                    /******** Make arryas and  csv with granularity ends***********/
                } else {
                    echo 'No metrics found for product ads report';
                    Log::info("filePath:app\Console\Commands\Ams\AmsAdvertisingEmailSchedule, Module Name: Advertising Reports to e-mail. Message :No metrics found for product ads report.");
                }//end if
                /*** Get selected metrics against adGroup parameter type  ends ***/
                /*** Get selected metrics against keyword parameter type  starts ***/
                $selectKeywordReportsSelectedMetrics = scheduledEmailAdvertisingReportsMetrics::where("fkReportScheduleId", $scheduleId)->where("fkParameterType", 4)->get();
                $keywordReportsSelectedMetricsCount = $selectKeywordReportsSelectedMetrics->count();
                if ($keywordReportsSelectedMetricsCount > 0) {
                    /*** Get selected metrics against parameter type  starts ***/
                    $getSelectedSponsordTypes = amsScheduleSelectedEmailReports::distinct()->where('fkReportScheduleId', $scheduleId)->where('fkParameterType', 4)->get(['fkSponsordTypeId']);
                    $getSelectedSponsorTypesArray = array();
                    foreach ($getSelectedSponsordTypes as $getSelectedSponsordType) {
                        $getSelectedSponsorTypesArray[] = $getSelectedSponsordType->fkSponsordTypeId;
                    } //end foreach
                    $commaSeprateSponsordTypeArray = [];
                    $commaSeprateSponsordType = '';
                    if (in_array(1, $getSelectedSponsorTypesArray)) {
                        $commaSeprateSponsordTypeArray[] = 'SD';
                    }
                    if (in_array(2, $getSelectedSponsorTypesArray)) {
                        $commaSeprateSponsordTypeArray[] = 'SP';
                    }
                    if (in_array(3, $getSelectedSponsorTypesArray)) {
                        $commaSeprateSponsordTypeArray[] = 'SB';
                    }
                    if (empty($commaSeprateSponsordTypeArray)) {
                        $commaSeprateSponsordType = 'SP,SD,SB';
                    } else {
                        $commaSeprateSponsordTypeImplode = implode(",", $commaSeprateSponsordTypeArray);
                        //$commaSeprateSponsordType = "'$commaSeprateSponsordTypeImplode'";
                        $commaSeprateSponsordType = $commaSeprateSponsordTypeImplode;
                    }
                    /*** Get selected metrics against parameter type  ends ***/
                    /******** Get column name against metric id starts ***********/
                    /******** Metrics that will send in all emails starts ***********/
                    $keywordMetricsArray = [];
                    switch ($granularity) {
                        case 'Daily':
                            $keywordMetricsArray = [
                                'brand_name',
                                'account_name',
                                'date_key',
                            ];
                            break;
                        default:
                            $keywordMetricsArray = [
                                'brand_name',
                                'account_name',
                                'year_',
                                'start_date',
                                'end_date'
                            ];
                            break;
                    }//end switch
                    /******** Metrics that will send in all emails ends ***********/
                    /******** Make refined metrics array starts ***********/
                    foreach ($selectKeywordReportsSelectedMetrics as $selectKeywordReportsSelectedMetric) {

                        $currentKeywordMetricId = $selectKeywordReportsSelectedMetric->fkReportMetricId;

                        /*************** Get column name against metric id starts ***********/
                        $getKeywordReportMetricsNames = amsReportsMetrics::where('id', $currentKeywordMetricId)->first();
                        $getKeywordReportMetricsNamesCount = $getKeywordReportMetricsNames->count();
                        if ($getKeywordReportMetricsNamesCount > 0) {
                            $tblKeywordColumnName = $getKeywordReportMetricsNames->tblColumnName;
                            $keywordMetricsArray[] = strtolower(trim($tblKeywordColumnName));
                        }//end if
                    }//end foreach
                    $keywordMetricsArray = array_change_key_case($keywordMetricsArray, CASE_LOWER);
                    /******** Make refined metrics array ends ***********/
                    /******** Make arryas and  csv with granularity starts***********/
                    $DB2 = 'mysqlDb2';
                    $keywordParameters = array($amsProfileId, $commaSeprateSponsordType, $timeFrame);
                    switch ($granularity) {
                        case 'Daily':
                            echo 'Daily';
                            $keywordStoredProceduresData = \DB::connection($DB2)->select('call spCalculateDailyKeywordSchedulingReport(?,?,?)', $keywordParameters);
                            \DB::disconnect($DB2);
                            if (!empty($keywordStoredProceduresData)) {
                                $filterDataArray = [];
                                $filterData = [];
                                foreach ($keywordStoredProceduresData as $orgnalKey => $orgnalvalue) {
                                    $filterKey = strtolower($orgnalKey);
                                    $filterDataArray['brand_id'] = $orgnalvalue->brand_id;
                                    $filterDataArray['brand_name'] = $orgnalvalue->brand_name;
                                    //$filterDataArray['accouint_id'] = $orgnalvalue->accouint_id;
                                    $filterDataArray['account_name'] = $orgnalvalue->account_name;
                                    $filterDataArray['profile_id'] = $orgnalvalue->profile_id;
                                    $filterDataArray['profile_name'] = $orgnalvalue->profile_name;
                                    $filterDataArray['date_key'] = $orgnalvalue->date_key;
                                    $filterDataArray['campaign_id'] = $orgnalvalue->campaign_id;
                                    $filterDataArray['campaign_name'] = $orgnalvalue->campaign_name;
                                    $filterDataArray['campaign_type'] = $orgnalvalue->campaign_type;
                                    $filterDataArray['adGroupId'] = $orgnalvalue->adGroupId;
                                    $filterDataArray['adGroupName'] = $orgnalvalue->adGroupName;
                                    $filterDataArray['keywordId'] = $orgnalvalue->keywordId;
                                    $filterDataArray['keywordText'] = $orgnalvalue->keywordText;
                                    $filterDataArray['impressions'] = $orgnalvalue->impressions;
                                    $filterDataArray['clicks'] = $orgnalvalue->clicks;
                                    $filterDataArray['cost'] = $orgnalvalue->cost;
                                    $filterDataArray['revenue'] = $orgnalvalue->revenue;
                                    $filterDataArray['order_conversion'] = $orgnalvalue->order_conversion;
                                    $filterDataArray['acos'] = $orgnalvalue->acos;
                                    $filterDataArray['cpc'] = $orgnalvalue->cpc;
                                    $filterDataArray['ctr'] = $orgnalvalue->ctr;
                                    $filterDataArray['cpa'] = $orgnalvalue->cpa;
                                    $filterDataArray['roas'] = $orgnalvalue->roas;
                                    $filterData[] = array_change_key_case($filterDataArray, CASE_LOWER);
                                }
                                $storeNew = [];
                                $matchesColumns = [];
                                foreach ($filterData as $key => $value) {
                                    foreach ($keywordMetricsArray as $key1) {
                                        if (array_key_exists($key1, $value)) {
                                            $matchesColumns[$key1] = $value[$key1];
                                        }
                                    }
                                    array_push($storeNew, $matchesColumns);
                                }
                                $keywordFinalMetrics = array();
                                $keywordFinalMetricsArray = array();
                                foreach ($storeNew as $keywordFinalMetricsKey => $keywordFinalMetricsValue) {
                                    if (isset($keywordFinalMetricsValue['date_key'])) {
                                        $keywordFinalMetrics['Date'] = $keywordFinalMetricsValue['date_key'];
                                    }
                                    //if(isset($keywordFinalMetricsValue['brand_name'])){ $keywordFinalMetrics['Brand Name'] = $keywordFinalMetricsValue['brand_name']; }
                                    //if(isset($keywordFinalMetricsValue['account_name'])){ $keywordFinalMetrics['Account Name'] = $keywordFinalMetricsValue['account_name']; }
                                    //if(isset($keywordFinalMetricsValue['campaign_id'])){ $keywordFinalMetrics['Campaign Id'] = $keywordFinalMetricsValue['campaign_id']; }
                                    if (isset($keywordFinalMetricsValue['campaign_name'])) {
                                        $keywordFinalMetrics['Campaign Name'] = $keywordFinalMetricsValue['campaign_name'];
                                    }
                                    if (isset($keywordFinalMetricsValue['campaign_type'])) {
                                        $keywordFinalMetrics['Campaign Type'] = $keywordFinalMetricsValue['campaign_type'];
                                    }
                                    if (isset($keywordFinalMetricsValue['adgroupid'])) {
                                        $keywordFinalMetrics['Ad Group Id'] = $keywordFinalMetricsValue['adgroupid'];
                                    }
                                    if (isset($keywordFinalMetricsValue['adgroupname'])) {
                                        $keywordFinalMetrics['Ad Group Name'] = $keywordFinalMetricsValue['adgroupname'];
                                    }
                                    if (isset($keywordFinalMetricsValue['keywordid'])) {
                                        $keywordFinalMetrics['Keyword Id'] = $keywordFinalMetricsValue['keywordid'];
                                    }
                                    if (isset($keywordFinalMetricsValue['keywordtext'])) {
                                        $keywordFinalMetrics['Keyword Text'] = $keywordFinalMetricsValue['keywordtext'];
                                    }
                                    if (isset($keywordFinalMetricsValue['impressions'])) {
                                        $keywordFinalMetrics['Impressions'] = $keywordFinalMetricsValue['impressions'];
                                    }
                                    if (isset($keywordFinalMetricsValue['clicks'])) {
                                        $keywordFinalMetrics['Clicks'] = $keywordFinalMetricsValue['clicks'];
                                    }
                                    if (isset($keywordFinalMetricsValue['cost'])) {
                                        $keywordFinalMetrics['Cost'] = $this->roundValue($keywordFinalMetricsValue['cost']);
                                    }
                                    if (isset($keywordFinalMetricsValue['revenue'])) {
                                        $keywordFinalMetrics['Revenue'] = $this->roundValue($keywordFinalMetricsValue['revenue']);
                                    }
                                    if (isset($keywordFinalMetricsValue['order_conversion'])) {
                                        $keywordFinalMetrics['Order Conversion'] = $keywordFinalMetricsValue['order_conversion'];
                                    }
                                    if (isset($keywordFinalMetricsValue['ctr'])) {
                                        $keywordFinalMetrics['Click-Thru Rate (CTR)'] = $this->roundValue($keywordFinalMetricsValue['ctr']);
                                    }
                                    if (isset($keywordFinalMetricsValue['cpc'])) {
                                        $keywordFinalMetrics['Cost Per Click (CPC)'] = $this->roundValue($keywordFinalMetricsValue['cpc']);
                                    }
                                    if (isset($keywordFinalMetricsValue['acos'])) {
                                        $keywordFinalMetrics['Total Advertising Cost of Sales (ACoS)'] = $this->roundValue($keywordFinalMetricsValue['acos']);
                                    }
                                    if (isset($keywordFinalMetricsValue['roas'])) {
                                        $keywordFinalMetrics['Total Return on Advertising Spend (RoAS)'] = $this->roundValue($keywordFinalMetricsValue['roas']);
                                    }
                                    if (isset($keywordFinalMetricsValue['cpa'])) {
                                        $keywordFinalMetrics['CPA'] = $this->roundValue($keywordFinalMetricsValue['cpa']);
                                    }
                                    array_push($keywordFinalMetricsArray, $keywordFinalMetrics);
                                }
                                $fileName = 'Keyword-Daily-' . date('YmdHis') . '-' . $scheduleId . '.xlsx';
                                $fileTempPath = public_path('files/advertisingEmail/Keyword-Daily-' . date('YmdHis') . '-' . $scheduleId . '.xlsx');
                                $this->_generateSoldByCSV($keywordFinalMetricsArray, $fileTempPath);
                                /*$fileNames[] =array('path'=>$fileTempPath,
                                    'name'=>$fileName
                                ) ;*/
                                //$finalDataArray['Keyword'] = $keywordFinalMetricsArray;
                                //$cronData['keywordReportStatus'] = '<p>Keyword Report : Successfully generated.Check the attachment.</p>';
                                $cronData['keywordReportStatus'] = '<p>Keyword Report : <a href="' . $apiServerLink . $fileName . '" download=""> Click Here To Download </a></p>';
                                $completeFilePath = $apiServerLink . $fileName;
                                $fileEntryArray = [];
                                $fileEntryArray = array('fkScheduleId' => $scheduleId, 'fkParameterTypeId' => 4, 'parameterTypeName' => 'Keyword', 'time' => $currentTime, 'date' => $date,
                                    'fileName' => $fileName,
                                    'filePath' => $fileTempPath,
                                    'completeFilePath' => $completeFilePath,
                                    'devServerLink' => $devServerLink,
                                    'apiServerLink' => $apiServerLink,
                                    'isDataFound' => '1',
                                    'isFileDeleted' => '0');
                                $this->_fileDbLogs($fileEntryArray);

                            } else {
                                $fileEntryArray = [];
                                $fileEntryArray = array('fkScheduleId' => $scheduleId, 'fkParameterTypeId' => 4, 'parameterTypeName' => 'Keyword', 'time' => $currentTime, 'date' => $date,
                                    'devServerLink' => $devServerLink,
                                    'apiServerLink' => $apiServerLink,
                                    'isDataFound' => '0',
                                    'isFileDeleted' => '0');
                                $this->_fileDbLogs($fileEntryArray);
                                $cronData['keywordReportStatus'] = '<p>Keyword Report : Data not found.</p>';
                                Log::info("filePath:app\Console\Commands\Ams\AmsAdvertisingEmailSchedule, Module Name: Advertising Reports to e-mail. Message :No data found against this schedule in keyword daily report.");
                            }
                            echo 'test';
                            // dd($fileNames);
                            break;
                        //weekely start
                        case 'Weekly':
                            echo 'Weekly';
                            $keywordStoredProceduresData = \DB::connection($DB2)->select('call spCalculateWeeklyKeywordSchedulingReport(?,?,?)', $keywordParameters);
                            \DB::disconnect($DB2);
                            if (!empty($keywordStoredProceduresData)) {
                                //dd($keywordStoredProceduresData);
                                $filterDataArray = [];
                                $filterData = [];
                                foreach ($keywordStoredProceduresData as $orgnalKey => $orgnalvalue) {
                                    $filterKey = strtolower($orgnalKey);
                                    $filterDataArray['brand_id'] = $orgnalvalue->brand_id;
                                    $filterDataArray['brand_name'] = $orgnalvalue->brand_name;
                                    //$filterDataArray['accouint_id'] = $orgnalvalue->accouint_id;
                                    $filterDataArray['account_name'] = $orgnalvalue->account_name;
                                    $filterDataArray['profile_id'] = $orgnalvalue->profile_id;
                                    $filterDataArray['profile_name'] = $orgnalvalue->profile_name;
                                    $filterDataArray['start_date'] = $orgnalvalue->start_date;
                                    $filterDataArray['end_date'] = $orgnalvalue->end_Date;
                                    $filterDataArray['year_'] = $orgnalvalue->year_;
                                    $filterDataArray['campaign_id'] = $orgnalvalue->campaign_id;
                                    $filterDataArray['campaign_name'] = $orgnalvalue->campaign_name;
                                    $filterDataArray['campaign_type'] = $orgnalvalue->campaign_type;
                                    $filterDataArray['adGroupId'] = $orgnalvalue->adGroupId;
                                    $filterDataArray['adGroupName'] = $orgnalvalue->adGroupName;
                                    $filterDataArray['keywordId'] = $orgnalvalue->keywordId;
                                    $filterDataArray['keywordText'] = $orgnalvalue->keywordText;
                                    $filterDataArray['impressions'] = $orgnalvalue->impressions;
                                    $filterDataArray['clicks'] = $orgnalvalue->clicks;
                                    $filterDataArray['cost'] = $orgnalvalue->cost;
                                    $filterDataArray['revenue'] = $orgnalvalue->revenue;
                                    $filterDataArray['order_conversion'] = $orgnalvalue->order_conversion;
                                    $filterDataArray['acos'] = $orgnalvalue->acos_;
                                    $filterDataArray['CPC'] = $orgnalvalue->CPC;
                                    $filterDataArray['CTR'] = $orgnalvalue->CTR;
                                    $filterDataArray['CPA'] = $orgnalvalue->CPA;
                                    $filterDataArray['ROAS'] = $orgnalvalue->ROAS;
                                    $filterData[] = array_change_key_case($filterDataArray, CASE_LOWER);
                                }
                                //dd($filterData);
                                $storeNew = [];
                                $matchesColumns = [];
                                foreach ($filterData as $key => $value) {

                                    foreach ($keywordMetricsArray as $key1) {

                                        if (array_key_exists($key1, $value)) {
                                            $matchesColumns[$key1] = $value[$key1];

                                        }
                                    }
                                    array_push($storeNew, $matchesColumns);
                                }
                                $keywordFinalMetrics = array();
                                $keywordFinalMetricsArray = array();
                                foreach ($storeNew as $keywordFinalMetricsKey => $keywordFinalMetricsValue) {
                                    //dd($keywordFinalMetricsValue);
                                    if (isset($keywordFinalMetricsValue['year_'])) {
                                        $keywordFinalMetrics['Year_'] = $keywordFinalMetricsValue['year_'];
                                    }
                                    if (isset($keywordFinalMetricsValue['start_date'])) {
                                        $keywordFinalMetrics['Start Date'] = $keywordFinalMetricsValue['start_date'];
                                    }
                                    if (isset($keywordFinalMetricsValue['end_date'])) {
                                        $keywordFinalMetrics['End Date'] = $keywordFinalMetricsValue['end_date'];
                                    }
                                    //if(isset($keywordFinalMetricsValue['brand_name'])){ $keywordFinalMetrics['Brand Name'] = $keywordFinalMetricsValue['brand_name']; }
                                    //if(isset($keywordFinalMetricsValue['account_name'])){ $keywordFinalMetrics['Account Name'] = $keywordFinalMetricsValue['account_name']; }
                                    //if(isset($keywordFinalMetricsValue['campaign_id'])){ $keywordFinalMetrics['Campaign Id'] = $keywordFinalMetricsValue['campaign_id']; }
                                    if (isset($keywordFinalMetricsValue['campaign_name'])) {
                                        $keywordFinalMetrics['Campaign Name'] = $keywordFinalMetricsValue['campaign_name'];
                                    }
                                    if (isset($keywordFinalMetricsValue['campaign_type'])) {
                                        $keywordFinalMetrics['Campaign Type'] = $keywordFinalMetricsValue['campaign_type'];
                                    }
                                    if (isset($keywordFinalMetricsValue['adgroupid'])) {
                                        $keywordFinalMetrics['Ad Group Id'] = $keywordFinalMetricsValue['adgroupid'];
                                    }
                                    if (isset($keywordFinalMetricsValue['adgroupname'])) {
                                        $keywordFinalMetrics['Ad Group Name'] = $keywordFinalMetricsValue['adgroupname'];
                                    }
                                    if (isset($keywordFinalMetricsValue['keywordid'])) {
                                        $keywordFinalMetrics['Keyword Id'] = $keywordFinalMetricsValue['keywordid'];
                                    }
                                    if (isset($keywordFinalMetricsValue['keywordtext'])) {
                                        $keywordFinalMetrics['Keyword Text'] = $keywordFinalMetricsValue['keywordtext'];
                                    }
                                    if (isset($keywordFinalMetricsValue['impressions'])) {
                                        $keywordFinalMetrics['Impressions'] = $keywordFinalMetricsValue['impressions'];
                                    }
                                    if (isset($keywordFinalMetricsValue['clicks'])) {
                                        $keywordFinalMetrics['Clicks'] = $keywordFinalMetricsValue['clicks'];
                                    }
                                    if (isset($keywordFinalMetricsValue['cost'])) {
                                        $keywordFinalMetrics['Cost'] = $this->roundValue($keywordFinalMetricsValue['cost']);
                                    }
                                    if (isset($keywordFinalMetricsValue['revenue'])) {
                                        $keywordFinalMetrics['Revenue'] = $this->roundValue($keywordFinalMetricsValue['revenue']);
                                    }
                                    if (isset($keywordFinalMetricsValue['order_conversion'])) {
                                        $keywordFinalMetrics['Order Conversion'] = $keywordFinalMetricsValue['order_conversion'];
                                    }
                                    if (isset($keywordFinalMetricsValue['ctr'])) {
                                        $keywordFinalMetrics['Click-Thru Rate (CTR)'] = $this->roundValue($keywordFinalMetricsValue['ctr']);
                                    }
                                    if (isset($keywordFinalMetricsValue['cpc'])) {
                                        $keywordFinalMetrics['Cost Per Click (CPC)'] = $this->roundValue($keywordFinalMetricsValue['cpc']);
                                    }
                                    if (isset($keywordFinalMetricsValue['acos'])) {
                                        $keywordFinalMetrics['Total Advertising Cost of Sales (ACoS)'] = $this->roundValue($keywordFinalMetricsValue['acos']);
                                    }
                                    if (isset($keywordFinalMetricsValue['roas'])) {
                                        $keywordFinalMetrics['Total Return on Advertising Spend (RoAS)'] = $this->roundValue($keywordFinalMetricsValue['roas']);
                                    }
                                    if (isset($keywordFinalMetricsValue['cpa'])) {
                                        $keywordFinalMetrics['CPA'] = $this->roundValue($keywordFinalMetricsValue['cpa']);
                                    }
                                    array_push($keywordFinalMetricsArray, $keywordFinalMetrics);
                                }
                                $fileName = 'Keyword-Weekly-' . date('YmdHis') . '-' . $scheduleId . '.xlsx';
                                $fileTempPath = public_path('files/advertisingEmail/Keyword-Weekly-' . date('YmdHis') . '-' . $scheduleId . '.xlsx');
                                $this->_generateSoldByCSV($keywordFinalMetricsArray, $fileTempPath);
                                /*$fileNames[] =array('path'=>$fileTempPath,
                                    'name'=>$fileName
                                ) ;*/
                                //$finalDataArray['Keyword'] = $keywordFinalMetricsArray;
                                //$cronData['keywordReportStatus'] = '<p>Keyword Report : Successfully generated.Check the attachment.</p>';
                                $cronData['keywordReportStatus'] = '<p>Keyword Report : <a href="' . $apiServerLink . $fileName . '" download=""> Click Here To Download </a></p>';
                                $completeFilePath = $apiServerLink . $fileName;
                                $fileEntryArray = [];
                                $fileEntryArray = array('fkScheduleId' => $scheduleId, 'fkParameterTypeId' => 4, 'parameterTypeName' => 'Keyword', 'time' => $currentTime, 'date' => $date,
                                    'fileName' => $fileName,
                                    'filePath' => $fileTempPath,
                                    'completeFilePath' => $completeFilePath,
                                    'devServerLink' => $devServerLink,
                                    'apiServerLink' => $apiServerLink,
                                    'isDataFound' => '1',
                                    'isFileDeleted' => '0');
                                $this->_fileDbLogs($fileEntryArray);

                            } else {
                                $fileEntryArray = [];
                                $fileEntryArray = array('fkScheduleId' => $scheduleId, 'fkParameterTypeId' => 4, 'parameterTypeName' => 'Keyword', 'time' => $currentTime, 'date' => $date,
                                    'devServerLink' => $devServerLink,
                                    'apiServerLink' => $apiServerLink,
                                    'isDataFound' => '0',
                                    'isFileDeleted' => '0');
                                $this->_fileDbLogs($fileEntryArray);
                                $cronData['keywordReportStatus'] = '<p>Keyword Report : Data not found.</p>';
                                Log::info("filePath:app\Console\Commands\Ams\AmsAdvertisingEmailSchedule, Module Name: Advertising Reports to e-mail. Message :No data found against this schedule in keyword daily report.");
                            }
                            break;
                        //monthly start
                        case 'Monthly':
                            echo 'Monthly';
                            $keywordStoredProceduresData = \DB::connection($DB2)->select('call spCalculateMonthlyKeywordSchedulingReport(?,?,?)', $keywordParameters);
                            \DB::disconnect($DB2);
                            if (!empty($keywordStoredProceduresData)) {
                                $filterDataArray = [];
                                $filterData = [];
                                foreach ($keywordStoredProceduresData as $orgnalKey => $orgnalvalue) {
                                    $filterKey = strtolower($orgnalKey);
                                    $filterDataArray['brand_id'] = $orgnalvalue->brand_id;
                                    $filterDataArray['brand_name'] = $orgnalvalue->brand_name;
                                    //$filterDataArray['accouint_id'] = $orgnalvalue->accouint_id;
                                    $filterDataArray['account_name'] = $orgnalvalue->account_name;
                                    $filterDataArray['profile_id'] = $orgnalvalue->profile_id;
                                    $filterDataArray['profile_name'] = $orgnalvalue->profile_name;
                                    $filterDataArray['start_date'] = $orgnalvalue->start_date;
                                    $filterDataArray['end_date'] = $orgnalvalue->end_Date;
                                    $filterDataArray['year_'] = $orgnalvalue->year_;
                                    $filterDataArray['campaign_id'] = $orgnalvalue->campaign_id;
                                    $filterDataArray['campaign_name'] = $orgnalvalue->campaign_name;
                                    $filterDataArray['campaign_type'] = $orgnalvalue->campaign_type;
                                    $filterDataArray['adGroupId'] = $orgnalvalue->adGroupId;
                                    $filterDataArray['adGroupName'] = $orgnalvalue->adGroupName;
                                    $filterDataArray['keywordId'] = $orgnalvalue->keywordId;
                                    $filterDataArray['keywordText'] = $orgnalvalue->keywordText;
                                    $filterDataArray['impressions'] = $orgnalvalue->impressions;
                                    $filterDataArray['clicks'] = $orgnalvalue->clicks;
                                    $filterDataArray['cost'] = $orgnalvalue->cost;
                                    $filterDataArray['revenue'] = $orgnalvalue->revenue;
                                    $filterDataArray['order_conversion'] = $orgnalvalue->order_conversion;
                                    $filterDataArray['acos'] = $orgnalvalue->acos_;
                                    $filterDataArray['CPC'] = $orgnalvalue->CPC;
                                    $filterDataArray['CTR'] = $orgnalvalue->CTR;
                                    $filterDataArray['CPA'] = $orgnalvalue->CPA;
                                    $filterDataArray['ROAS'] = $orgnalvalue->ROAS;
                                    $filterData[] = array_change_key_case($filterDataArray, CASE_LOWER);
                                }
                                $storeNew = [];
                                $matchesColumns = [];
                                foreach ($filterData as $key => $value) {
                                    foreach ($keywordMetricsArray as $key1) {
                                        if (array_key_exists($key1, $value)) {
                                            $matchesColumns[$key1] = $value[$key1];
                                        }
                                    }
                                    array_push($storeNew, $matchesColumns);
                                }
                                $keywordFinalMetrics = array();
                                $keywordFinalMetricsArray = array();
                                foreach ($storeNew as $keywordFinalMetricsKey => $keywordFinalMetricsValue) {
                                    //dd($keywordFinalMetricsValue);
                                    if (isset($keywordFinalMetricsValue['year_'])) {
                                        $keywordFinalMetrics['Year_'] = $keywordFinalMetricsValue['year_'];
                                    }
                                    if (isset($keywordFinalMetricsValue['start_date'])) {
                                        $keywordFinalMetrics['Start Date'] = $keywordFinalMetricsValue['start_date'];
                                    }
                                    if (isset($keywordFinalMetricsValue['end_date'])) {
                                        $keywordFinalMetrics['End Date'] = $keywordFinalMetricsValue['end_date'];
                                    }
                                    //if(isset($keywordFinalMetricsValue['brand_name'])){ $keywordFinalMetrics['Brand Name'] = $keywordFinalMetricsValue['brand_name']; }
                                    //if(isset($keywordFinalMetricsValue['account_name'])){ $keywordFinalMetrics['Account Name'] = $keywordFinalMetricsValue['account_name']; }
                                    //if(isset($keywordFinalMetricsValue['campaign_id'])){ $keywordFinalMetrics['Campaign Id'] = $keywordFinalMetricsValue['campaign_id']; }
                                    if (isset($keywordFinalMetricsValue['campaign_name'])) {
                                        $keywordFinalMetrics['Campaign Name'] = $keywordFinalMetricsValue['campaign_name'];
                                    }
                                    if (isset($keywordFinalMetricsValue['campaign_type'])) {
                                        $keywordFinalMetrics['Campaign Type'] = $keywordFinalMetricsValue['campaign_type'];
                                    }
                                    if (isset($keywordFinalMetricsValue['adgroupid'])) {
                                        $keywordFinalMetrics['Ad Group Id'] = $keywordFinalMetricsValue['adgroupid'];
                                    }
                                    if (isset($keywordFinalMetricsValue['adgroupname'])) {
                                        $keywordFinalMetrics['Ad Group Name'] = $keywordFinalMetricsValue['adgroupname'];
                                    }
                                    if (isset($keywordFinalMetricsValue['keywordid'])) {
                                        $keywordFinalMetrics['Keyword Id'] = $keywordFinalMetricsValue['keywordid'];
                                    }
                                    if (isset($keywordFinalMetricsValue['keywordtext'])) {
                                        $keywordFinalMetrics['Keyword Text'] = $keywordFinalMetricsValue['keywordtext'];
                                    }
                                    if (isset($keywordFinalMetricsValue['impressions'])) {
                                        $keywordFinalMetrics['Impressions'] = $keywordFinalMetricsValue['impressions'];
                                    }
                                    if (isset($keywordFinalMetricsValue['clicks'])) {
                                        $keywordFinalMetrics['Clicks'] = $keywordFinalMetricsValue['clicks'];
                                    }
                                    if (isset($keywordFinalMetricsValue['cost'])) {
                                        $keywordFinalMetrics['Cost'] = $this->roundValue($keywordFinalMetricsValue['cost']);
                                    }
                                    if (isset($keywordFinalMetricsValue['revenue'])) {
                                        $keywordFinalMetrics['Revenue'] = $this->roundValue($keywordFinalMetricsValue['revenue']);
                                    }
                                    if (isset($keywordFinalMetricsValue['order_conversion'])) {
                                        $keywordFinalMetrics['Order Conversion'] = $keywordFinalMetricsValue['order_conversion'];
                                    }
                                    if (isset($keywordFinalMetricsValue['ctr'])) {
                                        $keywordFinalMetrics['Click-Thru Rate (CTR)'] = $this->roundValue($keywordFinalMetricsValue['ctr']);
                                    }
                                    if (isset($keywordFinalMetricsValue['cpc'])) {
                                        $keywordFinalMetrics['Cost Per Click (CPC)'] = $this->roundValue($keywordFinalMetricsValue['cpc']);
                                    }
                                    if (isset($keywordFinalMetricsValue['acos'])) {
                                        $keywordFinalMetrics['Total Advertising Cost of Sales (ACoS)'] = $this->roundValue($keywordFinalMetricsValue['acos']);
                                    }
                                    if (isset($keywordFinalMetricsValue['roas'])) {
                                        $keywordFinalMetrics['Total Return on Advertising Spend (RoAS)'] = $this->roundValue($keywordFinalMetricsValue['roas']);
                                    }
                                    if (isset($keywordFinalMetricsValue['cpa'])) {
                                        $keywordFinalMetrics['CPA'] = $this->roundValue($keywordFinalMetricsValue['cpa']);
                                    }
                                    array_push($keywordFinalMetricsArray, $keywordFinalMetrics);
                                }
                                $fileName = 'Keyword-Monthly-' . date('YmdHis') . '-' . $scheduleId . '.xlsx';
                                $fileTempPath = public_path('files/advertisingEmail/Keyword-Monthly-' . date('YmdHis') . '-' . $scheduleId . '.xlsx');
                                $this->_generateSoldByCSV($keywordFinalMetricsArray, $fileTempPath);
                                /*$fileNames[] =array('path'=>$fileTempPath,
                                    'name'=>$fileName
                                ) ;*/
                                //$finalDataArray['Keyword'] = $keywordFinalMetricsArray;
                                //$cronData['keywordReportStatus'] = '<p>Keyword Report : Successfully generated.Check the attachment.</p>';
                                $cronData['keywordReportStatus'] = '<p>Keyword Report : <a href="' . $apiServerLink . $fileName . '" download=""> Click Here To Download </a></p>';
                                $completeFilePath = $apiServerLink . $fileName;
                                $fileEntryArray = [];
                                $fileEntryArray = array('fkScheduleId' => $scheduleId, 'fkParameterTypeId' => 4, 'parameterTypeName' => 'Keyword', 'time' => $currentTime, 'date' => $date,
                                    'fileName' => $fileName,
                                    'filePath' => $fileTempPath,
                                    'completeFilePath' => $completeFilePath,
                                    'devServerLink' => $devServerLink,
                                    'apiServerLink' => $apiServerLink,
                                    'isDataFound' => '1',
                                    'isFileDeleted' => '0');
                                $this->_fileDbLogs($fileEntryArray);

                            } else {
                                $fileEntryArray = [];
                                $fileEntryArray = array('fkScheduleId' => $scheduleId, 'fkParameterTypeId' => 4, 'parameterTypeName' => 'Keyword', 'time' => $currentTime, 'date' => $date,
                                    'devServerLink' => $devServerLink,
                                    'apiServerLink' => $apiServerLink,
                                    'isDataFound' => '0',
                                    'isFileDeleted' => '0');
                                $this->_fileDbLogs($fileEntryArray);
                                //$noDATAEamil = $this->_noDataEmailAlert($cronData);
                                $cronData['keywordReportStatus'] = '<p>Keyword Report : Data not found.</p>';
                                Log::info("filePath:app\Console\Commands\Ams\AmsAdvertisingEmailSchedule, Module Name: Advertising Reports to e-mail. Message :No data found against this schedule in keyword daily report.");
                            }
                            break;
                    }
                    /******** Make arryas and  csv with granularity ends***********/
                } else {
                    echo 'No metrics found for Keyword report';
                    Log::info("filePath:app\Console\Commands\Ams\AmsAdvertisingEmailSchedule, Module Name: Advertising Reports to e-mail. Message :No metrics found for Keyword report.");
                }
                /*** Get selected metrics against keyword parameter type  ends ***/
                /*** Get selected metrics against asin parameter type  starts ***/
                $selectAsinReportsSelectedMetrics = scheduledEmailAdvertisingReportsMetrics::where("fkReportScheduleId", $scheduleId)->where("fkParameterType", 5)->get();
                $selectAsinReportsSelectedMetricsCount = $selectAsinReportsSelectedMetrics->count();
                if ($selectAsinReportsSelectedMetricsCount > 0) {
                    /*** Get selected metrics against parameter type  starts ***/
                    $getSelectedSponsordTypes = amsScheduleSelectedEmailReports::distinct()->where('fkReportScheduleId', $scheduleId)->where('fkParameterType', 5)->get(['fkSponsordTypeId']);
                    $getSelectedSponsorTypesArray = array();
                    foreach ($getSelectedSponsordTypes as $getSelectedSponsordType) {
                        $getSelectedSponsorTypesArray[] = $getSelectedSponsordType->fkSponsordTypeId;
                    } //end foreach
                    $commaSeprateSponsordTypeArray = [];
                    $commaSeprateSponsordType = '';
                    if (in_array(1, $getSelectedSponsorTypesArray)) {
                        $commaSeprateSponsordTypeArray[] = 'SD';
                    }
                    if (in_array(2, $getSelectedSponsorTypesArray)) {
                        $commaSeprateSponsordTypeArray[] = 'SP';
                    }
                    if (in_array(3, $getSelectedSponsorTypesArray)) {
                        $commaSeprateSponsordTypeArray[] = 'SB';
                    }
                    if (empty($commaSeprateSponsordTypeArray)) {
                        $commaSeprateSponsordType = 'SP,SD,SB';
                    } else {
                        $commaSeprateSponsordTypeImplode = implode(",", $commaSeprateSponsordTypeArray);
                        //$commaSeprateSponsordType = "'$commaSeprateSponsordTypeImplode'";
                        $commaSeprateSponsordType = $commaSeprateSponsordTypeImplode;
                    }
                    /*** Get selected metrics against parameter type  ends ***/
                    /******** Get column name against metric id starts ***********/
                    /******** Metrics that will send in all emails starts ***********/
                    $asinsMetricsArray = [];
                    switch ($granularity) {
                        case 'Daily':
                            $asinsMetricsArray = [
                                'brand_name',
                                'account_name',
                                'date_key',
                            ];
                            break;
                        default:
                            $asinsMetricsArray = [
                                'brand_name',
                                'account_name',
                                'year_',
                                'start_date',
                                'end_date'
                            ];
                            break;
                    }//end switch
                    /******** Metrics that will send in all emails ends ***********/
                    /******** Make refined metrics array starts ***********/
                    foreach ($selectAsinReportsSelectedMetrics as $selectAsinReportsSelectedMetric) {
                        $currentAsinMetricId = $selectAsinReportsSelectedMetric->fkReportMetricId;
                        /*************** Get column name against metric id starts ***********/
                        $getAsinReportMetricsNames = amsReportsMetrics::where('id', $currentAsinMetricId)->first();
                        $getAsinReportMetricsNamesCount = $getAsinReportMetricsNames->count();
                        if ($getAsinReportMetricsNamesCount > 0) {
                            $tblAsinColumnName = $getAsinReportMetricsNames->tblColumnName;
                            $asinsMetricsArray[] = strtolower(trim($tblAsinColumnName));
                        }//end if
                    }//end foreach=
                    $asinsMetricsArray = array_change_key_case($asinsMetricsArray, CASE_LOWER);
                    /******** Make refined metrics array ends ***********/
                    /******** Make arryas and  csv with granularity starts***********/
                    $DB2 = 'mysqlDb2';
                    $asinParameters = array($amsProfileId, $commaSeprateSponsordType, $timeFrame);
                    switch ($granularity) {
                        case 'Daily':
                            echo 'Daily';
                            $asinsStoredProceduresData = \DB::connection($DB2)->select('call spCalculateDailyAsinsSchedulingReport(?,?,?)', $asinParameters);
                            \DB::disconnect($DB2);
                            if (!empty($asinsStoredProceduresData)) {
                                $filterDataArray = [];
                                $filterData = [];
                                foreach ($asinsStoredProceduresData as $orgnalKey => $orgnalvalue) {
                                    $filterKey = strtolower($orgnalKey);
                                    $filterDataArray['brand_id'] = $orgnalvalue->brand_id;
                                    $filterDataArray['brand_name'] = $orgnalvalue->brand_name;
                                    //$filterDataArray['account_id'] = $orgnalvalue->account_id;
                                    $filterDataArray['account_name'] = $orgnalvalue->account_name;
                                    $filterDataArray['profile_id'] = $orgnalvalue->profile_id;
                                    $filterDataArray['profile_name'] = $orgnalvalue->profile_name;
                                    $filterDataArray['date_key'] = $orgnalvalue->date_key;
                                    $filterDataArray['campaign_id'] = $orgnalvalue->campaign_id;
                                    $filterDataArray['campaign_name'] = $orgnalvalue->campaign_name;
                                    $filterDataArray['campaign_type'] = $orgnalvalue->campaign_type;
                                    $filterDataArray['adgroupid'] = $orgnalvalue->adGroupId;
                                    $filterDataArray['adgroupname'] = $orgnalvalue->adGroupName;
                                    $filterDataArray['keywordid'] = $orgnalvalue->keywordId;
                                    $filterDataArray['keywordtext'] = $orgnalvalue->keywordText;
                                    $filterDataArray['asin'] = $orgnalvalue->asin_;
                                    $filterDataArray['other_asin'] = $orgnalvalue->other_asin;
                                    $filterDataArray['sku'] = $orgnalvalue->sku;
                                    $filterDataArray['currency'] = $orgnalvalue->currency;
                                    $filterDataArray['match_type'] = $orgnalvalue->match_type;
                                    $filterDataArray['attributed_units_ordered'] = $orgnalvalue->attributedunitsordered;
                                    $filterDataArray['sales_other_sku'] = $orgnalvalue->sales_other_sku;
                                    $filterDataArray['units_ordered_other_sku'] = $orgnalvalue->units_ordered_other_sku;
                                    $filterData[] = array_change_key_case($filterDataArray, CASE_LOWER);
                                }
                                $storeNew = [];
                                $matchesColumns = [];
                                foreach ($filterData as $key => $value) {
                                    foreach ($asinsMetricsArray as $key1) {
                                        if (array_key_exists($key1, $value)) {
                                            $matchesColumns[$key1] = $value[$key1];
                                        }
                                    }
                                    array_push($storeNew, $matchesColumns);
                                }
                                $asinsFinalMetrics = array();
                                $asinsFinalMetricsArray = array();
                                foreach ($storeNew as $asinsFinalMetricsKey => $asinsFinalMetricsValue) {
                                    if (isset($asinsFinalMetricsValue['date_key'])) {
                                        $asinsFinalMetrics['Date'] = $asinsFinalMetricsValue['date_key'];
                                    }
                                    //if(isset($asinsFinalMetricsValue['brand_name'])){ $asinsFinalMetrics['Brand Name'] = $asinsFinalMetricsValue['brand_name']; }
                                    //if(isset($asinsFinalMetricsValue['account_name'])){ $asinsFinalMetrics['Account Name'] = $asinsFinalMetricsValue['account_name']; }
                                    //if(isset($asinsFinalMetricsValue['campaign_id'])){ $asinsFinalMetrics['Campaign Id'] = $asinsFinalMetricsValue['campaign_id']; }
                                    if (isset($asinsFinalMetricsValue['campaign_name'])) {
                                        $asinsFinalMetrics['Campaign Name'] = $asinsFinalMetricsValue['campaign_name'];
                                    }
                                    if (isset($asinsFinalMetricsValue['campaign_type'])) {
                                        $asinsFinalMetrics['Campaign Type'] = $asinsFinalMetricsValue['campaign_type'];
                                    }
                                    if (isset($asinsFinalMetricsValue['adgroupid'])) {
                                        $asinsFinalMetrics['Ad Group Id'] = $asinsFinalMetricsValue['adgroupid'];
                                    }
                                    if (isset($asinsFinalMetricsValue['adgroupname'])) {
                                        $asinsFinalMetrics['Ad Group Name'] = $asinsFinalMetricsValue['adgroupname'];
                                    }
                                    if (isset($asinsFinalMetricsValue['keywordid'])) {
                                        $asinsFinalMetrics['Keyword Id'] = $asinsFinalMetricsValue['keywordid'];
                                    }
                                    if (isset($asinsFinalMetricsValue['keywordtext'])) {
                                        $asinsFinalMetrics['Keyword Text'] = $asinsFinalMetricsValue['keywordtext'];
                                    }
                                    if (isset($asinsFinalMetricsValue['asin'])) {
                                        $asinsFinalMetrics['Asin'] = $asinsFinalMetricsValue['asin'];
                                    }
                                    if (isset($asinsFinalMetricsValue['other_asin'])) {
                                        $asinsFinalMetrics['Other Asin'] = $asinsFinalMetricsValue['other_asin'];
                                    }
                                    if (isset($asinsFinalMetricsValue['sku'])) {
                                        $asinsFinalMetrics['Sku'] = $asinsFinalMetricsValue['sku'];
                                    }
                                    if (isset($asinsFinalMetricsValue['currency'])) {
                                        $asinsFinalMetrics['Currency'] = $asinsFinalMetricsValue['currency'];
                                    }
                                    if (isset($asinsFinalMetricsValue['match_type'])) {
                                        $asinsFinalMetrics['Match Type'] = $asinsFinalMetricsValue['match_type'];
                                    }
                                    if (isset($asinsFinalMetricsValue['attributed_units_ordered'])) {
                                        $asinsFinalMetrics['Attributed Units Ordered'] = $asinsFinalMetricsValue['attributed_units_ordered'];
                                    }
                                    if (isset($asinsFinalMetricsValue['sales_other_sku'])) {
                                        $asinsFinalMetrics['Sales Other Sku'] = $asinsFinalMetricsValue['sales_other_sku'];
                                    }
                                    if (isset($asinsFinalMetricsValue['units_ordered_other_sku'])) {
                                        $asinsFinalMetrics['Units Ordered Other Sku'] = $asinsFinalMetricsValue['units_ordered_other_sku'];
                                    }
                                    array_push($asinsFinalMetricsArray, $asinsFinalMetrics);
                                }
                                $fileName = 'Asins-Daily-' . date('YmdHis') . '-' . $scheduleId . '.xlsx';
                                $fileTempPath = public_path('files/advertisingEmail/Asins-Daily-' . date('YmdHis') . '-' . $scheduleId . '.xlsx');
                                $this->_generateSoldByCSV($asinsFinalMetricsArray, $fileTempPath);
                                /*$fileNames[] =array('path'=>$fileTempPath,
                                    'name'=>$fileName
                                ) ;*/
                                //$finalDataArray['Asins'] = $asinsFinalMetricsArray;
                                //$cronData['asinsReportStatus'] = '<p>Asins Report : Successfully generated.Check the attachment.</p>';
                                $cronData['asinsReportStatus'] = '<p>Asins Report : <a href="' . $apiServerLink . $fileName . '" download=""> Click Here To Download </a></p>';
                                $completeFilePath = $apiServerLink . $fileName;
                                $fileEntryArray = [];
                                $fileEntryArray = array('fkScheduleId' => $scheduleId, 'fkParameterTypeId' => 5, 'parameterTypeName' => 'ASINS', 'time' => $currentTime, 'date' => $date,
                                    'fileName' => $fileName,
                                    'filePath' => $fileTempPath,
                                    'completeFilePath' => $completeFilePath,
                                    'devServerLink' => $devServerLink,
                                    'apiServerLink' => $apiServerLink,
                                    'isDataFound' => '1',
                                    'isFileDeleted' => '0');
                                $this->_fileDbLogs($fileEntryArray);

                            } else {
                                $fileEntryArray = [];
                                $fileEntryArray = array('fkScheduleId' => $scheduleId, 'fkParameterTypeId' => 5, 'parameterTypeName' => 'ASINS', 'time' => $currentTime, 'date' => $date,
                                    'devServerLink' => $devServerLink,
                                    'apiServerLink' => $apiServerLink,
                                    'isDataFound' => '0',
                                    'isFileDeleted' => '0');
                                $this->_fileDbLogs($fileEntryArray);
                                $cronData['asinsReportStatus'] = '<p>Asins Report : Data not found.</p>';
                                Log::info("filePath:app\Console\Commands\Ams\AmsAdvertisingEmailSchedule, Module Name: Advertising Reports to e-mail. Message :No data found against this schedule in asins daily report.");
                            }
                            echo 'test';
                            // dd($fileNames);
                            break;
                        //weekely start
                        case 'Weekly':
                            echo 'Weekly';
                            $asinsStoredProceduresData = \DB::connection($DB2)->select('call spCalculateWeeklyAsinsSchedulingReport(?,?,?)', $asinParameters);
                            \DB::disconnect($DB2);
                            if (!empty($asinsStoredProceduresData)) {
                                //dd($keywordStoredProceduresData);
                                $filterDataArray = [];
                                $filterData = [];
                                foreach ($asinsStoredProceduresData as $orgnalKey => $orgnalvalue) {
                                    $filterKey = strtolower($orgnalKey);
                                    $filterDataArray['brand_id'] = $orgnalvalue->brand_id;
                                    $filterDataArray['brand_name'] = $orgnalvalue->brand_name;
                                    //$filterDataArray['accouint_id'] = $orgnalvalue->accouint_id;
                                    $filterDataArray['account_name'] = $orgnalvalue->account_name;
                                    $filterDataArray['profile_id'] = $orgnalvalue->profile_id;
                                    $filterDataArray['profile_name'] = $orgnalvalue->profile_name;
                                    $filterDataArray['start_date'] = $orgnalvalue->start_date;
                                    $filterDataArray['end_date'] = $orgnalvalue->end_Date;
                                    $filterDataArray['year_'] = $orgnalvalue->year_;
                                    $filterDataArray['campaign_id'] = $orgnalvalue->campaign_id;
                                    $filterDataArray['campaign_name'] = $orgnalvalue->campaign_name;
                                    $filterDataArray['campaign_type'] = $orgnalvalue->campaign_type;
                                    $filterDataArray['adgroupid'] = $orgnalvalue->adGroupId;
                                    $filterDataArray['adgroupname'] = $orgnalvalue->adGroupName;
                                    $filterDataArray['keywordid'] = $orgnalvalue->KeywordId;
                                    $filterDataArray['keywordtext'] = $orgnalvalue->KeywordText;
                                    $filterDataArray['asin'] = $orgnalvalue->asin;
                                    $filterDataArray['other_asin'] = $orgnalvalue->other_asin;
                                    $filterDataArray['sku'] = $orgnalvalue->sku;
                                    $filterDataArray['currency'] = $orgnalvalue->currency;
                                    $filterDataArray['match_type'] = $orgnalvalue->match_type;
                                    $filterDataArray['attributed_units_ordered'] = $orgnalvalue->attributed_units_ordered;
                                    $filterDataArray['sales_other_sku'] = $orgnalvalue->sales_other_sku;
                                    $filterDataArray['units_ordered_other_sku'] = $orgnalvalue->units_ordered_other_sku;
                                    $filterData[] = array_change_key_case($filterDataArray, CASE_LOWER);
                                }
                                //dd($filterData);
                                $storeNew = [];
                                $matchesColumns = [];
                                foreach ($filterData as $key => $value) {

                                    foreach ($asinsMetricsArray as $key1) {

                                        if (array_key_exists($key1, $value)) {
                                            $matchesColumns[$key1] = $value[$key1];

                                        }
                                    }
                                    array_push($storeNew, $matchesColumns);
                                }
                                $asinsFinalMetrics = array();
                                $asinsFinalMetricsArray = array();
                                foreach ($storeNew as $asinsFinalMetricsKey => $asinsFinalMetricsValue) {
                                    //dd($keywordFinalMetricsValue);
                                    if (isset($asinsFinalMetricsValue['year_'])) {
                                        $asinsFinalMetrics['Year_'] = $asinsFinalMetricsValue['year_'];
                                    }
                                    if (isset($asinsFinalMetricsValue['start_date'])) {
                                        $asinsFinalMetrics['Start Date'] = $asinsFinalMetricsValue['start_date'];
                                    }
                                    if (isset($asinsFinalMetricsValue['end_date'])) {
                                        $asinsFinalMetrics['End Date'] = $asinsFinalMetricsValue['end_date'];
                                    }
                                    //if(isset($asinsFinalMetricsValue['brand_name'])){ $asinsFinalMetrics['Brand Name'] = $asinsFinalMetricsValue['brand_name']; }
                                    //if(isset($asinsFinalMetricsValue['account_name'])){ $asinsFinalMetrics['Account Name'] = $asinsFinalMetricsValue['account_name']; }
                                    //if(isset($asinsFinalMetricsValue['campaign_id'])){ $asinsFinalMetrics['Campaign Id'] = $asinsFinalMetricsValue['campaign_id']; }
                                    if (isset($asinsFinalMetricsValue['campaign_name'])) {
                                        $asinsFinalMetrics['Campaign Name'] = $asinsFinalMetricsValue['campaign_name'];
                                    }
                                    if (isset($asinsFinalMetricsValue['campaign_type'])) {
                                        $asinsFinalMetrics['Campaign Type'] = $asinsFinalMetricsValue['campaign_type'];
                                    }
                                    if (isset($asinsFinalMetricsValue['adgroupid'])) {
                                        $asinsFinalMetrics['Ad Group Id'] = $asinsFinalMetricsValue['adgroupid'];
                                    }
                                    if (isset($asinsFinalMetricsValue['adgroupname'])) {
                                        $asinsFinalMetrics['Ad Group Name'] = $asinsFinalMetricsValue['adgroupname'];
                                    }
                                    if (isset($asinsFinalMetricsValue['keywordid'])) {
                                        $asinsFinalMetrics['Keyword Id'] = $asinsFinalMetricsValue['keywordid'];
                                    }
                                    if (isset($asinsFinalMetricsValue['keywordtext'])) {
                                        $asinsFinalMetrics['Keyword Text'] = $asinsFinalMetricsValue['keywordtext'];
                                    }
                                    if (isset($asinsFinalMetricsValue['asin'])) {
                                        $asinsFinalMetrics['Asin'] = $asinsFinalMetricsValue['asin'];
                                    }
                                    if (isset($asinsFinalMetricsValue['other_asin'])) {
                                        $asinsFinalMetrics['Other Asin'] = $asinsFinalMetricsValue['other_asin'];
                                    }
                                    if (isset($asinsFinalMetricsValue['sku'])) {
                                        $asinsFinalMetrics['Sku'] = $asinsFinalMetricsValue['sku'];
                                    }
                                    if (isset($asinsFinalMetricsValue['currency'])) {
                                        $asinsFinalMetrics['Currency'] = $asinsFinalMetricsValue['currency'];
                                    }
                                    if (isset($asinsFinalMetricsValue['match_type'])) {
                                        $asinsFinalMetrics['Match Type'] = $asinsFinalMetricsValue['match_type'];
                                    }
                                    if (isset($asinsFinalMetricsValue['attributed_units_ordered'])) {
                                        $asinsFinalMetrics['Attributed Units Ordered'] = $asinsFinalMetricsValue['attributed_units_ordered'];
                                    }
                                    if (isset($asinsFinalMetricsValue['sales_other_sku'])) {
                                        $asinsFinalMetrics['Sales Other Sku'] = $asinsFinalMetricsValue['sales_other_sku'];
                                    }
                                    if (isset($asinsFinalMetricsValue['units_ordered_other_sku'])) {
                                        $asinsFinalMetrics['Units Ordered Other Sku'] = $asinsFinalMetricsValue['units_ordered_other_sku'];
                                    }
                                    array_push($asinsFinalMetricsArray, $asinsFinalMetrics);
                                }
                                $fileName = 'asins-Weekly-' . date('YmdHis') . '-' . $scheduleId . '.xlsx';
                                $fileTempPath = public_path('files/advertisingEmail/asins-Weekly-' . date('YmdHis') . '-' . $scheduleId . '.xlsx');
                                $this->_generateSoldByCSV($asinsFinalMetricsArray, $fileTempPath);
                                /*$fileNames[] =array('path'=>$fileTempPath,
                                    'name'=>$fileName
                                ) ;*/
                                //$finalDataArray['Keyword'] = $asinsFinalMetricsArray;
                                //$cronData['asinsReportStatus'] = '<p>Asins Report : Successfully generated.Check the attachment.</p>';
                                $cronData['asinsReportStatus'] = '<p>Asins Report : <a href="' . $apiServerLink . $fileName . '" download=""> Click Here To Download </a></p>';
                                $completeFilePath = $apiServerLink . $fileName;
                                $fileEntryArray = [];
                                $fileEntryArray = array('fkScheduleId' => $scheduleId, 'fkParameterTypeId' => 5, 'parameterTypeName' => 'ASINS', 'time' => $currentTime, 'date' => $date,
                                    'fileName' => $fileName,
                                    'filePath' => $fileTempPath,
                                    'completeFilePath' => $completeFilePath,
                                    'devServerLink' => $devServerLink,
                                    'apiServerLink' => $apiServerLink,
                                    'isDataFound' => '1',
                                    'isFileDeleted' => '0');
                                $this->_fileDbLogs($fileEntryArray);

                            } else {
                                $fileEntryArray = [];
                                $fileEntryArray = array('fkScheduleId' => $scheduleId, 'fkParameterTypeId' => 5, 'parameterTypeName' => 'ASINS', 'time' => $currentTime, 'date' => $date,
                                    'devServerLink' => $devServerLink,
                                    'apiServerLink' => $apiServerLink,
                                    'isDataFound' => '0',
                                    'isFileDeleted' => '0');
                                $this->_fileDbLogs($fileEntryArray);
                                $cronData['asinsReportStatus'] = '<p>Asins Report : Data not found.</p>';
                                Log::info("filePath:app\Console\Commands\Ams\AmsAdvertisingEmailSchedule, Module Name: Advertising Reports to e-mail. Message :No data found against this schedule in asins daily report.");
                            }
                            break;
                        //monthly start
                        case 'Monthly':
                            echo 'Monthly';
                            $asinsStoredProceduresData = \DB::connection($DB2)->select('call spCalculateMonthlyAsinsSchedulingReport (?,?,?)', $asinParameters);
                            \DB::disconnect($DB2);
                            if (!empty($asinsStoredProceduresData)) {
                                $filterDataArray = [];
                                $filterData = [];
                                foreach ($asinsStoredProceduresData as $orgnalKey => $orgnalvalue) {
                                    $filterKey = strtolower($orgnalKey);
                                    $filterDataArray['brand_id'] = $orgnalvalue->brand_id;
                                    $filterDataArray['brand_name'] = $orgnalvalue->brand_name;
                                    //$filterDataArray['accouint_id'] = $orgnalvalue->accouint_id;
                                    $filterDataArray['account_name'] = $orgnalvalue->account_name;
                                    $filterDataArray['profile_id'] = $orgnalvalue->profile_id;
                                    $filterDataArray['profile_name'] = $orgnalvalue->profile_name;
                                    $filterDataArray['start_date'] = $orgnalvalue->start_date;
                                    $filterDataArray['end_date'] = $orgnalvalue->end_Date;
                                    $filterDataArray['year_'] = $orgnalvalue->year_;
                                    $filterDataArray['campaign_id'] = $orgnalvalue->campaign_id;
                                    $filterDataArray['campaign_name'] = $orgnalvalue->campaign_name;
                                    $filterDataArray['campaign_type'] = $orgnalvalue->campaign_type;
                                    $filterDataArray['adgroupid'] = $orgnalvalue->adGroupId;
                                    $filterDataArray['adgroupname'] = $orgnalvalue->adGroupName;
                                    $filterDataArray['keywordid'] = $orgnalvalue->KeywordId;
                                    $filterDataArray['keywordtext'] = $orgnalvalue->KeywordText;
                                    $filterDataArray['asin'] = $orgnalvalue->asin;
                                    $filterDataArray['other_asin'] = $orgnalvalue->other_asin;
                                    $filterDataArray['sku'] = $orgnalvalue->sku;
                                    $filterDataArray['currency'] = $orgnalvalue->currency;
                                    $filterDataArray['match_type'] = $orgnalvalue->match_type;
                                    $filterDataArray['attributed_units_ordered'] = $orgnalvalue->attributed_units_ordered;
                                    $filterDataArray['sales_other_sku'] = $orgnalvalue->sales_other_sku;
                                    $filterDataArray['units_ordered_other_sku'] = $orgnalvalue->units_ordered_other_sku;
                                    $filterData[] = array_change_key_case($filterDataArray, CASE_LOWER);
                                }
                                $storeNew = [];
                                $matchesColumns = [];
                                foreach ($filterData as $key => $value) {
                                    foreach ($asinsMetricsArray as $key1) {
                                        if (array_key_exists($key1, $value)) {
                                            $matchesColumns[$key1] = $value[$key1];
                                        }
                                    }
                                    array_push($storeNew, $matchesColumns);
                                }
                                $asinsFinalMetrics = array();
                                $asinsFinalMetricsArray = array();
                                foreach ($storeNew as $asinsFinalMetricsKey => $asinsFinalMetricsValue) {
                                    //dd($keywordFinalMetricsValue);
                                    if (isset($asinsFinalMetricsValue['year_'])) {
                                        $asinsFinalMetrics['Year_'] = $asinsFinalMetricsValue['year_'];
                                    }
                                    if (isset($asinsFinalMetricsValue['start_date'])) {
                                        $asinsFinalMetrics['Start Date'] = $asinsFinalMetricsValue['start_date'];
                                    }
                                    if (isset($asinsFinalMetricsValue['end_date'])) {
                                        $asinsFinalMetrics['End Date'] = $asinsFinalMetricsValue['end_date'];
                                    }
                                    //if(isset($asinsFinalMetricsValue['brand_name'])){ $asinsFinalMetrics['Brand Name'] = $asinsFinalMetricsValue['brand_name']; }
                                    //if(isset($asinsFinalMetricsValue['account_name'])){ $asinsFinalMetrics['Account Name'] = $asinsFinalMetricsValue['account_name']; }
                                    //if(isset($asinsFinalMetricsValue['campaign_id'])){ $asinsFinalMetrics['Campaign Id'] = $asinsFinalMetricsValue['campaign_id']; }
                                    if (isset($asinsFinalMetricsValue['campaign_name'])) {
                                        $asinsFinalMetrics['Campaign Name'] = $asinsFinalMetricsValue['campaign_name'];
                                    }
                                    if (isset($asinsFinalMetricsValue['campaign_type'])) {
                                        $asinsFinalMetrics['Campaign Type'] = $asinsFinalMetricsValue['campaign_type'];
                                    }
                                    if (isset($asinsFinalMetricsValue['adgroupid'])) {
                                        $asinsFinalMetrics['Ad Group Id'] = $asinsFinalMetricsValue['adgroupid'];
                                    }
                                    if (isset($asinsFinalMetricsValue['adgroupname'])) {
                                        $asinsFinalMetrics['Ad Group Name'] = $asinsFinalMetricsValue['adgroupname'];
                                    }
                                    if (isset($asinsFinalMetricsValue['keywordid'])) {
                                        $asinsFinalMetrics['Keyword Id'] = $asinsFinalMetricsValue['keywordid'];
                                    }
                                    if (isset($asinsFinalMetricsValue['keywordtext'])) {
                                        $asinsFinalMetrics['Keyword Text'] = $asinsFinalMetricsValue['keywordtext'];
                                    }
                                    if (isset($asinsFinalMetricsValue['asin'])) {
                                        $asinsFinalMetrics['Asin'] = $asinsFinalMetricsValue['asin'];
                                    }
                                    if (isset($asinsFinalMetricsValue['other_asin'])) {
                                        $asinsFinalMetrics['Other Asin'] = $asinsFinalMetricsValue['other_asin'];
                                    }
                                    if (isset($asinsFinalMetricsValue['sku'])) {
                                        $asinsFinalMetrics['Sku'] = $asinsFinalMetricsValue['sku'];
                                    }
                                    if (isset($asinsFinalMetricsValue['currency'])) {
                                        $asinsFinalMetrics['Currency'] = $asinsFinalMetricsValue['currency'];
                                    }
                                    if (isset($asinsFinalMetricsValue['match_type'])) {
                                        $asinsFinalMetrics['Match Type'] = $asinsFinalMetricsValue['match_type'];
                                    }
                                    if (isset($asinsFinalMetricsValue['attributed_units_ordered'])) {
                                        $asinsFinalMetrics['Attributed Units Ordered'] = $asinsFinalMetricsValue['attributed_units_ordered'];
                                    }
                                    if (isset($asinsFinalMetricsValue['sales_other_sku'])) {
                                        $asinsFinalMetrics['Sales Other Sku'] = $asinsFinalMetricsValue['sales_other_sku'];
                                    }
                                    if (isset($asinsFinalMetricsValue['units_ordered_other_sku'])) {
                                        $asinsFinalMetrics['Units Ordered Other Sku'] = $asinsFinalMetricsValue['units_ordered_other_sku'];
                                    }
                                    array_push($asinsFinalMetricsArray, $asinsFinalMetrics);
                                }
                                $fileName = 'Asins-Monthly-' . date('YmdHis') . '-' . $scheduleId . '.xlsx';
                                $fileTempPath = public_path('files/advertisingEmail/Asins-Monthly-' . date('YmdHis') . '-' . $scheduleId . '.xlsx');
                                $this->_generateSoldByCSV($asinsFinalMetricsArray, $fileTempPath);
                                /*$fileNames[] =array('path'=>$fileTempPath,
                                    'name'=>$fileName
                                ) ;*/
                                //$finalDataArray['Asins'] = $asinsFinalMetricsArray;
                                //$cronData['asinsReportStatus'] = '<p>Asins Report : Successfully generated.Check the attachment.</p>';
                                $cronData['asinsReportStatus'] = '<p>Asins Report : <a href="' . $apiServerLink . $fileName . '" download=""> Click Here To Download </a></p>';
                                $completeFilePath = $apiServerLink . $fileName;
                                $fileEntryArray = [];
                                $fileEntryArray = array('fkScheduleId' => $scheduleId, 'fkParameterTypeId' => 5, 'parameterTypeName' => 'ASINS', 'time' => $currentTime, 'date' => $date,
                                    'fileName' => $fileName,
                                    'filePath' => $fileTempPath,
                                    'completeFilePath' => $completeFilePath,
                                    'devServerLink' => $devServerLink,
                                    'apiServerLink' => $apiServerLink,
                                    'isDataFound' => '1',
                                    'isFileDeleted' => '0');
                                $this->_fileDbLogs($fileEntryArray);

                            } else {
                                $fileEntryArray = [];
                                $fileEntryArray = array('fkScheduleId' => $scheduleId, 'fkParameterTypeId' => 5, 'parameterTypeName' => 'ASINS', 'time' => $currentTime, 'date' => $date,
                                    'devServerLink' => $devServerLink,
                                    'apiServerLink' => $apiServerLink,
                                    'isDataFound' => '0',
                                    'isFileDeleted' => '0');
                                $this->_fileDbLogs($fileEntryArray);
                                //$noDATAEamil = $this->_noDataEmailAlert($cronData);
                                $cronData['asinsReportStatus'] = '<p>Asins Report : Data not found.</p>';
                                Log::info("filePath:app\Console\Commands\Ams\AmsAdvertisingEmailSchedule, Module Name: Advertising Reports to e-mail. Message :No data found against this schedule in Asins Monthly report.");
                            }
                            break;
                    }
                    /******** Make arryas and  csv with granularity ends***********/
                } else {
                    echo 'No metrics found for Asins report';
                    Log::info("filePath:app\Console\Commands\Ams\AmsAdvertisingEmailSchedule, Module Name: Advertising Reports to e-mail. Message :No metrics found for Asins report.");
                }
                /*** Get selected metrics against asin parameter type  ends ***/
                /* Send email for all report types starts*/
                /* if (!empty($finalDataArray)){
                 $reportNameWithDashes = preg_replace('/[[:space:]]+/', '-', ucwords($reportName));
                 $fileName = $reportNameWithDashes. '-' .$granularity. '-' . date('YmdHis') . '-' . $scheduleId . '.xlsx';
                 $fileTempPath = public_path('files/advertisingEmail/' . $fileName);
                 $fileNames[] =array('path'=>$fileTempPath,
                     'name'=>$fileName
                 ) ;
                 $this->_generateSoldByCSV($finalDataArray, $fileTempPath);
                 }*/
                if (!empty($managerEmailArray)) {
                    $sendEamil = $this->_sendScheduleReportEmail($fileNames, $cronData);
                }
                /* Send email for all report types ends*/
                /****************** Delete attachements starts ***************************/
                if (!empty($fileNames)) {
                    foreach ($fileNames as $fileInfo) {
                        //echo $fileInfo['path'];
                        if (isset($fileInfo['path'])) {
                            $filePathDelete = $fileInfo['path'];
                            if (File::exists($filePathDelete)) {
                                //File::delete($filePathDelete);
                            }
                        }
                    }
                }
                /****************** Delete attachements starts ***************************/
                //$sendEamil = $this->_sendSoldByEmailAlert($fileTempPath, $fileName, $cronData);
                /* Send email for all report types ends*/
                $updateRunningScheduleStatus = scheduleAdvertisingReports::where('id', $scheduleId)->update(['status' => 0]);
                $updateRunningScheduleStatus = scheduleAdvertisingReports::where('id', $scheduleId)->update(['completedTime' => date('Y-m-d H:i:s')]);
                }
            }//end foreach
        } else {
            Log::info("filePath:app\Console\Commands\Ams\AmsAdvertisingEmailSchedule, Module Name: Advertising Reports to e-mail. Schedule Not Found.");
        } //end if
        /*************** Get schedules to run ends **************************/
    }//end handler
    /*************** Private Functions For Email Scheduling Starts **************************/
    private function _generateSoldByCSV($bbResult, $fileTempPath)
    {
        /*        $sheetsArray = array();
                $sheetsArray = $bbResult;
                $sheets = new SheetCollection($sheetsArray);
               return (new FastExcel($sheetsArray))->export($fileTempPath);*/
        return (new FastExcel($bbResult))->export($fileTempPath);

        //return (new FastExcel(collect($bbResult)))->export($fileTempPath);

        $bbResult = array();

    }//end function Step 3.1

    private function _sendScheduleReportEmail($fileNames, $cronData)
    {
        $email = $cronData['email'];
       $addCC = $cronData['addCC'];
        $reportName = $cronData['reportName'];
        $granularity = $cronData['granularity'];
        $messages = array();
        $messages[0] = "<p>Report name : " . $reportName . "</p>";
        $messages[1] = "<p>Granularity : " . $granularity . "</p>";
        $messages[2] = "<p>Following reports were scheduled : </p>";

        if (isset($cronData['campaignReportStatus'])){
            array_push($messages, $cronData['campaignReportStatus']);
        }
        if (isset($cronData['adGroupReportStatus'])){
            array_push($messages, $cronData['adGroupReportStatus']);
        }
        if (isset($cronData['productAdsReportStatus'])){
            array_push($messages, $cronData['productAdsReportStatus']);
        }
        if (isset($cronData['keywordReportStatus'])){
            array_push($messages, $cronData['keywordReportStatus']);
        }
        if (isset($cronData['asinsReportStatus'])){
            array_push($messages, $cronData['asinsReportStatus']);
        }
        $bodyHTML = ((new BuyBoxEmailAlertMarkdown("Advertising Reports to e-mail", $messages))->render());
        $data = [];
        /*$data["toEmails"] = array(
            $email,
        );*/
        $data["toEmails"] = $email;

        if (!empty($addCC)) {
            $data["cc"] = $addCC;
        }
        $data["subject"] = "Advertising Reports to e-mail";
        $data["bodyHTML"] = $bodyHTML;
        if (!empty($fileNames)) {
            $data["attachments"] = $fileNames;
        }

        /*$data["attachments"] = array(
            array(
                "path" => $fileTempPath,
                "name" => $newFileName
            ),
        );*/
        return SendMailViaPhpMailerLib($data);

    }//end function Step 3.2

    private function _noDataEmailAlert($cronData)
    {
        $email = $cronData['email'];
        $addCC = $cronData['addCC'];
        $reportName = $cronData['reportName'];
        $messages = array();
        $messages[0] = "<p>Report name : " . $reportName . "</p>";
        $messages[1] = "<p>No data found against this report.</p>";
        $bodyHTML = ((new BuyBoxEmailAlertMarkdown("Advertising Reports to e-mail", $messages))->render());

        $data = [];
        $data["toEmails"] = array(
            $email,
        );
        if (!empty($addCC)) {
            $data["cc"] = array(
                $addCC,
            );
        }
        $data["subject"] = "Advertising Reports to e-mail";
        $data["bodyHTML"] = $bodyHTML;

        return SendMailViaPhpMailerLib($data);

    }//end function Step 3.2
    private function roundValue($value)
    {
        $roundedValue = round($value,2);
        return $roundedValue;
    }
    /*param $data
      type array
    */
    private function _fileDbLogs($data){
        if (!empty($data)){
            $addFileDbLogs = amsAdvertisingScheduleFiles::create($data);
        }
    }
    /*************** Private Functions For Email Scheduling Ends **************************/
}
