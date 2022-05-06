<?php

namespace App\Console\Commands\Ams\AdGroup\SP;

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
    protected $signature = 'getadgroupreportlinkdata:spadgroup';

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
     * @return mixed
     * @throws \GuzzleHttp\Exception\GuzzleException
     * Execute the console command.
     *
     */
    public function handle()
    {

        Log::info("filePath:Commands\Ams\AdGroup\SP\getReportLinkDataCron. Start Cron.");
        Log::info($this->description);
                $getSpAdGroupDownloadLinkObject = new AMSModel();
                $AllGetReportsDownloadLink = $getSpAdGroupDownloadLinkObject->getSpAdGroupDownloadLink();
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
                                AMSModel::UpdateSpAdGroupStatus($single->id, $totalNumberOfRecords);
                                Log::info("Make Array For Data Insertion");
                                $DataArray = [];
                                for ($i = 0; $i < $totalNumberOfRecords; $i++) {
                                    $storeArray = [];
                                    $storeArray['fkReportsDownloadLinksId'] = $single->id;
                                    $storeArray['fkProfileId'] = $single->profileID;
                                    $storeArray['fkBatchId'] = $single->fkBatchId;
                                    $storeArray['fkAccountId'] = $single->fkAccountId;
                                    $storeArray['fkConfigId'] = $fkConfigId;
                                    $storeArray['attributedSales7d'] = $body[$i]->attributedSales7d;
                                    $storeArray['attributedSales30d'] = $body[$i]->attributedSales30d;
                                    $storeArray['attributedUnitsOrdered30d'] = $body[$i]->attributedUnitsOrdered30d;
                                    $storeArray['attributedSales1d'] = $body[$i]->attributedSales1d;
                                    $storeArray['attributedConversions1d'] = $body[$i]->attributedConversions1d;
                                    $storeArray['attributedSales1dSameSKU'] = $body[$i]->attributedSales1dSameSKU;
                                    $storeArray['attributedConversions30d'] = $body[$i]->attributedConversions30d;
                                    $storeArray['adGroupId'] = $body[$i]->adGroupId;
                                    $storeArray['attributedConversions7d'] = $body[$i]->attributedConversions7d;
                                    $storeArray['attributedConversions14d'] = $body[$i]->attributedConversions14d;
                                    $storeArray['attributedConversions7dSameSKU'] = $body[$i]->attributedConversions7dSameSKU;
                                    $storeArray['attributedConversions1dSameSKU'] = $body[$i]->attributedConversions1dSameSKU;
                                    $storeArray['cost'] = $body[$i]->cost;
                                    $storeArray['adGroupName'] = $body[$i]->adGroupName;
                                    $storeArray['attributedUnitsOrdered7d'] = $body[$i]->attributedUnitsOrdered7d;
                                    $storeArray['attributedSales7dSameSKU'] = $body[$i]->attributedSales7dSameSKU;
                                    $storeArray['campaignId'] = $body[$i]->campaignId;
                                    $storeArray['attributedSales14dSameSKU'] = $body[$i]->attributedSales14dSameSKU;
                                    $storeArray['attributedSales30dSameSKU'] = $body[$i]->attributedSales30dSameSKU;
                                    $storeArray['impressions'] = $body[$i]->impressions;
                                    $storeArray['attributedUnitsOrdered1d'] = $body[$i]->attributedUnitsOrdered1d;
                                    $storeArray['attributedConversions30dSameSKU'] = $body[$i]->attributedConversions30dSameSKU;
                                    $storeArray['attributedConversions14dSameSKU'] = $body[$i]->attributedConversions14dSameSKU;
                                    $storeArray['clicks'] = $body[$i]->clicks;
                                    $storeArray['attributedSales14d'] = $body[$i]->attributedSales14d;
                                    $storeArray['campaignName'] = $body[$i]->campaignName;
                                    $storeArray['attributedUnitsOrdered14d'] = $body[$i]->attributedUnitsOrdered14d;
                                    $storeArray['reportDate'] = $single->reportDate;
                                    $storeArray['creationDate'] = date('Y-m-d');
                                    array_push($DataArray, $storeArray);
                                }// end for-loop
                                if (!empty($DataArray)) {
                                    $insertDataObject = new AMSModel();
                                    $insertDataObject->addSpDownloadedAdGroupReport($DataArray);
                                }
                                // store report status
                                AMSModel::insertTrackRecord('report name : AdGroup Report Data' . ' profile id: ' . $single->profileID, 'record found');
                            } else {
                                // store report status
                                AMSModel::insertTrackRecord('report name : AdGroup Report Data' . ' profile id: ' . $single->profileID, 'not record found');
                            }
                        } catch (\Exception $ex) {
                            if ($ex->getCode() == 401) {
                                if (strstr($ex->getMessage(), '401 Unauthorized')) { // if auth token expire
                                    Log::error('Refresh Access token. In file filePath:Commands\Ams\AdGroup\SP\getReportLinkDataCron.');
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
                        }//end catch
                        } else {
                            Log::info("AMS access token not found.");
                        }
                    } else {
                        Log::info("Client Id not found.");
                    }//end if
                    }// end foreach
                } else {
                    Log::info("All Get Reports download link not found.");
                }
        Log::info("filePath:Commands\Ams\AdGroup\SP\getReportLinkDataCron. End Cron.");
    }
}
