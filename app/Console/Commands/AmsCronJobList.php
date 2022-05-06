<?php

namespace App\Console\Commands;

use Artisan;
use App\Models\AMSModel;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Log;

class AmsCronJobList extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'amscronjobs:cronlist';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'This command run every minute and check coming ams cron job.';

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

        $currentTimeNow = date('H');
        $currentRunTime = date('Y-m-d H:i:s');
        $CronArrayResponse = AMSModel::getAllEnabledCronList();
        if ($CronArrayResponse != false) {
            // get enable cron lists
            AMSModel::insertTrackRecord('get enable crons list', 'record found');
            foreach ($CronArrayResponse as $singleCron) {
                // create variable CronRun
                $cronRunStaus = $singleCron->cronRun;
                // create variable cronType
                $cronType = $singleCron->cronType;
                // convert cron into hour
                $CronTime = date('H', strtotime($singleCron->cronTime));
                // create variable for last Time Cron
                $CronLastRun = $singleCron->lastRun;
                // check last cron time
                if ($CronLastRun == '0000:00:00 00:00:00') {
                    $lastDateTimeFormat = date('Y-m-d H:i:s', strtotime('-1 day', time()));
                    $CronLastRun = date('Y-m-d H', strtotime($lastDateTimeFormat));
                } else {
                    $CronLastRun = date('Y-m-d H', strtotime($CronLastRun));
                }
                // create variable for Next Time Cron
                $nextRunTime = $singleCron->nextRunTime;
                // check Next Cron Time is not NA
                if ($nextRunTime == '0000:00:00 00:00:00') {
                    $nextRunTime = date('Y-m-d H:i:s', strtotime('+1 day', time()));
                    $nextRunTime = date('Y-m-d H', strtotime($nextRunTime));
                } else {
                    $nextRunTime = date('Y-m-d H', strtotime($nextRunTime));
                    // if next time is greater than last time
                    if ($CronLastRun > $nextRunTime) {
                        $nextRunTime = date('Y-m-d H:i:s', strtotime('+1 day', time()));
                        $nextRunTime = date('Y-m-d H', strtotime($nextRunTime));
                    }
                }
                // currently Retort Status
                $checkReportStatus = \DB::table('tbl_ams_crons')->where('cronType', $singleCron->cronType)->get()->first();
                if(empty($checkReportStatus)){
                    Log::info('tbl_ams_crons table is empty.');
                }
                // check Current system Time equal to Cron Set Time
                // Check Last run cron time less than coming next Cron time
                if ($CronTime == $currentTimeNow && $CronLastRun < $nextRunTime && $checkReportStatus->cronRun == 0) {
                    // tracker code
                    AMSModel::insertTrackRecord('got enabled crons type ' . $cronType, 'record found');
                    // Update Token
                    Artisan::call('getallaccesstoken:amsauth');
                    // call function gathering api data
                    $this->innerFunction($singleCron);
                } elseif ($cronRunStaus == 1 && $CronTime < $currentTimeNow) { // change cronRun status again 0
                    // tracker code
                    AMSModel::insertTrackRecord('change enabled crons type ' . $cronType, 'success');
                    Log::info('start update query for update CronRun status to 0');
                    $updateArray = array(
                        'modifiedDate' => date('Y-m-d H:i:s'),
                        'cronRun' => '0',
                    );
                    AMSModel::updateCronRunStatus($cronType, $updateArray);
                    Log::info('end update query for update CronRun status to 0');
                } else {
                    Log::info('Currently no cron time occur.');
                }
            }// end foreach loop
            Log::info('End foreach loop');
        } else {
            Log::info('not record found');
        }
        Log::info('End Cron for AMS');
    }

    /**
     * This function is used to run until cron status '1'
     *
     * @param $data
     * @return mixed
     */
    private function innerFunction($data)
    {
        Artisan::call('generate:batch');
        $DBArray = \DB::table('tbl_ams_crons')->where('cronType', $data->cronType)->get()->first();
        Log::info($data->cronType);
        Log::info($data->cronRun);
        if ($DBArray->cronRun == 0) {
            // update cron status when it done on time
            $updateArray = array(
                'lastRun' => date('Y-m-d H:i:s'),
                'nextRunTime' => date('Y-m-d H:i:s', strtotime('+1 day', time())),
                'modifiedDate' => date('Y-m-d H:i:s'),
                'cronRun' => '1',
            );
            AMSModel::updateCronRunStatus($DBArray->cronType, $updateArray);
            Log::info('End Update Query');
            // tracker code
            AMSModel::insertTrackRecord('got enabled crons status : 0', 'record found');
            // check new Profile
            Artisan::call('getprofileid:amsprofile');
            // Create variable for Report Type
            $ReportType = $data->cronType;
            Log::info('Start Switch');
            switch ($ReportType) {
                case "Advertising_Campaign_Reports":
                    Log::info('start cron ');
                    Log::info($data->cronType);
                    // First Get Report Id
                    Artisan::call('getcampaignreportid:spcampaign');
                    // Second Get Report Link
                    Artisan::call('getcampaignreportlink:spcampaign');
                    // Third Get Report Data From Link
                    Artisan::call('getcampaignreportlinkdata:spcampaign');
                    // Forth Get bid value of keyword via Campaign
                    // This command is commented as per discussion.
                    //Its not use in bidding rule.And needed to handle large amount of data
                    /*Artisan::call('keywordBid:keywordBidSP');*/
                    Log::info('end cron ');
                    break;
                case "Ad_Group_Reports":
                    Log::info('start cron ');
                    Log::info($data->cronType);
                    // First Get Report Id
                    Artisan::call('getadgroupreportid:spadgroup');
                    // Second Get Report Link
                    Artisan::call('getadgroupreportlink:spadgroup');
                    // Third Get Report Data From Link
                    Artisan::call('getadgroupreportlinkdata:spadgroup');
                    Log::info('end cron ');
                    break;
                case "Keyword_Reports":
                    Log::info('start cron ');
                    Log::info($data->cronType);
                    // First Get Report Id
                    Artisan::call('getkeywordreportid:spkeyword');
                    // Second Get Report Link
                    Artisan::call('getkeywordreportlink:spkeyword');
                    // Third Get Report Data From Link
                    Artisan::call('getkeywordreportlinkdata:spkeyword');
                    Log::info('end cron ');
                    break;
                case "Product_Ads_Report":
                    Log::info('start cron ');
                    Log::info($data->cronType);
                    // First Get Report Id
                    Artisan::call('getproductsadsreportid:productsads');
                    // Second Get Report Link
                    Artisan::call('getproductsadsreportlink:productsads');
                    // Third Get Report Data From Link
                    Artisan::call('getproductsadsreportlinkdata:productsads');
                    Log::info('end cron ');
                    break;
                case "ASINs_Report":
                    Log::info('start cron ');
                    Log::info($data->cronType);
                    // First Get Report Id
                    Artisan::call('getASINreport:asinreport');
                    // Second Get Report Link
                    Artisan::call('getasinreportlink:asinreport');
                    // Third Get Report Data From Link
                    Artisan::call('getasinreportlinkdata:asinreport');
                    Log::info('end cron ');
                    break;
                case "Product_Attribute_Targeting_Reports":
                    Log::info('start cron ');
                    Log::info($data->cronType);
                    // First Get Report Id
                    Artisan::call('gettargetreportid:targets');
                    // Second Get Report Link
                    Artisan::call('gettargetreportlink:targets');
                    // Third Get Report Data From Link
                    Artisan::call('gettargetreportlinkdata:targets');
                    Log::info('end cron ');
                    break;
                case "Sponsored_Brand_Reports": // keyword SB reports
                    Log::info('start cron ');
                    Log::info($data->cronType);
                    // First Get Report Id
                    Artisan::call('getkeywordreportid:sbkeyword');
                    // Second Get Report Link
                    Artisan::call('getkeywordreportlink:sbkeyword');
                    // Third Get Report Data From Link
                    Artisan::call('getkeywordreportlinkdata:sbkeyword');
                    Log::info('end cron ');
                    break;
                case "Sponsored_Brand_Campaigns":
                    Log::info('start cron ');
                    Log::info($data->cronType);
                    // First Get Report Id
                    Artisan::call('getcampaignreportid:sbcampaign');
                    // Second Get Report Link
                    Artisan::call('getcampaignreportlink:sbcampaign');
                    // Third Get Report Data From Link
                    Artisan::call('getcampaignreportlinkdata:sbcampaign');
                    Log::info('end cron ');
                    break;
                case "Sponsored_Display_Campaigns":
                    Log::info('start cron ');
                    Log::info($data->cronType);
                    // First Get Report Id
                    Artisan::call('getcampaignreportid:sdcampaign');
                    // Second Get Report Link
                    Artisan::call('getcampaignreportlink:sdcampaign');
                    // Third Get Report Data From Link
                    Artisan::call('getcampaignreportlinkdata:sdcampaign');
                    Log::info('end cron ');
                    break;
                case "Sponsored_Display_Adgroup":
                    Log::info('start cron ');
                    Log::info($data->cronType);
                    // First Get Report Id
                    Artisan::call('getsdadgroupreportid:sdadgroup');
                    // Second Get Report Link
                    Artisan::call('getsdadgroupreportlink:sdadgroup');
                    // Third Get Report Data From Link
                    Artisan::call('getsdadgroupreportlinkdata:sdadgroup');
                    Log::info('end cron ');
                    break;
                case "Sponsored_Display_ProductAds":
                    Log::info('start cron ');
                    Log::info($data->cronType);
                    // First Get Report Id
                    Artisan::call('getsdproductsadsreportid:sdproductsads');
                    // Second Get Report Link
                    Artisan::call('getsdproductsadsreportlink:sdproductsads');
                    // Third Get Report Data From Link
                    Artisan::call('getsdproductsadsreportlinkdata:sdproductsads');
                    Log::info('end cron ');
                    break;
                case "Sponsored_Brand_Adgroup":
                    Log::info('start cron ');
                    Log::info($data->cronType);
                    // First Get Report Id
                    Artisan::call('getsbadgroupreportid:sbadgroup');
                    // Second Get Report Link
                    Artisan::call('getsbadgroupreportlink:sbadgroup');
                    // Third Get Report Data From Link
                    Artisan::call('getsbadgroupreportlinkdata:sbadgroup');
                    Log::info('end cron ');
                    break;
                case "Sponsored_Brand_Targeting":
                    Log::info('start cron ');
                    Log::info($data->cronType);
                    // First Get Report Id
                    Artisan::call('gettargetreportid:sbtargets');
                    // Second Get Report Link
                    Artisan::call('gettargetreportlink:sbtargets');
                    // Third Get Report Data From Link
                    Artisan::call('gettargetreportlinkdata:sbtargets');
                    Log::info('end cron ');
                    break;
                default:
                    Log::info('Report not selected.');
            }// end switch statement
            Log::info('End Switch');
            Log::info('Start Update Query');
            //return $this->innerFunction($data);
        } else {
            // tracker code
            AMSModel::insertTrackRecord('got enabled crons status : 1', 'record found');
            Log::info('cron status is 1');
        }
    }
}
