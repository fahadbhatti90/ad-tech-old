<?php

namespace App\Console\Commands\Ams\Keyword\SB;

use Artisan;
use DB;
use App\Models\AMSModel;
use GuzzleHttp\Client;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Log;
use Rap2hpoutre\FastExcel\FastExcel;

class getReportLinkDataCron extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'getkeywordreportlinkdata:sbkeyword';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'This command is used to get Keyword SB link data.';

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
     */
    public function handle()
    {
        Log::info("filePath:Commands\Ams\Keyword\SB\getReportLinkDataCron. Start Cron.");
        Log::info($this->description);
                $AllGetReportsDownloadLinkObject = new AMSModel();
                $AllGetReportsDownloadLink = $AllGetReportsDownloadLinkObject->getSbKeywordDownloadLink();
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
                                AMSModel::UpdateSbKeywordStatus($single->id, $totalNumberOfRecords);
                                Log::info("Make Array For Data Insertion");
                                $DataArray = [];
                                for ($i = 0; $i < $totalNumberOfRecords; $i++) {
                                    $storeArray = [];
                                    $storeArray['fkReportsDownloadLinksId'] = $single->id;
                                    $storeArray['fkProfileId'] = $single->profileID;
                                    $storeArray['fkBatchId'] = $single->fkBatchId;
                                    $storeArray['fkAccountId'] = $single->fkAccountId;
                                    $storeArray['fkConfigId'] = $fkConfigId;
                                    $storeArray['attributedSalesNewToBrand14d'] = $body[$i]->attributedSalesNewToBrand14d;
                                    $storeArray['cost'] = $body[$i]->cost;
                                    $storeArray['adGroupName'] = $body[$i]->adGroupName;
                                    $storeArray['campaignId'] = $body[$i]->campaignId;
                                    $storeArray['keywordId'] = $body[$i]->keywordId;
                                    $storeArray['matchType'] = $body[$i]->matchType;
                                    $storeArray['attributedSales14dSameSKU'] = $body[$i]->attributedSales14dSameSKU;
                                    $storeArray['attributedOrdersNewToBrandPercentage14d'] = $body[$i]->attributedOrdersNewToBrandPercentage14d;
                                    $storeArray['impressions'] = $body[$i]->impressions;
                                    $storeArray['adGroupId'] = $body[$i]->adGroupId;
                                    $storeArray['keywordText'] = $body[$i]->keywordText;
                                    $storeArray['campaignBudget'] = $body[$i]->campaignBudget;
                                    $storeArray['attributedOrderRateNewToBrand14d'] = $body[$i]->attributedOrderRateNewToBrand14d;
                                    $storeArray['attributedConversions14d'] = $body[$i]->attributedConversions14d;
                                    $storeArray['campaignBudgetType'] = $body[$i]->campaignBudgetType;
                                    $storeArray['campaignStatus'] = $body[$i]->campaignStatus;
                                    $storeArray['attributedConversions14dSameSKU'] = $body[$i]->attributedConversions14dSameSKU;
                                    $storeArray['clicks'] = $body[$i]->clicks;
                                    $storeArray['attributedOrdersNewToBrand14d'] = $body[$i]->attributedOrdersNewToBrand14d;
                                    $storeArray['attributedUnitsOrderedNewToBrand14d'] = $body[$i]->attributedUnitsOrderedNewToBrand14d;
                                    $storeArray['attributedSales14d'] = $body[$i]->attributedSales14d;
                                    $storeArray['attributedSalesNewToBrandPercentage14d'] = $body[$i]->attributedSalesNewToBrandPercentage14d;
                                    $storeArray['campaignName'] = $body[$i]->campaignName;
                                    $storeArray['attributedUnitsOrderedNewToBrandPercentage14d'] = $body[$i]->attributedUnitsOrderedNewToBrandPercentage14d;
                                    $storeArray['keywordBid'] = $body[$i]->keywordBid;
                                    $storeArray['keywordStatus'] = $body[$i]->keywordStatus;
                                    $storeArray['targetId'] = isset($body[$i]->targetId) ? $body[$i]->targetId : 0;
                                    $storeArray['targetingExpression'] = isset($body[$i]->targetingExpression) ? $body[$i]->targetingExpression : 'NA';
                                    $storeArray['targetingText'] = isset($body[$i]->targetingText) ? $body[$i]->targetingText : 'NA';
                                    $storeArray['targetingType'] = isset($body[$i]->targetingType)
                                        ? $body[$i]->targetingType : 'NA';
                                    $storeArray['attributedDetailPageViewsClicks14d'] = $body[$i]->attributedDetailPageViewsClicks14d;
                                    $storeArray['unitsSold14d'] = $body[$i]->unitsSold14d;
                                    $storeArray['dpv14d'] = $body[$i]->dpv14d;
                                    $storeArray['reportDate'] = $single->reportDate;
                                    $storeArray['creationDate'] = date('Y-m-d');
                                    $insertDataObject = new AMSModel();
                                    $insertDataObject->addSBDownloadedkeywordReport($storeArray);
                                }// end for-loop

                                // store report status
                                AMSModel::insertTrackRecord('report name : Keyword SB Report Data' . ' profile id: ' . $single->profileID, 'record found');
                            } else {
                                // store report status
                                AMSModel::insertTrackRecord('report name : Keyword SB Report Data' . ' profile id: ' . $single->profileID, 'not record found');
                            }
                        } catch (\Exception $ex) {
                            if ($ex->getCode() == 401) {
                                if (strstr($ex->getMessage(), '401 Unauthorized')) { // if auth token expire
                                    Log::error('Refresh Access token. In file filePath:Commands\Ams\Keyword\SB\getReportLinkDataCron');
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
                            AMSModel::insertTrackRecord('Keyword SB Report Data', 'fail');
                            Log::error($ex->getMessage());
                        }//end catch
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
        Log::info("filePath:Commands\Ams\Keyword\SB\getReportLinkDataCron. End Cron.");
    }
}
