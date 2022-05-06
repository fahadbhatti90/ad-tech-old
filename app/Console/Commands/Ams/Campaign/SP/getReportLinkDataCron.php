<?php

namespace App\Console\Commands\Ams\Campaign\SP;

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
    protected $signature = 'getcampaignreportlinkdata:spcampaign';

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
     * @return mixed
     * @throws \GuzzleHttp\Exception\GuzzleException
     *  Execute the console command.
     *
     */
    public function handle()
    {
        Log::info("filePath:Commands\Ams\Campaign\SP\getReportLinkDataCron. Start Cron.");
        Log::info($this->description);
        $errorType = "0";
        Log::info("Message from a $errorType ");
        $AllGetReportsDownloadLink = AMSModel::getAllReportsDownloadLink();
                if (!empty($AllGetReportsDownloadLink)) {
                    foreach ($AllGetReportsDownloadLink as $single) {
                        $clientId = $single->client_id;
                        if (!empty($clientId)) {
                        $fkConfigId = $single->fkConfigId;
                        $body = array();
                        // Create a client with a base URI
                        $url = $single->location;
                        a:
                        $obaccess_token = new AMSModel();
                        $getAMSTokenById = $obaccess_token->getAMSTokenById($fkConfigId);
                        $access_token = $getAMSTokenById->access_token;
                        if (!empty($access_token)) {
                        try {
                            $client = new Client();
                            $response = $client->request('GET', $url, [
                                'headers' => [
                                    'Authorization' => 'Bearer ' . $access_token,
                                    'Amazon-Advertising-API-ClientId' => $clientId,
                                    'Amazon-Advertising-API-Scope' => $single->profileID],
                                'delay' => Config::get('constants.delayTimeInApi'),
                                'connect_timeout' => Config::get('constants.connectTimeOutInApi'),
                                'timeout' => Config::get('constants.timeoutInApi'),
                            ]);
                            $body = json_decode(gzdecode($response->getBody()->getContents()));
                            if (!empty($body) && $body != null) {
                                $totalNumberOfRecords = count($body);
                                AMSModel::UpdateCampaignsReportsStatus($single->id, $totalNumberOfRecords);
                                Log::info("Make Array For Data Insertion");
                                $DataArray = [];
                                for ($i = 0; $i < $totalNumberOfRecords; $i++) {
                                    $storeArray = [];
                                    $storeArray['fkReportsDownloadLinksId'] = $single->id;
                                    $storeArray['fkProfileId'] = $single->profileID;
                                    $storeArray['fkBatchId'] = $single->fkBatchId;
                                    $storeArray['fkAccountId'] = $single->fkAccountId;
                                    $storeArray['fkConfigId'] = $fkConfigId;
                                    $storeArray['bidPlus'] = ($body[$i]->bidPlus == true) ? 1 : 0;
                                    $storeArray['attributedSales7d'] = $body[$i]->attributedSales7d;
                                    $storeArray['attributedSales30d'] = $body[$i]->attributedSales30d;
                                    $storeArray['attributedUnitsOrdered30d'] = $body[$i]->attributedUnitsOrdered30d;
                                    $storeArray['attributedSales1d'] = $body[$i]->attributedSales1d;
                                    $storeArray['attributedConversions1d'] = $body[$i]->attributedConversions1d;
                                    $storeArray['attributedSales1dSameSKU'] = $body[$i]->attributedSales1dSameSKU;
                                    $storeArray['attributedConversions30d'] = $body[$i]->attributedConversions30d;
                                    $storeArray['attributedConversions7d'] = $body[$i]->attributedConversions7d;
                                    $storeArray['attributedConversions14d'] = $body[$i]->attributedConversions14d;
                                    $storeArray['campaignStatus'] = $body[$i]->campaignStatus;
                                    $storeArray['attributedConversions7dSameSKU'] = $body[$i]->attributedConversions7dSameSKU;
                                    $storeArray['attributedConversions1dSameSKU'] = $body[$i]->attributedConversions1dSameSKU;
                                    $storeArray['cost'] = $body[$i]->cost;
                                    $storeArray['portfolioId'] = isset($body[$i]->portfolioId) ? $body[$i]->portfolioId : '0';
                                    $storeArray['portfolioName'] = isset($body[$i]->portfolioName) ? $body[$i]->portfolioName : 'NA';
                                    $storeArray['attributedUnitsOrdered7d'] = $body[$i]->attributedUnitsOrdered7d;
                                    $storeArray['attributedSales7dSameSKU'] = $body[$i]->attributedSales7dSameSKU;
                                    $storeArray['campaignId'] = $body[$i]->campaignId;
                                    $storeArray['attributedSales14dSameSKU'] = $body[$i]->attributedSales14dSameSKU;
                                    $storeArray['attributedSales30dSameSKU'] = $body[$i]->attributedSales30dSameSKU;
                                    $storeArray['impressions'] = $body[$i]->impressions;
                                    $storeArray['attributedUnitsOrdered1d'] = $body[$i]->attributedUnitsOrdered1d;
                                    $storeArray['attributedConversions30dSameSKU'] = $body[$i]->attributedConversions30dSameSKU;
                                    $storeArray['campaignBudget'] = $body[$i]->campaignBudget;
                                    $storeArray['attributedConversions14dSameSKU'] = $body[$i]->attributedConversions14dSameSKU;
                                    $storeArray['clicks'] = $body[$i]->clicks;
                                    $storeArray['attributedSales14d'] = $body[$i]->attributedSales14d;
                                    $storeArray['campaignName'] = $body[$i]->campaignName;
                                    $storeArray['attributedUnitsOrdered14d'] = $body[$i]->attributedUnitsOrdered14d;
                                    //created 24_feb_2020
                                    $storeArray['attributedUnitsOrdered1dSameSKU'] = $body[$i]->attributedUnitsOrdered1dSameSKU;
                                    $storeArray['attributedUnitsOrdered7dSameSKU'] = $body[$i]->attributedUnitsOrdered7dSameSKU;
                                    $storeArray['attributedUnitsOrdered14dSameSKU'] = $body[$i]->attributedUnitsOrdered14dSameSKU;
                                    $storeArray['attributedUnitsOrdered30dSameSKU'] = $body[$i]->attributedUnitsOrdered30dSameSKU;
                                    //-------------------
                                    $storeArray['reportDate'] = $single->reportDate;
                                    $storeArray['creationDate'] = date('Y-m-d');
                                    array_push($DataArray, $storeArray);
                                }// end for loop
                                if (!empty($DataArray)) {
                                    $insertDataObject = new AMSModel();
                                    $insertDataObject->addDownloadedcampaignReport($DataArray);
                                }
                                // store report status
                                AMSModel::insertTrackRecord('report name : Campaign SP Report Data' . ' profile id: ' . $single->profileID, 'record found');
                            } else {
                                // store report status
                                AMSModel::insertTrackRecord('report name : Campaign SP Report Data' . ' profile id: ' . $single->profileID, 'not record found');
                            }
                        } catch (\Exception $ex) {
                            if ($ex->getCode() == 401) {
                                if (strstr($ex->getMessage(), '401 Unauthorized')) { // if auth token expire
                                    Log::error('Refresh Access token. In file filePath:Commands\Ams\Campaign\SP\getReportLinkDataCron');
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
                            AMSModel::insertTrackRecord('Campaign SP Report Data', 'fail');
                            Log::error($ex->getMessage());
                        }
                        } else {
                            Log::info("AMS access token not found.");
                        }//end catch
                    } else {
                        Log::info("Client Id not found.");
                    }//end if
                    }// end foreach
                } else {
                    Log::info("All Get Reports download link not found.");
                }
        Log::info("filePath:Commands\Ams\Campaign\SP\getReportLinkDataCron. End Cron.");
    }
}
