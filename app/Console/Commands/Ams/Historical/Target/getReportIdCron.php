<?php

namespace App\Console\Commands\Ams\Historical\Target;

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
    protected $signature = 'gettargethistoricalreportid:targets {profile*}';

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
     * @return mixed
     * @throws \GuzzleHttp\Exception\GuzzleException
     * Execute the console command.
     *
     */
    public function handle()
    {
        $profileArray  = $this->argument('profile');
        Log::info($this->description);
        $AllProfileIdObject = new AMSModel();
        $AllProfileID = $AllProfileIdObject->getAllHistoricalProfiles($profileArray);
        if (!empty($AllProfileID)) {
            foreach ($AllProfileID as $single) {
                $clientId = $single->client_id;
                $fkConfigId = $single->fkConfigId;
                if (!empty($clientId)) {
                    // Create a client with a base URI
                    $url = Config::get('constants.amsApiUrl') . '/' . Config::get('constants.apiVersion') . '/' . Config::get('constants.targetsReport');
                    $client = new Client();
                    if ($single->productTargetingSixtyDays == 0) { // check Sixty Days is inserted OR not
                        AMSModel::UpdateProfileSixtyStatus($single->profileId, 'productTargetingSixtyDays', 1);
                        for ($i = 60; $i >= 1; $i--) {
                            a:
                            $obaccess_token = new AMSModel();
                            $getAMSTokenById = $obaccess_token->getAMSTokenById($fkConfigId);
                            $accessToken = $getAMSTokenById->access_token;
                            if (!empty($accessToken)) {
                                try {
                                    $reportDateSixtyDays = date('Ymd', strtotime('-' . $i . ' day', time()));
                                    $getAccountId['batchId'] = AMSModel::getSpecificAccountId($single->id, 1, $reportDateSixtyDays);
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
                                            'reportDate' => $reportDateSixtyDays,
                                            'metrics' => Config::get('constants.productTargetingMetrics')],
                                        'delay' => Config::get('constants.delayTimeInApi'),
                                        'connect_timeout' => Config::get('constants.connectTimeOutInApi'),
                                    ]);
                                    $DataArray = array();
                                    $body = array();
                                    $body = json_decode($response->getBody()->getContents());
                                    if (!empty($body)) {
                                        $storeArray = [];
                                        Log::info("Make Array For Data Insertion");
                                        $storeArray['fkBatchId'] = $getAccountId['batchId']->batchId;
                                        $storeArray['fkAccountId'] = $getAccountId['batchId']->fkAccountId;
                                        $storeArray['fkConfigId'] = $fkConfigId;
                                        $storeArray['profileID'] = $single->id;
                                        $storeArray['reportId'] = $body->reportId;
                                        $storeArray['recordType'] = $body->recordType;
                                        $storeArray['reportType'] = "Product_Targeting";
                                        $storeArray['status'] = $body->status;
                                        $storeArray['statusDetails'] = $body->statusDetails;
                                        $storeArray['reportDate'] = $reportDateSixtyDays;
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
                                    if ($i <= 60) {
                                        $i++;
                                    }
                                    if ($i > 60) {
                                        $i = 60;
                                    }
                                    if ($ex->getCode() == 401) {
                                        Log::error('Refresh Access token. In file filePath:Commands\Ams\Target\getReportIdCron');
                                        $authCommandArray = array();
                                        $authCommandArray['fkConfigId'] = $fkConfigId;
                                        \Artisan::call('getaccesstoken:amsauth', $authCommandArray);
                                        goto a;
                                    } else if ($ex->getCode() == 429) { //https://advertising.amazon.com/API/docs/v2/guides/developer_notes#Rate-limiting
                                        sleep(Config::get('constants.sleepTime') + 2);
                                        goto a;
                                    } else if ($ex->getCode() == 502) {
                                        sleep(Config::get('constants.sleepTime') + 2);
                                        goto a;
                                    }
                                    // store report status
                                    AMSModel::insertTrackRecord('Product Target Report Id', 'fail');
                                    Log::error($ex->getMessage());
                                }// end catch
                            } else {
                                Log::info("AMS access token not found.");
                            }
                        }// end for loop
                    } // end else
                } else {
                    Log::info("Client Id not found.");
                }
            }// end foreach
        } else {
            Log::info("Profile not found.");
        }
        Log::info("filePath:App\Console\Commands\Ams\Historical\Target\getReportIdCron. End Cron.");
    }
}
