<?php

namespace App\Console\Commands\ams\portfolio\SP;

use App\Models\AMSModel;
use App\Models\DayPartingModels\PfCampaignSchedule;
use App\Models\DayPartingModels\DayPartingCampaignScheduleIds;
use App\Models\DayPartingModels\PortfolioAllCampaignList;
use GuzzleHttp\Client;
use Artisan;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Log;

class pauseCampaignList extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'pauseSPCampaignlist:portfolio';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'This Command is used to pause Sponsored Product Campaign List';

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
        Log::info("filePath:Commands\Ams\Portfolio\SP\pauseSPCampaignlist. Start Cron.");
        Log::info($this->description);
        $allScheduleCampaignsPf = PfCampaignSchedule::select('id', 'scheduleName', 'portfolioCampaignType', 'isScheduleExpired')
            //->where('isScheduleExpired', 0)
            //->where('isActive', 0)
            ->whereHas('sponsoredProductPivotForCrons')
            ->with('sponsoredProductPivotForCrons:id,name,campaignId,profileId,state,fkScheduleId')->get();
        if (!$allScheduleCampaignsPf->isEmpty()) {
            foreach ($allScheduleCampaignsPf as $singleRecord) {

                if (!$singleRecord->sponsoredProductPivotForCrons->isEmpty()) {
                    $currentTime = date('H:i:00');
                    $cronTime = $singleRecord->sponsoredProductPivotForCrons->first()->pivot->enablingPausingtime;
                    $userSelection = $singleRecord->sponsoredProductPivotForCrons->first()->pivot->userSelection;
                    Log::info('Check Cron Timing to Run On Campaigns');
                    Log::info('Current Time =' . $currentTime);
                    Log::info('Cron Time =' . $cronTime);
                    if ($userSelection == 1 || $userSelection == 2) {
                        Log::info('User selected option=>' . $userSelection . 'pausing campaigns');
                        $campaignList = $this->getPauseCampaignData($singleRecord);
                    } elseif ($userSelection == 3) {
                        Log::info('User selected option=>' . $userSelection . 'enabling campaigns permanently');
                        $campaignList = $this->getEnableCampaignData($singleRecord);
                    }
                    if ($currentTime === $cronTime) {
                        Log::info('Current Time =' . $currentTime . ' Matches Cron time =' . $cronTime);
                        $result = $this->pausingEnablingCampaigns($campaignList);
                        if ($result == TRUE) {
                            Log::info('pauseSPCampaignlist:portfolio = Enabling Pausing Campaigns Return .' . $result);
                            //$scheduleData['isScheduleExpired'] = 1;
                            //$scheduleData['updated_at'] = date('Y-m-d H:i:s');
                            Log::info('pauseSPCampaignlist:portfolio = update Campaigns');
                            //PfCampaignSchedule::updateSchedule($singleRecord->id, $scheduleData);
                            Log::info('pauseSPCampaignlist:portfolio = update Pivot table');

                            foreach ($campaignList as $key => $value) {

                                DayPartingCampaignScheduleIds::where('fkScheduleId', $singleRecord->id)
                                    ->where('fkCampaignId', $value['id'])
                                    ->update([
                                        'isEnablingPausingDone' => 1
                                    ]);
                            }
                        }
                    }
                } else {
                    Log::info('Schedule Found but there are no campaigns');
                }
            }

        } else {

            Log::info('No pausingCampaignList');
        }
        Log::info("filePath:Commands\Ams\Portfolio\SP\pauseSPCampaignlist. End Cron.");
    }

    /**
     * @param $singleRecord
     * @return array
     */
    private function getPauseCampaignData($singleRecord)
    {
        $apiVarData = [];
        $apiVarDataToSend = [];
        $campaigns = $singleRecord->sponsoredProductPivotForCrons;
        if (!$campaigns->isEmpty()) {
            foreach ($campaigns as $singleCampaign) {
                $apiVarData['campaignId'] = intval($singleCampaign->campaignId);
                $apiVarData['profileId'] = intval($singleCampaign->profileId);
                $apiVarData['state'] = "paused";
                $apiVarData['id'] = intval($singleCampaign->id);
                array_push($apiVarDataToSend, $apiVarData);
            }
        }
        return $apiVarDataToSend;
    }

    /**
     * @param $singleRecord
     * @return array
     */
    private function getEnableCampaignData($singleRecord)
    {
        $apiVarData = [];
        $apiVarDataToSend = [];
        $campaigns = $singleRecord->sponsoredProductPivotForCrons;
        if (!$campaigns->isEmpty()) {
            foreach ($campaigns as $singleCampaign) {
                $apiVarData['campaignId'] = intval($singleCampaign->campaignId);
                $apiVarData['profileId'] = intval($singleCampaign->profileId);
                $apiVarData['state'] = "enabled";
                $apiVarData['id'] = intval($singleCampaign->id);
                array_push($apiVarDataToSend, $apiVarData);
            }
        }
        return $apiVarDataToSend;
    }

    /**
     * @param $postData
     * @return bool
     */
    private function pausingEnablingCampaigns($postData)
    {
        if (!empty($postData)) {
            Log::info("Auth token get from DB Start!");
            // Get Access Token
            $obAccessToken = new AMSModel();
            $dataAccessTaken['accessToken'] = $obAccessToken->getAMSToken();

            if ($dataAccessTaken['accessToken'] != FALSE) {
                // Get client Id
                $obClientId = new AMSModel();
                $dataClientId['clientId'] = $obClientId->getParameter();
                if ($dataClientId['clientId'] != FALSE) {
                    $postCount = count($postData);
                    $storeDataArrayUpdate = [];
                    for ($i = 0; $i < $postCount; $i++) {
                        b:
                        // Making Array to send over PUT Call
                        $apiPostDataToSend = [];
                        $apiPostDataToSend[] = [
                            'campaignId' => $postData[$i]['campaignId'],
                            'state' => $postData[$i]['state']
                        ];
                        // Create a client with a base URI
                        $apiUrl = getApiUrlForDiffEnv(env('APP_ENV'));
                        $url = $apiUrl . '/' . Config::get('constants.apiVersion') . '/' . Config::get('constants.spCampaignUrl');
                        Log::info(env('APP_ENV') . ' Url ->' . $url);
                        $client = new Client();
                        // Header
                        $headers = [
                            'Authorization' => 'Bearer ' . $dataAccessTaken['accessToken']->access_token,
                            'Content-Type' => 'application/json',
                            'Amazon-Advertising-API-ClientId' => $dataClientId['clientId']->client_id,
                            'Amazon-Advertising-API-Scope' => $postData[$i]['profileId']
                        ];

                        try {
                            $response = $client->request('PUT', $url, [
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
                                $storeDataArray['state'] = $postData[$i]['state'];
                                $storeDataArray['updated_at'] = date("Y-m-d H:i:s");
                                array_push($storeDataArrayUpdate, $storeDataArray);
                            }
                        } catch (\Exception $ex) {
                            if ($ex->getCode() == 401) {
                                if (strstr($ex->getMessage(), '401 Unauthorized')) { // if auth token expire
                                    Log::error('Refresh Access token. In file filePath:Commands\Ams\Portfolio\SP\pauseCampaignList');
                                    Artisan::call('getaccesstoken:amsauth');
                                    $obAccessToken = new AMSModel();
                                    $dataAccessTaken['accessToken'] = $obAccessToken->getAMSToken();
                                    goto b;
                                } elseif (strstr($ex->getMessage(), 'advertiser found for scope')) {
                                    // store profile list not valid
                                    Log::info("Invalid Profile Id: " . $postData[$i]['profileId']);
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
                    } // End For Loop
                    // Update Campaign Records
                    if (!empty($storeDataArrayUpdate)) {
                        PortfolioAllCampaignList::updateCampaign($storeDataArrayUpdate);
                        return TRUE;
                    } else {
                        return FALSE;

                    }
                } else {
                    Log::info("Client Id not found Ams\Portfolio\SP\updateCampaignList.");
                }
            } else {
                Log::info("AMS access token not found Ams\Portfolio\SP\updateCampaignList.");
            }
        } else {
            Log::info("No Post Data in Campaigns");
            return FALSE;
        }

    }
}
