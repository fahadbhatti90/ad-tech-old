<?php

namespace App\Console\Commands\Ams\AdGroup\SB;

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
    protected $signature = 'getsbadgroupreportlinkdata:sbadgroup';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'This command is used to get AdGroup encoded Data From location.';

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
        Log::info("filePath:Commands\Ams\AdGroup\SB\getReportLinkDataCron. Start Cron.");
        Log::info($this->description);
                $getSpAdGroupDownloadLinkObject = new AMSModel();
                $AllGetReportsDownloadLink = $getSpAdGroupDownloadLinkObject->getSBAdGroupDownloadLink();
                if (!empty($AllGetReportsDownloadLink)) {
                    foreach ($AllGetReportsDownloadLink as $single) {
                        $clientId = $single->client_id;
                        $fkConfigId = $single->fkConfigId;
                        if (!empty($clientId)) {
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
                                AMSModel::UpdateSBAdGroupStatus($single->id, $totalNumberOfRecords);
                                Log::info("Make Array For Data Insertion");
                                $DataArray = [];
                                for ($i = 0; $i < $totalNumberOfRecords; $i++) {
                                    $storeArray = [];
                                    $storeArray['fkReportsDownloadLinksId'] = $single->id;
                                    $storeArray['fkProfileId'] = $single->profileID;
                                    $storeArray['fkBatchId'] = $single->fkBatchId;
                                    $storeArray['fkAccountId'] = $single->fkAccountId;
                                    $storeArray['fkConfigId'] = $fkConfigId;
                                    $storeArray['campaignId'] = $body[$i]->campaignId;
                                    $storeArray['campaignName'] = $body[$i]->campaignName;
                                    $storeArray['campaignBudget'] = $body[$i]->campaignBudget;
                                    $storeArray['campaignBudgetType'] = $body[$i]->campaignBudgetType;
                                    $storeArray['campaignStatus'] = $body[$i]->campaignStatus;
                                    $storeArray['adGroupName'] = $body[$i]->adGroupName;
                                    $storeArray['adGroupId'] = $body[$i]->adGroupId;
                                    $storeArray['impressions'] = $body[$i]->impressions;
                                    $storeArray['clicks'] = $body[$i]->clicks;
                                    $storeArray['cost'] = $body[$i]->cost;
                                    $storeArray['attributedDetailPageViewsClicks14d'] = $body[$i]->attributedDetailPageViewsClicks14d;
                                    $storeArray['attributedSales14d'] = $body[$i]->attributedSales14d;
                                    $storeArray['attributedSales14dSameSKU'] = $body[$i]->attributedSales14dSameSKU;
                                    $storeArray['attributedConversions14d'] = $body[$i]->attributedConversions14d;
                                    $storeArray['attributedConversions14dSameSKU'] = $body[$i]->attributedConversions14dSameSKU;
                                    $storeArray['attributedOrdersNewToBrand14d'] = $body[$i]->attributedOrdersNewToBrand14d;
                                    $storeArray['attributedOrdersNewToBrandPercentage14d'] = $body[$i]->attributedOrdersNewToBrandPercentage14d;
                                    $storeArray['attributedOrderRateNewToBrand14d'] = $body[$i]->attributedOrderRateNewToBrand14d;
                                    $storeArray['attributedSalesNewToBrand14d'] = $body[$i]->attributedSalesNewToBrand14d;
                                    $storeArray['attributedSalesNewToBrandPercentage14d'] = $body[$i]->attributedSalesNewToBrandPercentage14d;
                                    $storeArray['attributedUnitsOrderedNewToBrand14d'] = $body[$i]->attributedUnitsOrderedNewToBrand14d;
                                    $storeArray['attributedUnitsOrderedNewToBrandPercentage14d'] = $body[$i]->attributedUnitsOrderedNewToBrandPercentage14d;
                                    $storeArray['unitsSold14d'] = $body[$i]->unitsSold14d;
                                    $storeArray['dpv14d'] = $body[$i]->dpv14d;
                                    $storeArray['reportDate'] = $single->reportDate;
                                    $storeArray['creationDate'] = date('Y-m-d');
                                    $insertDataObject = new AMSModel();
                                    $insertDataObject->addSBDownloadedAdGroupReport($storeArray);
                                }// end for-loop
//
                                // store report status
                                AMSModel::insertTrackRecord('report name : AdGroup Report Data' . ' profile id: ' . $single->profileID, 'record found');
                            } else {
                                // store report status
                                AMSModel::insertTrackRecord('report name : AdGroup Report Data' . ' profile id: ' . $single->profileID, 'not record found');
                            }
                        } catch (\Exception $ex) {
                            if ($ex->getCode() == 401) {
                                if (strstr($ex->getMessage(), '401 Unauthorized')) { // if auth token expire
                                    Log::error('Refresh Access token. In file filePath:Commands\Ams\AdGroup\SB\getReportLinkDataCron.');
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
                            AMSModel::insertTrackRecord('AdGroup Report Data', 'fail');
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
        Log::info("filePath:Commands\Ams\AdGroup\SB\getReportLinkDataCron. End Cron.");
    }
}
