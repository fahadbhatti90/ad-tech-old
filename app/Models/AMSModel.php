<?php

namespace App\Models;

use App\Models\AMSApiModel;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Config;
use Illuminate\Database\Eloquent\Model;

/**
 * Class AMSModel
 * @package App\Models
 */
class AMSModel extends Model
{
    protected $tbAmsApi = 'tb_ams_api';
    private $AdgroupReportDwonloadLinks = 'tbl_ams_adgroup_reports_download_links_sp';
    private $targetsReportsDownloadedData = 'tbl_ams_targets_reports_downloaded_data';
    private $s = 'tbl_ams_asin_reports_downloaded_sp';
    private static $_grantType = 'refresh_token';
    private static $tbl_ams_keywordbid = 'tbl_ams_keywordbid';
    private static $tbl_ams_keywordbid_tracker = 'tbl_ams_keywordbid_tracker';
    public $table = "tbl_ams_profiles";

    public function accounts()
    {
        return $this->hasMany('App\Models\AccountModels\AccountModel', 'fkId')->where("fkAccountType", 1);
    }//end function

    /**
     * @param $data
     * @return bool
     * @uses in App\Http\Controllers\AMSController
     */
    public static function addRecord($data)
    {
        $data['grant_type'] = self::$_grantType;
        $data['created_at'] = date('Y-m-d H:i:s');
        $data['updated_at'] = date('Y-m-d H:i:s');
        DB::beginTransaction();
        try {
            $getLastInsertedId = AMSApiModel::insertGetId($data);
            DB::commit();
            return $getLastInsertedId;
        } catch (\Exception $e) {
            \Log::info($e->getMessage());
            DB::rollback();
            return FALSE;
        }
    }

    /**
     * @param $startDate
     * @param $endDate
     * @param $reportType
     * @return array
     */
    public function getDataFromDB($startDate, $endDate, $reportType)
    {
        $response = array();
        $reportStartDate = date_format(date_create($startDate), "Ymd");
        $reportEndDate = date_format(date_create($endDate), "Ymd");
        switch ($reportType) {
            case "Advertising_Campaign_Reports":
                $response = DB::table('tbl_ams_campaigns_reports_downloaded_sp')
                    ->whereBetween('reportDate', [$reportStartDate, $reportEndDate])->get();
                break;
            case "Ad_Group_Reports":
                $response = DB::table('tbl_ams_adgroup_reports_downloaded_data_sp')
                    ->whereBetween('reportDate', [$reportStartDate, $reportEndDate])->get();
                break;
            case "Keyword_Reports":
                $response = DB::table('tbl_ams_keyword_reports_downloaded_data_sp')
                    ->whereBetween('reportDate', [$reportStartDate, $reportEndDate])->get();
                break;
            case "Product_Ads_Report":
                $response = DB::table('tbl_ams_productsads_reports_downloaded_data')
                    ->whereBetween('reportDate', [$reportStartDate, $reportEndDate])->get();
                break;
            case "ASINs_Report":
                $response = DB::table('tbl_ams_asin_reports_downloaded_sp')
                    ->whereBetween('reportDate', [$reportStartDate, $reportEndDate])->get();
                break;
            case "Product_Attribute_Targeting_Reports":
                $response = DB::table($this->targetsReportsDownloadedData)
                    ->whereBetween('reportDate', [$reportStartDate, $reportEndDate])->get();
                break;
            case "Sponsored_Brand_Reports":
                $response = DB::table('tbl_ams_keyword_reports_downloaded_data_sb')
                    ->whereBetween('reportDate', [$reportStartDate, $reportEndDate])->get();
                break;
            case "Sponsored_Brand_Campaigns":
                $response = DB::table('tbl_ams_campaigns_reports_downloaded_sb')
                    ->whereBetween('reportDate', [$reportStartDate, $reportEndDate])->get();
                break;
            case "Sponsored_Display_Campaigns":
                $response = DB::table('tbl_ams_campaigns_reports_downloaded_sd')
                    ->whereBetween('reportDate', [$reportStartDate, $reportEndDate])->get();
                break;
            case "Sponsored_Display_ProductAds":
                $response = DB::table('tbl_ams_productsads_reports_downloaded_data_sd')
                    ->whereBetween('reportDate', [$reportStartDate, $reportEndDate])->get();
                break;
            case "Sponsored_Display_Adgroup":
                $response = DB::table('tbl_ams_adgroup_reports_downloaded_data_sd')
                    ->whereBetween('reportDate', [$reportStartDate, $reportEndDate])->get();
                break;
            case "Sponsored_Brand_Adgroup":
                $response = DB::table('tbl_ams_adgroup_reports_downloaded_data_sb')
                    ->whereBetween('reportDate', [$reportStartDate, $reportEndDate])->get();
                break;
            case "Sponsored_Brand_Targeting":
                $response = DB::table('tbl_ams_targets_reports_downloaded_data_sb')
                    ->whereBetween('reportDate', [$reportStartDate, $reportEndDate])->get();
                break;
            default:
                Log::info('Report not selected.');
        }
        return $response;
    }

    /**
     * @param $startDate
     * @param $endDate
     * @param $reportType
     * @return array
     */
    public function checkDataFromDB($startDate, $endDate, $reportType)
    {
        $response = array();
        $reportStartDate = date_format(date_create($startDate), "Ymd");
        $reportEndDate = date_format(date_create($endDate), "Ymd");
        switch ($reportType) {
            case "Advertising_Campaign_Reports":
                $response = DB::table('tbl_ams_campaigns_reports_downloaded_sp')
                    ->whereBetween('reportDate', [$reportStartDate, $reportEndDate])->get()->first();
                break;
            case "Ad_Group_Reports":
                $response = DB::table('tbl_ams_adgroup_reports_downloaded_data_sp')
                    ->whereBetween('reportDate', [$reportStartDate, $reportEndDate])->get()->first();
                break;
            case "Keyword_Reports":
                $response = DB::table('tbl_ams_keyword_reports_downloaded_data_sp')
                    ->whereBetween('reportDate', [$reportStartDate, $reportEndDate])->get()->first();
                break;
            case "Product_Ads_Report":
                $response = DB::table('tbl_ams_productsads_reports_downloaded_data')
                    ->whereBetween('reportDate', [$reportStartDate, $reportEndDate])->get()->first();
                break;
            case "ASINs_Report":
                $response = DB::table('tbl_ams_asin_reports_downloaded_sp')
                    ->whereBetween('reportDate', [$reportStartDate, $reportEndDate])->get()->first();
                break;
            case "Product_Attribute_Targeting_Reports":
                $response = DB::table($this->targetsReportsDownloadedData)
                    ->whereBetween('reportDate', [$reportStartDate, $reportEndDate])->get()->first();
                break;
            case "Sponsored_Brand_Reports":
                $response = DB::table('tbl_ams_keyword_reports_downloaded_data_sb')
                    ->whereBetween('reportDate', [$reportStartDate, $reportEndDate])->get()->first();
                break;
            case "Sponsored_Brand_Campaigns":
                $response = DB::table('tbl_ams_campaigns_reports_downloaded_sb')
                    ->whereBetween('reportDate', [$reportStartDate, $reportEndDate])->get()->first();
                break;
            case "Sponsored_Display_Campaigns":
                $response = DB::table('tbl_ams_campaigns_reports_downloaded_sd')
                    ->whereBetween('reportDate', [$reportStartDate, $reportEndDate])->get()->first();
                break;
            case "Sponsored_Display_ProductAds":
                $response = DB::table('tbl_ams_productsads_reports_downloaded_data_sd')
                    ->whereBetween('reportDate', [$reportStartDate, $reportEndDate])->get()->first();
                break;
            case "Sponsored_Display_Adgroup":
                $response = DB::table('tbl_ams_adgroup_reports_downloaded_data_sd')
                    ->whereBetween('reportDate', [$reportStartDate, $reportEndDate])->get()->first();
                break;
            case "Sponsored_Brand_Adgroup":
                $response = DB::table('tbl_ams_adgroup_reports_downloaded_data_sb')
                    ->whereBetween('reportDate', [$reportStartDate, $reportEndDate])->get()->first();
                break;
            case "Sponsored_Brand_Targeting":
                $response = DB::table('tbl_ams_targets_reports_downloaded_data_sb')
                    ->whereBetween('reportDate', [$reportStartDate, $reportEndDate])->get()->first();
                break;
            default:
                Log::info('Report not selected.');
        }
        if (!empty($response)) {
            return $response;
        }
        return FALSE;
    }

    /**
     * @return bool
     */
    public function getParameter()
    {
        $record = DB::table($this->tbAmsApi)->get()->first();
        if ($record) {
            return $record;
        }
        return FALSE;
    }

    /**
     * @return bool
     */
    public function getParameterById($fkConfigId)
    {
        $record = DB::table($this->tbAmsApi)->select('id', 'grant_type', 'refresh_token', 'client_id', 'client_secret')->where('id', $fkConfigId)->get()->first();
        if ($record) {
            return $record;
        }
        return FALSE;
    }

    /**
     * @return bool
     */
    public function getParameterAndAuthById($fkConfigId)
    {
        $record = DB::table($this->tbAmsApi)
            ->join('tbl_ams_authtoken', 'tb_ams_api.id', '=', 'tbl_ams_authtoken.fkConfigId')
            ->select('tb_ams_api.id', 'tb_ams_api.grant_type', 'tb_ams_api.refresh_token', 'tb_ams_api.client_id', 'tb_ams_api.client_secret', 'tbl_ams_authtoken.access_token')->where('tb_ams_api.id', $fkConfigId)->get()->first();
        if ($record) {
            return $record;
        }
        return FALSE;
    }

