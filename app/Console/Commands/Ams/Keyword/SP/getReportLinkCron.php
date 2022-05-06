<?php

namespace App\Console\Commands\Ams\Keyword\SP;

use Artisan;
use DB;
use App\Models\AMSModel;
use GuzzleHttp\Client;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Log;

class getReportLinkCron extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'getkeywordreportlink:spkeyword';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'This command is used to get keyword SP report link location.';

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
        Log::info("filePath:Commands\Ams\Keyword\SP\getReportLinkCron. Start Cron.");
        Log::info($this->description);
        $reportType = 'Keyword_SP';
                // get Specific Report Type ID
                $AllGetReportsObject = new AMSModel();
                $AllGetReports = $AllGetReportsObject->getAllReportID($reportType);
                if (!empty($AllGetReports)) {
                    $DataArray = array();
                    foreach ($AllGetReports as $single) {
                        $clientId = $single->client_id;
                        $fkConfigId = $single->fkConfigId;
                        if (!empty($clientId)) {
                        $body = array();
                        // Create a client with a base URI
                        $url = Config::get('constants.amsApiUrl') . '/' . Config::get('constants.apiVersion') . '/' . Config::get('constants.downloadReport');
                        a:
                        $obaccess_token = new AMSModel();
                        $getAMSTokenById = $obaccess_token->getAMSTokenById($fkConfigId);
                        $accessToken = $getAMSTokenById->access_token;
                        if (!empty($accessToken)) {
                        try {
                            $client = new Client();
                            $response = $client->request('GET', $url . '/' . $single->reportId, [
                                'headers' => [
                                    'Authorization' => 'Bearer ' . $accessToken,
                                    'Content-Type' => 'application/json',
                                    'Amazon-Advertising-API-ClientId' => $clientId,
                                    'Amazon-Advertising-API-Scope' => $single->profileId],
                                'delay' => Config::get('constants.delayTimeInApi'),
                                'connect_timeout' => Config::get('constants.connectTimeOutInApi'),
                                'timeout' => Config::get('constants.timeoutInApi'),
                            ]);
                            $body = json_decode($response->getBody()->getContents());
                            if (!empty($body)) {
                                $storeArray = [];
                                $reportDate = $single->amsReportDate; // ams report date
                                AMSModel::UpdateReportsStatus($single->amsID, $reportType, $reportDate, 1);
                                Log::info("Make Array For Data Insertion");
                                $storeArray['profileID'] = $single->profileId;
                                $storeArray['fkAccountId'] = $single->fkAccountId;
                                $storeArray['fkBatchId'] = $single->fkBatchId;
                                $storeArray['fkConfigId'] = $fkConfigId;
                                $storeArray['reportId'] = $body->reportId;
                                $storeArray['status'] = $body->status;
                                $storeArray['statusDetails'] = $body->statusDetails;
                                $storeArray['isDone'] = 0;
                                if ($body->status == 'FAILURE') {
                                    $storeArray['location'] = 'not available';
                                    $storeArray['fileSize'] = 'not available';
                                    $storeArray['isDone'] = 3; // not find URL
                                } else {
                                    if (isset($body->location)) {
                                        $storeArray['location'] = $body->location;
                                    } else {
                                        AMSModel::UpdateReportsStatus($single->amsID, $reportType, $reportDate, 0);
                                        goto a;
                                    }
                                    $storeArray['fileSize'] = $body->fileSize;
                                    if ($storeArray['fileSize'] == 22) {
                                        $storeArray['isDone'] = 2; // FILE SIZE is 22 because its empty not record found
                                    }
                                }
                                $storeArray['reportDate'] = $reportDate;
                                $storeArray['creationDate'] = date('Y-m-d');
                                // store date into DB
                                $addReportIdObj = new AMSModel();
                                $addReportIdObj->addSpKeywordReportDownloadLink($storeArray);
                                // store report status
                                AMSModel::insertTrackRecord('report name : Keyword SP Report Link' . ' profile id: ' . $single->profileId, 'record found');
                            } else {
                                // store report status
                                AMSModel::insertTrackRecord('report name : Keyword SP Report Link' . ' profile id: ' . $single->profileId, 'not record found');
                            }
                        } catch (\Exception $ex) {
                            if ($ex->getCode() == 401) {
                                if (strstr($ex->getMessage(), '401 Unauthorized')) { // if auth token expire
                                    Log::error('Refresh Access token. In file filePath:Commands\Ams\Keyword\SP\getReportLinkCron');
                                    $authCommandArray = array();
                                    $authCommandArray['fkConfigId'] = $fkConfigId;
                                    \Artisan::call('getaccesstoken:amsauth', $authCommandArray);
                                    goto a;
                                } elseif (strstr($ex->getMessage(), 'advertiser found for scope')) {
                                    // store profile list not valid
                                    Log::info("Invalid Profile Id: " . $single->profileId);
                                }
                            } else if ($ex->getCode() == 429) { //https://advertising.amazon.com/API/docs/v2/guides/developer_notes#Rate-limiting
                                sleep(Config::get('constants.sleepTime') + 2);
                                goto a;
                            } else if ($ex->getCode() == 502) {
                                sleep(Config::get('constants.sleepTime') + 2);
                                goto a;
                            }
                            // store report status
                            AMSModel::insertTrackRecord('Keyword SP Report Link', 'fail');
                            Log::error($ex->getMessage());
                        }//end catch
                        } else {
                            Log::info("AMS access token not found.");
                        }//end else
                    } else {
                        Log::info("Client Id not found.");
                    }
                    }// end foreach
                } else {
                    Log::info("All Get Reports download link not found.");
                }
        Log::info("filePath:Commands\Ams\Keyword\SP\getReportLinkCron. End Cron.");
    }
}
