<?php

namespace App\Console\Commands\Ams\Target\SB;

use Artisan;
use DB;

;

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
    protected $signature = 'gettargetreportid:sbtargets';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'This command is used to get Target reportId.';

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
        Log::info("filePath:Commands\Ams\Target\SB\getReportIdCron. Start Cron.");
        Log::info($this->description);
        $AllProfileIdObject = new AMSModel();
        $AllProfileID = $AllProfileIdObject->getAllProfiles();
        if (!empty($AllProfileID)) {
            foreach ($AllProfileID as $single) {
                $clientId = $single->client_id;
                $fkConfigId = $single->fkConfigId;
                if (!empty($clientId)) {
                    $body = array();
                    // Create a client with a base URI
                    $url = Config::get('constants.amsApiUrl') . '/' . Config::get('constants.apiVersion') . '/' . Config::get('constants.targetsReportSb');
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
                                    'Amazon-Advertising-API-Scope' => $single->profileId
                                ],
                                'json' => [
                                    'segment' => '',
                                    'reportDate' => $reportDateSingleDay,
                                    'metrics' => Config::get('constants.sbTargetingMetrics')],
                                'delay' => Config::get('constants.delayTimeInApi'),
                                'connect_timeout' => Config::get('constants.connectTimeOutInApi'),
                                'timeout' => Config::get('constants.timeoutInApi'),
                            ]);
                            $DataArray = array();
                            $body = json_decode($response->getBody()->getContents());
                            if (!empty($body)) {
                                $storeArray = [];
                                Log::info("Make Array For Data Insertion");
                                $storeArray['fkBatchId'] = $getAccountId['batchId']->batchId;
                                $storeArray['fkAccountId'] = $getAccountId['batchId']->fkAccountId;
                                $storeArray['profileID'] = $single->id;
                                $storeArray['fkConfigId'] = $fkConfigId;
                                $storeArray['reportId'] = $body->reportId;
                                $storeArray['recordType'] = $body->recordType;
                                $storeArray['reportType'] = "Product_Targeting_SB";
                                $storeArray['status'] = $body->status;
                                $storeArray['statusDetails'] = $body->statusDetails;
                                $storeArray['reportDate'] = $reportDateSingleDay;
                                $storeArray['creationDate'] = date('Y-m-d');
                                array_push($DataArray, $storeArray);
                                // store report status
                                AMSModel::insertTrackRecord('report name : Product Target Report Id' . ' profile id: ' . $single->id, 'record found');
                            } else {
                                // store report status
                                AMSModel::insertTrackRecord('report name : Product Target Report Id' . ' profile id: ' . $single->id, 'record not found');
                            }
                            if (!empty($DataArray)) {
                                $addReportIdObj = new AMSModel();
                                $addReportIdObj->addReportId($DataArray);
                            }
                        } catch (\Exception $ex) {
                            if ($ex->getCode() == 401) {
                                if (strstr($ex->getMessage(), '401 Unauthorized')) { // if auth token expire
                                    Log::error('Refresh Access token. In file filePath:Commands\Ams\Target\SB\getReportIdCron');
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
                            AMSModel::insertTrackRecord('Product Target Report Id', 'fail');
                            Log::error($ex->getMessage());
                        }// end catch
                    } else {
                        Log::info("AMS access token not found.");
                    }//end else
                } else {
                    Log::info("Client Id not found.");
                }//end else
            }// end foreach
        } else {
            Log::info("Profile not found.");
        }
        Log::info("filePath:Commands\Ams\Target\SB\getReportIdCron. End Cron.");
    }
}