    /**
     * @param $id
     * @param $accountType
     * @param $date
     * @return bool
     */
    public static function getSpecificAccountId($id, $accountType, $date)
    {
        $record = DB::table('tbl_account')->where([
            ['fkId', '=', $id],
            ['fkAccountType', '=', $accountType],
        ])->get()->first();
        if ($record) {
            $todayDate = date('Ymd', strtotime('-1 day', time()));
            $recordBatchID = DB::table('tbl_batch_id')->where([
                ['fkAccountId', '=', $record->id],
                ['reportDate', '=', $date]
            ])->get()->first();
            if ($recordBatchID) {
                return $recordBatchID;
            } else {
                $singleArray = [];
                $singleArray['fkAccountId'] = $record->id;
                $singleArray['batchID'] = $date . $record->id;
                $singleArray['reportDate'] = $date;
                $singleArray["created_at"] = date('Y-m-d H:i:s');
                $singleArray["updated_at"] = date('Y-m-d H:i:s');
                DB::table('tbl_batch_id')->insert($singleArray);
                $recordBatchID = DB::table('tbl_batch_id')->where([
                    ['fkAccountId', '=', $record->id],
                    ['reportDate', '=', $date]
                ])->get()->first();
                if ($recordBatchID) {
                    return $recordBatchID;
                }
            }
            return FALSE;
        }
        return FALSE;
    }

    /**
     * @param $body
     */
    public function addAMSToken($data, $fkConfigId)
    {
        Log::info('Start Insert data into DB.');
        $record = DB::table('tbl_ams_authtoken')->select('id')->where('fkConfigId', $fkConfigId)->get();
        if ($record->isEmpty()) {
            $data['creationDate'] = date('Y-m-d H:i:s');
            try {
                DB::table('tbl_ams_authtoken')->insert($data);
                // store track data
                AMSModel::insertTrackRecord('Authentication Data', 'insert record');
            } catch (\Illuminate\Database\QueryException $ex) {
                Log::error($ex->getMessage());
            }
            Log::info('Insert Access Token.');
        } else {
            Log::info('Start Update Access Token.');
            $data['updationDate'] = date('Y-m-d H:i:s');
            try {
                DB::table('tbl_ams_authtoken')
                    ->where('fkConfigId', $fkConfigId)
                    ->update($data);
            } catch (\Illuminate\Database\QueryException $ex) {
                Log::error($ex->getMessage());
            }
            Log::info('End Update Access Token.');
        }
        Log::info('End Insertion query.');
    }

    /**
     * @param $storeArray
     */
    public function AddProfileRecords($storeArray, $fkConfigId)
    {
        // update all profile status
        DB::table('tbl_ams_profiles')
            ->where('fkConfigId', $fkConfigId)
            ->update(['isActive' => 0]);
        foreach ($storeArray as $row) {
            $existData = DB::table('tbl_ams_profiles')->where('profileId', $row['profileId'])->get();
            if ($existData->isEmpty()) {
                DB::table('tbl_ams_profiles')->Insert($row);
            } else {
                // change profile status
                $data = [];
                $data['isActive'] = 1;
                DB::table('tbl_ams_profiles')
                    ->where('profileId', $row['profileId'])
                    ->update($data);
            }
        }
    }

    /**
     * @param $storeArray
     */
    public static function addSandboxProfiles($storeArray)
    {

        foreach ($storeArray as $row) {
            // update all profile status
            DB::table('tbl_ams_profiles')
                ->where('profileId', $row['profileId'])
                ->update(['isSandboxProfile' => 0]);
            $existData = DB::table('tbl_ams_profiles')->where('profileId', $row['profileId'])->get();

            if ($existData->isEmpty()) {
                DB::table('tbl_ams_profiles')->Insert($row);
            } else {
                // change profile status
                $data = [];
                $data['isSandboxProfile'] = 1;
                DB::table('tbl_ams_profiles')
                    ->where('profileId', $row['profileId'])
                    ->update($data);
            }
        }

    }

    /**
     * @return bool
     * @uses in App\Console\Commands\Ams\Campaign\SP\getReportIdCron
     * @uses in App\Console\Commands\Ams\ASIN\getReportIdCron
     * @uses in App\Console\Commands\Ams\Keyword\SB\getReportIdCron
     * @uses in App\Console\Commands\Ams\Keyword\SP\getReportIdCron
     * @uses in App\Console\Commands\Ams\ProductsAds\getReportIdCron
     * @uses in App\Console\Commands\Ams\Target\getReportIdCron
     * @uses in App\Console\Commands\Ams\AdGroup\SP\getReportIdCron
     */
    public function getAMSToken()
    {
        $record = DB::table('tbl_ams_authtoken')->get()->first();

        if ($record) {
            return $record;
        }
        return FALSE;
    }

    /**
     * @return bool
     * @uses in App\Console\Commands\Ams\Campaign\SP\getReportIdCron
     * @uses in App\Console\Commands\Ams\ASIN\getReportIdCron
     * @uses in App\Console\Commands\Ams\Keyword\SB\getReportIdCron
     * @uses in App\Console\Commands\Ams\Keyword\SP\getReportIdCron
     * @uses in App\Console\Commands\Ams\ProductsAds\getReportIdCron
     * @uses in App\Console\Commands\Ams\Target\getReportIdCron
     * @uses in App\Console\Commands\Ams\AdGroup\SP\getReportIdCron
     */
    public function getAMSTokenById($fkConfigId)
    {
        $record = DB::table('tbl_ams_authtoken')->select('id', 'fkConfigId', 'access_token')->where('fkConfigId', $fkConfigId)->get()->first();

        if ($record) {
            return $record;
        }
        return FALSE;
    }

    /**
     * @return bool
     * @uses in App\Console\Commands\Ams\Campaign\SP\getReportIdCron
     * @uses in App\Console\Commands\Ams\ASIN\getReportIdCron
     * @uses in App\Console\Commands\Ams\Keyword\SB\getReportIdCron
     * @uses in App\Console\Commands\Ams\Keyword\SP\getReportIdCron
     * @uses in App\Console\Commands\Ams\ProductsAds\getReportIdCron
     * @uses in App\Console\Commands\Ams\Target\getReportIdCron
     * @uses in App\Console\Commands\Ams\AdGroup\SP\getReportIdCron
     */
    public function getAllAmsApiCreds()
    {
        $record = DB::table('tb_ams_api')
            ->join('tbl_ams_authtoken', 'tb_ams_api.id', '=', 'tbl_ams_authtoken.fkConfigId')
            ->select('tb_ams_api.id', 'tb_ams_api.grant_type', 'tb_ams_api.refresh_token', 'tb_ams_api.client_id', 'tb_ams_api.client_secret', 'tbl_ams_authtoken.access_token')->get();
        if ($record) {
            return $record;
        }
        return FALSE;
    }

    /**
     * @return bool
     * @uses in Commands\Ams\Auth\getAllAuth
     */
    public function getAllAmsParameters()
    {
        $record = DB::table('tb_ams_api')
            ->select('tb_ams_api.id')->get();
        if ($record) {
            return $record;
        }
        return FALSE;
    }

    /**
     * @param $storeArray
     * @uses in App\Console\Commands\Ams\Campaign\SP\getReportIdCron
     * @uses in App\Console\Commands\Ams\ASIN\getReportIdCron
     * @uses in App\Console\Commands\Ams\Keyword\SB\getReportIdCron
     * @uses in App\Console\Commands\Ams\Keyword\SP\getReportIdCron
     * @uses in App\Console\Commands\Ams\ProductsAds\getReportIdCron
     * @uses in App\Console\Commands\Ams\ProductsAds\SD\getReportIdCron
     * @uses in App\Console\Commands\Ams\Target\getReportIdCron
     * @uses in App\Console\Commands\Ams\AdGroup\SP\getReportIdCron
     * @uses in App\Console\Commands\Ams\AdGroup\SD\getReportIdCron
     */
    public function addReportId($storeArray)
    {
        Log::info('Start Insert data into DB.');
        DB::transaction(function () use ($storeArray) {
            foreach ($storeArray as $row) {
                $existData = DB::table('tbl_ams_report_id')->where([
                    ['profileID', $row['profileID']],
                    ['reportType', $row['reportType']],
                    ['reportDate', $row['reportDate']]
                ])->get();
                if ($existData->isEmpty()) {
                    try {
                        DB::table('tbl_ams_report_id')->insert($row);
                        // store report status
                        AMSModel::insertTrackRecord($row['reportType'], 'record stored');
                    } catch (\Illuminate\Database\QueryException $ex) {
                        Log::error($ex->getMessage());
                    }
                } else {
                    Log::info('Already exist in DB. ProfileId :' . $row['profileID'] . ' reportType:' . $row['reportType'] . ' reportDate' . $row['reportDate']);
                }
            }
        }, 3);
        Log::info('End Insertion query.');
    }

    /**
     * @return bool
     * @uses in App\Console\Commands\AmsCronJobList
     */
    public static function getAllEnabledCronList()
    {
        $response = DB::table('tbl_ams_crons')
            ->where(['cronStatus' => 'run'])
            ->get();
        if (!$response->isEmpty()) {
            return $response;
        }
        return FALSE;
    }

    /**
     * @param $cronType
     * @param $updateArray
     * @uses in App\Console\Commands\AmsCronJobList
     */
    public static function updateCronRunStatus($cronType, $updateArray)
    {
        Log::info('AMS Model file methods name : updateCronRunStatus.');
        // tracker code
        AMSModel::insertTrackRecord('change enable crons status : 0', 'record found');
        DB::table('tbl_ams_crons')
            ->where('cronType', $cronType)
            ->update($updateArray);
        Log::info('End AMS Model file methods name : updateCronRunStatus.');
    }

    /**
     * @param $DataArray
     */
    public function AddHSACampaigns($DataArray)
    {
        for ($i = 0; $i < count($DataArray); $i++) {
            for ($j = 0; $j < count($DataArray[$i]); $j++) {
                DB::table('tbl_ams_campaignhsa')
                    ->Insert($DataArray[$i][$j]);
            }
        }
    }

