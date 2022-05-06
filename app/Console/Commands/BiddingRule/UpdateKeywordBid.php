<?php

namespace App\Console\Commands\BiddingRule;

use App\Models\AMSModel;
use App\Models\BiddingRuleTracker;
use GuzzleHttp\Client;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Log;

class UpdateKeywordBid extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'updateKeywordbid:updatebid {data*}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'This command is use for update the bidding rule value of specific keyword SP.';

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
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function handle()
    {
        Log::info("App\Console\Commands\BiddingRule\UpdateKeywordBid. Start Cron.");
        Log::info($this->description);
                $dataArgumants = $this->argument('data');
                if (!empty($dataArgumants)) {
                    $clientId = $dataArgumants['clientId'];
                    $fkConfigId = $dataArgumants['fkConfigId'];
                    if (!empty($clientId)) {
                    $jsonArray = array();
                    $url = ''; // Create a client with a base URI
                    if ($dataArgumants['reportType'] == 'SP') {
                        $url = Config::get('constants.amsApiUrl') . '/' . Config::get('constants.apiVersion') . '/' . Config::get('constants.spKeywordUpdateBid');
                        $jsonArrayObj = (object)[
                            'keywordId' => $dataArgumants['keywordId'],
                            'state' => $dataArgumants['state'],
                            'bid' => $dataArgumants['newbid']
                        ];
                        array_push($jsonArray, $jsonArrayObj);
                    } else if ($dataArgumants['reportType'] == 'SB') {
                        $url = Config::get('constants.amsApiUrl') . '/' . Config::get('constants.sbKeywordList');
                        $jsonArrayObj = (object)[
                            'keywordId' => $dataArgumants['keywordId'],
                            'adGroupId' => $dataArgumants['adGroupId'],
                            'campaignId' => $dataArgumants['campaignId'],
                            'state' => $dataArgumants['state'],
                            'bid' => $dataArgumants['newbid']
                        ];
                        array_push($jsonArray, $jsonArrayObj);
                    }
                    if (!empty($jsonArray)) {
                        a:
                        $obaccess_token = new AMSModel();
                        $getAMSTokenById = $obaccess_token->getAMSTokenById($fkConfigId);
                        $accessToken = $getAMSTokenById->access_token;
                        if (!empty($accessToken)) {
                        try {
                            $client = new Client();
                            $response = $client->request('PUT', $url, [
                                'headers' => [
                                    'Authorization' => 'Bearer ' . $accessToken,
                                    'Content-Type' => 'application/json',
                                    'Amazon-Advertising-API-ClientId' => $clientId,
                                    'Amazon-Advertising-API-Scope' => $dataArgumants['profileId']
                                ],
                                'json' => $jsonArray,
                                'delay' => Config::get('constants.delayTimeInApi'),
                                'connect_timeout' => Config::get('constants.connectTimeOutInApi'),
                                'timeout' => Config::get('constants.timeoutInApi'),
                            ]);
                            $body = json_decode($response->getBody()->getContents());
                            if (!empty($body) && $body != null) {
                                Log::info("Make Array For Data Insertion");
                                $storeArray = [
                                    'profileId' => $dataArgumants['profileId'],
                                    'adGroupId' => $dataArgumants['adGroupId'],
                                    'campaignId' => $dataArgumants['campaignId'],
                                    'state' => $dataArgumants['state'],
                                    'reportType' => $dataArgumants['reportType'],
                                    'oldBid' => $dataArgumants['oldbid'],
                                    'bid' => $dataArgumants['newbid'],
                                    'keywordId' => $body[0]->keywordId,
                                    'code' => $body[0]->code,
                                    'creationDate' => date('Y-m-d')
                                ];
                                BiddingRuleTracker::create($storeArray);
                            }
                        } catch (\Exception $ex) {
                            if ($ex->getCode() == 401) {
                                if (strstr($ex->getMessage(), '401 Unauthorized')) { // if auth token expire
                                    Log::error('Refresh Access token. In file filePath:App\Console\Commands\BiddingRule\UpdateKeywordBid');
                                    $authCommandArray = array();
                                    $authCommandArray['fkConfigId'] = $fkConfigId;
                                    \Artisan::call('getaccesstoken:amsauth', $authCommandArray);
                                    goto a;
                                } elseif (strstr($ex->getMessage(), 'advertiser found for scope')) {
                                    // store profile list not valid
                                    Log::info("Invalid Profile Id: " . $dataArgumants['profileId']);
                                }
                            } else if ($ex->getCode() == 429) { //https://advertising.amazon.com/API/docs/v2/guides/developer_notes#Rate-limiting
                                sleep(Config::get('constants.sleepTime') + 2);
                                goto a;
                            } else if ($ex->getCode() == 502) {
                                sleep(Config::get('constants.sleepTime') + 2);
                                goto a;
                            }
                            // store report status
                            AMSModel::insertTrackRecord(json_encode($ex->getMessage()), 'fail');
                            Log::error($ex->getMessage());
                        }
                    } else {
                        Log::info("AMS access token not found.");
                    }
                    } else {
                        Log::info("JSON Array Empty");
                    }
                } else {
                    Log::info("Client Id not found.");
                }
                } else {
                    Log::info("All Get Reports download link not found.");
                }
        Log::info("filePath:App\Console\Commands\BiddingRule\UpdateKeywordBid. End Cron.");
    }
}
