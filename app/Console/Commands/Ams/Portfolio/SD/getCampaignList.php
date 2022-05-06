<?php

namespace App\Console\Commands\AMS\Portfolio\SD;

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
    protected $signature = 'getSDCampaignlist:portfolio';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'This Command is used to get Sponsored Display Campaign List';

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
        Log::info("filePath:Commands\Ams\Portfolio\SD\getSDCampaignlist. Start Cron.");
        Log::info($this->description);
        $obAccessToken = new AMSModel();

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
            $campaignStoreArray = [];
            foreach ($allProfileIds as $single) {
                if ($single->marketplaceStringId != 'A1AM78C64UM0Y8') {
                    $fkConfigId = $single->fkConfigId;
                    $clientId = $single->client_id;
                    $accessToken = $single->access_token;
                    $responseBody = array();
                    // Create a client with a base URI
                    $apiUrl = getApiUrlForDiffEnv(env('APP_ENV'));
                    $url = $apiUrl . '/' . Config::get('constants.sdCampaignUrl');
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

                            $responseCount = count($responseBody);
                            for ($i = 0; $i < $responseCount; $i++) {
                                $campaignSdDataInsert = [];
                                $campaignSdDataInsert['pageType'] = 'NA';
                                $campaignSdDataInsert['url'] = 'NA';
                                $campaignSdDataInsert['brandName'] = 'NA';
                                $campaignSdDataInsert['brandLogoAssetID'] = 'NA';
                                $campaignSdDataInsert['headline'] = 'NA';
                                $campaignSdDataInsert['shouldOptimizeAsins'] = 'NA';
                                $campaignSdDataInsert['brandLogoUrl'] = 'NA';
                                $campaignSdDataInsert['asins'] = 'NA';
                                $campaignSdDataInsert['strategy'] = 'NA';
                                $campaignSdDataInsert['predicate'] = 'NA';
                                $campaignSdDataInsert['percentage'] = 0;
                                $campaignSdDataInsert['name'] = $responseBody[$i]->name;
                                $campaignSdDataInsert['budget'] = isset($responseBody[$i]->budget) ? $responseBody[$i]->budget : 'NA';
                                $campaignSdDataInsert['bidOptimization'] = isset($responseBody[$i]->bidOptimization) ? $responseBody[$i]->bidOptimization : 'NA';
                                $campaignSdDataInsert['targetingType'] = isset($responseBody[$i]->targetingType) ? $responseBody[$i]->targetingType : 'NA';
                                $campaignSdDataInsert['premiumBidAdjustment'] = isset($responseBody[$i]->premiumBidAdjustment) ? $responseBody[$i]->premiumBidAdjustment : 'NA';
                                $campaignSdDataInsert['fkProfileId'] = $single->id;
                                $campaignSdDataInsert['fkConfigId'] = $fkConfigId;
                                $campaignSdDataInsert['profileId'] = $single->profileId;
                                $campaignSdDataInsert['portfolioId'] = (isset($responseBody[$i]->portfolioId) ? $responseBody[$i]->portfolioId : 0);
                                $campaignSdDataInsert['campaignId'] = isset($responseBody[$i]->campaignId) ? $responseBody[$i]->campaignId : 0;
                                $campaignSdDataInsert['budgetType'] = isset($responseBody[$i]->budgetType) ? $responseBody[$i]->budgetType : 'NA';
                                $campaignSdDataInsert['startDate'] = isset($responseBody[$i]->startDate) ? $responseBody[$i]->startDate : 'NA';
                                $campaignSdDataInsert['state'] = isset($responseBody[$i]->state) ? $responseBody[$i]->state : 'NA';
                                $campaignSdDataInsert['servingStatus'] = isset($responseBody[$i]->servingStatus) ? $responseBody[$i]->servingStatus : 'NA';
                                if (isset($responseBody[$i]->bidding)) {
                                    $campaignSpDataInsert['strategy'] = isset($responseBody[$i]->bidding->strategy) ? $responseBody[$i]->bidding->strategy : 'NA';
                                    if (isset($responseBody[$i]->bidding->adjustments)) {
                                        $campaignSdDataInsert['predicate'] = isset($responseBody[$i]->bidding->adjustments->predicate) ? $responseBody[$i]->bidding->adjustments->predicate : 'NA';
                                        $campaignSdDataInsert['percentage'] = isset($responseBody[$i]->bidding->adjustments->percentage) ? $responseBody[$i]->bidding->adjustments->percentage : 0;
                                    }
                                }
                                if (isset($responseBody[$i]->landingPage)) {
                                    $campaignSdDataInsert['pageType'] = isset($responseBody[$i]->landingPage->pageType) ? $responseBody[$i]->landingPage->pageType : 'NA';
                                    $campaignSdDataInsert['url'] = isset($responseBody[$i]->landingPage->url) ? $responseBody[$i]->landingPage->url : 'NA';
                                }  // End check Landing Page
                                $campaignSdDataInsert['reportType'] = Config::get('constants.portfolioSponsoredDisplay');
                                $campaignSdDataInsert['created_at'] = date('Y-m-d H:i:s');
                                $campaignSdDataInsert['updated_at'] = date('Y-m-d H:i:s');
                                if (isset($responseBody[$i]->creative)) {
                                    $campaignSdDataInsert['brandName'] = isset($responseBody[$i]->creative->brandName) ? $responseBody[$i]->creative->brandName : 'NA';
                                    $campaignSdDataInsert['brandLogoAssetID'] = isset($responseBody[$i]->creative->brandLogoAssetID) ? $responseBody[$i]->creative->brandLogoAssetID : 'NA';
                                    $campaignSdDataInsert['headline'] = isset($responseBody[$i]->creative->headline) ? $responseBody[$i]->creative->headline : 'NA';
                                    $campaignSdDataInsert['shouldOptimizeAsins'] = isset($responseBody[$i]->creative->shouldOptimizeAsins) ? $responseBody[$i]->creative->shouldOptimizeAsins : 'NA';
                                    $campaignSdDataInsert['brandLogoUrl'] = isset($responseBody[$i]->creative->brandLogoUrl) ? $responseBody[$i]->creative->brandLogoUrl : 'NA';
                                    $campaignSdDataInsert['asins'] = isset($responseBody[$i]->creative->asins) ? implode(',', $responseBody[$i]->creative->asins) : 'NA';
                                } // End check creative field else
                                array_push($campaignStoreArray, $campaignSdDataInsert);
                            } // End For Loop

                            if (!empty($campaignStoreArray)) {
                                PortfolioAllCampaignList::insertDailyCampaigns($campaignStoreArray);
                                PortfolioAllCampaignList::insertCampaignList($campaignStoreArray);
                            } else {
                                Log::info("No Sponsored Display Campaign for insertion" . json_encode($campaignStoreArray));
                            }
                        }
                    } catch (\Exception $ex) {
                        Log::info(Config::get('constants.portfolioSponsoredBrand') . ' Data' . ' profile id: ' . $single->profileId . ' Url = ' . $url);
                        if ($ex->getCode() == 401) {
                            Log::error('Refresh Access token. In file filePath:Commands\Ams\Portfolio\SD\getCampaignList');
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
                        } else if ($ex->getCode() == 429) { //https://advertising.amazon.com/API/docs/v2/guides/developer_notes#Rate-limiting
                            sleep(Config::get('constants.sleepTime') + 2);
                            goto b;
                        } else if ($ex->getCode() == 502) {
                            sleep(Config::get('constants.sleepTime') + 2);
                            goto b;
                        }
                        // store report status
                        Log::error($ex->getMessage());
                    }// End catch
                }
            }// end foreach
        } else {
            Log::info("Profile List not found.");
        }
        Log::info("filePath:Commands\Ams\Portfolio\SD\getSDCampaignlist. End Cron.");
    }
}
