<?php

namespace App\Console\Commands\Ams\Campaign\SB;

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
    protected $signature = 'getCampaignList:campaignSB';

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
        Log::info("filePath:App\Console\Commands\Ams\Campaign\SB\getCampaignList. Start Cron.");
        Log::info($this->description);
        //update all auth tokens before this cron
        Artisan::call('getallaccesstoken:amsauth');
        $obAccessToken = new AMSModel();
        Log::info("Auth token get from DB Start!");
        $allProfileIdsObject = new AMSModel();
        b:
        $allProfileIds = $allProfileIdsObject->getAllProfiles();
        if (!empty($allProfileIds)) {
            foreach ($allProfileIds as $single) {
                $clientId = $single->client_id;
                $fkConfigId = $single->fkConfigId;

                Log::info("Bidding Rule Profile Id = ".$single->profileId." Config Id = ". $fkConfigId);
                if (!empty($clientId)) {
                    $responseBody = array();
                    // Create a client with a base URI
                    $url = Config::get('constants.amsApiUrl') . '/' . Config::get('constants.sbCampaignUrl');
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
                                    $dbStore['type'] = 'SB';
                                    $dbStore['campaignType'] = 'sponsoredBrands';
                                    $dbStore['name'] = $responseBody[$i]->name;
                                    $dbStore['targetingType'] = 'NA';
                                    $dbStore['premiumBidAdjustment'] = 'NA';
                                    $dbStore['dailyBudget'] = 00.00;
                                    $dbStore['budget'] = isset($responseBody[$i]->budget) ? $responseBody[$i]->budget : 'NA';
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
                                    if (isset($responseBody[$i]->landingPage)) {
                                        $dbStore['pageType'] = isset($responseBody[$i]->landingPage->pageType) ? $responseBody[$i]->landingPage->pageType : 'NA';
                                        $dbStore['url'] = isset($responseBody[$i]->landingPage->url) ? $responseBody[$i]->landingPage->url : 'NA';
                                    }  // End check Landing Page
                                    $dbStore['brandName'] = 'NA';
                                    $dbStore['brandLogoAssetID'] = 'NA';
                                    $dbStore['headline'] = 'NA';
                                    $dbStore['shouldOptimizeAsins'] = 'NA';
                                    $dbStore['brandLogoUrl'] = 'NA';
                                    $dbStore['asins'] = 'NA';
                                    if (isset($responseBody[$i]->creative)) {
                                        $dbStore['brandName'] = isset($responseBody[$i]->creative->brandName) ? $responseBody[$i]->creative->brandName : 'NA';
                                        $dbStore['brandLogoAssetID'] = isset($responseBody[$i]->creative->brandLogoAssetID) ? $responseBody[$i]->creative->brandLogoAssetID : 'NA';
                                        $dbStore['headline'] = isset($responseBody[$i]->creative->headline) ? $responseBody[$i]->creative->headline : 'NA';
                                        $dbStore['shouldOptimizeAsins'] = isset($responseBody[$i]->creative->shouldOptimizeAsins) ? $responseBody[$i]->creative->shouldOptimizeAsins : 'NA';
                                        $dbStore['brandLogoUrl'] = isset($responseBody[$i]->creative->brandLogoUrl) ? $responseBody[$i]->creative->brandLogoUrl : 'NA';
                                        $dbStore['asins'] = isset($responseBody[$i]->creative->asins) ? implode(',', $responseBody[$i]->creative->asins) : 'NA';
                                    }
                                    $dbStore['strategy'] = 'NA';
                                    $dbStore['predicate'] = 'NA';
                                    $dbStore['percentage'] = 0;
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
                                    Log::error('Refresh Access token. In file filePath:App\Console\Commands\Ams\Campaign\SB\getCampaignList');
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
        Log::info("filePath:App\Console\Commands\Ams\Campaign\SB\getCampaignList. End Cron.");
    }
}
