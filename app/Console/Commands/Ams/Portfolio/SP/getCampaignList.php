<?php

namespace App\Console\Commands\AMS\Portfolio\SP;

use Artisan;
use App\Models\DayPartingModels\PortfolioAllCampaignList;
use App\Models\AMSModel;
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
    protected $signature = 'getSPCampaignlist:portfolio';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'This Command is used to get Sponsored Product Campaign List';

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
        Log::info("filePath:Commands\Ams\Portfolio\SP\getSPCampaignlist. Start Cron.");
        Log::info($this->description);
        Log::info("Auth token get from DB Start!");
        $allProfileIdsObject = new AMSModel();
        b:
        $responseForProfile = getNotifyWhichEnvDataToUse(env('APP_ENV'));
        if ($responseForProfile == TRUE) {
            $allProfileIds = $allProfileIdsObject->getAllProfiles();
        } elseif ($responseForProfile == FALSE) {
            $allProfileIds = $allProfileIdsObject->getAllSandboxProfiles();
        }

        if (!empty($allProfileIds)) {
            foreach ($allProfileIds as $single) {
                $fkConfigId = $single->fkConfigId;
                $clientId = $single->client_id;
                $accessToken = $single->access_token;
                // Create a client with a base URI
                $apiUrl = getApiUrlForDiffEnv(env('APP_ENV'));
                $url = $apiUrl . '/' . Config::get('constants.apiVersion') . '/' . Config::get('constants.spCampaignUrl');
                Log::info('Url = ' . $url);
                $client = new Client();
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
                    $responseBody = json_decode($response->getBody()->getContents());
                    if (!empty($responseBody) && !is_null($responseBody)) {
                        $campaignStoreArray = [];
                        $responseCount = count($responseBody);
                        for ($i = 0; $i < $responseCount; $i++) {
                            $campaignSpDataInsert = [];
                            $campaignSpDataInsert['pageType'] = 'NA';
                            $campaignSpDataInsert['url'] = 'NA';
                            $campaignSpDataInsert['brandName'] = 'NA';
                            $campaignSpDataInsert['brandLogoAssetID'] = 'NA';
                            $campaignSpDataInsert['headline'] = 'NA';
                            $campaignSpDataInsert['shouldOptimizeAsins'] = 'NA';
                            $campaignSpDataInsert['brandLogoUrl'] = 'NA';
                            $campaignSpDataInsert['asins'] = 'NA';
                            $campaignSpDataInsert['strategy'] = 'NA';
                            $campaignSpDataInsert['predicate'] = 'NA';
                            $campaignSpDataInsert['percentage'] = 0;
                            $campaignSpDataInsert['name'] = $responseBody[$i]->name;
                            $campaignSpDataInsert['budget'] = isset($responseBody[$i]->dailyBudget) ? $responseBody[$i]->dailyBudget : 0;
                            $campaignSpDataInsert['bidOptimization'] = isset($responseBody[$i]->bidOptimization) ? $responseBody[$i]->bidOptimization : 'NA';
                            $campaignSpDataInsert['targetingType'] = isset($responseBody[$i]->targetingType) ? $responseBody[$i]->targetingType : 'NA';
                            $campaignSpDataInsert['premiumBidAdjustment'] = isset($responseBody[$i]->premiumBidAdjustment) ? $responseBody[$i]->premiumBidAdjustment : 'NA';
                            $campaignSpDataInsert['fkProfileId'] = $single->id;
                            $campaignSpDataInsert['fkConfigId'] = $fkConfigId;
                            $campaignSpDataInsert['profileId'] = $single->profileId;
                            $campaignSpDataInsert['portfolioId'] = (isset($responseBody[$i]->portfolioId) ? $responseBody[$i]->portfolioId : 0);
                            $campaignSpDataInsert['campaignId'] = isset($responseBody[$i]->campaignId) ? $responseBody[$i]->campaignId : 0;
                            $campaignSpDataInsert['budgetType'] = isset($responseBody[$i]->budgetType) ? $responseBody[$i]->budgetType : 'NA';
                            $campaignSpDataInsert['startDate'] = isset($responseBody[$i]->startDate) ? $responseBody[$i]->startDate : 'NA';
                            $campaignSpDataInsert['state'] = isset($responseBody[$i]->state) ? $responseBody[$i]->state : 'NA';
                            $campaignSpDataInsert['servingStatus'] = isset($responseBody[$i]->servingStatus) ? $responseBody[$i]->servingStatus : 'NA';

                            if (isset($responseBody[$i]->bidding)) {
                                $campaignSpDataInsert['strategy'] = isset($responseBody[$i]->bidding->strategy) ? $responseBody[$i]->bidding->strategy : 'NA';
                                if (isset($responseBody[$i]->bidding->adjustments)) {
                                    $campaignSpDataInsert['predicate'] = isset($responseBody[$i]->bidding->adjustments->predicate) ? $responseBody[$i]->bidding->adjustments->predicate : 'NA';
                                    $campaignSpDataInsert['percentage'] = isset($responseBody[$i]->bidding->adjustments->percentage) ? $responseBody[$i]->bidding->adjustments->percentage : 0;
                                }
                            }
                            if (isset($responseBody[$i]->landingPage)) {
                                $campaignSpDataInsert['pageType'] = isset($responseBody[$i]->landingPage->pageType) ? $responseBody[$i]->landingPage->pageType : 'NA';
                                $campaignSpDataInsert['url'] = isset($responseBody[$i]->landingPage->url) ? $responseBody[$i]->landingPage->url : 'NA';
                            }  // End check Landing Page

                            $campaignSpDataInsert['reportType'] = Config::get('constants.portfolioSponsoredProduct');
                            $campaignSpDataInsert['created_at'] = date('Y-m-d H:i:s');
                            $campaignSpDataInsert['updated_at'] = date('Y-m-d H:i:s');

                            if (isset($responseBody[$i]->creative)) {
                                $campaignSpDataInsert['brandName'] = isset($responseBody[$i]->creative->brandName) ? $responseBody[$i]->creative->brandName : 'NA';
                                $campaignSpDataInsert['brandLogoAssetID'] = isset($responseBody[$i]->creative->brandLogoAssetID) ? $responseBody[$i]->creative->brandLogoAssetID : 'NA';
                                $campaignSpDataInsert['headline'] = isset($responseBody[$i]->creative->headline) ? $responseBody[$i]->creative->headline : 'NA';
                                $campaignSpDataInsert['shouldOptimizeAsins'] = isset($responseBody[$i]->creative->shouldOptimizeAsins) ? $responseBody[$i]->creative->shouldOptimizeAsins : 'NA';
                                $campaignSpDataInsert['brandLogoUrl'] = isset($responseBody[$i]->creative->brandLogoUrl) ? $responseBody[$i]->creative->brandLogoUrl : 'NA';
                                $dbStore['asins'] = isset($responseBody[$i]->creative->asins) ? implode(',', $responseBody[$i]->creative->asins) : 'NA';

                            }
                            array_push($campaignStoreArray, $campaignSpDataInsert);
                            // End check creative field else
                        } // End For Loop

                        // Insertion In Database
                        if (!empty($campaignStoreArray)) {
                            PortfolioAllCampaignList::insertDailyCampaigns($campaignStoreArray);
                            PortfolioAllCampaignList::insertCampaignList($campaignStoreArray);
                            unset($campaignSpDataInsert);
                            unset($campaignStoreArray);
                        }
                    } else {
                        Log::info('no record found In file filePath:Commands\Ams\Portfolio\SP\getCampaignList ');
                        // store report status
                        AMSModel::insertTrackRecord(Config::get('constants.portfolioSponsoredBrand') . ' Data' . ' profile id: ' . $single->profileId, 'not record found');
                    } // End Else
                } catch (\Exception $ex) {
                    //dd($single);
                    if ($ex->getCode() == 401) {
                        if (strstr($ex->getMessage(), '401 Unauthorized')) { // if auth token expire
                            Log::error('Refresh Access token. In file filePath:Commands\Ams\Portfolio\SP\getCampaignList');
                            $authCommandArray = array();
                            $authCommandArray['fkConfigId'] = $fkConfigId;
                            \Artisan::call('getaccesstoken:amsauth', $authCommandArray);
                            $responseEnv = getNotifyWhichEnvDataToUse(env('APP_ENV'));
                            if ($responseEnv == TRUE) {
                                \Artisan::call('getprofileid:amsprofile');
                            } elseif ($responseForProfile == FALSE) {
                                \Artisan::call('getprofileid:amssandboxprofile');
                            }
                            $obaccess_token = new AMSModel();
                            $getAMSTokenById = $obaccess_token->getAMSTokenById($fkConfigId);
                            $accessToken = $getAMSTokenById->access_token;
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
                    AMSModel::insertTrackRecord('Report Id', 'fail');
                    Log::error($ex->getMessage());
                }// End catch
            }// end foreach
        } else {
            Log::info("Profile List not found.");
        }
        Log::info("filePath:Commands\Ams\Portfolio\SP\getSPCampaignlist. End Cron.");
    }
}