    /**
     * @return mixed
     */
    public function getAllProfiles()
    {
        return DB::table('tbl_ams_profiles')
            ->join('tb_ams_api', 'tbl_ams_profiles.fkConfigId', '=', 'tb_ams_api.id')
            ->join('tbl_ams_authtoken', 'tb_ams_api.id', '=', 'tbl_ams_authtoken.fkConfigId')
            ->select('tbl_ams_profiles.id', 'tbl_ams_profiles.fkConfigId', 'tbl_ams_profiles.profileId', 'tbl_ams_profiles.countryCode', 'tbl_ams_profiles.currencyCode', 'tbl_ams_profiles.timezone', 'tbl_ams_profiles.marketplaceStringId', 'tbl_ams_profiles.entityId', 'tbl_ams_profiles.type', 'tbl_ams_profiles.name', 'tbl_ams_profiles.adGroupSpSixtyDays', 'tbl_ams_profiles.aSINsSixtyDays', 'tbl_ams_profiles.campaignSpSixtyDays', 'tbl_ams_profiles.keywordSbSixtyDays', 'tbl_ams_profiles.productAdsSixtyDays', 'tbl_ams_profiles.productTargetingSixtyDays', 'tbl_ams_profiles.creationDate', 'tbl_ams_profiles.SponsoredBrandCampaignsSixtyDays', 'tbl_ams_profiles.SponsoredDisplayCampaignsSixtyDays', 'tbl_ams_profiles.SponsoredDisplayProductAdsSixtyDays', 'tbl_ams_profiles.SponsoredDisplayAdgroupSixtyDays', 'tbl_ams_profiles.isActive', 'tbl_ams_profiles.isSandboxProfile', 'tbl_ams_profiles.SponsoredBrandAdgroupSixtyDays', 'tbl_ams_profiles.SponsoredBrandTargetingSixtyDays', 'tbl_ams_profiles.keywordSpSixtyDays', 'tb_ams_api.client_id', 'tbl_ams_authtoken.access_token')
            ->where('isActive', 1)
            ->where('type', '!=', 'agency')
            ->get();
    }

    /**
     * @return mixed
     */
    public function getAllHistoricalProfiles($profileArray = array())
    {

        if (empty($profileArray)) {
            return DB::table('tbl_ams_profiles')
                ->where('isActive', 1)
                ->where('type', '!=', 'agency')
                ->get();
        }
        return DB::table('tbl_ams_profiles')
            ->join('tb_ams_api', 'tbl_ams_profiles.fkConfigId', '=', 'tb_ams_api.id')
            ->join('tbl_ams_authtoken', 'tb_ams_api.id', '=', 'tbl_ams_authtoken.fkConfigId')
            ->select('tbl_ams_profiles.id', 'tbl_ams_profiles.fkConfigId', 'tbl_ams_profiles.profileId', 'tbl_ams_profiles.countryCode', 'tbl_ams_profiles.currencyCode', 'tbl_ams_profiles.timezone', 'tbl_ams_profiles.marketplaceStringId', 'tbl_ams_profiles.entityId', 'tbl_ams_profiles.type', 'tbl_ams_profiles.name', 'tbl_ams_profiles.adGroupSpSixtyDays', 'tbl_ams_profiles.aSINsSixtyDays', 'tbl_ams_profiles.campaignSpSixtyDays', 'tbl_ams_profiles.keywordSbSixtyDays', 'tbl_ams_profiles.productAdsSixtyDays', 'tbl_ams_profiles.productTargetingSixtyDays', 'tbl_ams_profiles.creationDate', 'tbl_ams_profiles.SponsoredBrandCampaignsSixtyDays', 'tbl_ams_profiles.SponsoredDisplayCampaignsSixtyDays', 'tbl_ams_profiles.SponsoredDisplayProductAdsSixtyDays', 'tbl_ams_profiles.SponsoredDisplayAdgroupSixtyDays', 'tbl_ams_profiles.isActive', 'tbl_ams_profiles.isSandboxProfile', 'tbl_ams_profiles.SponsoredBrandAdgroupSixtyDays', 'tbl_ams_profiles.SponsoredBrandTargetingSixtyDays', 'tbl_ams_profiles.keywordSpSixtyDays', 'tb_ams_api.client_id', 'tbl_ams_authtoken.access_token')
            ->where('tbl_ams_profiles.id', $profileArray['0'])
            ->where('tbl_ams_profiles.profileId', $profileArray['1'])
            ->get();
    }

    /**
     * @return mixed
     */
    public function getAllSandboxProfiles()
    {
        return DB::table('tbl_ams_profiles')
            ->join('tb_ams_api', 'tbl_ams_profiles.fkConfigId', '=', 'tb_ams_api.id')
            ->join('tbl_ams_authtoken', 'tb_ams_api.id', '=', 'tbl_ams_authtoken.fkConfigId')
            ->select('tbl_ams_profiles.id', 'tbl_ams_profiles.fkConfigId', 'tbl_ams_profiles.profileId', 'tbl_ams_profiles.countryCode', 'tbl_ams_profiles.currencyCode', 'tbl_ams_profiles.timezone', 'tbl_ams_profiles.marketplaceStringId', 'tbl_ams_profiles.entityId', 'tbl_ams_profiles.type', 'tbl_ams_profiles.name', 'tbl_ams_profiles.adGroupSpSixtyDays', 'tbl_ams_profiles.aSINsSixtyDays', 'tbl_ams_profiles.campaignSpSixtyDays', 'tbl_ams_profiles.keywordSbSixtyDays', 'tbl_ams_profiles.productAdsSixtyDays', 'tbl_ams_profiles.productTargetingSixtyDays', 'tbl_ams_profiles.creationDate', 'tbl_ams_profiles.SponsoredBrandCampaignsSixtyDays', 'tbl_ams_profiles.SponsoredDisplayCampaignsSixtyDays', 'tbl_ams_profiles.SponsoredDisplayProductAdsSixtyDays', 'tbl_ams_profiles.SponsoredDisplayAdgroupSixtyDays', 'tbl_ams_profiles.isActive', 'tbl_ams_profiles.isSandboxProfile', 'tbl_ams_profiles.SponsoredBrandAdgroupSixtyDays', 'tbl_ams_profiles.SponsoredBrandTargetingSixtyDays', 'tbl_ams_profiles.keywordSpSixtyDays', 'tb_ams_api.client_id', 'tbl_ams_authtoken.access_token')
            ->where('isSandboxProfile', 1)
            ->get();
    }

    /**
     * @param $profileId
     * @param $status
     */
    public static function UpdateProfileSixtyStatus($profileId, $type, $status)
    {
        DB::transaction(function () use ($profileId, $type, $status) {
            DB::table('tbl_ams_profiles')
                ->where(['profileId' => $profileId])
                ->where('isActive', 1)
                ->update([$type => $status]);
        }, 3);
    }

    /**
     * @return mixed
     */
    public function getAllReportID($type)
    {
        if ($type != null) {
            return DB::table('tbl_ams_report_id')
                ->join('tbl_ams_profiles', 'tbl_ams_report_id.profileID', '=', 'tbl_ams_profiles.id')
                ->join('tb_ams_api', 'tbl_ams_report_id.fkConfigId', '=', 'tb_ams_api.id')
                ->select('tbl_ams_report_id.id', 'tbl_ams_report_id.fkBatchId as fkBatchId', 'tbl_ams_report_id.fkAccountId as fkAccountId', 'tbl_ams_report_id.reportId', 'tbl_ams_report_id.reportType', 'tbl_ams_profiles.profileId', 'tbl_ams_profiles.id as amsID', 'tbl_ams_report_id.reportDate AS amsReportDate', 'tb_ams_api.client_id', 'tbl_ams_profiles.fkConfigId')
                ->where('tbl_ams_report_id.reportType', $type)
                ->where('tbl_ams_report_id.reportDate', date('Ymd', strtotime('-1 day', time())))
                ->where('tbl_ams_report_id.isDone', 0)
                ->where('tbl_ams_profiles.isActive', 1)
                ->get();
        }
    }

    /**
     * @return mixed
     */
    public function getSbKeywordDownloadLink()
    {
        $ReportDate = date('Ymd', strtotime('-1 day', time()));
        return DB::table('tbl_ams_keyword_reports_download_links_sb')
            ->join('tb_ams_api', 'tbl_ams_keyword_reports_download_links_sb.fkConfigId', '=', 'tb_ams_api.id')
            ->select('tbl_ams_keyword_reports_download_links_sb.id', 'tbl_ams_keyword_reports_download_links_sb.fkBatchId', 'tbl_ams_keyword_reports_download_links_sb.fkAccountId', 'tbl_ams_keyword_reports_download_links_sb.profileID', 'tbl_ams_keyword_reports_download_links_sb.reportId', 'tbl_ams_keyword_reports_download_links_sb.status', 'tbl_ams_keyword_reports_download_links_sb.statusDetails', 'tbl_ams_keyword_reports_download_links_sb.location', 'tbl_ams_keyword_reports_download_links_sb.fileSize', 'tbl_ams_keyword_reports_download_links_sb.reportDate', 'tbl_ams_keyword_reports_download_links_sb.creationDate', 'tbl_ams_keyword_reports_download_links_sb.isDone', 'tbl_ams_keyword_reports_download_links_sb.fkConfigId', 'tb_ams_api.client_id')
            ->where([
                //  ['reportDate', $ReportDate],
                ['status', '=', 'SUCCESS'],
                ['isDone', '=', 0],
            ])->get();
    }

    /**
     * @return mixed
     */
    public static function UpdateSbKeywordStatus($ReportId, $numberRecords)
    {
        DB::table('tbl_ams_keyword_reports_download_links_sb')
            ->where(['id' => $ReportId])
            ->update(['isDone' => 1]);
    }

    /**
     * @return mixed
     */
    public static function getSpASINDownloadLink()
    {
        $ReportDate = date('Ymd', strtotime('-1 day', time()));
        return DB::table('tbl_ams_asin_reports_download_links')
            ->join('tb_ams_api', 'tbl_ams_asin_reports_download_links.fkConfigId', '=', 'tb_ams_api.id')
            ->select('tbl_ams_asin_reports_download_links.id', 'tbl_ams_asin_reports_download_links.fkBatchId', 'tbl_ams_asin_reports_download_links.fkAccountId', 'tbl_ams_asin_reports_download_links.profileID', 'tbl_ams_asin_reports_download_links.reportId', 'tbl_ams_asin_reports_download_links.status', 'tbl_ams_asin_reports_download_links.statusDetails', 'tbl_ams_asin_reports_download_links.location', 'tbl_ams_asin_reports_download_links.fileSize', 'tbl_ams_asin_reports_download_links.reportDate', 'tbl_ams_asin_reports_download_links.creationDate', 'tbl_ams_asin_reports_download_links.isDone', 'tbl_ams_asin_reports_download_links.fkConfigId', 'tb_ams_api.client_id')
            ->where([
                ['status', '=', 'SUCCESS'],
                ['isDone', '=', 0],
            ])->get();
    }

