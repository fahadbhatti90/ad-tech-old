<?php

namespace App\Console\Commands\AMS\Campaign\SD;

use Artisan;
use DB;
use App\Models\AMSModel;
use GuzzleHttp\Client;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Log;

class getReportLinkDataCron extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'getcampaignreportlinkdata:sdcampaign';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'This command is used to get Report Location Data.';

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
        Log::info("filePath:Commands\Ams\Campaign\SD\getReportLinkDataCron. Start Cron.");
        Log::info($this->description);
        $errorType = "0";
        Log::info("Message from a $errorType ");
        $AllGetReportsDownloadLink = AMSModel::getAllReportsDownloadLinkSD();
                if (!empty($AllGetReportsDownloadLink)) {
                    foreach ($AllGetReportsDownloadLink as $single) {
                        $clientId = $single->client_id;
                        $fkConfigId = $single->fkConfigId;
                        if (!empty($clientId)) {
                        $body = array();
                        // Create a client with a base URI
                        $url = $single->location;
                        a:
                        $obaccess_token = new AMSModel();
                        $getAMSTokenById = $obaccess_token->getAMSTokenById($fkConfigId);
                        $accessToken = $getAMSTokenById->access_token;
                        if (!empty($accessToken)) {
                        try {
                            $client = new Client();
                            $response = $client->request('GET', $url, [
                                'headers' => [
                                    'Authorization' => 'Bearer ' . $accessToken,
                                    'Amazon-Advertising-API-ClientId' => $clientId,
                                    'Amazon-Advertising-API-Scope' => $single->profileID],
                                'delay' => Config::get('constants.delayTimeInApi'),
                                'connect_timeout' => Config::get('constants.connectTimeOutInApi'),
                                'timeout' => Config::get('constants.timeoutInApi'),
                            ]);
                            $body = json_decode(gzdecode($response->getBody()->getContents()));
                            if (!empty($body) && $body != null) {
                                $totalNumberOfRecords = count($body);
                                AMSModel::UpdateCampaignsSDReportsStatus($single->id, $totalNumberOfRecords);
                                Log::info("Make Array For Data Insertion");
                                $DataArray = [];
                                for ($i = 0; $i < $totalNumberOfRecords; $i++) {
                                    $storeArray = [];
                                    $storeArray['fkReportsDownloadLinksId'] = $single->id;
                                    $storeArray['fkProfileId'] = $single->profileID;
                                    $storeArray['fkBatchId'] = $single->fkBatchId;
                                    $storeArray['fkAccountId'] = $single->fkAccountId;
                                    $storeArray['fkConfigId'] = $fkConfigId;
                                    $storeArray['campaignName'] = $body[$i]->campaignName;
                                    $storeArray['campaignId'] = $body[$i]->campaignId;
                                    $storeArray['campaignStatus'] = $body[$i]->campaignStatus;
                                    $storeArray['impressions'] = $body[$i]->impressions;
                                    $storeArray['clicks'] = $body[$i]->clicks;
                                    $storeArray['cost'] = $body[$i]->cost;
                                    $storeArray['currency'] = $body[$i]->currency;
                                    $storeArray['attributedDPV14d'] = $body[$i]->attributedDPV14d;
                                    $storeArray['attributedUnitsSold14d'] = $body[$i]->attributedUnitsSold14d;
                                    $storeArray['attributedSales14d'] = $body[$i]->attributedSales14d;
                                    $storeArray['reportDate'] = $single->reportDate;
                                    $storeArray['creationDate'] = date('Y-m-d');
                                    array_push($DataArray, $storeArray);
                                }// end for loop
                                if (!empty($DataArray)) {
                                    $insertDataObject = new AMSModel();
                                    $insertDataObject->addDownloadedcampaignSDReport($DataArray);
                                }
                                // store report status
                                AMSModel::insertTrackRecord('report name : Campaign SD Report Data' . ' profile id: ' . $single->profileID, 'record found');
                            } else {
                                // store report status
                                AMSModel::insertTrackRecord('report name : Campaign SD Report Data' . ' profile id: ' . $single->profileID, 'not record found');
                            }
                        } catch (\Exception $ex) {
                            if ($ex->getCode() == 401) {
                                if (strstr($ex->getMessage(), '401 Unauthorized')) { // if auth token expire
                                    Log::error('Refresh Access token. In file filePath:Commands\Ams\Campaign\SD\getReportLinkDataCron');
                                    $authCommandArray = array();
                                    $authCommandArray['fkConfigId'] = $fkConfigId;
                                    \Artisan::call('getaccesstoken:amsauth', $authCommandArray);
                                    goto a;
                                } elseif (strstr($ex->getMessage(), 'advertiser found for scope')) {
                                    // store profile list not valid
                                    Log::info("Invalid Profile Id: " . $single->profileID);
                                }
                            } else if ($ex->getCode() == 429) { //https://advertising.amazon.com/API/docs/v2/guides/developer_notes#Rate-limiting
                                sleep(Config::get('constants.sleepTime') + 2);
                                goto a;
                            } else if ($ex->getCode() == 502) {
                                sleep(Config::get('constants.sleepTime') + 2);
                                goto a;
                            }
                            // store report status
                            AMSModel::insertTrackRecord('Campaign SD Report Data', 'fail');
                            Log::error($ex->getMessage());
                        }
                        } else {
                            Log::info("AMS access token not found.");
                        }
                    } else {
                        Log::info("Client Id not found.");
                    }
                    }// end foreach
                } else {
                    Log::info("All Get Reports download link not found.");
                }
        Log::info("filePath:Commands\Ams\Campaign\SD\getReportLinkDataCron. End Cron.");
    }
}
