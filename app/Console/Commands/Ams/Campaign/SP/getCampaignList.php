<?php

namespace App\Console\Commands\Ams\Campaign\SP;

use Artisan;
use DB;
use App\Models\AMSModel;
use App\Models\DayPartingModels\PortfolioAllCampaignList;
use GuzzleHttp\Client;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Log;

class getCampaignList extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'getCampaignList:campaignSP';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

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
        Log::info("filePath:App\Console\Commands\Ams\Campaign\SP\getCampaignList. Start Cron.");
        Log::info($this->description);
        //update all auth tokens before this cron
        Artisan::call('getallaccesstoken:amsauth');
        $obAccessToken = new AMSModel();
        Log::info("Auth token get from DB Start!");
                b:
                $allProfileIdsObject = new AMSModel();
                $allProfileIds = $allProfileIdsObject->getAllProfiles();
                if (!empty($allProfileIds)) {
                    foreach ($allProfileIds as $single) {
                        $clientId = $single->client_id;
                        $fkConfigId = $single->fkConfigId;
                        Log::info("Bidding Rule Profile Id = ".$single->profileId." Config Id = ". $fkConfigId);
                        if (!empty($clientId)) {
                        $responseBody = array();
                        // Create a client with a base URI
                        $url = Config::get('constants.amsApiUrl') . '/' . Config::get('constants.apiVersion') . '/' . Config::get('constants.spCampaignUrl');
                        $obaccess_token = new AMSModel();
                        $getAMSTokenById = $obaccess_token->getAMSTokenById($fkConfigId);
                        $accessToken = $getAMSTokenById->access_token;
                        if (!empty($accessToken)) {
                        try {
                            $client = new Client();
                            $response = $client->request('GET', $url, [
                                'headers' => [
                                    'Authorization' => 'Bearer ' . $accessToken,
                                    'Content-Type' => 'application/json',
                                    'Amazon-Advertising-API-ClientId' => $clientId,
                                    'Amazon-Advertising-API-Scope' => $single->profileId
                                ],
                                'delay' => Config::get('constants.delayTimeInApi'),
                                'connect_timeout' => Config::get('constants.connectTimeOutInApi'),
                                'timeout' => Config::get('constants.timeoutInApi'),
                            ]);
                            $responseBody = json_decode($response->getBody()->getContents());

                            if (!empty($responseBody) && !is_null($responseBody)) {
                                $campaignStoreArray = array();
                                $responseCount = count($responseBody);
                                for ($i = 0; $i < $responseCount; $i++) {
                                    $dbStore = [];
                                    $dbStore['fkProfileId'] = $single->id;
                                    $dbStore['fkConfigId'] = $fkConfigId;
                                    $dbStore['profileId'] = $single->profileId;
                                    $dbStore['type'] = 'SP';
                                    $dbStore['campaignType'] = $responseBody[$i]->campaignType;
                                    $dbStore['name'] = $responseBody[$i]->name;
                                    $dbStore['targetingType'] = $responseBody[$i]->targetingType;
                                    $dbStore['premiumBidAdjustment'] = $responseBody[$i]->premiumBidAdjustment;
                                    $dbStore['dailyBudget'] = $responseBody[$i]->dailyBudget;
                                    $dbStore['budget'] = isset($responseBody[$i]->budget) ? $responseBody[$i]->budget : 00.00;
                                    $dbStore['endDate'] = isset($responseBody[$i]->endDate) ? $responseBody[$i]->endDate : 'NA';
                                    $dbStore['bidOptimization'] = isset($responseBody[$i]->bidOptimization) ? $responseBody[$i]->bidOptimization : 'NA';
                                    $dbStore['portfolioId'] = (isset($responseBody[$i]->portfolioId) ? $responseBody[$i]->portfolioId : 0);
                                    $dbStore['campaignId'] = isset($responseBody[$i]->campaignId) ? $responseBody[$i]->campaignId : 0;
                                    $dbStore['budgetType'] = isset($responseBody[$i]->budgetType) ? $responseBody[$i]->budgetType : 'NA';
                                    $dbStore['startDate'] = isset($responseBody[$i]->startDate) ? $responseBody[$i]->startDate : 'NA';
                                    $dbStore['state'] = isset($responseBody[$i]->state) ? $responseBody[$i]->state : 'NA';
                                    $dbStore['servingStatus'] = isset($responseBody[$i]->servingStatus) ? $responseBody[$i]->servingStatus : 'NA';
                                    $dbStore['createdAt'] = date('Y-m-d H:i:s');
                                    $dbStore['updatedAt'] = date('Y-m-d H:i:s');
                                    $dbStore['pageType'] = 'NA';
                                    $dbStore['url'] = 'NA';
                                    $dbStore['brandName'] = 'NA';
                                    $dbStore['brandLogoAssetID'] = 'NA';
                                    $dbStore['headline'] = 'NA';
                                    $dbStore['shouldOptimizeAsins'] = 'NA';
                                    $dbStore['brandLogoUrl'] = 'NA';
                                    $dbStore['asins'] = 'NA';
                                    $dbStore['strategy'] = 'NA';
                                    $dbStore['predicate'] = 'NA';
                                    $dbStore['percentage'] = 0;
                                    if (isset($responseBody[$i]->bidding)) {
                                        $dbStore['strategy'] = isset($responseBody[$i]->bidding->strategy) ? $responseBody[$i]->bidding->strategy : 'NA';
                                        if (isset($responseBody[$i]->bidding->adjustments)) {
                                            $predicate = isset($responseBody[$i]->bidding->adjustments[0]->predicate) ? $responseBody[$i]->bidding->adjustments[0]->predicate : 'NA';
                                            $percentage = isset($responseBody[$i]->bidding->adjustments[0]->percentage) ? $responseBody[$i]->bidding->adjustments[0]->percentage : 0;
                                            $dbStore['predicate'] = $predicate;
                                            $dbStore['percentage'] = $percentage;
                                        }
                                    }  // End check Bidding Property
                                    array_push($campaignStoreArray, $dbStore);
                                } // End For Loop
                                PortfolioAllCampaignList::storeCampaignList($campaignStoreArray);
                                unset($campaignStoreArray);
                                unset($dbStore);
                            } else {
                                //
                            }
                        } catch (\Exception $ex) {
                            if ($ex->getCode() == 401) {
                                if (strstr($ex->getMessage(), '401 Unauthorized')) { // if auth token expire
                                    Log::error('Refresh Access token. In file filePath:App\Console\Commands\Ams\Campaign\SP\getCampaignList');
                                    $authCommandArray = array();
                                    $authCommandArray['fkConfigId'] = $fkConfigId;
                                    \Artisan::call('getaccesstoken:amsauth', $authCommandArray);
                                    \Artisan::call('getprofileid:amsprofile');
                                    goto b;
                                } elseif (strstr($ex->getMessage(), 'advertiser found for scope')) {
                                    // store profile list not valid
                                    Log::info("Invalid Profile Id: " . $single->profileId);
                                }
                            } else if ($ex->getCode() == 429) { //https://advertising.amazon.com/API/docs/v2/guides/developer_notes#Rate-limiting
                                sleep(Config::get('constants.sleepTime') + 2);
                                goto b;
                            } else if ($ex->getCode() == 502) {
                                sleep(Config::get('constants.sleepTime') + 2);
                                goto b;
                            }
                            // store report status
                            AMSModel::insertTrackRecord('App\Console\Commands\Ams\Campaign\SB\getCampaignList', 'fail');
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
                    Log::info("Profile List not found.");
                }
        Log::info("filePath:App\Console\Commands\Ams\Campaign\SP\getCampaignList. End Cron.");
    }
}