    /**
     * @return mixed
     */
    public static function UpdateSpASINStatus($ReportId, $numberRecords)
    {
        DB::table('tbl_ams_asin_reports_download_links')
            ->where(['id' => $ReportId])
            ->update(['isDone' => 1]);
    }

    /**
     * @return mixed
     */
    public function getSpKeywordDownloadLink()
    {
        $ReportDate = date('Ymd', strtotime('-1 day', time()));
        return DB::table('tbl_ams_keyword_reports_download_links_sp')
            ->join('tb_ams_api', 'tbl_ams_keyword_reports_download_links_sp.fkConfigId', '=', 'tb_ams_api.id')
            ->select('tbl_ams_keyword_reports_download_links_sp.id', 'tbl_ams_keyword_reports_download_links_sp.fkBatchId', 'tbl_ams_keyword_reports_download_links_sp.fkAccountId', 'tbl_ams_keyword_reports_download_links_sp.profileID', 'tbl_ams_keyword_reports_download_links_sp.reportId', 'tbl_ams_keyword_reports_download_links_sp.status', 'tbl_ams_keyword_reports_download_links_sp.statusDetails', 'tbl_ams_keyword_reports_download_links_sp.location', 'tbl_ams_keyword_reports_download_links_sp.fileSize', 'tbl_ams_keyword_reports_download_links_sp.reportDate', 'tbl_ams_keyword_reports_download_links_sp.creationDate', 'tbl_ams_keyword_reports_download_links_sp.isDone', 'tbl_ams_keyword_reports_download_links_sp.fkConfigId', 'tb_ams_api.client_id')
            ->where([
                //['reportDate', $ReportDate],
                ['status', '=', 'SUCCESS'],
                ['isDone', '=', 0],
            ])->get();
    }

    /**
     * @return mixed
     */
    public static function UpdateSpKeywordStatus($ReportId, $numberRecords)
    {
        DB::table('tbl_ams_keyword_reports_download_links_sp')
            ->where(['id' => $ReportId])
            ->update(['isDone' => 1]);
    }

    /**
     * @return mixed
     */
    public static function getSpTargetsDownloadLink()
    {
        $ReportDate = date('Ymd', strtotime('-1 day', time()));
        return DB::table('tbl_ams_targets_reports_download_links')
            ->join('tb_ams_api', 'tbl_ams_targets_reports_download_links.fkConfigId', '=', 'tb_ams_api.id')
            ->select('tbl_ams_targets_reports_download_links.id', 'tbl_ams_targets_reports_download_links.fkBatchId', 'tbl_ams_targets_reports_download_links.fkAccountId', 'tbl_ams_targets_reports_download_links.profileID', 'tbl_ams_targets_reports_download_links.reportId', 'tbl_ams_targets_reports_download_links.status', 'tbl_ams_targets_reports_download_links.statusDetails', 'tbl_ams_targets_reports_download_links.location', 'tbl_ams_targets_reports_download_links.fileSize', 'tbl_ams_targets_reports_download_links.reportDate', 'tbl_ams_targets_reports_download_links.creationDate', 'tbl_ams_targets_reports_download_links.isDone', 'tbl_ams_targets_reports_download_links.fkConfigId', 'tb_ams_api.client_id')
            ->where([
                ['status', '=', 'SUCCESS'],
                ['isDone', '=', 0],
            ])->get();
    }

    /**
     * @return mixed
     */
    public static function getSBTargetsDownloadLink()
    {
        $ReportDate = date('Ymd', strtotime('-1 day', time()));
        return DB::table('tbl_ams_targets_reports_download_links_sb')
            ->join('tb_ams_api', 'tbl_ams_targets_reports_download_links_sb.fkConfigId', '=', 'tb_ams_api.id')
            ->select('tbl_ams_targets_reports_download_links_sb.id', 'tbl_ams_targets_reports_download_links_sb.fkBatchId', 'tbl_ams_targets_reports_download_links_sb.fkAccountId', 'tbl_ams_targets_reports_download_links_sb.profileID', 'tbl_ams_targets_reports_download_links_sb.reportId', 'tbl_ams_targets_reports_download_links_sb.status', 'tbl_ams_targets_reports_download_links_sb.statusDetails', 'tbl_ams_targets_reports_download_links_sb.location', 'tbl_ams_targets_reports_download_links_sb.fileSize', 'tbl_ams_targets_reports_download_links_sb.reportDate', 'tbl_ams_targets_reports_download_links_sb.creationDate', 'tbl_ams_targets_reports_download_links_sb.isDone', 'tbl_ams_targets_reports_download_links_sb.fkConfigId', 'tb_ams_api.client_id')
            ->where([
                ['status', '=', 'SUCCESS'],
                ['isDone', '=', 0],
            ])->get();
    }

    /**
     * @return mixed
     */
    public function UpdateSpTargetsStatus($ReportId, $numberRecords)
    {
        DB::table('tbl_ams_targets_reports_download_links')
            ->where(['id' => $ReportId])
            ->update(['isDone' => 1]);
    }

    /**
     * @return mixed
     */
    public function UpdateSbTargetsStatus($ReportId, $numberRecords)
    {
        DB::table('tbl_ams_targets_reports_download_links_sb')
            ->where(['id' => $ReportId])
            ->update(['isDone' => 1]);
    }

    /**
     * @return mixed
     * @uses  App\Console\Commands\Ams\ProductsAds\SD\getReportLinkDataCron
     */
    public function getSDProductsAdsDownloadLink()
    {
        $ReportDate = date('Ymd', strtotime('-1 day', time()));
        return DB::table('tbl_ams_productsads_reports_download_links_sd')
            ->join('tb_ams_api', 'tbl_ams_productsads_reports_download_links_sd.fkConfigId', '=', 'tb_ams_api.id')
            ->select('tbl_ams_productsads_reports_download_links_sd.id', 'tbl_ams_productsads_reports_download_links_sd.fkBatchId', 'tbl_ams_productsads_reports_download_links_sd.fkAccountId', 'tbl_ams_productsads_reports_download_links_sd.profileID', 'tbl_ams_productsads_reports_download_links_sd.reportId', 'tbl_ams_productsads_reports_download_links_sd.status', 'tbl_ams_productsads_reports_download_links_sd.statusDetails', 'tbl_ams_productsads_reports_download_links_sd.location', 'tbl_ams_productsads_reports_download_links_sd.fileSize', 'tbl_ams_productsads_reports_download_links_sd.reportDate', 'tbl_ams_productsads_reports_download_links_sd.creationDate', 'tbl_ams_productsads_reports_download_links_sd.isDone', 'tbl_ams_productsads_reports_download_links_sd.fkConfigId', 'tb_ams_api.client_id')
            ->where([
                ['status', '=', 'SUCCESS'],
                ['isDone', '=', 0],
            ])->get();
    }

    /**
     * @return mixed
     */
    public function getSpProductsAdsDownloadLink()
    {
        $ReportDate = date('Ymd', strtotime('-1 day', time()));
        return DB::table('tbl_ams_productsads_reports_download_links')
            ->join('tb_ams_api', 'tbl_ams_productsads_reports_download_links.fkConfigId', '=', 'tb_ams_api.id')
            ->select('tbl_ams_productsads_reports_download_links.id', 'tbl_ams_productsads_reports_download_links.fkBatchId', 'tbl_ams_productsads_reports_download_links.fkAccountId', 'tbl_ams_productsads_reports_download_links.profileID', 'tbl_ams_productsads_reports_download_links.reportId', 'tbl_ams_productsads_reports_download_links.status', 'tbl_ams_productsads_reports_download_links.statusDetails', 'tbl_ams_productsads_reports_download_links.location', 'tbl_ams_productsads_reports_download_links.fileSize', 'tbl_ams_productsads_reports_download_links.reportDate', 'tbl_ams_productsads_reports_download_links.creationDate', 'tbl_ams_productsads_reports_download_links.isDone', 'tbl_ams_productsads_reports_download_links.fkConfigId', 'tb_ams_api.client_id')
            ->where([
                ['status', '=', 'SUCCESS'],
                ['isDone', '=', 0],
            ])->get();
    }

    /**
     * @return mixed
     * * @uses  App\Console\Commands\Ams\ProductsAds\SD\getReportLinkDataCron
     */
    public function UpdateSDProductsAdsStatus($ReportId, $numberRecords)
    {
        DB::table('tbl_ams_productsads_reports_download_links_sd')
            ->where(['id' => $ReportId])
            ->update(['isDone' => 1]);
    }

    /**
     * @return mixed
     */
    public function UpdateSpProductsAdsStatus($ReportId, $numberRecords)
    {
        DB::table('tbl_ams_productsads_reports_download_links')
            ->where(['id' => $ReportId])
            ->update(['isDone' => 1]);
    }

    /**
     * @return mixed
     */
    public function getSpAdGroupDownloadLink()
    {
        return DB::table('tbl_ams_adgroup_reports_download_links_sp')
            ->join('tb_ams_api', 'tbl_ams_adgroup_reports_download_links_sp.fkConfigId', '=', 'tb_ams_api.id')
            ->select("tbl_ams_adgroup_reports_download_links_sp.id", "tbl_ams_adgroup_reports_download_links_sp.fkBatchId", "tbl_ams_adgroup_reports_download_links_sp.fkAccountId", "tbl_ams_adgroup_reports_download_links_sp.profileID", "tbl_ams_adgroup_reports_download_links_sp.fkConfigId", "tbl_ams_adgroup_reports_download_links_sp.reportId", "tbl_ams_adgroup_reports_download_links_sp.status", "tbl_ams_adgroup_reports_download_links_sp.statusDetails", "tbl_ams_adgroup_reports_download_links_sp.location", "tbl_ams_adgroup_reports_download_links_sp.fileSize", "tbl_ams_adgroup_reports_download_links_sp.reportDate", "tbl_ams_adgroup_reports_download_links_sp.creationDate", "isDone", 'tb_ams_api.client_id')
            ->where([
                ['status', '=', 'SUCCESS'],
                ['isDone', '=', 0],
            ])->get();
    }

