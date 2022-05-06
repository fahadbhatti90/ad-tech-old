<?php

namespace App\Console\Commands\Ams\Portfolio;

use App\Models\AMSModel;
use App\Models\DayPartingModels\DayPartingCampaignScheduleIds;
use App\Models\DayPartingModels\PfCampaignSchedule;
use App\Models\DayPartingModels\PortfolioAllCampaignList;
use GuzzleHttp\Client;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Log;
use Artisan;

class pauseCampaignListDayParting extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'pauseCampaignListDayParting:portfolio';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'This Command is used to pause or enable Campaign List of Day parting';

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
        Log::info("filePath:Commands\Ams\Portfolio\pauseCampaignListDayParting. Start Cron.");
        Log::info($this->description);
        $pauseEnableCampaign = PfCampaignSchedule::select('id', 'scheduleName', 'portfolioCampaignType', 'isScheduleExpired')
            ->with('pauseCampaignPivotForCrons:id,name,campaignId,profileId,state,fkScheduleId,fkConfigId,reportType')
            ->get();
        if (!$pauseEnableCampaign->isEmpty()) {
            foreach ($pauseEnableCampaign as $singleRecord) {
                $currentTime = date('H:i:00');
                $pauseCron = $singleRecord->pauseCampaignPivotForCrons;
                if (!$pauseCron->isEmpty()) {
                    foreach ($pauseCron as $campaign) {

                        $cronTime = $campaign->pivot->enablingPausingtime;
                        $userSelection = $campaign->pivot->userSelection;
                        Log::info('Check Cron Timing Pause Campaign to Run On Campaigns');
                        Log::info('Current Time pauseCampaignlist =' . $currentTime);
                        Log::info('Cron Time pauseCampaignlist =' . $cronTime);
                        if ($userSelection == 1 || $userSelection == 2) {
                            Log::info('User selected option=>' . $userSelection . 'pausing campaigns');
                            $campaignList = $this->getEnablePauseCampaign($campaign, 'paused');
                        } elseif ($userSelection == 3) {
                            Log::info('User selected option=>' . $userSelection . 'enabling campaigns permanently');
                            $campaignList = $this->getEnablePauseCampaign($campaign, 'enabled');
                        }

                        if ($currentTime === $cronTime) {
                            Log::info('Current Time =' . $currentTime . ' Matches Cron time =' . $cronTime);
                            $result = $this->pausingEnablingCampaigns($campaignList);
                            if ($result == TRUE) {
                                Log::info('pauseCampaignListDayParting:portfolio = Enabling Pausing Campaigns Return .' . $result);
                                Log::info('pauseCampaignListDayParting:portfolio = update Pivot table');

                                DayPartingCampaignScheduleIds::where('fkScheduleId', $singleRecord->id)
                                    ->where('fkCampaignId', $campaignList['id'])
                                    ->where('enablingPausingTime', '!=',NULL)
                                    ->update([
                                        'isEnablingPausingDone' => 1
                                    ]);
                            }
                        }
                    } // End foreach loop
                }
            }
        }
        Log::info("filePath:Commands\Ams\Portfolio\pauseCampaignListDayParting. End Cron.");
    }

    /**
     * @param $singleRecord
     * @param $url
     * @return array
     */
    private function getEnablePauseCampaign($campaignRecord, $state)
    {
        $apiVarData = [];
        $apiUrl = getApiUrlForDiffEnv(env('APP_ENV'));
        $sponsoredBrandUrl = $apiUrl . '/' . Config::get('constants.sbCampaignUrl');
        $sponsoredProductUrl = $apiUrl . '/' . Config::get('constants.apiVersion') . '/' . Config::get('constants.spCampaignUrl');
        $sponsoredDisplayUrl = $apiUrl . '/' . Config::get('constants.sdCampaignUrl');


        if (!empty($campaignRecord)) {
            $apiVarData['campaignId'] = intval($campaignRecord['campaignId']);
            $apiVarData['profileId'] = intval($campaignRecord['profileId']);
            $apiVarData['fkConfigId'] = intval($campaignRecord['fkConfigId']);
            $apiVarData['id'] = intval($campaignRecord['id']);
            $apiVarData['state'] = $state;
            if ($campaignRecord['reportType'] == 'sponsoredBrand') {
                $apiVarData['url'] = $sponsoredBrandUrl;
            }

            if ($campaignRecord['reportType'] == 'sponsoredProducts') {
                $apiVarData['url'] = $sponsoredProductUrl;
            }
            if ($campaignRecord['reportType'] == 'sponsoredDisplay') {
                $apiVarData['url'] = $sponsoredDisplayUrl;
            }
        }


        return $apiVarData;
    }

    /**
     * @param $postData
     * @return bool
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    private function pausingEnablingCampaigns($postData)
    {

        if (!empty($postData)) {
            Log::info("pauseCampaignListDayParting = Auth token get from DB Start!");
            $storeDataArrayUpdate = [];
                $fkConfigId = $postData['fkConfigId'];
                // Get Access Token
                $obAccessToken = new AMSModel();
                $dataAccessTakenData = $obAccessToken->getParameterAndAuthById($fkConfigId);
                if ($dataAccessTakenData != FALSE && !empty($dataAccessTakenData)) {
                    $clientId = $dataAccessTakenData->client_id;
                    $accessToken = $dataAccessTakenData->access_token;
                    b:
                    // Making Array to send over PUT Call
                    $apiPostDataToSend = [];
                    $apiPostDataToSend[] = [
                        'campaignId' => $postData['campaignId'],
                        'state' => $postData['state']
                    ];

                    Log::info(env('APP_ENV') . ' Url -> ' . $postData['url']);
                    $client = new Client();
                    // Header
                    $headers = [
                        'Authorization' => 'Bearer ' . $accessToken,
                        'Content-Type' => 'application/json',
                        'Amazon-Advertising-API-ClientId' => $clientId,
                        'Amazon-Advertising-API-Scope' => $postData['profileId']
                    ];

                    try {
                        $response = $client->request('PUT', $postData['url'], [
                            'headers' => $headers,
                            'body' => json_encode($apiPostDataToSend),
                            'delay' => Config::get('constants.delayTimeInApi'),
                            'connect_timeout' => Config::get('constants.connectTimeOutInApi'),
                            'timeout' => Config::get('constants.timeoutInApi')
                        ]);

                        $responseBody = json_decode($response->getBody()->getContents());

                        if (!empty($responseBody) && !is_null($responseBody)) {
                            $storeDataArray = [];
                            $storeDataArray['campaignId'] = $responseBody[0]->campaignId;
                            $storeDataArray['state'] = $postData['state'];
                            $storeDataArray['updated_at'] = date("Y-m-d H:i:s");
                            array_push($storeDataArrayUpdate, $storeDataArray);
                        }
                        // Update Campaign Records
                        if (!empty($storeDataArrayUpdate)) {
                            PortfolioAllCampaignList::updateCampaign($storeDataArrayUpdate);
                            return TRUE;
                        } else {
                            return FALSE;
                        }
                    } catch (\Exception $ex) {
                        if ($ex->getCode() == 401) {
                            if (strstr($ex->getMessage(), '401 Unauthorized')) { // if auth token expire
                                Log::error('Refresh Access token. In file filePath:Commands\Ams\Portfolio\pauseCampaignList');
                                $authCommandArray = array();
                                $authCommandArray['fkConfigId'] = $fkConfigId;
                                \Artisan::call('getaccesstoken:amsauth', $authCommandArray);
                                $obaccess_token = new AMSModel();
                                $getAMSTokenById = $obaccess_token->getAMSTokenById($fkConfigId);
                                $accessToken = $getAMSTokenById->access_token;
                                goto b;
                            } elseif (strstr($ex->getMessage(), 'advertiser found for scope')) {
                                // store profile list not valid
                                Log::info("Invalid Profile Id: " . $postData['profileId']);
                            }
                        } else if ($ex->getCode() == 429) { //https://advertising.amazon.com/API/docs/v2/guides/developer_notes#Rate-limiting
                            sleep(Config::get('constants.sleepTime') + 2);
                            goto b;
                        } else if ($ex->getCode() == 502) {
                            sleep(Config::get('constants.sleepTime') + 2);
                            goto b;
                        }
                        Log::error($ex->getMessage());
                    }// End catch
                } else {
                    Log::info("AMS client Id or access token not found Ams\Portfolio\pauseCampaignList.");
                }
        } else {
            Log::info("No Post Data in Campaigns");
            return FALSE;
        }

    }
}
