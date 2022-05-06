<?php

namespace App\Console\Commands\Ams\Portfolio;

use App\Models\AccountModels\AccountModel;
use App\Models\AMSModel;
use App\Models\ClientModels\ClientModel;
use App\Models\DayPartingModels\DayPartingCampaignScheduleIds;
use App\Models\DayPartingModels\DayPartingPortfolioScheduleIds;
use App\models\DayPartingModels\DayPartingScheduleCronStatuses;
use App\Models\DayPartingModels\PfCampaignSchedule;
use App\Models\DayPartingModels\PortfolioAllCampaignList;
use App\User;
use Artisan;
use GuzzleHttp\Client;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Log;

class updateAllCampaignList extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'updateAllCampaignList:portfolio';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'This Command is used to update All Campaign List';

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
     * @throws \ReflectionException
     */
    public function handle()
    {
        $allScheduleCampaignsPf = PfCampaignSchedule::select('id', 'scheduleName', 'portfolioCampaignType', 'mon', 'tue', 'wed', 'thu', 'fri', 'sat', 'sun', 'startTime', 'endTime', 'emailReceiptStart','emailReceiptEnd', 'ccEmails', 'isScheduleExpired','isCronRunning', 'created_at','fkProfileId')
            ->where('isScheduleExpired', 0)
            ->where('isActive', 1)
            ->with('sponsoredBrand:id,name,campaignId,profileId,fkConfigId')
            ->with('sponsoredDisplay:id,name,campaignId,profileId,fkConfigId')
            ->with('sponsoredProduct:id,name,campaignId,profileId,fkConfigId')
            ->get();
        Log::info("filePath:Commands\Ams\Portfolio\updateAllCampaignList. Start Cron.");
        Log::info($this->description);
        if (!$allScheduleCampaignsPf->isEmpty()) {
            foreach ($allScheduleCampaignsPf as $singleRecord) {

                Log::info("Loop start for ". $singleRecord->scheduleName);
                $currentDateTime = date('Y-m-d H:i:s');
                $expireScheduleDate = date("Y-m-d H:i:s", strtotime($singleRecord->created_at . '  +7 day'));
                $recurring = $singleRecord->reccuringSchedule;

                // check if current date is less than expiring of scheduling date
                if ($currentDateTime < $expireScheduleDate || $recurring != 0 ) {
                    $currentDate = date('Y-m-d');
                    $stopScheduleDate = $singleRecord->stopScheduleDate;

                    if (is_null($stopScheduleDate) || $currentDate == $stopScheduleDate){
                        $todayName = strtolower(date('l'));
                        $currentTime = date('H:i:00');
                        $enableCampaignList = $this->getEnablePauseCampaignDataOne($singleRecord, 'enabled');
                        $pauseCampaignList = $this->getEnablePauseCampaignDataOne($singleRecord, 'paused');
                        switch ($todayName) {
                            case "monday":
                                {
                                    Log::info($todayName.'  value = ' . $singleRecord->mon);
                                    if ($singleRecord->mon === 1) {
                                        Log::info($todayName.' time check  schedule Name = ' . $singleRecord->scheduleName);
                                        $this->cronEnablingPausingActiveDays($singleRecord, $currentTime, $pauseCampaignList, $enableCampaignList, 'mon');
                                    } elseif($singleRecord->mon === 0) {
                                        Log::info(''.$todayName.' is not active it will run whole day = ' . $singleRecord->scheduleName);
                                        $this->cronEnablingPausingInActiveDays($singleRecord, $currentTime, $pauseCampaignList,  $enableCampaignList, 'mon');
                                    }
                                    break;
                                }
                            case "tuesday":
                                {
                                    Log::info($todayName.' value = ' . $singleRecord->tue);
                                    if ($singleRecord->tue === 1) {
                                        Log::info($todayName.' time check  schedule Name = ' . $singleRecord->scheduleName);
                                        $this->cronEnablingPausingActiveDays($singleRecord, $currentTime, $pauseCampaignList, $enableCampaignList, 'tue');
                                    } elseif($singleRecord->tue === 0) {
                                        Log::info(''.$todayName.' is not active it will run whole day = ' . $singleRecord->scheduleName);
                                        $this->cronEnablingPausingInActiveDays($singleRecord, $currentTime, $pauseCampaignList,  $enableCampaignList, 'tue');
                                    }
                                    break;
                                }
                            case "wednesday":
                                {
                                    Log::info($todayName.' value = ' . $singleRecord->wed);
                                    if ($singleRecord->wed === 1) {
                                        Log::info($todayName.' time check  schedule Name = ' . $singleRecord->scheduleName);
                                        $this->cronEnablingPausingActiveDays($singleRecord, $currentTime, $pauseCampaignList, $enableCampaignList, 'wed');
                                    } elseif($singleRecord->wed === 0) {
                                        Log::info(''.$todayName.' is not active it will run whole day = ' . $singleRecord->scheduleName);
                                        $this->cronEnablingPausingInActiveDays($singleRecord, $currentTime, $pauseCampaignList,  $enableCampaignList, 'wed');
                                    }
                                    break;
                                }
                            case "thursday":
                                {
                                    Log::info($todayName.' value = ' . $singleRecord->thu);
                                    if ($singleRecord->thu == 1) {
                                        Log::info($todayName.' time check  schedule Name = ' . $singleRecord->scheduleName);
                                        $this->cronEnablingPausingActiveDays($singleRecord, $currentTime, $pauseCampaignList, $enableCampaignList, 'thu');
                                    }else {
                                        Log::info(''.$todayName.' is not active it will run whole day = ' . $singleRecord->scheduleName);
                                        $this->cronEnablingPausingInActiveDays($singleRecord,  $currentTime, $pauseCampaignList, $enableCampaignList, 'thu');
                                    }
                                    break;
                                }
                            case "friday":
                                {
                                    Log::info($todayName.' value = ' . $singleRecord->fri);
                                    if ($singleRecord->fri === 1) {
                                        Log::info($todayName.' time check  schedule Name = ' . $singleRecord->scheduleName);
                                        $this->cronEnablingPausingActiveDays($singleRecord, $currentTime, $pauseCampaignList, $enableCampaignList, 'fri');
                                    } elseif($singleRecord->fri === 0) {
                                        Log::info($todayName.' is not active it will run whole day = ' . $singleRecord->scheduleName);
                                        $this->cronEnablingPausingInActiveDays($singleRecord, $currentTime, $pauseCampaignList, $enableCampaignList, 'fri');
                                    }
                                    break;
                                }
                            case "saturday":
                                {
                                    Log::info($todayName.' value = ' . $singleRecord->sat);
                                    if ($singleRecord->sat === 1) {
                                        Log::info($todayName.' time check  schedule Name = ' . $singleRecord->scheduleName);
                                        $this->cronEnablingPausingActiveDays($singleRecord, $currentTime, $pauseCampaignList, $enableCampaignList, 'sat');
                                    } elseif($singleRecord->sat === 0) {
                                        Log::info(''.$todayName.' is not active it will run whole day = ' . $singleRecord->scheduleName);
                                        $this->cronEnablingPausingInActiveDays($singleRecord, $currentTime, $pauseCampaignList, $enableCampaignList, 'sat');
                                    }

                                    break;
                                }
                            case "sunday":
                                {
                                    Log::info($todayName.' value = ' . $singleRecord->sun);
                                    if ($singleRecord->sun === 1) {
                                        Log::info($todayName.' time check  schedule Name = ' . $singleRecord->scheduleName);
                                        $this->cronEnablingPausingActiveDays($singleRecord, $currentTime, $pauseCampaignList, $enableCampaignList, 'sun');
                                    } elseif($singleRecord->sun === 0) {
                                        Log::info(''.$todayName.' is not active it will run whole day = ' . $singleRecord->scheduleName);
                                        $this->cronEnablingPausingInActiveDays($singleRecord, $currentTime, $pauseCampaignList, $enableCampaignList, 'sun');
                                    }

                                    break;
                                }
                        }
                    }
                }else {

                    $expireData['isScheduleExpired'] = 1;
                    PfCampaignSchedule::updateSchedule($singleRecord->id, $expireData);
                    Log::info('Schedule week completed. Name = ' . $singleRecord->scheduleName);
                    switch ($singleRecord->portfolioCampaignType) {
                        case 'Campaign':
                            {
                                DayPartingCampaignScheduleIds::where('fkScheduleId', $singleRecord->id)
                                    ->update([
                                        'userSelection' => 1,
                                        'enablingPausingTime' => '23:59:00',
                                        'enablingPausingStatus' => 'expired'
                                    ]);
                                break;
                            }
                        case 'Portfolio':
                            {
                                DayPartingPortfolioScheduleIds::where('fkScheduleId', $singleRecord->id)->update([
                                    'userSelection' => 1,
                                    'enablingPausingTime' => '23:59:00',
                                    'enablingPausingStatus' => 'expired'
                                ]);
                                DayPartingCampaignScheduleIds::where('fkScheduleId', $singleRecord->id)->update([
                                    'userSelection' => 1,
                                    'enablingPausingTime' => '23:59:00',
                                    'enablingPausingStatus' => 'expired'
                                ]);
                                break;
                            }
                    }// Switch Case End
                }
                Log::info("Loop End for ". $singleRecord->scheduleName);
            }
        }else{
            Log::info("DayParting No Campaigns To Run");
        }
        Log::info("filePath:Commands\Ams\Portfolio\updateAllCampaignList. End Cron.");
    }

    /**
     * @param $recordSchedule
     * @param $state
     * @return array
     */
    private function getEnablePauseCampaignDataOne($recordSchedule, $state)
    {
        $apiVarData = [];
        $apiVarDataToSend = [];
        // Urls For All Campaigns
        $apiUrl = getApiUrlForDiffEnv(env('APP_ENV'));
        $sponsoredBrandUrl = $apiUrl . '/' . Config::get('constants.sbCampaignUrl');
        $sponsoredProductUrl = $apiUrl . '/' . Config::get('constants.apiVersion') . '/' . Config::get('constants.spCampaignUrl');
        $sponsoredDisplayUrl = $apiUrl . '/' . Config::get('constants.sdCampaignUrl');

        $sbCampaign = $recordSchedule->sponsoredBrand;
        if (!$sbCampaign->isEmpty()){
            foreach ($sbCampaign as $singleCampaign) {
                $apiVarData['campaignId'] = intval($singleCampaign->campaignId);
                $apiVarData['profileId'] = intval($singleCampaign->profileId);
                $apiVarData['fkConfigId'] = intval($singleCampaign->fkConfigId);
                $apiVarData['state'] = $state;
                $apiVarData['url'] = $sponsoredBrandUrl;
                array_push($apiVarDataToSend, $apiVarData);
            }
        }

        $spCampaign = $recordSchedule->sponsoredProduct;
        if (!$spCampaign->isEmpty()){
            foreach ($spCampaign as $singleCampaign) {
                $apiVarData['campaignId'] = intval($singleCampaign->campaignId);
                $apiVarData['profileId'] = intval($singleCampaign->profileId);
                $apiVarData['fkConfigId'] = intval($singleCampaign->fkConfigId);
                $apiVarData['state'] = $state;
                $apiVarData['url'] = $sponsoredProductUrl;
                array_push($apiVarDataToSend, $apiVarData);
            }
        }

        $sdCampaign = $recordSchedule->sponsoredDisplay;
        if (!$sdCampaign->isEmpty()){
            foreach ($sdCampaign as $singleCampaign) {
                $apiVarData['campaignId'] = intval($singleCampaign->campaignId);
                $apiVarData['profileId'] = intval($singleCampaign->profileId);
                $apiVarData['fkConfigId'] = intval($singleCampaign->fkConfigId);
                $apiVarData['state'] = $state;
                $apiVarData['url'] = $sponsoredDisplayUrl;
                array_push($apiVarDataToSend, $apiVarData);
            }
        }
        
        return $apiVarDataToSend;
    }

    /**
     * @param $singleRecord
     * @param $currentTime
     * @param $pauseCampaignList
     * @param $enableCampaignList
     * @param $cronDay
     * @return bool
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws \ReflectionException
     */
    private function cronEnablingPausingActiveDays($singleRecord, $currentTime, $pauseCampaignList, $enableCampaignList, $cronDay )
    {
        $response = TRUE;
        Log::info('cronEnablingPausingActiveDays   = schedule name = '. $singleRecord->scheduleName.' is Running = ' . $singleRecord->isCronRunning);
        $managerEmailArray = $this->getEmailManagers($singleRecord->fkProfileId);
        if ($singleRecord->isCronRunning === 0) {
            Log::info('cronEnablingPausingActiveDays  = cron is running'.$singleRecord->isCronRunning);
            Log::info('cronEnablingPausingActiveDays  = cron is not running check start time');
            Log::info('cronEnablingPausingActiveDays  = current Time = '. $currentTime . ' Cron Start Time ='. $singleRecord->startTime);

            if ($currentTime === $singleRecord->startTime) {
                Log::info('cronEnablingPausingActiveDays  = start time matches schedule Name = '. $singleRecord->scheduleName);
                $return = $this->enablePauseScheduleCampaigns($enableCampaignList);
                Log::info('cronEnablingPausingActiveDays  =  Return = '. json_encode($return));
                if ($return == TRUE) {
                    Log::info('cronEnablingPausingActiveDays  = campaigns enabled successfully against schedule Name  = '. $singleRecord->scheduleName);
                    $scheduleData = $this->scheduleUpdateStatuses(1, 1, 0, 0);
                    PfCampaignSchedule::updateSchedule($singleRecord->id, $scheduleData);
                    $cronScheduleStatuses['fkScheduleId'] = $singleRecord->id;
                    $cronScheduleStatuses['scheduleDate'] = date('Y-m-d');
                    $cronScheduleStatuses['scheduleStatus'] = 1;
                    $cronScheduleStatuses['cronMessage'] = NULL;
                    DayPartingScheduleCronStatuses::updateScheduleStatuses($cronScheduleStatuses);
                    //DayPartingScheduleCronStatuses::insertScheduleStatuses($cronScheduleStatuses);
                    if ($singleRecord->emailReceiptStart == 1) {
                        Log::info('cronEnablingPausingActiveDays  = Email enabled on start time against schedule Name  = '. $singleRecord->scheduleName);
                        if (!empty($managerEmailArray)){
                            _sendEmailForEnabledCampaign($managerEmailArray, $singleRecord->ccEmails, $singleRecord->scheduleName);
                        }
                    }
                    $response = TRUE;
                } else {
                    Log::info('cronEnablingPausingActiveDays  = campaigns Error against schedule Name  = '. $singleRecord->scheduleName);
                    $scheduleData = $this->scheduleUpdateStatuses(0, 0, 1, 0);
                    PfCampaignSchedule::updateSchedule($singleRecord->id, $scheduleData);
                    $cronScheduleStatuses['fkScheduleId'] = $singleRecord->id;
                    $cronScheduleStatuses['scheduleDate'] = date('Y-m-d');
                    $cronScheduleStatuses['scheduleStatus'] = 2;
                    $cronScheduleStatuses['cronMessage'] = $return['errorMessage'];
                    DayPartingScheduleCronStatuses::updateScheduleStatuses($cronScheduleStatuses);
                    //DayPartingScheduleCronStatuses::insertScheduleStatuses($cronScheduleStatuses);
                    if (!empty($managerEmailArray)){
                        _sendEmailForErrorCampaign($managerEmailArray, $singleRecord->ccEmails, $singleRecord->scheduleName);
                    }
                    $response = FALSE;
                }
            }
        } elseif ($singleRecord->isCronRunning === 1) {
            Log::info('cronEnablingPausingActiveDays  = cron is running '.$singleRecord->isCronRunning. ' check end time');
            if ($currentTime == $singleRecord->endTime) {
                Log::info('cronEnablingPausingActiveDays  = end time matches schedule Name = '. $singleRecord->scheduleName);
                $return = $this->enablePauseScheduleCampaigns($pauseCampaignList);
                
                Log::info('cronEnablingPausingActiveDays  =  Return = '. json_encode($return));
                if ($return == TRUE) {
                    Log::info('cronEnablingPausingActiveDays  = campaigns paused successfully against schedule Name  = '. $singleRecord->scheduleName);
                    $scheduleData = $this->scheduleUpdateStatuses(0, 1, 0, 1);
                    PfCampaignSchedule::updateSchedule($singleRecord->id, $scheduleData);
                    $cronScheduleStatuses['fkScheduleId'] = $singleRecord->id;
                    $cronScheduleStatuses['scheduleDate'] = date('Y-m-d');
                    $cronScheduleStatuses['scheduleStatus'] = 1;
                    $cronScheduleStatuses['cronMessage'] = NULL;
                    DayPartingScheduleCronStatuses::updateScheduleStatuses($cronScheduleStatuses);
                    //DayPartingScheduleCronStatuses::insertScheduleStatuses($cronScheduleStatuses);
                    if ($singleRecord->emailReceiptEnd == 1) {
                        Log::info('cronEnablingPausingActiveDays  = Email paused on end time against schedule Name  = '. $singleRecord->scheduleName);
                        if (!empty($managerEmailArray)){
                            _sendEmailForPausedCampaign($managerEmailArray, $singleRecord->ccEmails, $singleRecord->scheduleName);
                        }
                    }
                    $response = TRUE;
                } else {
                    Log::info('cronEnablingPausingActiveDays  = campaigns Error against schedule Name  = '. $singleRecord->scheduleName);
                    $scheduleData = $this->scheduleUpdateStatuses(0, 0, 1, 0);
                    PfCampaignSchedule::updateSchedule($singleRecord->id, $scheduleData);
                    $cronScheduleStatuses['fkScheduleId'] = $singleRecord->id;
                    $cronScheduleStatuses['scheduleDate'] = date('Y-m-d');
                    $cronScheduleStatuses['scheduleStatus'] = 2;
                    $cronScheduleStatuses['cronMessage'] = $return['errorMessage'];
                    DayPartingScheduleCronStatuses::updateScheduleStatuses($cronScheduleStatuses);
                    //DayPartingScheduleCronStatuses::insertScheduleStatuses($cronScheduleStatuses);
                    Log::info('cronEnablingPausingActiveDays  = Error Email sent against schedule Name  = '. $singleRecord->scheduleName);
                    if (!empty($managerEmailArray)){
                        _sendEmailForErrorCampaign($managerEmailArray, $singleRecord->ccEmails, $singleRecord->scheduleName);
                    }
                    $response = FALSE;
                }
            }
        }

        return $response;
    }

    /**
     * @param $singleRecord
     * @param $currentTime
     * @param $pauseCampaignList
     * @param $enableCampaignList
     * @param $cronDay
     * @return bool
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws \ReflectionException
     */
    private function cronEnablingPausingInActiveDays($singleRecord, $currentTime, $pauseCampaignList, $enableCampaignList, $cronDay)
    {
        $timeToPauseCampaign = '23:59:00';
        $response = TRUE;
        $managerEmailArray = $this->getEmailManagers($singleRecord->fkProfileId);

        if ($singleRecord->isCronRunning == 0) {
            Log::info('cronEnablingPausingInActiveDays  = if cron were running = '.$singleRecord->isCronRunning);
            $return = $this->enablePauseScheduleCampaigns($enableCampaignList);
            Log::info('enablePauseScheduleCampaigns function response'. json_encode($return));
            if ($return == TRUE) {
                Log::info('cronEnablingPausingInActiveDays  = campaigns enabled successfully against schedule Name  = '. $singleRecord->scheduleName);
                $scheduleData = $this->scheduleUpdateStatuses(2, 1, 0, 0);
                PfCampaignSchedule::updateSchedule($singleRecord->id, $scheduleData);
                $cronScheduleStatuses['fkScheduleId'] = $singleRecord->id;
                $cronScheduleStatuses['scheduleDate'] = date('Y-m-d');
                $cronScheduleStatuses['scheduleStatus'] = 1;
                $cronScheduleStatuses['cronMessage'] = NULL;
                DayPartingScheduleCronStatuses::updateScheduleStatuses($cronScheduleStatuses);
                //DayPartingScheduleCronStatuses::insertScheduleStatuses($cronScheduleStatuses);
                if ($singleRecord->emailReceiptStart == 1) {
                    if (!empty($managerEmailArray)){
                        Log::info('cronEnablingPausingInActiveDays  = Email enabled on start time against schedule Name  = '. $singleRecord->scheduleName);
                        _sendEmailForEnabledCampaign($managerEmailArray, $singleRecord->ccEmails, $singleRecord->scheduleName);
                    }
                }
                $response = TRUE;
            } else {
                Log::info('cronEnablingPausingInActiveDays  = campaigns Error against schedule Name  = '. $singleRecord->scheduleName);
                $scheduleData = $this->scheduleUpdateStatuses(0, 0, 1, 0);
                PfCampaignSchedule::updateSchedule($singleRecord->id, $scheduleData);
                $cronScheduleStatuses['fkScheduleId'] = $singleRecord->id;
                $cronScheduleStatuses['scheduleDate'] = date('Y-m-d');
                $cronScheduleStatuses['scheduleStatus'] = 2;
                $cronScheduleStatuses['cronMessage'] = $return['errorMessage'];
                DayPartingScheduleCronStatuses::updateScheduleStatuses($cronScheduleStatuses);
                //DayPartingScheduleCronStatuses::insertScheduleStatuses($cronScheduleStatuses);
                Log::info('cronEnablingPausingInActiveDays = Error Email sent against schedule Name  = '. $singleRecord->scheduleName);
                if (!empty($managerEmailArray)){
                    Log::info('cronEnablingPausingInActiveDays = Email enabled on start time against schedule Name  = '. $singleRecord->scheduleName);
                    _sendEmailForErrorCampaign($managerEmailArray, $singleRecord->ccEmails, $singleRecord->scheduleName);
                }
                $response = FALSE;
            }
        }
        if ($singleRecord->isCronRunning == 2){
            if ($currentTime === $timeToPauseCampaign){
                $return = $this->enablePauseScheduleCampaigns($pauseCampaignList);
                Log::info('enablePauseScheduleCampaigns function response'. json_encode($return));
                if ($return == TRUE) {
                    Log::info('cronEnablingPausingActiveDays = campaigns paused successfully against schedule Name  = '. $singleRecord->scheduleName);
                    $scheduleData = $this->scheduleUpdateStatuses(0, 0, 0, 0);
                    PfCampaignSchedule::updateSchedule($singleRecord->id, $scheduleData);
                    $cronScheduleStatuses['fkScheduleId'] = $singleRecord->id;
                    $cronScheduleStatuses['scheduleDate'] = date('Y-m-d');
                    $cronScheduleStatuses['scheduleStatus'] = 2;
                    $cronScheduleStatuses['cronMessage'] = NULL;
                    DayPartingScheduleCronStatuses::updateScheduleStatuses($cronScheduleStatuses);
                    $response = TRUE;
                } else {
                    Log::info('cronEnablingPausingActiveDays = campaigns Error against schedule Name  = '. $singleRecord->scheduleName);
                    $scheduleData = $this->scheduleUpdateStatuses(0, 0, 1, 0);
                    PfCampaignSchedule::updateSchedule($singleRecord->id, $scheduleData);
                    $cronScheduleStatuses['fkScheduleId'] = $singleRecord->id;
                    $cronScheduleStatuses['scheduleDate'] = date('Y-m-d');
                    $cronScheduleStatuses['scheduleStatus'] = 2;
                    $cronScheduleStatuses['cronMessage'] = $return['errorMessage'];
                    DayPartingScheduleCronStatuses::updateScheduleStatuses($cronScheduleStatuses);
                    $response = FALSE;
                }
            }
        }
        return $response;
    }

    /**
     * @param $postData
     * @return bool
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    private function enablePauseScheduleCampaigns($postData)
    {
        if (!empty($postData)){
            Log::info("Auth token get from DB Start updateAllCampaignList DayParting!");
                    $postCount = count($postData);
                    $storeDataArrayUpdate = [];
                    for ($i = 0; $i < $postCount; $i++) {
                        $fkConfigId = $postData[$i]['fkConfigId'];
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
                            'campaignId' => $postData[$i]['campaignId'],
                            'state' => $postData[$i]['state']
                        ];

                        $client = new Client();
                        // Header
                        $headers = [
                            'Authorization' => 'Bearer ' . $accessToken,
                            'Content-Type' => 'application/json',
                            'Amazon-Advertising-API-ClientId' => $clientId,
                            'Amazon-Advertising-API-Scope' => $postData[$i]['profileId']
                        ];
                        Log::info('Url Day Parting Campaigns = '. $postData[$i]['url']);
                        Log::info('Url Day Parting Post Data '. json_encode($apiPostDataToSend));

                        try {
                            $response = $client->request('PUT', $postData[$i]['url'], [
                                'headers' => $headers,
                                'body' => json_encode($apiPostDataToSend),
                                'delay' => Config::get('constants.delayTimeInApi'),
                                'connect_timeout' => Config::get('constants.connectTimeOutInApi'),
                                'timeout' => Config::get('constants.timeoutInApi')
                            ]);

                            $responseBody = json_decode($response->getBody()->getContents());
                            Log::info('Day Parting Campaign Id'. $postData[$i]['campaignId']. 'Response = '.json_encode($responseBody));
                            if (!empty($responseBody) && !is_null($responseBody)) {
                                $storeDataArray = [];
                                $storeDataArray['campaignId'] = $responseBody[0]->campaignId;
                                $storeDataArray['state'] = $postData[$i]['state'];
                                $storeDataArray['updated_at'] = date("Y-m-d H:i:s");
                                array_push($storeDataArrayUpdate, $storeDataArray);
                            }

                        } catch (\Exception $ex) {

                            if ($ex->getCode() == 401) {
                                Log::error('Refresh Access token. In file filePath:Commands\Ams\Portfolio\updateAllCampaignList');
                                $authCommandArray = array();
                                $authCommandArray['fkConfigId'] = $fkConfigId;
                                \Artisan::call('getaccesstoken:amsauth', $authCommandArray);
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
                            Log::error($ex->getMessage());
                        }// End catch
                } else {
                    Log::info("AMS client Id or access token not found Ams\Portfolio\updateAllCampaignList.");
                }
                    } // End For Loop
                    // Update Campaign Records
                    Log::info('Day Parting storeDataArrayUpdate ' .json_encode($storeDataArrayUpdate));
                    if (!empty($storeDataArrayUpdate)) {
                        Log::info('Day Parting storeDataArrayUpdate not empty ');
                        PortfolioAllCampaignList::updateCampaign($storeDataArrayUpdate);
                        return TRUE;
                    } else {
                        Log::info('Day Parting storeDataArrayUpdate not empty ');
                        return FALSE;

                    }
        }else{
            Log::info("No Post Data in Campaigns");
            return FALSE;
        }
    }

    /**
     * @param $isCronRunning
     * @param $isCronSuccess
     * @param $isCronError
     * @param $isCronEnd
     * @return mixed
     */
    private function scheduleUpdateStatuses($isCronRunning, $isCronSuccess, $isCronError, $isCronEnd)
    {
        $scheduleData['isCronRunning'] = $isCronRunning;
        $scheduleData['isCronSuccess'] = $isCronSuccess;
        $scheduleData['isCronError'] = $isCronError;
        $scheduleData['isCronEnd'] = $isCronEnd;

        return $scheduleData;
    }

    /**
     * @param $fkProfileId
     * @return array
     */
    function getEmailManagers($fkProfileId)
    {
        $GetManagerId = AccountModel::where('fkId', $fkProfileId)->where('fkAccountType', 1)->first();
        $brandId = '';
        if(!empty($GetManagerId)){
            $brandId = $GetManagerId->fkBrandId;
        }

        $managerEmailArray = [];
        if(!empty($brandId) || $brandId != 0){
            $getBrandAssignedUsers = ClientModel::with("brandAssignedUsers")->find($brandId);
            foreach ($getBrandAssignedUsers->brandAssignedUsers as $getBrandAssignedUser) {
                $brandAssignedUserId = $getBrandAssignedUser->pivot->fkManagerId;
                $GetManagerEmail = User::where('id', $brandAssignedUserId)->first();
                $managerEmailArray[] = $GetManagerEmail->email;
            }
        }
        return $managerEmailArray;
    }

}