    /**
     * @return mixed
     */
    public function getSBAdGroupDownloadLink()
    {
        //$ReportDate = date('Ymd', strtotime('-1 day', time()));
        return DB::table('tbl_ams_adgroup_reports_download_links_sb')
            ->join('tb_ams_api', 'tbl_ams_adgroup_reports_download_links_sb.fkConfigId', '=', 'tb_ams_api.id')
            ->select('tbl_ams_adgroup_reports_download_links_sb.id', 'tbl_ams_adgroup_reports_download_links_sb.fkBatchId', 'tbl_ams_adgroup_reports_download_links_sb.fkAccountId', 'tbl_ams_adgroup_reports_download_links_sb.profileID', 'tbl_ams_adgroup_reports_download_links_sb.reportId', 'tbl_ams_adgroup_reports_download_links_sb.status', 'tbl_ams_adgroup_reports_download_links_sb.statusDetails', 'tbl_ams_adgroup_reports_download_links_sb.location', 'tbl_ams_adgroup_reports_download_links_sb.fileSize', 'tbl_ams_adgroup_reports_download_links_sb.reportDate', 'tbl_ams_adgroup_reports_download_links_sb.creationDate', 'tbl_ams_adgroup_reports_download_links_sb.isDone', 'tbl_ams_adgroup_reports_download_links_sb.fkConfigId', 'tb_ams_api.client_id')
            ->where([
                //['reportDate', $ReportDate],
                ['status', '=', 'SUCCESS'],
                ['isDone', '=', 0],
            ])->get();
    }

    /**
     * @return mixed
     * @uses  App\Console\Commands\Ams\AdGroup\SD\getReportLinkDataCron
     */
    public function getSDAdGroupDownloadLink()
    {
        return DB::table('tbl_ams_adgroup_reports_download_links_sd')
            ->join('tb_ams_api', 'tbl_ams_adgroup_reports_download_links_sd.fkConfigId', '=', 'tb_ams_api.id')
            ->select('tbl_ams_adgroup_reports_download_links_sd.id', 'tbl_ams_adgroup_reports_download_links_sd.fkBatchId', 'tbl_ams_adgroup_reports_download_links_sd.fkAccountId', 'tbl_ams_adgroup_reports_download_links_sd.profileID', 'tbl_ams_adgroup_reports_download_links_sd.reportId', 'tbl_ams_adgroup_reports_download_links_sd.status', 'tbl_ams_adgroup_reports_download_links_sd.statusDetails', 'tbl_ams_adgroup_reports_download_links_sd.location', 'tbl_ams_adgroup_reports_download_links_sd.fileSize', 'tbl_ams_adgroup_reports_download_links_sd.reportDate', 'tbl_ams_adgroup_reports_download_links_sd.creationDate', 'tbl_ams_adgroup_reports_download_links_sd.isDone', 'tbl_ams_adgroup_reports_download_links_sd.fkConfigId', 'tb_ams_api.client_id')
            ->where([
                ['status', '=', 'SUCCESS'],
                ['isDone', '=', 0],
            ])->get();
    }

    /**
     * @return mixed
     */
    public static function UpdateSpAdGroupStatus($ReportId, $numberRecords)
    {
        DB::table('tbl_ams_adgroup_reports_download_links_sp')
            ->where(['id' => $ReportId])
            ->update(['isDone' => 1]);
    }

    /**
     * @return mixed
     */
    public static function UpdateSBAdGroupStatus($ReportId, $numberRecords)
    {
        DB::table('tbl_ams_adgroup_reports_download_links_sb')
            ->where(['id' => $ReportId])
            ->update(['isDone' => 1]);
    }

    /**
     * @return mixed
     *  * @uses  App\Console\Commands\Ams\AdGroup\SD\getReportLinkDataCron
     */
    public static function UpdateSDAdGroupStatus($ReportId, $numberRecords)
    {
        DB::table('tbl_ams_adgroup_reports_download_links_sd')
            ->where(['id' => $ReportId])
            ->update(['isDone' => 1]);
    }

    /**
     * @param $storeArray
     */
    public function addcampaignReportLocation($storeArray)
    {
        Log::info('Start Insert data into DB.');
        DB::transaction(function () use ($storeArray) {
            //foreach ($storeArray as $row) {
            $existData = DB::table('tbl_ams_campaigns_reports_download_links_sp')->where([
                'profileID' => $storeArray['profileID'],
                'fileSize' => $storeArray['fileSize'],
                'reportDate' => $storeArray['reportDate']
            ])->get();
            if ($existData->isEmpty()) {
                try {
                    $id = DB::table('tbl_ams_campaigns_reports_download_links_sp')->insertGetId($storeArray);
                    // store report status
                    AMSModel::insertTrackRecord('Campaign SP Report Link', 'record stored');
                    Log::info('Insert Record id = ' . $id);
                } catch (\Illuminate\Database\QueryException $ex) {
                    Log::error($ex->getMessage());
                }
            } else {
                Log::info('Already exist in DB. ProfileId :' . $storeArray['profileID'] . ' reportID:' . $storeArray['reportId'] . ' reportDate' . $storeArray['reportDate']);
            }
            //}
        }, 3);
        Log::info('End Insertion query.');
    }

    /**
     * @param $storeArray
     */
    public function addcampaignReportLocationSB($storeArray)
    {
        Log::info('Start Insert data into DB.');
        DB::transaction(function () use ($storeArray) {
            //foreach ($storeArray as $row) {
            $existData = DB::table('tbl_ams_campaigns_reports_download_links_sb')->where([
                'profileID' => $storeArray['profileID'],
                'fileSize' => $storeArray['fileSize'],
                'reportDate' => $storeArray['reportDate']
            ])->get();
            if ($existData->isEmpty()) {
                try {
                    $id = DB::table('tbl_ams_campaigns_reports_download_links_sb')->insertGetId($storeArray);
                    // store report status
                    AMSModel::insertTrackRecord('Campaign SB Report Link', 'record stored');
                    Log::info('Insert Record id = ' . $id);
                } catch (\Illuminate\Database\QueryException $ex) {
                    Log::error($ex->getMessage());
                }
            } else {
                Log::info('Already exist in DB. ProfileId :' . $storeArray['profileID'] . ' reportID:' . $storeArray['reportId'] . ' reportDate' . $storeArray['reportDate']);
            }
            //}
        }, 3);
        Log::info('End Insertion query.');
    }

    /**
     * @param $storeArray
     */
    public function addcampaignReportLocationSD($storeArray)
    {
        Log::info('Start Insert data into DB.');
        DB::transaction(function () use ($storeArray) {
            //foreach ($storeArray as $row) {
            $existData = DB::table('tbl_ams_campaigns_reports_download_links_sd')->where([
                'profileID' => $storeArray['profileID'],
                'fileSize' => $storeArray['fileSize'],
                'reportDate' => $storeArray['reportDate']
            ])->get();
            if ($existData->isEmpty()) {
                try {
                    $id = DB::table('tbl_ams_campaigns_reports_download_links_sd')->insertGetId($storeArray);
                    // store report status
                    AMSModel::insertTrackRecord('Campaign SD Report Link', 'record stored');
                    Log::info('Insert Record id = ' . $id);
                } catch (\Illuminate\Database\QueryException $ex) {
                    Log::error($ex->getMessage());
                }
            } else {
                Log::info('Already exist in DB. ProfileId :' . $storeArray['profileID'] . ' reportID:' . $storeArray['reportId'] . ' reportDate' . $storeArray['reportDate']);
            }
            //}
        }, 3);
        Log::info('End Insertion query.');
    }

    /**
     * @param $storeArray
     * @uses  App\Console\Commands\Ams\ProductsAds\SD\getReportLinkCron
     */
    public function addSDproductsAdsReportLocation($storeArray)
    {
        Log::info('Start Insert data into DB.');
        DB::transaction(function () use ($storeArray) {
            //foreach ($storeArray as $row) {
            $existData = DB::table('tbl_ams_productsads_reports_download_links_sd')->where([
                'profileID' => $storeArray['profileID'],
                'fileSize' => $storeArray['fileSize'],
                'reportDate' => $storeArray['reportDate']
            ])->get();
            if ($existData->isEmpty()) {
                try {
                    $id = DB::table('tbl_ams_productsads_reports_download_links_sd')->insertGetId($storeArray);
                    // store report status
                    AMSModel::insertTrackRecord('SD_ProductAds Report Link', 'record stored');
                    Log::info('Insert Record id = ' . $id);
                } catch (\Illuminate\Database\QueryException $ex) {
                    Log::error($ex->getMessage());
                }
            } else {
                Log::info('Already exist in DB. ProfileId :' . $storeArray['profileID'] . ' reportID:' . $storeArray['reportId'] . ' reportDate' . $storeArray['reportDate']);
            }
            //}
        }, 3);
        Log::info('End Insertion query.');
    }

    /**
     * @param $storeArray
     */
    public function addproductsAdsReportLocation($storeArray)
    {
        Log::info('Start Insert data into DB.');
        DB::transaction(function () use ($storeArray) {
            //foreach ($storeArray as $row) {
            $existData = DB::table('tbl_ams_productsads_reports_download_links')->where([
                'profileID' => $storeArray['profileID'],
                'fileSize' => $storeArray['fileSize'],
                'reportDate' => $storeArray['reportDate']
            ])->get();
            if ($existData->isEmpty()) {
                try {
                    $id = DB::table('tbl_ams_productsads_reports_download_links')->insertGetId($storeArray);
                    // store report status
                    AMSModel::insertTrackRecord('ProductAds Report Link', 'record stored');
                    Log::info('Insert Record id = ' . $id);
                } catch (\Illuminate\Database\QueryException $ex) {
                    Log::error($ex->getMessage());
                }
            } else {
                Log::info('Already exist in DB. ProfileId :' . $storeArray['profileID'] . ' reportID:' . $storeArray['reportId'] . ' reportDate' . $storeArray['reportDate']);
            }
            //}
        }, 3);
        Log::info('End Insertion query.');
    }

