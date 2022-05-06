<?php

namespace App\Console\Commands\Ams\Keyword\Data;

use Artisan;
use DB;
use App\models\BiddingRule;
use App\Models\AMSModel;
use GuzzleHttp\Client;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Log;

class listofkeyword extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'keywordlist:amsKeywordlist {fkBiddingRuleId}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'This command is used to get keyword list of specific campaign type.';

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
        $fkBiddingRuleId = $this->argument('fkBiddingRuleId');
        Log::info("filePath:App\Console\Commands\Ams\Keyword\ListData\listofkeyword. Start Cron.");
        Log::info($this->description);
                $getDataForBiddingRuleCorn = BiddingRule::getDataForBiddingRuleCorn($fkBiddingRuleId);
                if (!empty($getDataForBiddingRuleCorn)) {
                    $DataArray = array();
                    foreach ($getDataForBiddingRuleCorn as $single) {
                        $clientId = $single->client_id;
                        $fkConfigId = $single->fkConfigId;
                        if (!empty($clientId)) {
                        $url = ''; // Create a client with a base URI
                        $reportType = '';
                        if ($single->sponsoredType == 'sponsoredProducts') {
                            $url = Config::get('constants.amsApiUrl') . '/' . Config::get('constants.apiVersion') . '/' . Config::get('constants.spKeywordList') . '?startIndex=0&campaignType=' . $single->sponsoredType . '&campaignIdFilter=' . $single->campaignId;
                            $reportType = 'SP';
                        } else if ($single->sponsoredType == 'sponsoredBrands') {
                            $url = Config::get('constants.amsApiUrl') . '/' . Config::get('constants.sbKeywordList');
                            $reportType = 'SB';
                        } else if ($single->sponsoredType == 'sponsoredDisplay') {
                            $url = Config::get('constants.amsApiUrl') . '/' . Config::get('constants.sdTargetsList');
                            $reportType = 'SD';
                        }
                        b:
                        $obaccess_token = new AMSModel();
                        $getAMSTokenById = $obaccess_token->getAMSTokenById($fkConfigId);
                        $accessToken = $getAMSTokenById->access_token;
                        if (!empty($accessToken)) {
                        $client = new Client();
                        $body = array();
                        try {
                            $response = $client->request('GET', $url, [
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
                                BiddingRule::updateBiddingCampaignApiStatus($single->id);
                                $DataArray = array();
                                Log::info("Make Array For Data Insertion");
                                for ($i = 0; $i < count($body); $i++) {
                                    $storeArray = [];
                                    $storeArray['fkId'] = $single->id;
                                    $storeArray['fkBiddingRuleId'] = $single->fkBiddingRuleId;
                                    $storeArray['fkConfigId'] = $fkConfigId;
                                    $storeArray['profileId'] = $single->profileId;
                                    $storeArray['reportType'] = $reportType;
                                    $storeArray['keywordId'] = $body[$i]->keywordId;
                                    $storeArray['adGroupId'] = $body[$i]->adGroupId;
                                    $storeArray['campaignId'] = $body[$i]->campaignId;
                                    $storeArray['keywordText'] = $body[$i]->keywordText;
                                    $storeArray['matchType'] = $body[$i]->matchType;
                                    $storeArray['state'] = $body[$i]->state;
                                    $storeArray['bid'] = isset($body[$i]->bid) ? $body[$i]->bid : '0.00';
                                    $storeArray['servingStatus'] = isset($body[$i]->servingStatus) ? $body[$i]->servingStatus : 'NA';
                                    $storeArray['creationDate'] = isset($body[$i]->creationDate) ? $body[$i]->creationDate : 'NA';
                                    $storeArray['lastUpdatedDate'] = isset($body[$i]->lastUpdatedDate) ? $body[$i]->lastUpdatedDate : 'NA';
                                    $storeArray['createdAt'] = date('Y-m-d H:i:s');
                                    $storeArray['updatedAt'] = date('Y-m-d H:i:s');
                                    if ($single->campaignId == $body[$i]->campaignId && $reportType == 'SB') {
                                        array_push($DataArray, $storeArray);
                                    } elseif ($single->campaignId == $body[$i]->campaignId && $reportType == 'SP') {
                                        array_push($DataArray, $storeArray);
                                    }
                                }// end for loop
                                if (!empty($DataArray)) {
                                    // store profile list not valid
                                    BiddingRule::storeKeywordData($DataArray);
                                }
                            } else {
                                // if body is empty
                            }
                        } catch (\Exception $ex) {
                            if ($ex->getCode() == 401) {
                                if (strstr($ex->getMessage(), '401 Unauthorized')) { // if auth token expire
                                    Log::error('Refresh Access token. In file filePath:App\Console\Commands\Ams\Keyword\ListData\listofkeyword');
                                    $authCommandArray = array();
                                    $authCommandArray['fkConfigId'] = $fkConfigId;
                                    \Artisan::call('getaccesstoken:amsauth', $authCommandArray);
                                    goto b;
                                } elseif (strstr($ex->getMessage(), 'advertiser found for scope')) {
                                    // store profile list not valid
                                    BiddingRule::inValidProfile($single->id, $single->fkBiddingRuleId, $single->profileId,
                                        $single->campaignId);
                                }
                            } else if ($ex->getCode() == 429) { //https://advertising.amazon.com/API/docs/v2/guides/developer_notes#Rate-limiting
                                sleep(Config::get('constants.sleepTime') + 2);
                                goto b;
                            } else if ($ex->getCode() == 502) {
                                sleep(Config::get('constants.sleepTime') + 2);
                                goto b;
                            }
                            Log::error($ex->getMessage());
                        }// end catch
                        } else {
                            Log::info("AMS access token not found.");
                        }
                    } else {
                        Log::info("Client Id not found.");
                    }
                    }// end foreach
                } else {
                    Log::info("bidding rule campaign not found.");
                }
        Log::info("filePath:App\Console\Commands\Ams\Keyword\ListData\listofkeyword. End Cron.");
    }
}