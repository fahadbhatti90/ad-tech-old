<?php

namespace App\Console\Commands\AMS\Portfolio\SB;

use Artisan;
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
    protected $signature = 'getSBCampaignlist:portfolio';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'This Command is used to get Sponsored Brand Campaign List';

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
        Log::info("filePath:Commands\Ams\Portfolio\SB\getSBCampaignlist. Start Cron.");
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
            foreach ($allProfileIds as $single) {
                $fkConfigId = $single->fkConfigId;
                $clientId = $single->client_id;
                $accessToken = $single->access_token;
                // Create a client with a base URI
                $apiUrl = getApiUrlForDiffEnv(env('APP_ENV'));
                $url = $apiUrl . '/' . Config::get('constants.sbCampaignUrl');
                $client = new Client();

                try {
                    $response = $client->request('GET', $url, [
                        'headers' => [
                            'Authorization' => 'Bearer ' . $accessToken,
                            'Content-Type' => 'application/json',
                            'Amazon-Advertising-API-ClientId' => $clientId,
                            'Amazon-Advertising-API-Scope' => $single->profileId
                            //'Amazon-Advertising-API-Scope' => 1888811920420544
                        ],
                        'delay' => Config::get('constants.delayTimeInApi'),
                        'connect_timeout' => Config::get('constants.connectTimeOutInApi'),
                        'timeout' => Config::get('constants.timeoutInApi'),
                    ]);

                    $responseBody = json_decode($response->getBody()->getContents());

                    if (!empty($responseBody) && !is_null($responseBody)) {
                        $campaignStoreArray = [];
                        $responseCount = count($responseBody);
                        for ($i = 0; $i < $responseCount; $i++) {
                            $campaignSbDataInsert = [];
                            $campaignSbDataInsert['pageType'] = 'NA';
                            $campaignSbDataInsert['url'] = 'NA';
                            $campaignSbDataInsert['brandName'] = 'NA';
                            $campaignSbDataInsert['brandLogoAssetID'] = 'NA';
                            $campaignSbDataInsert['headline'] = 'NA';
                            $campaignSbDataInsert['shouldOptimizeAsins'] = 'NA';
                            $campaignSbDataInsert['brandLogoUrl'] = 'NA';
                            $campaignSbDataInsert['asins'] = 'NA';
                            $campaignSbDataInsert['strategy'] = 'NA';
                            $campaignSbDataInsert['predicate'] = 'NA';
                            $campaignSbDataInsert['percentage'] = 0;
                            $campaignSbDataInsert['name'] = $responseBody[$i]->name;
                            $campaignSbDataInsert['budget'] = isset($responseBody[$i]->budget) ? $responseBody[$i]->budget : 'NA';
                            $campaignSbDataInsert['bidOptimization'] = isset($responseBody[$i]->bidOptimization) ? $responseBody[$i]->bidOptimization : 'NA';
                            $campaignSbDataInsert['targetingType'] = isset($responseBody[$i]->targetingType) ? $responseBody[$i]->targetingType : 'NA';
                            $campaignSbDataInsert['premiumBidAdjustment'] = isset($responseBody[$i]->premiumBidAdjustment) ? $responseBody[$i]->premiumBidAdjustment : 'NA';
                            $campaignSbDataInsert['fkProfileId'] = $single->id;
                            $campaignSbDataInsert['profileId'] = $single->profileId;
                            $campaignSbDataInsert['fkConfigId'] = $fkConfigId;
                            $campaignSbDataInsert['portfolioId'] = (isset($responseBody[$i]->portfolioId) ? $responseBody[$i]->portfolioId : 0);
                            $campaignSbDataInsert['campaignId'] = isset($responseBody[$i]->campaignId) ? $responseBody[$i]->campaignId : 0;
                            $campaignSbDataInsert['budgetType'] = isset($responseBody[$i]->budgetType) ? $responseBody[$i]->budgetType : 'NA';
                            $campaignSbDataInsert['startDate'] = isset($responseBody[$i]->startDate) ? $responseBody[$i]->startDate : 'NA';
                            $campaignSbDataInsert['state'] = isset($responseBody[$i]->state) ? $responseBody[$i]->state : 'NA';
                            $campaignSbDataInsert['servingStatus'] = isset($responseBody[$i]->servingStatus) ? $responseBody[$i]->servingStatus : 'NA';

                            if (isset($responseBody[$i]->landingPage)) {
                                $campaignSbDataInsert['pageType'] = isset($responseBody[$i]->landingPage->pageType) ? $responseBody[$i]->landingPage->pageType : 'NA';
                                $campaignSbDataInsert['url'] = isset($responseBody[$i]->landingPage->url) ? $responseBody[$i]->landingPage->url : 'NA';
                            }  // End check Landing Page
                            $campaignSbDataInsert['reportType'] = Config::get('constants.portfolioSponsoredBrand');
                            $campaignSbDataInsert['created_at'] = date('Y-m-d H:i:s');
                            $campaignSbDataInsert['updated_at'] = date('Y-m-d H:i:s');
                            if (isset($responseBody[$i]->creative)) {
                                $campaignSbDataInsert['brandName'] = isset($responseBody[$i]->creative->brandName) ? $responseBody[$i]->creative->brandName : 'NA';
                                $campaignSbDataInsert['brandLogoAssetID'] = isset($responseBody[$i]->creative->brandLogoAssetID) ? $responseBody[$i]->creative->brandLogoAssetID : 'NA';
                                $campaignSbDataInsert['headline'] = isset($responseBody[$i]->creative->headline) ? $responseBody[$i]->creative->headline : 'NA';
                                $campaignSbDataInsert['shouldOptimizeAsins'] = isset($responseBody[$i]->creative->shouldOptimizeAsins) ? $responseBody[$i]->creative->shouldOptimizeAsins : 'NA';
                                $campaignSbDataInsert['brandLogoUrl'] = isset($responseBody[$i]->creative->brandLogoUrl) ? $responseBody[$i]->creative->brandLogoUrl : 'NA';
                                $campaignSbDataInsert['asins'] = isset($responseBody[$i]->creative->asins) ? implode(',', $responseBody[$i]->creative->asins) : 'NA';
                            }

                            array_push($campaignStoreArray, $campaignSbDataInsert);
                            // End check creative field else
                        } // End For Loop

                        PortfolioAllCampaignList::insertDailyCampaigns($campaignStoreArray);
                        PortfolioAllCampaignList::insertCampaignList($campaignStoreArray);
                        unset($campaignStoreArray);
                        unset($campaignSbDataInsert);
                    } else {
                        // store report status
                        AMSModel::insertTrackRecord(Config::get('constants.portfolioSponsoredBrand') . ' Data' . ' profile id: ' . $single->profileId, 'not record found');
                    }
                } catch (\Exception $ex) {

                    if ($ex->getCode() == 401) {
                        Log::error('Refresh Access token. In file filePath:Commands\Ams\Portfolio\SB\getCampaignList');
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
                    AMSModel::insertTrackRecord('AdGroup Report Id', 'fail');
                    Log::error($ex->getMessage());
                }// end catch
            }// end foreach
        } else {
            Log::info("Profile List not found.");
        }
        Log::info("filePath:Commands\Ams\Portfolio\SB\getSBCampaignlist. End Cron.");
    }
}