    /**
     * @param $storeArray
     */
    public function addSbKeywordReportDownloadLink($storeArray)
    {
        Log::info('Start Insert data into DB.');
        DB::transaction(function () use ($storeArray) {
            //foreach ($storeArray as $row) {
            $existData = DB::table('tbl_ams_keyword_reports_download_links_sb')->where([
                'profileID' => $storeArray['profileID'],
                'fileSize' => $storeArray['fileSize'],
                'reportDate' => $storeArray['reportDate']
            ])->get();
            if ($existData->isEmpty()) {
                try {
                    $id = DB::table('tbl_ams_keyword_reports_download_links_sb')->insertGetId($storeArray);
                    // store report status
                    AMSModel::insertTrackRecord('Keyword SB Report Link', 'record stored');
                    Log::info('Insert Record id = ' . $id);
                } catch (\Illuminate\Database\QueryException $ex) {
                    Log::error($ex->getMessage());
                }
            } else {
                Log::info('Already exist in DB. ProfileId :' . $storeArray['profileID'] . ' reportID:' . $storeArray['reportId'] . ' reportDate' . $storeArray['reportDate']);
            }
            //}
        }, 3);
        Log::info('End Insertion query.');
    }

    /**
     * @param $storeArray
     */
    public function addSpKeywordReportDownloadLink($storeArray)
    {
        Log::info('Start Insert data into DB.');
        DB::transaction(function () use ($storeArray) {
            //foreach ($storeArray as $row) {
            $existData = DB::table('tbl_ams_keyword_reports_download_links_sp')->where([
                'profileID' => $storeArray['profileID'],
                'fileSize' => $storeArray['fileSize'],
                'reportDate' => $storeArray['reportDate']
            ])->get();
            if ($existData->isEmpty()) {
                try {
                    $id = DB::table('tbl_ams_keyword_reports_download_links_sp')->insertGetId($storeArray);
                    // store report status
                    AMSModel::insertTrackRecord('Keyword SP Report Link', 'record stored');
                    Log::info('Insert Record id = ' . $id);
                } catch (\Illuminate\Database\QueryException $ex) {
                    Log::error($ex->getMessage());
                }
            } else {
                Log::info('Already exist in DB. ProfileId :' . $storeArray['profileID'] . ' reportID:' . $storeArray['reportId'] . ' reportDate' . $storeArray['reportDate']);
            }
            //}
        }, 3);
        Log::info('End Insertion query.');
    }

    /**
     * @param $storeArray
     */
    public function addSpASINReportDownloadLink($storeArray)
    {
        Log::info('Start Insert data into DB.');
        DB::transaction(function () use ($storeArray) {
            //foreach ($storeArray as $row) {
            $existData = DB::table('tbl_ams_asin_reports_download_links')->where([
                'profileID' => $storeArray['profileID'],
                'fileSize' => $storeArray['fileSize'],
                'reportDate' => $storeArray['reportDate']
            ])->get();
            if ($existData->isEmpty()) {
                try {
                    $id = DB::table('tbl_ams_asin_reports_download_links')->insertGetId($storeArray);
                    // store report status
                    AMSModel::insertTrackRecord('ASIN Report Link', 'record stored');
                    Log::info('Insert Record id = ' . $id);
                } catch (\Illuminate\Database\QueryException $ex) {
                    Log::error($ex->getMessage());
                }
            } else {
                Log::info('Already exist in DB. ProfileId :' . $storeArray['profileID'] . ' reportID:' . $storeArray['reportId'] . ' reportDate' . $storeArray['reportDate']);
            }
            //}
        }, 3);
        Log::info('End Insertion query.');
    }

    /**
     * @param $storeArray
     */
    public function addSpTargetsReportDownloadLink($storeArray)
    {
        Log::info('Start Insert data into DB.');
        DB::transaction(function () use ($storeArray) {
            //foreach ($storeArray as $row) {
            $existData = DB::table('tbl_ams_targets_reports_download_links')->where([
                'profileID' => $storeArray['profileID'],
                'fileSize' => $storeArray['fileSize'],
                'reportDate' => $storeArray['reportDate']
            ])->get();
            if ($existData->isEmpty()) {
                try {
                    $id = DB::table('tbl_ams_targets_reports_download_links')->insertGetId($storeArray);
                    // store report status
                    AMSModel::insertTrackRecord('Product Target Report Link', 'record stored');
                    Log::info('Insert Record id = ' . $id);
                } catch (\Illuminate\Database\QueryException $ex) {
                    Log::error($ex->getMessage());
                }
            } else {
                Log::info('Already exist in DB. ProfileId :' . $storeArray['profileID'] . ' reportID:' . $storeArray['reportId'] . ' reportDate' . $storeArray['reportDate']);
            }
            //}
        }, 3);
        Log::info('End Insertion query.');
    }

    /**
     * @param $storeArray
     */
    public function addSbTargetsReportDownloadLink($storeArray)
    {
        Log::info('Start Insert data into DB.');
        DB::transaction(function () use ($storeArray) {
            //foreach ($storeArray as $row) {
            $existData = DB::table('tbl_ams_targets_reports_download_links_sb')->where([
                'profileID' => $storeArray['profileID'],
                'fileSize' => $storeArray['fileSize'],
                'reportDate' => $storeArray['reportDate']
            ])->get();
            if ($existData->isEmpty()) {
                try {
                    $id = DB::table('tbl_ams_targets_reports_download_links_sb')->insertGetId($storeArray);
                    // store report status
                    AMSModel::insertTrackRecord('Product Target Report Link', 'record stored');
                    Log::info('Insert Record id = ' . $id);
                } catch (\Illuminate\Database\QueryException $ex) {
                    Log::error($ex->getMessage());
                }
            } else {
                Log::info('Already exist in DB. ProfileId :' . $storeArray['profileID'] . ' reportID:' . $storeArray['reportId'] . ' reportDate' . $storeArray['reportDate']);
            }
            //}
        }, 3);
        Log::info('End Insertion query.');
    }

    /**
     * @param $storeArray
     * @uses  App\Console\Commands\Ams\AdGroup\SD\getReportLinkCron
     */
    public function addSDAdGroupReportDownloadLink($storeArray)
    {
        Log::info('Start Insert data into DB.');
        DB::transaction(function () use ($storeArray) {
            //foreach ($storeArray as $row) {
            $existData = DB::table('tbl_ams_adgroup_reports_download_links_sd')->where([
                'profileID' => $storeArray['profileID'],
                'fileSize' => $storeArray['fileSize'],
                'reportDate' => $storeArray['reportDate']
            ])->get();
            if ($existData->isEmpty()) {
                try {
                    $id = DB::table('tbl_ams_adgroup_reports_download_links_sd')->insertGetId($storeArray);
                    // store report status
                    AMSModel::insertTrackRecord('SD_Adgroup Report Link', 'record stored');
                    Log::info('Insert Record id = ' . $id);
                } catch (\Illuminate\Database\QueryException $ex) {
                    Log::error($ex->getMessage());
                }
            } else {
                Log::info('Already exist in DB. ProfileId :' . $storeArray['profileID'] . ' reportID:' . $storeArray['reportId'] . ' reportDate' . $storeArray['reportDate']);
            }
            //}
        }, 3);
        Log::info('End Insertion query.');
    }

    /**
     * @param $storeArray
     */
    public function addSpAdGroupReportDownloadLink($storeArray)
    {
        Log::info('Start Insert data into DB.');
        DB::transaction(function () use ($storeArray) {
            //foreach ($storeArray as $row) {
            $existData = DB::table('tbl_ams_adgroup_reports_download_links_sp')->where([
                'profileID' => $storeArray['profileID'],
                'fileSize' => $storeArray['fileSize'],
                'reportDate' => $storeArray['reportDate']
            ])->get();
            if ($existData->isEmpty()) {
                try {
                    $id = DB::table('tbl_ams_adgroup_reports_download_links_sp')->insertGetId($storeArray);
                    // store report status
                    AMSModel::insertTrackRecord('Adgroup Report Link', 'record stored');
                    Log::info('Insert Record id = ' . $id);
                } catch (\Illuminate\Database\QueryException $ex) {
                    Log::error($ex->getMessage());
                }
            } else {
                Log::info('Already exist in DB. ProfileId :' . $storeArray['profileID'] . ' reportID:' . $storeArray['reportId'] . ' reportDate' . $storeArray['reportDate']);
            }
            //}
        }, 3);
        Log::info('End Insertion query.');
    }

    /**
     * @param $storeArray
     */
    public function addSBAdGroupReportDownloadLink($storeArray)
    {
        Log::info('Start Insert data into DB.');
        DB::transaction(function () use ($storeArray) {
            //foreach ($storeArray as $row) {
            $existData = DB::table('tbl_ams_adgroup_reports_download_links_sb')->where([
                'profileID' => $storeArray['profileID'],
                'fileSize' => $storeArray['fileSize'],
                'reportDate' => $storeArray['reportDate']
            ])->get();
            if ($existData->isEmpty()) {
                try {
                    $id = DB::table('tbl_ams_adgroup_reports_download_links_sb')->insertGetId($storeArray);
                    // store report status
                    AMSModel::insertTrackRecord('Adgroup Report Link', 'record stored');
                    Log::info('Insert Record id = ' . $id);
                } catch (\Illuminate\Database\QueryException $ex) {
                    Log::error($ex->getMessage());
                }
            } else {
                Log::info('Already exist in DB. ProfileId :' . $storeArray['profileID'] . ' reportID:' . $storeArray['reportId'] . ' reportDate' . $storeArray['reportDate']);
            }
            //}
        }, 3);
        Log::info('End Insertion query.');
    }

