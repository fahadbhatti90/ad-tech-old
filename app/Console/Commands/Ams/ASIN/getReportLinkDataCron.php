<?php

namespace App\Console\Commands\Ams\ASIN;

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
    protected $signature = 'getasinreportlinkdata:asinreport';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'This command is used to get ASIN encoded Data From location';

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
        Log::info("filePath:App\Console\Commands\Ams\ASIN\getReportLinkDataCron. Start Cron.");
        Log::info($this->description);
        $errorType = "0";
        Log::info("Message from a $errorType ");
        $AllGetReportsDownloadLink = AMSModel::getSpASINDownloadLink();
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
                                AMSModel::UpdateSpASINStatus($single->id, $totalNumberOfRecords);
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
                                    $storeArray['adGroupName'] = $body[$i]->adGroupName;
                                    $storeArray['adGroupId'] = $body[$i]->adGroupId;
                                    $storeArray['keywordId'] = $body[$i]->keywordId;
                                    $storeArray['keywordText'] = $body[$i]->keywordText;
                                    $storeArray['asin'] = $body[$i]->asin;
                                    $storeArray['otherAsin'] = $body[$i]->otherAsin;
                                    $storeArray['sku'] = isset($body[$i]->sku) ? $body[$i]->sku : 'NA';
                                    $storeArray['currency'] = $body[$i]->currency;
                                    $storeArray['matchType'] = $body[$i]->matchType;
                                    //created 24_feb_2020
                                    $storeArray['attributedUnitsOrdered1d'] = $body[$i]->attributedUnitsOrdered1d;
                                    $storeArray['attributedUnitsOrdered7d'] = $body[$i]->attributedUnitsOrdered7d;
                                    $storeArray['attributedUnitsOrdered14d'] = $body[$i]->attributedUnitsOrdered14d;
                                    $storeArray['attributedUnitsOrdered30d'] = $body[$i]->attributedUnitsOrdered30d;
                                    //-------------------
                                    $storeArray['attributedUnitsOrdered1dOtherSKU'] = $body[$i]->attributedUnitsOrdered1dOtherSKU;
                                    $storeArray['attributedUnitsOrdered7dOtherSKU'] = $body[$i]->attributedUnitsOrdered7dOtherSKU;
                                    $storeArray['attributedUnitsOrdered14dOtherSKU'] = $body[$i]->attributedUnitsOrdered14dOtherSKU;
                                    $storeArray['attributedUnitsOrdered30dOtherSKU'] = $body[$i]->attributedUnitsOrdered30dOtherSKU;
                                    $storeArray['attributedSales1dOtherSKU'] = $body[$i]->attributedSales1dOtherSKU;
                                    $storeArray['attributedSales7dOtherSKU'] = $body[$i]->attributedSales7dOtherSKU;
                                    $storeArray['attributedSales14dOtherSKU'] = $body[$i]->attributedSales14dOtherSKU;
                                    $storeArray['attributedSales30dOtherSKU'] = $body[$i]->attributedSales30dOtherSKU;
                                    $storeArray['reportDate'] = $single->reportDate;
                                    $storeArray['creationDate'] = date('Y-m-d');
                                    array_push($DataArray, $storeArray);
                                }// end for loop
                                if (!empty($DataArray)) {
                                    $insertDataObject = new AMSModel();
                                    $insertDataObject->addSpDownloadedASINReport($DataArray);
                                }
                                // store report status
                                AMSModel::insertTrackRecord('report name : ASIN Report Data' . ' profile id: ' . $single->profileID, 'record found');
                            } else {
                                // store report status
                                AMSModel::insertTrackRecord('report name : ASIN Report Data' . ' profile id: ' . $single->profileID, 'not record found');
                            }
                        } catch (\Exception $ex) {
                            if ($ex->getCode() == 401) {
                                if (strstr($ex->getMessage(), '401 Unauthorized')) { // if auth token expire
                                    Log::error('Refresh Access token. In file filePath:Commands\Ams\ASIN\getReportLinkDataCron');
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
                            AMSModel::insertTrackRecord('ASIN Report Data', 'fail');
                            Log::error($ex->getMessage());
                        }//end catch
                        } else {
                            Log::info("AMS access token not found.");
                        }//end else
                    } else {
                        Log::info("Client Id not found.");
                    }//end else
                    }// end foreach

                } else {
                    Log::info("All Get Reports download link not found.");
                }
        Log::info("filePath:App\Console\Commands\Ams\ASIN\getReportLinkDataCron. End Cron.");
    }
}
