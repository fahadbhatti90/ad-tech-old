<?php

namespace App\Console\Commands\Ams\AdGroup\SD;

use Artisan;
use DB;
use App\Models\AMSModel;
use GuzzleHttp\Client;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Log;

class getReportIdCron extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'getsdadgroupreportid:sdadgroup';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'This command is used to generate Sponsored Display AdGroup ReportId.';

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
        Log::info("filePath:Commands\Ams\AdGroup\SD\getReportIdCron. Start Cron.");
        Log::info($this->description);
        Log::info("AMS Auth token get from DB Start!");
        $AllProfileIdObject = new AMSModel();
        $AllProfileID = $AllProfileIdObject->getAllProfiles();
        if (!empty($AllProfileID)) {
            foreach ($AllProfileID as $single) {
                //skip that marketplace's profiles that not supported on sd
                if ($single->marketplaceStringId != 'A1AM78C64UM0Y8') {
                    $clientId = $single->client_id;
                    $fkConfigId = $single->fkConfigId;
                    if (!empty($clientId)) {
                        // get account id from
                        $body = array();
                        // Create a client with a base URI
                        $url = Config::get('constants.amsApiUrl') . '/' . Config::get('constants.SDadGroupsReport');
                        $client = new Client();
                        b:
                        $obaccess_token = new AMSModel();
                        $getAMSTokenById = $obaccess_token->getAMSTokenById($fkConfigId);
                        $accessToken = $getAMSTokenById->access_token;
                        if (!empty($accessToken)) {
                            try {
                                $reportDateSingleDay = date('Ymd', strtotime('-1 day', time()));
                                // get account id from
                                $getAccountId['batchId'] = AMSModel::getSpecificAccountId($single->id, 1, $reportDateSingleDay);
                                if ($getAccountId['batchId'] == FALSE) {
                                    continue; // if account id not found then continue.
                                }
                                $response = $client->request('POST', $url, [
                                    'headers' => [
                                        'Authorization' => 'Bearer ' . $accessToken,
                                        'Content-Type' => 'application/json',
                                        'Amazon-Advertising-API-ClientId' => $clientId,
                                        'Amazon-Advertising-API-Scope' => $single->profileId],
                                    'json' => [
                                        'tactic' => 'remarketing',
                                        'reportDate' => $reportDateSingleDay,
                                        'metrics' => Config::get('constants.sdAdGroupMetrics')],
                                    'delay' => Config::get('constants.delayTimeInApi'),
                                    'connect_timeout' => Config::get('constants.connectTimeOutInApi'),
                                    'timeout' => Config::get('constants.timeoutInApi'),
                                ]);
                                $DataArray = [];
                                $body = json_decode($response->getBody()->getContents());
                                if (!empty($body) && $body != null) {
                                    $storeArray = [];
                                    Log::info("Make Array For Data Insertion");
                                    $storeArray['fkBatchId'] = $getAccountId['batchId']->batchId;
                                    $storeArray['fkAccountId'] = $getAccountId['batchId']->fkAccountId;
                                    $storeArray['profileID'] = $single->id;
                                    $storeArray['fkConfigId'] = $fkConfigId;
                                    $storeArray['reportId'] = $body->reportId;
                                    $storeArray['recordType'] = $body->recordType;
                                    $storeArray['reportType'] = "AdGroup_SD";
                                    $storeArray['status'] = $body->status;
                                    $storeArray['statusDetails'] = $body->statusDetails;
                                    $storeArray['reportDate'] = $reportDateSingleDay;
                                    $storeArray['creationDate'] = date('Y-m-d');
                                    array_push($DataArray, $storeArray);
                                    // store report status
                                    AMSModel::insertTrackRecord('report name : Sponsored Display AdGroup Report Id' . ' profile id: ' . $single->id, 'record found');
                                } else {
                                    // store report status
                                    AMSModel::insertTrackRecord('report name : Sponsored Display AdGroup Report Id' . ' profile id: ' . $single->id, 'not record found');
                                }
                                if (!empty($DataArray)) {
                                    $addReportIdObj = new AMSModel();
                                    $addReportIdObj->addReportId($DataArray);
                                }
                            } catch (\Exception $ex) {
                                Log::error(json_encode($ex));
                                if ($ex->getCode() == 401) {
                                    if (strstr($ex->getMessage(), '401 Unauthorized')) { // if auth token expire
                                        Log::error('Refresh Access token. In file filePath:Commands\Ams\AdGroup\SD\getReportIdCron');
                                        $authCommandArray = array();
                                        $authCommandArray['fkConfigId'] = $fkConfigId;
                                        \Artisan::call('getaccesstoken:amsauth', $authCommandArray);
                                        goto b;
                                    } elseif (strstr($ex->getMessage(), 'advertiser found for scope')) {
                                        // store profile list not valid
                                        Log::info("Invalid Profile Id: " . $single->profileId);
                                    }
                                } else if ($ex->getCode() == 429) { //https://advertising.amazon.com/API/docs/v2/guides/developer_notes#Rate-limiting
                                    sleep(Config::get('constants.sleepTime') + 2);
                                    goto b;
                                } else if ($ex->getCode() == 502) {
                                    sleep(Config::get('constants.sleepTime') + 2);
                                    goto b;
                                }
                                // store report status
                                AMSModel::insertTrackRecord('AdGroup SD Report Id', 'fail');
                                Log::error($ex->getMessage());
                            }// end catch
                        } else {
                            Log::info("AMS access token not found.");
                        }
                    } else {
                        Log::info("Client Id not found.");
                    }
                }
            }// end foreach
        } else {
            Log::info("All Get Reports download ID not found.");
        }
        Log::info("filePath:Commands\Ams\AdGroup\SD\getReportIdCron. End Cron.");
    }
}