    /**
     * @param $storeArray
     */
    public function addDownloadedcampaignReport($storeArray)
    {
        Log::info('Start Insert data into DB.');
        DB::transaction(function () use ($storeArray) {
            foreach ($storeArray as $row) {
                try {
                    DB::table('tbl_ams_campaigns_reports_downloaded_sp')->insertOrIgnore($row);
                } catch (\Illuminate\Database\QueryException $ex) {
                    Log::error($ex->getMessage());
                }
            }
        }, 3);
        Log::info('End Insertion query.');
    }

    /**
     * @param $storeArray
     */
    public function addDownloadedcampaignReportSB($storeArray)
    {
        Log::info('Start Insert data into DB.');
        DB::transaction(function () use ($storeArray) {
            foreach ($storeArray as $row) {
                try {
                    DB::table('tbl_ams_campaigns_reports_downloaded_sb')->insertOrIgnore($row);
                } catch (\Illuminate\Database\QueryException $ex) {
                    Log::error($ex->getMessage());
                }
            }
        }, 3);
        Log::info('End Insertion query.');
    }

    /**
     * @param $storeArray
     */
    public function addDownloadedcampaignSDReport($storeArray)
    {
        Log::info('Start Insert data into DB.');
        DB::transaction(function () use ($storeArray) {
            foreach ($storeArray as $row) {
                try {
                    DB::table('tbl_ams_campaigns_reports_downloaded_sd')->insertOrIgnore($row);
                } catch (\Illuminate\Database\QueryException $ex) {
                    Log::error($ex->getMessage());
                }
            }
        }, 3);
        Log::info('End Insertion query.');
    }

    /**
     * @param $storeArray
     */
    public function addSBDownloadedkeywordReport($storeArray)
    {
        DB::table('tbl_ams_keyword_reports_downloaded_data_sb')->insertOrIgnore($storeArray);
    }

    /**
     * @param $storeArray
     */
    public function addSPDownloadedkeywordReport($storeArray)
    {
        Log::info('Start Insert data into DB.');
        DB::transaction(function () use ($storeArray) {
            foreach ($storeArray as $row) {
                try {
                    DB::table('tbl_ams_keyword_reports_downloaded_data_sp')->insertOrIgnore($row);
                } catch (\Illuminate\Database\QueryException $ex) {
                    Log::error($ex->getMessage());
                }
            }
        }, 3);
        Log::info('End Insertion query.');
    }

    /**
     * @param $storeArray
     */
    public function addSpDownloadedASINReport($storeArray)
    {
        Log::info('Start Insert data into DB.');
        DB::transaction(function () use ($storeArray) {
            foreach ($storeArray as $row) {
                try {
                    DB::table('tbl_ams_asin_reports_downloaded_sp')->insertOrIgnore($row);
                } catch (\Illuminate\Database\QueryException $ex) {
                    Log::error($ex->getMessage());
                }
            }
        }, 3);
        Log::info('End Insertion query.');
    }

    /**
     * @param $storeArray
     */
    public function addSpDownloadedTargetReport($storeArray)
    {
        Log::info('Start Insert data into DB.');
        DB::transaction(function () use ($storeArray) {
            foreach ($storeArray as $row) {
                try {
                    DB::table($this->targetsReportsDownloadedData)->insertOrIgnore($row);
                } catch (\Illuminate\Database\QueryException $ex) {
                    Log::error($ex->getMessage());
                }
            }
        }, 3);
        Log::info('End Insertion query.');
    }

    /**
     * @param $storeArray
     */
    public function addSbDownloadedTargetReport($storeArray)
    {
        Log::info('Start Insert data into DB.');
        DB::transaction(function () use ($storeArray) {
            foreach ($storeArray as $row) {
                try {
                    DB::table('tbl_ams_targets_reports_downloaded_data_sb')->insertOrIgnore($row);
                } catch (\Illuminate\Database\QueryException $ex) {
                    Log::error($ex->getMessage());
                }
            }
        }, 3);
        Log::info('End Insertion query.');
    }

    /**
     * @param $storeArray
     * @uses Commands\Ams\ProductsAds\SD\getReportLinkDataCron
     */
    public function addsdproductsadsDownloadedReport($storeArray)
    {
        Log::info('Start Insert data into DB.');
        DB::transaction(function () use ($storeArray) {
            foreach ($storeArray as $row) {
                try {
                    DB::table('tbl_ams_productsads_reports_downloaded_data_sd')->insertOrIgnore($row);
                } catch (\Illuminate\Database\QueryException $ex) {
                    Log::error($ex->getMessage());
                }
            }
        }, 3);
        Log::info('End Insertion query.');
    }

    /**
     * @param $storeArray
     */
    public function addproductsadsDownloadedReport($storeArray)
    {
        Log::info('Start Insert data into DB.');
        DB::transaction(function () use ($storeArray) {
            foreach ($storeArray as $row) {
                try {
                    DB::table('tbl_ams_productsads_reports_downloaded_data')->insertOrIgnore($row);
                } catch (\Illuminate\Database\QueryException $ex) {
                    Log::error($ex->getMessage());
                }
            }
        }, 3);
        Log::info('End Insertion query.');
    }

    /**
     * @param $storeArray
     */
    public function addSpDownloadedAdGroupReport($storeArray)
    {
        Log::info('Start Insert data into DB.');
        DB::transaction(function () use ($storeArray) {
            foreach ($storeArray as $row) {
                try {
                    DB::table('tbl_ams_adgroup_reports_downloaded_data_sp')->insertOrIgnore($row);
                } catch (\Illuminate\Database\QueryException $ex) {
                    Log::error($ex->getMessage());
                }
            }
        }, 3);
        Log::info('End Insertion query.');
    }

    /**
     * @param $storeArray
     */
    public function addSBDownloadedAdGroupReport($storeArray)
    {
        DB::table('tbl_ams_adgroup_reports_downloaded_data_sb')->insertOrIgnore($storeArray);
    }

    /**
     * @param $storeArray
     * @uses  App\Console\Commands\Ams\AdGroup\SD\getReportLinkDataCron
     */
    public function addSDDownloadedAdGroupReport($storeArray)
    {
        Log::info('Start Insert data into DB.');
        DB::transaction(function () use ($storeArray) {
            foreach ($storeArray as $row) {
                try {
                    DB::table('tbl_ams_adgroup_reports_downloaded_data_sd')->insertOrIgnore($row);
                } catch (\Illuminate\Database\QueryException $ex) {
                    Log::error($ex->getMessage());
                }
            }
        }, 3);
        Log::info('End Insertion query.');
    }

    /**
     * @return mixed
     */
    public static function getAllReportsDownloadLink()
    {
        $ReportDate = date('Ymd', strtotime('-1 day', time()));
        return DB::table('tbl_ams_campaigns_reports_download_links_sp')
            ->join('tb_ams_api', 'tbl_ams_campaigns_reports_download_links_sp.fkConfigId', '=', 'tb_ams_api.id')
            ->select('tbl_ams_campaigns_reports_download_links_sp.id', 'tbl_ams_campaigns_reports_download_links_sp.fkBatchId', 'tbl_ams_campaigns_reports_download_links_sp.fkAccountId', 'tbl_ams_campaigns_reports_download_links_sp.profileID', 'tbl_ams_campaigns_reports_download_links_sp.fkConfigId', 'tbl_ams_campaigns_reports_download_links_sp.reportId', 'tbl_ams_campaigns_reports_download_links_sp.status', 'tbl_ams_campaigns_reports_download_links_sp.statusDetails', 'tbl_ams_campaigns_reports_download_links_sp.location', 'tbl_ams_campaigns_reports_download_links_sp.fileSize', 'tbl_ams_campaigns_reports_download_links_sp.reportDate', 'tbl_ams_campaigns_reports_download_links_sp.creationDate', 'tbl_ams_campaigns_reports_download_links_sp.isDone', 'tb_ams_api.client_id')
            ->where([
                ['status', '=', 'SUCCESS'],
                ['isDone', '=', 0],
            ])->get();
    }

    /**
     * @return mixed
     */
    public static function getAllReportsDownloadLinkSB()
    {
        $ReportDate = date('Ymd', strtotime('-1 day', time()));
        return DB::table('tbl_ams_campaigns_reports_download_links_sb')
            ->join('tb_ams_api', 'tbl_ams_campaigns_reports_download_links_sb.fkConfigId', '=', 'tb_ams_api.id')
            ->select('tbl_ams_campaigns_reports_download_links_sb.id', 'tbl_ams_campaigns_reports_download_links_sb.fkBatchId', 'tbl_ams_campaigns_reports_download_links_sb.fkAccountId', 'tbl_ams_campaigns_reports_download_links_sb.profileID', 'tbl_ams_campaigns_reports_download_links_sb.reportId', 'tbl_ams_campaigns_reports_download_links_sb.status', 'tbl_ams_campaigns_reports_download_links_sb.statusDetails', 'tbl_ams_campaigns_reports_download_links_sb.location', 'tbl_ams_campaigns_reports_download_links_sb.fileSize', 'tbl_ams_campaigns_reports_download_links_sb.reportDate', 'tbl_ams_campaigns_reports_download_links_sb.creationDate', 'tbl_ams_campaigns_reports_download_links_sb.isDone', 'tbl_ams_campaigns_reports_download_links_sb.fkConfigId', 'tb_ams_api.client_id')
            ->where([
                ['status', '=', 'SUCCESS'],
                ['isDone', '=', 0],
            ])->get();
    }

