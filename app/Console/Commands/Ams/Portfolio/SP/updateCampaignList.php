<?php

namespace App\Console\Commands\Ams\Portfolio\SP;


use App\Models\AccountModels\AccountModel;
use App\Models\ClientModels\ClientModel;
use App\Models\DayPartingModels\DayPartingCampaignScheduleIds;
use App\Models\DayPartingModels\DayPartingPortfolioScheduleIds;
use App\User;
use Artisan;
use App\Models\AMSModel;
use GuzzleHttp\Client;
use Illuminate\Console\Command;
use App\Models\DayPartingModels\PfCampaignSchedule;
use App\Models\DayPartingModels\PortfolioAllCampaignList;
use App\Models\DayPartingModels\DayPartingScheduleCronStatuses;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Log;

class updateCampaignList extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'updateSPCampaignlist:portfolio';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'This Command is used to update Sponsored Product Campaign List';
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
     * @throws \ReflectionException
     */
    public function handle()
    {
        // Get All Campaigns List
        $allScheduleCampaignsPf = PfCampaignSchedule::select('id', 'scheduleName', 'managerEmail', 'ccEmails', 'portfolioCampaignType', 'mon', 'tue', 'wed', 'thu', 'fri', 'sat', 'sun', 'startTime',
            'endTime', 'emailReceiptStart', 'emailReceiptEnd', 'isScheduleExpired', 'isCronRunning', 'created_at', 'fkProfileId')
            ->where('isScheduleExpired', 0)
            ->where('isActive', 1)
            ->whereHas('sponsoredProduct')
            ->with('sponsoredProduct:id,name,campaignId,profileId')->get();
        Log::info("filePath:Commands\Ams\Portfolio\SP\updateSPCampaignlist. Start Cron.");
        Log::info($this->description);
        if (!$allScheduleCampaignsPf->isEmpty()) {
            foreach ($allScheduleCampaignsPf as $singleRecord) {
                Log::info('Schedule Name = ' . $singleRecord->scheduleName);
                $currentDate = date('Y-m-d H:i:s');
                $expireSheduleDate = date("Y-m-d H:i:s", strtotime($singleRecord->created_at . '  +7 day'));
                // check if current date is less than expiring of scheduling date
                if ($currentDate < $expireSheduleDate) {
                    $todayName = strtolower(date('l'));
                    $currentTime = date('H:i:00');
                    $enableCampaignList = $this->getEnableCampaignData($singleRecord);
                    $pauseCampaignList = $this->getPauseCampaignData($singleRecord);

                    switch ($todayName) {
                        case "monday":
                            {
                                Log::info($todayName.' value = ' . $singleRecord->mon);
                                if ($singleRecord->mon === 1) {
                                    Log::info($todayName.' time check schedule Name = ' . $singleRecord->scheduleName);
                                    $this->cronEnablingPausingActiveDays($singleRecord, $currentTime, $pauseCampaignList, $enableCampaignList, 'mon');
                                } elseif($singleRecord->mon === 0) {
                                    Log::info($todayName.' is not active it will run whole day = ' . $singleRecord->scheduleName);
                                    $this->cronEnablingPausingInActiveDays($singleRecord, $currentTime, $pauseCampaignList,  $enableCampaignList, 'mon');
                                }
                                break;
                            }
                        case "tuesday":
                            {
                                Log::info($todayName.' value = ' . $singleRecord->tue);
                                if ($singleRecord->tue === 1) {
                                    Log::info($todayName.' time check schedule Name = ' . $singleRecord->scheduleName);
                                    $this->cronEnablingPausingActiveDays($singleRecord, $currentTime, $pauseCampaignList, $enableCampaignList, 'tue');
                                } elseif($singleRecord->tue === 0) {
                                    Log::info($todayName.' is not active it will run whole day = ' . $singleRecord->scheduleName);
                                    $this->cronEnablingPausingInActiveDays($singleRecord, $currentTime, $pauseCampaignList,  $enableCampaignList, 'tue');
                                }
                                break;
                            }
                        case "wednesday":
                            {
                                Log::info($todayName.' value = ' . $singleRecord->wed);
                                if ($singleRecord->wed === 1) {
                                    Log::info($todayName.' time check schedule Name = ' . $singleRecord->scheduleName);
                                    $this->cronEnablingPausingActiveDays($singleRecord, $currentTime, $pauseCampaignList, $enableCampaignList, 'wed');
                                } elseif($singleRecord->wed === 0) {
                                    Log::info($todayName.' is not active it will run whole day = ' . $singleRecord->scheduleName);
                                    $this->cronEnablingPausingInActiveDays($singleRecord, $currentTime, $pauseCampaignList,  $enableCampaignList, 'wed');
                                }
                                break;
                            }
                        case "thursday":
                            {
                                Log::info($todayName.' value = ' . $singleRecord->thu);
                                if ($singleRecord->thu == 1) {
                                    Log::info($todayName.' time check schedule Name = ' . $singleRecord->scheduleName);
                                    $result = $this->cronEnablingPausingActiveDays($singleRecord, $currentTime, $pauseCampaignList, $enableCampaignList, 'thu');
                                }else {
                                    Log::info($todayName.' is not active it will run whole day = ' . $singleRecord->scheduleName);
                                    $this->cronEnablingPausingInActiveDays($singleRecord,  $currentTime, $pauseCampaignList, $enableCampaignList, 'thu');
                                }

                                break;
                            }
                        case "friday":
                            {
                                Log::info($todayName.' value = ' . $singleRecord->fri);
                                if ($singleRecord->fri === 1) {
                                    Log::info($todayName.' time check schedule Name = ' . $singleRecord->scheduleName);
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
                                    Log::info($todayName.' time check schedule Name = ' . $singleRecord->scheduleName);
                                    $this->cronEnablingPausingActiveDays($singleRecord, $currentTime, $pauseCampaignList, $enableCampaignList, 'sat');
                                } elseif($singleRecord->sat === 0) {
                                    Log::info($todayName.' is not active it will run whole day = ' . $singleRecord->scheduleName);
                                    $this->cronEnablingPausingInActiveDays($singleRecord, $currentTime, $pauseCampaignList, $enableCampaignList, 'sat');
                                }

                                break;
                            }
                        case "sunday":
                            {
                                Log::info($todayName.' value = ' . $singleRecord->sun);
                                if ($singleRecord->sun === 1) {
                                    Log::info($todayName.' time check schedule Name = ' . $singleRecord->scheduleName);
                                    $this->cronEnablingPausingActiveDays($singleRecord, $currentTime, $pauseCampaignList, $enableCampaignList, 'sun');
                                } elseif($singleRecord->sun === 0) {
                                    Log::info($todayName.' is not active it will run whole day = ' . $singleRecord->scheduleName);
                                    $this->cronEnablingPausingInActiveDays($singleRecord, $currentTime, $pauseCampaignList, $enableCampaignList, 'sun');
                                }

                                break;
                            }
                    }

                } else {
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
            } // End foreach Loop all schedule List
        } else {
            Log::info("No Sponsored Product Campaign To Run");
        }
        Log::info("filePath:Commands\Ams\Portfolio\SP\updateSPCampaignlist. End Cron.");
    }

    private function scheduleUpdateStatuses($isCronRunning, $isCronSuccess, $isCronError, $isCronEnd)
    {
        $scheduleData['isCronRunning'] = $isCronRunning;
        $scheduleData['isCronSuccess'] = $isCronSuccess;
        $scheduleData['isCronError'] = $isCronError;
        $scheduleData['isCronEnd'] = $isCronEnd;

        return $scheduleData;
    }
    /**
     * @param $singleRecord
     * @param $currentTime
     * @param $pauseCampaignList
     * @param $enableCampaignList
     * @return bool
     * @throws \ReflectionException
     */
    private function cronEnablingPausingActiveDays($singleRecord, $currentTime, $pauseCampaignList, $enableCampaignList, $cronDay )
    {
        $scheduleData = [];
        $response = TRUE;
        Log::info('cronEnablingPausingActiveDays = schedule name = '. $singleRecord->scheduleName.' is Running = ' . $singleRecord->isCronRunning);
        $managerEmailArray = $this->getEmailManagers($singleRecord->fkProfileId);
        if ($singleRecord->isCronRunning === 0) {
            Log::info('cronEnablingPausingActiveDays = cron is running'.$singleRecord->isCronRunning);
            Log::info('cronEnablingPausingActiveDays = cron is not running check start time');
            Log::info('cronEnablingPausingActiveDays = current Time = '. $currentTime . ' Cron Start Time ='. $singleRecord->startTime);
            if ($currentTime === $singleRecord->startTime) {
                Log::info('cronEnablingPausingActiveDays = start time matches schedule Name = '. $singleRecord->scheduleName);
                $return = $this->enableScheduleCampaignPfOntime($enableCampaignList);
                if ($return['status'] == TRUE) {
                    Log::info('cronEnablingPausingActiveDays = campaigns enabled successfully against schedule Name  = '. $singleRecord->scheduleName);
                    $scheduleData = $this->scheduleUpdateStatuses(1, 1, 0, 0);
                    PfCampaignSchedule::updateSchedule($singleRecord->id, $scheduleData);
                    $cronScheduleStatuses['fkScheduleId'] = $singleRecord->id;
                    $cronScheduleStatuses[$cronDay] = 1;
                    $cronScheduleStatuses['cronMessage'] = NULL;
                    DayPartingScheduleCronStatuses::insertScheduleStatuses($cronScheduleStatuses);
                    if ($singleRecord->emailReceiptStart == 1) {
                        Log::info('cronEnablingPausingActiveDays = Email enabled on start time against schedule Name  = '. $singleRecord->scheduleName);
                        if (!empty($managerEmailArray)){
                            _sendEmailForEnabledCampaign($managerEmailArray, $singleRecord->ccEmails, $singleRecord->scheduleName);
                        }
                    }
                    $response = TRUE;
                } else {
                    Log::info('cronEnablingPausingActiveDays = campaigns Error against schedule Name  = '. $singleRecord->scheduleName);
                    $scheduleData = $this->scheduleUpdateStatuses(0, 0, 1, 0);
                    PfCampaignSchedule::updateSchedule($singleRecord->id, $scheduleData);
                    $cronScheduleStatuses['fkScheduleId'] = $singleRecord->id;
                    $cronScheduleStatuses[$cronDay] = 2;
                    $cronScheduleStatuses['cronMessage'] = $return['errorMessage'];
                    DayPartingScheduleCronStatuses::insertScheduleStatuses($cronScheduleStatuses);
                    if (!empty($managerEmailArray)){
                        _sendEmailForErrorCampaign($managerEmailArray, $singleRecord->ccEmails, $singleRecord->scheduleName);
                    }
                    $response = FALSE;
                }
            }
        } elseif ($singleRecord->isCronRunning === 1) {
            Log::info('cronEnablingPausingActiveDays = cron is running'.$singleRecord->isCronRunning. ' check end time');
            if ($currentTime == $singleRecord->endTime) {
                Log::info('cronEnablingPausingActiveDays = end time matches schedule Name = '. $singleRecord->scheduleName);
                $return = $this->pausedScheduleCampaignPfOntime($pauseCampaignList);
                Log::info('cronEnablingPausingActiveDays =  Return = '. json_encode($return));
                if ($return['status'] == TRUE) {
                    Log::info('cronEnablingPausingActiveDays = campaigns paused successfully against schedule Name  = '. $singleRecord->scheduleName);
                    $scheduleData = $this->scheduleUpdateStatuses(0, 1, 0, 1);
                    PfCampaignSchedule::updateSchedule($singleRecord->id, $scheduleData);
                    $cronScheduleStatuses['fkScheduleId'] = $singleRecord->id;
                    $cronScheduleStatuses[$cronDay] = 1;
                    $cronScheduleStatuses['cronMessage'] = NULL;
                    DayPartingScheduleCronStatuses::insertScheduleStatuses($cronScheduleStatuses);
                    if ($singleRecord->emailReceiptEnd == 1) {
                        Log::info('cronEnablingPausingActiveDays = Email paused on end time against schedule Name  = '. $singleRecord->scheduleName);
                        if (!empty($managerEmailArray)){
                            _sendEmailForPausedCampaign($managerEmailArray, $singleRecord->ccEmails, $singleRecord->scheduleName);
                        }
                    }
                    $response = TRUE;
                } else {
                    Log::info('cronEnablingPausingActiveDays = campaigns Error against schedule Name  = '. $singleRecord->scheduleName);
                    $scheduleData = $this->scheduleUpdateStatuses(0, 0, 1, 0);
                    $scheduleData['isCronRunning'] = 0;
                    $scheduleData['isCronSuccess'] = 0;
                    $scheduleData['isCronError'] = 1;
                    PfCampaignSchedule::updateSchedule($singleRecord->id, $scheduleData);
                    $cronScheduleStatuses['fkScheduleId'] = $singleRecord->id;
                    $cronScheduleStatuses[$cronDay] = 2;
                    $cronScheduleStatuses['cronMessage'] = $return['errorMessage'];
                    DayPartingScheduleCronStatuses::insertScheduleStatuses($cronScheduleStatuses);
                    Log::info('cronEnablingPausingActiveDays = Error Email sent against schedule Name  = '. $singleRecord->scheduleName);
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
     * @param $enableCampaignList
     * @return bool
     * @throws \ReflectionException
     */
    private function cronEnablingPausingInActiveDays($singleRecord, $currentTime, $pauseCampaignList, $enableCampaignList, $cronDay)
    {
        $timeToPauseCampaign = '23:59:00';
        $response = TRUE;
        $managerEmailArray = $this->getEmailManagers($singleRecord->fkProfileId);
        if ($singleRecord->isCronRunning == 0) {
            Log::info('cronEnablingPausingInActiveDays = if cron were running = '.$singleRecord->isCronRunning);
            $return = $this->enableScheduleCampaignPfOntime($enableCampaignList);
            if ($return['status'] == TRUE) {
                Log::info('cronEnablingPausingInActiveDays = campaigns enabled successfully against schedule Name  = '. $singleRecord->scheduleName);
                $scheduleData = $this->scheduleUpdateStatuses(2, 1, 0, 0);
                PfCampaignSchedule::updateSchedule($singleRecord->id, $scheduleData);
                $cronScheduleStatuses['fkScheduleId'] = $singleRecord->id;
                $cronScheduleStatuses[$cronDay] = 1;
                $cronScheduleStatuses['cronMessage'] = NULL;
                DayPartingScheduleCronStatuses::insertScheduleStatuses($cronScheduleStatuses);
                if ($singleRecord->emailReceiptStart == 1) {
                    if (!empty($managerEmailArray)){
                        Log::info('cronEnablingPausingInActiveDays = Email enabled on start time against schedule Name  = '. $singleRecord->scheduleName);
                        _sendEmailForEnabledCampaign($managerEmailArray, $singleRecord->ccEmails, $singleRecord->scheduleName);
                    }
                }
                $response = TRUE;
            } else {
                Log::info('cronEnablingPausingInActiveDays = campaigns Error against schedule Name  = '. $singleRecord->scheduleName);
                $scheduleData = $this->scheduleUpdateStatuses(0, 0, 1, 0);
                PfCampaignSchedule::updateSchedule($singleRecord->id, $scheduleData);
                $cronScheduleStatuses['fkScheduleId'] = $singleRecord->id;
                $cronScheduleStatuses[$cronDay] = 2;
                $cronScheduleStatuses['cronMessage'] = $return['errorMessage'];
                DayPartingScheduleCronStatuses::insertScheduleStatuses($cronScheduleStatuses);
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
                $return = $this->pausedScheduleCampaignPfOntime($pauseCampaignList);
                if ($return['status'] == TRUE) {
                    Log::info('cronEnablingPausingActiveDays = campaigns paused successfully against schedule Name  = '. $singleRecord->scheduleName);
                    $scheduleData = $this->scheduleUpdateStatuses(0, 0, 0, 0);
                    PfCampaignSchedule::updateSchedule($singleRecord->id, $scheduleData);
                    $cronScheduleStatuses['fkScheduleId'] = $singleRecord->id;
                    $response = TRUE;
                } else {
                    Log::info('cronEnablingPausingActiveDays = campaigns Error against schedule Name  = '. $singleRecord->scheduleName);
                    $scheduleData = $this->scheduleUpdateStatuses(0, 0, 1, 0);
                    PfCampaignSchedule::updateSchedule($singleRecord->id, $scheduleData);
                    $cronScheduleStatuses['fkScheduleId'] = $singleRecord->id;
                    $response = FALSE;
                }
            }
        }
        return $response;
    }
    /**
     * @param $singleRecord
     * @return array
     */
    private function getEnableCampaignData($singleRecord)
    {
        $apiVarData = [];
        $apiVarDataToSend = [];
        $campaigns = $singleRecord->sponsoredProduct;
        if (!$campaigns->isEmpty()){
            foreach ($campaigns as $singleCampaign) {
                $apiVarData['campaignId'] = intval($singleCampaign->campaignId);
                $apiVarData['profileId'] = intval($singleCampaign->profileId);
                $apiVarData['state'] = "enabled";
                $apiVarData['scheduleId'] = $singleRecord->id;
                array_push($apiVarDataToSend, $apiVarData);
            }
        }
        return $apiVarDataToSend;
    }
    /**
     * @param $singleRecord
     * @return array
     */
    private function getPauseCampaignData($singleRecord)
    {
        $apiVarData = [];
        $apiVarDataToSend = [];
        $campaigns = $singleRecord->sponsoredProduct;
        if (!$campaigns->isEmpty()){
            foreach ($campaigns as $singleCampaign) {
                $apiVarData['campaignId'] = intval($singleCampaign->campaignId);
                $apiVarData['profileId'] = intval($singleCampaign->profileId);
                $apiVarData['state'] = "paused";
                $apiVarData['scheduleId'] = $singleRecord->id;
                array_push($apiVarDataToSend, $apiVarData);
            }
        }
        return $apiVarDataToSend;
    }

    /**
     * @param $postData
     * @return bool
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    private function enableScheduleCampaignPfOntime($postData)
    {
        if (!empty($postData)){
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
                    Log::info('Postt Count = '. $postCount);
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
                        Log::info(env('APP_ENV').' Url ->'. $url);
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
                                $storeDataArray['state'] = "enabled";
                                $storeDataArray['updated_at'] = date("Y-m-d H:i:s");
                                array_push($storeDataArrayUpdate, $storeDataArray);
                            }

                        } catch (\Exception $ex) {
                            if ($ex->getCode() == 401) {
                                if (strstr($ex->getMessage(), 'HTTP 401 Unauthorized')) { // if auth token expire
                                    Log::error('Refresh Access token. In file filePath:Commands\Ams\Portfolio\SP\updateCampaignList');
                                    Artisan::call('getaccesstoken:amsauth');
                                    $obAccessToken = new AMSModel();
                                    $dataAccessTaken['accessToken'] = $obAccessToken->getAMSToken();
                                    goto b;
                                } elseif (strstr($ex->getMessage(), 'advertiser found for scope')) {
                                    // store profile list not valid
                                    Log::info("Invalid Profile Id: " . $postData[$i]['profileId']);
                                    $Error = "Invalid Profile Id: " . $postData[$i]['profileId'];
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
                    if (!empty($storeDataArrayUpdate) && !isset($Error)) {
                        PortfolioAllCampaignList::updateCampaign($storeDataArrayUpdate);
                        $returnResponse['status'] = TRUE;
                        return $returnResponse;
                    } else {
                        $returnResponse['status'] = FALSE;
                        $returnResponse['errorMessage'] = $Error;
                        return $returnResponse;
                    }

                } else {
                    Log::info("Client Id not found Ams\Portfolio\SP\updateCampaignList.");
                }
            } else {
                Log::info("AMS access token not found Ams\Portfolio\SP\updateCampaignList.");
            }
        }else{
            Log::info("No Post Data in Campaigns");
            return FALSE;
        }
    }

    /**
     * @param $postData
     * @return bool
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    private function pausedScheduleCampaignPfOntime($postData)
    {
        if (!empty($postData)){
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
                        Log::info(env('APP_ENV').' Url ->'. $url);
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
                                $storeDataArray['state'] = "paused";
                                $storeDataArray['updated_at'] = date("Y-m-d H:i:s");
                                array_push($storeDataArrayUpdate, $storeDataArray);
                            }
                        } catch (\Exception $ex) {
                            if ($ex->getCode() == 401) {
                                if (strstr($ex->getMessage(), 'HTTP 401 Unauthorized')) { // if auth token expire
                                    Log::error('Refresh Access token. In file filePath:Commands\Ams\Portfolio\SP\updateCampaignList');
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
                    if (!empty($storeDataArrayUpdate) && !isset($Error)) {
                        PortfolioAllCampaignList::updateCampaign($storeDataArrayUpdate);
                        $returnResponse['status'] = TRUE;
                        return $returnResponse;
                    } else {
                        $returnResponse['status'] = FALSE;
                        $returnResponse['errorMessage'] = $Error;
                        return $returnResponse;
                    }
                } else {
                    Log::info("Client Id not found Ams\Portfolio\SP\updateCampaignList.");
                }
            } else {
                Log::info("AMS access token not found Ams\Portfolio\SP\updateCampaignList.");
            }
        }else{
            Log::info("No Post Data in Campaigns");
            return FALSE;
        }

    }



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