    /**
     * @return mixed
     */
    public static function getAllReportsDownloadLinkSD()
    {
        $ReportDate = date('Ymd', strtotime('-1 day', time()));
        return DB::table('tbl_ams_campaigns_reports_download_links_sd')
            ->join('tb_ams_api', 'tbl_ams_campaigns_reports_download_links_sd.fkConfigId', '=', 'tb_ams_api.id')
            ->select('tbl_ams_campaigns_reports_download_links_sd.id', 'tbl_ams_campaigns_reports_download_links_sd.fkBatchId', 'tbl_ams_campaigns_reports_download_links_sd.fkAccountId', 'tbl_ams_campaigns_reports_download_links_sd.profileID', 'tbl_ams_campaigns_reports_download_links_sd.reportId', 'tbl_ams_campaigns_reports_download_links_sd.status', 'tbl_ams_campaigns_reports_download_links_sd.statusDetails', 'tbl_ams_campaigns_reports_download_links_sd.location', 'tbl_ams_campaigns_reports_download_links_sd.fileSize', 'tbl_ams_campaigns_reports_download_links_sd.reportDate', 'tbl_ams_campaigns_reports_download_links_sd.creationDate', 'tbl_ams_campaigns_reports_download_links_sd.isDone', 'tbl_ams_campaigns_reports_download_links_sd.fkConfigId', 'tb_ams_api.client_id')
            ->where([
                ['status', '=', 'SUCCESS'],
                ['isDone', '=', 0],
            ])->get();
    }

    /**
     * @return mixed
     */
    public static function UpdateCampaignsReportsStatus($ReportId, $numberRecords)
    {
        DB::table('tbl_ams_campaigns_reports_download_links_sp')
            ->where(['id' => $ReportId])
            ->update(['isDone' => 1]);
    }

    /**
     * @return mixed
     */
    public static function UpdateCampaignsReportsStatusSB($ReportId, $numberRecords)
    {
        DB::table('tbl_ams_campaigns_reports_download_links_sb')
            ->where(['id' => $ReportId])
            ->update(['isDone' => 1]);
    }

    /**
     * @return mixed
     */
    public static function UpdateCampaignsSDReportsStatus($ReportId, $numberRecords)
    {
        DB::table('tbl_ams_campaigns_reports_download_links_sd')
            ->where(['id' => $ReportId])
            ->update(['isDone' => 1]);
    }

    /**
     * @return mixed
     */
    public static function UpdateReportsStatus($profileID, $reportType, $reportDate, $status)
    {
        DB::table('tbl_ams_report_id')
            ->where(['profileID' => $profileID])
            ->where(['reportType' => $reportType])
            ->where(['reportDate' => $reportDate])
            ->update(['isDone' => $status]);
    }


    /**
     * This function is use in App\Http\Controllers\AMSController
     */
    public static function getAMSCronList()
    {
        $record = DB::table('tbl_ams_crons')
            ->orderBy('modifiedDate', 'desc')
            ->get();
        if ($record) {
            return $record;
        }
        return FALSE;
    }

    /**
     * This function is use in App\Http\Controllers\AMSController
     * @param $data
     */
    public static function addCronTimeStatus($data)
    {
        Log::info('Start Insert data into DB.');
        $nextRunTime = '0000:00:00 00:00:00';
        if ($data['cronstatus'] == "run") {
            $nextRunTime = date('Y-m-d H:i:s', strtotime('+1 day', time()));
        }
        $isExist = DB::table('tbl_ams_crons')->where('cronType', '=', $data['crontype'])->first();
        if (!$isExist) {
            $InsertArray = array(
                'cronType' => $data['crontype'],
                'cronTime' => $data['crontime'],
                'cronStatus' => $data['cronstatus'],
                'nextRunTime' => $nextRunTime,
                'lastRun' => '0000:00:00 00:00:00',
                'cronRun' => '0',
                'modifiedDate' => '0000:00:00 00:00:00',
            );
            try {
                DB::table('tbl_ams_crons')->insert($InsertArray);
                Log::info('Insert Record AMS Cron Type = ' . $data['crontype']);
            } catch (\Illuminate\Database\QueryException $ex) {
                Log::error($ex->getMessage());
                return false;
            }
        } else {
            $updateArray = array(
                'cronTime' => $data['crontime'],
                'cronStatus' => $data['cronstatus'],
                'cronRun' => '0',
                'modifiedDate' => date('Y-m-d H:i:s')
            );
            try {
                DB::table('tbl_ams_crons')->where('cronType', $data['crontype'])->update($updateArray);
                Log::info('Update Record AMS Cron Type = ' . $data['crontype']);
            } catch (\Illuminate\Database\QueryException $ex) {
                Log::error($ex->getMessage());
                return false;
            }
        }
        Log::info('End Insertion query.');
    }

    /**
     * @return array
     */
    public static function getAllAMSDashboard()
    {
        // create Data array
        $data = array();
        // get count of profile
        $data['profiles'] = DB::table('tbl_ams_profiles')->count();
        // get last date of adgroup_reports
        $data['adgroup_reports'] = DB::table('tbl_ams_adgroup_reports_downloaded_data_sp')
            ->select('reportDate')
            ->orderByRaw('reportDate DESC')
            ->get()
            ->first();
        // get last date of product ads reports
        $data['productads_reports'] = DB::table('tbl_ams_productsads_reports_downloaded_data')
            ->select('reportDate')
            ->orderByRaw('reportDate DESC')
            ->get()
            ->first();
        // get last date of targets reports
        $data['targets_reports'] = DB::table('tbl_ams_targets_reports_download_links')
            ->select('reportDate')
            ->orderByRaw('reportDate DESC')
            ->get()
            ->first();
        // get last date of ASIN_reports
        $data['ASIN_reports'] = DB::table('tbl_ams_asin_reports_downloaded_sp')
            ->select('reportDate')
            ->orderByRaw('reportDate DESC')
            ->get()
            ->first();
        // get last date of campaigns_reports_sp
        $data['campaigns_reports_sp'] = DB::table('tbl_ams_campaigns_reports_downloaded_sp')
            ->select('reportDate')
            ->orderByRaw('reportDate DESC')
            ->get()
            ->first();
        // get last date of keyword reports sb
        $data['keyword_reports_sb'] = DB::table('tbl_ams_keyword_reports_downloaded_data_sb')
            ->select('reportDate')
            ->orderByRaw('reportDate DESC')
            ->get()
            ->first();
        // get last date of keyword_reports_sp
        $data['keyword_reports_sp'] = DB::table('tbl_ams_keyword_reports_downloaded_data_sp')
            ->select('reportDate')
            ->orderByRaw('reportDate DESC')
            ->get()
            ->first();
        return $data;
    }

    /**
     * @param $reportName
     * @param $status
     *
     * @uses  AMSModel
     * @uses  Commands\Ams\AdGroup\SP\getReportIdCron
     * @uses  Commands\Ams\AdGroup\SP\getReportLinkCron
     */
    public static function insertTrackRecord($reportName, $status)
    {
        $InsertArray = array(
            'reportName' => $reportName,
            'status' => $status,
            'dated' => date('Y-m-d H:i:s'),
        );
        try {
            DB::table('tbl_ams_tracker')->insert($InsertArray);
            Log::info('Insert Record AMS Tracker = ' . $reportName);

        } catch (\Illuminate\Database\QueryException $ex) {
            AMSModel::insertTrackRecord(json_encode($ex->getMessage()), 'fail');
            Log::error($ex->getMessage());
        }
    }

    /**
     * This function is used to get DISTINCT campaign ID for sp report
     *
     * @return mixed
     * @uses App\Console\Commands\Ams\Keyword\SP\getKeywordList
     *
     */
    public static function getSPCampaignList()
    {
        return DB::table('tbl_ams_campaigns_reports_downloaded_sp as tbl_camp')
            ->distinct()
            ->select('tbl_camp.campaignId', 'tbl_camp.fkProfileId as profileId', 'tbl_camp.fkAccountId', 'tb_ams_api.client_id', 'tbl_camp.fkConfigId')
            ->join('tbl_ams_profiles as tbl_pro', 'tbl_pro.profileId', '=', 'tbl_camp.fkProfileId')
            ->join('tb_ams_api', 'tbl_camp.fkConfigId', '=', 'tb_ams_api.id')
            ->where('tbl_pro.isSandboxProfile', 0)
            ->where('tbl_pro.isActive', 1)
            ->get();
    }

    /**
     * This function is used to get daily keyword bid values
     *
     * @param $profileId
     * @param $campaignId
     * @return bool
     * @uses App\Console\Commands\Ams\Keyword\SP\getKeywordList
     *
     */
    public static function updateKeywordBidProfileStatus($profileId, $campaignId)
    {
        DB::beginTransaction();
        try {
            $record = DB::table(AMSModel::$tbl_ams_keywordbid_tracker)->where([
                ['profileId', '=', $profileId],
                ['campaignId', '=', $campaignId],
                ['status', '=', 1],
                ['dated', '=', date('Y-m-d')],
            ])->get()->first();
            if (!$record) {
                $InsertArray = array(
                    'profileId' => $profileId,
                    'campaignId' => $campaignId,
                    'status' => 1,
                    'dated' => date('Y-m-d'),
                    'createdAt' => date('Y-m-d H:i:s'),
                    'updatedAt' => date('Y-m-d H:i:s')
                );
                DB::table(AMSModel::$tbl_ams_keywordbid_tracker)->insert($InsertArray);
                DB::commit();
            } else {
                // if data found
            }

        } catch (\Exception $e) {
            // got error add to log file
            // $e->getMessage()
            DB::rollback();
            // something went wrong
            return false;
        }

    }

    /**
     * This function is used to store keyword data
     *
     * @param $DataArray
     * @return bool
     */
    public static function storeKeywordBidData($DataArray)
    {
        DB::beginTransaction();
        try {
            foreach ($DataArray as $single) {
                $record = DB::table(AMSModel::$tbl_ams_keywordbid)->where([
                    ['keywordId', '=', $single['keywordId']],
                    ['reportDate', '=', $single['reportDate']],
                    ['fkAccountId', '=', $single['fkAccountId']],
                ])->get()->first();
                DB::commit();
                if (!$record) {
                    DB::table(AMSModel::$tbl_ams_keywordbid)->insert($single);
                    DB::commit();
                } else {
                    // if data found
                }
            }// end foreach
        } catch (\Exception $e) {
            // got error add to log file
            // $e->getMessage()
            DB::rollback();
            // something went wrong
            return false;
        }
    }
}