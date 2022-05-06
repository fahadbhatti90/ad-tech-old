<?php

namespace App\Console\Commands\ams\Portfolio;

use App\Models\AMSModel;
use App\Models\DayPartingModels\PfCampaignSchedule;
use App\Models\DayPartingModels\Portfolios;
use GuzzleHttp\Client;
use Artisan;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Log;

class updatePortfolioList extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'updatePortfolioDetailData:portfolio';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'This command is used to update portfolio details';

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
        $allSchedulePortfolios = PfCampaignSchedule::select('id', 'scheduleName', 'managerEmail', 'ccEmails', 'portfolioCampaignType', 'mon', 'tue', 'wed', 'thu', 'fri', 'sat', 'sun', 'startTime', 'endTime', 'emailReceiptStart', 'emailReceiptEnd', 'isScheduleExpired', 'isCronRunning', 'created_at')
            ->where('isScheduleExpired', 0)
            ->where('isActive', 1)
            ->whereHas('portfolios')
            ->with('portfolios:id,name,portfolioId,profileId')->get();

        Log::info("filePath:Commands\Ams\Portfolio\updatePortfolioDetailData. Start Cron.");
        Log::info($this->description);

        if (!$allSchedulePortfolios->isEmpty()) {
            foreach ($allSchedulePortfolios as $singleRecord) {
                Log::info('Schedule Name = ' . $singleRecord->scheduleName);
                $currentDate = date('Y-m-d H:i:s');
                $expireSheduleDate = date("Y-m-d H:i:s", strtotime($singleRecord->created_at . '  +7 day'));
                // check if current date is less than expiring of scheduling date
                if ($currentDate < $expireSheduleDate) {
                    $todayName = strtolower(date('l'));
                    $currentTime = date('H:i:00');
                    $enablePortfolioList = $this->getEnablePortfoliosData($singleRecord);
                    $pausePortfolioList = $this->getPausePortfoliosData($singleRecord);

                    switch ($todayName) {
                        case "monday":
                            {
                                Log::info($todayName.' value = ' . $singleRecord->mon);
                                if ($singleRecord->mon === 1) {
                                    Log::info($todayName.' time check schedule Name = ' . $singleRecord->scheduleName);
                                    $this->cronEnablingPausingActiveDays($singleRecord, $currentTime, $pausePortfolioList, $enablePortfolioList);
                                } elseif($singleRecord->mon === 0) {
                                    Log::info($todayName.' is not active it will run whole day = ' . $singleRecord->scheduleName);
                                    $this->cronEnablingPausingInActiveDays($singleRecord,  $enablePortfolioList);
                                }
                                break;
                            }
                        case "tuesday":
                            {
                                Log::info($todayName.' value = ' . $singleRecord->tue);
                                if ($singleRecord->tue === 1) {
                                    Log::info($todayName.' time check schedule Name = ' . $singleRecord->scheduleName);
                                    $this->cronEnablingPausingActiveDays($singleRecord, $currentTime, $pausePortfolioList, $enablePortfolioList);
                                } elseif($singleRecord->tue === 0) {
                                    Log::info($todayName.' is not active it will run whole day = ' . $singleRecord->scheduleName);
                                    $this->cronEnablingPausingInActiveDays($singleRecord,  $enablePortfolioList);
                                }
                                break;
                            }
                        case "wednesday":
                            {
                                Log::info($todayName.' value = ' . $singleRecord->wed);
                                if ($singleRecord->wed === 1) {
                                    Log::info($todayName.' time check schedule Name = ' . $singleRecord->scheduleName);
                                    $this->cronEnablingPausingActiveDays($singleRecord, $currentTime, $pausePortfolioList, $enablePortfolioList);
                                } elseif($singleRecord->wed === 0) {
                                    Log::info($todayName.' is not active it will run whole day = ' . $singleRecord->scheduleName);
                                    $this->cronEnablingPausingInActiveDays($singleRecord,  $enablePortfolioList);
                                }
                                break;
                            }
                        case "thursday":
                            {
                                Log::info($todayName.' value = ' . $singleRecord->thu);
                                if ($singleRecord->thu == 1) {
                                    Log::info($todayName.' time check schedule Name = ' . $singleRecord->scheduleName);
                                    $this->cronEnablingPausingActiveDays($singleRecord, $currentTime, $pausePortfolioList, $enablePortfolioList);
                                }else {
                                    Log::info($todayName.' is not active it will run whole day = ' . $singleRecord->scheduleName);
                                    $this->cronEnablingPausingInActiveDays($singleRecord,  $enablePortfolioList);
                                }

                                break;
                            }
                        case "friday":
                            {
                                Log::info($todayName.' value = ' . $singleRecord->fri);
                                if ($singleRecord->fri === 1) {
                                    Log::info($todayName.' time check schedule Name = ' . $singleRecord->scheduleName);
                                    $this->cronEnablingPausingActiveDays($singleRecord, $currentTime, $pausePortfolioList, $enablePortfolioList);
                                } elseif($singleRecord->fri === 0) {
                                    Log::info($todayName.' is not active it will run whole day = ' . $singleRecord->scheduleName);
                                    $this->cronEnablingPausingInActiveDays($singleRecord,  $enablePortfolioList);
                                }
                                break;
                            }
                        case "saturday":
                            {
                                Log::info($todayName.' value = ' . $singleRecord->sat);
                                if ($singleRecord->sat === 1) {
                                    Log::info($todayName.' time check schedule Name = ' . $singleRecord->scheduleName);
                                    $this->cronEnablingPausingActiveDays($singleRecord, $currentTime, $pausePortfolioList, $enablePortfolioList);
                                } elseif($singleRecord->sat === 0) {
                                    Log::info($todayName.' is not active it will run whole day = ' . $singleRecord->scheduleName);
                                    $this->cronEnablingPausingInActiveDays($singleRecord,  $enablePortfolioList);
                                }

                                break;
                            }
                        case "sunday":
                            {
                                Log::info($todayName.' value = ' . $singleRecord->sun);
                                if ($singleRecord->sun === 1) {
                                    Log::info($todayName.' time check schedule Name = ' . $singleRecord->scheduleName);
                                    $this->cronEnablingPausingActiveDays($singleRecord, $currentTime, $pausePortfolioList, $enablePortfolioList);
                                } elseif($singleRecord->sun === 0) {
                                    Log::info($todayName.' is not active it will run whole day = ' . $singleRecord->scheduleName);
                                    $this->cronEnablingPausingInActiveDays($singleRecord,  $enablePortfolioList);
                                }

                                break;
                            }
                    }

                } else {
                    $expireData['isScheduledExpired'] = 1;
                    PfCampaignSchedule::updateSchedule($singleRecord->id, $expireData);
                    Log::info('Schedule week completed. Name = ' . $singleRecord->scheduleName);
                }
            } // End foreach Loop all schedule List
        } else {
            Log::info("No Portfolios To Enable or Pause Now To Run");
        }
        Log::info("filePath:Commands\Ams\Portfolio\updatePortfolioDetailData. End Cron.");
    }

    /**
     * @param $singleRecord
     * @param $currentTime
     * @param $pausePortfolioList
     * @param $enablePortfolioList
     * @return bool
     * @throws \ReflectionException
     */
    private function cronEnablingPausingActiveDays($singleRecord, $currentTime, $pausePortfolioList, $enablePortfolioList )
    {
        $response = TRUE;
        Log::info('cronEnablingPausingActiveDays = schedule name = '. $singleRecord->scheduleName.' is Running = ' . $singleRecord->isCronRunning);
        if ($singleRecord->isCronRunning === 0) {
            Log::info('cronEnablingPausingActiveDays = cron is running'.$singleRecord->isCronRunning);
            Log::info('cronEnablingPausingActiveDays = cron is not running check start time');
            Log::info('cronEnablingPausingActiveDays = current Time = '. $currentTime . ' Cron Start Time ='. $singleRecord->startTime);
            if ($currentTime === $singleRecord->startTime) {
                Log::info('cronEnablingPausingActiveDays = start time matches schedule Name = '. $singleRecord->scheduleName);
                $return = $this->pausingEnablingPortfolios($enablePortfolioList);

                if ($return == TRUE) {
                    Log::info('cronEnablingPausingActiveDays = campaigns enabled successfully against schedule Name  = '. $singleRecord->scheduleName);
                    $scheduleData['isCronRunning'] = 1;
                    $scheduleData['isCronSuccess'] = 1;
                    $scheduleData['isCronError'] = 0;
                    PfCampaignSchedule::updateSchedule($singleRecord->id, $scheduleData);
                    if ($singleRecord->emailReceiptStart == 1) {
                        Log::info('cronEnablingPausingActiveDays = Email enabled on start time against schedule Name  = '. $singleRecord->scheduleName);
                        _sendEmailForEnabledCampaign($singleRecord->managerEmail, $singleRecord->ccEmails, $singleRecord->scheduleName);
                    }
                    $response = TRUE;
                } else {
                    Log::info('cronEnablingPausingActiveDays = campaigns Error against schedule Name  = '. $singleRecord->scheduleName);
                    $scheduleData['isCronRunning'] = 0;
                    $scheduleData['isCronSuccess'] = 0;
                    $scheduleData['isCronError'] = 1;
                    PfCampaignSchedule::updateSchedule($singleRecord->id, $scheduleData);
                    _sendEmailForErrorCampaign($singleRecord->managerEmail, $singleRecord->ccEmails, $singleRecord->scheduleName);
                    $response = FALSE;
                }
            }
        } elseif ($singleRecord->isCronRunning === 1) {
            Log::info('cronEnablingPausingActiveDays = cron is running'.$singleRecord->isCronRunning. ' check end time');
            if ($currentTime == $singleRecord->endTime) {
                Log::info('cronEnablingPausingActiveDays = end time matches schedule Name = '. $singleRecord->scheduleName);
                $return = $this->pausingEnablingPortfolios($pausePortfolioList);
                if ($return == TRUE) {
                    Log::info('cronEnablingPausingActiveDays = campaigns paused successfully against schedule Name  = '. $singleRecord->scheduleName);
                    $scheduleData['isCronRunning'] = 0;
                    $scheduleData['isCronSuccess'] = 1;
                    $scheduleData['isCronError'] = 0;
                    PfCampaignSchedule::updateSchedule($singleRecord->id, $scheduleData);
//                    if ($singleRecord->emailReceiptEnd == 1) {
//                        Log::info('cronEnablingPausingActiveDays = Email paused on end time against schedule Name  = '. $singleRecord->scheduleName);
//                        _sendEmailForPausedCampaign($singleRecord->managerEmail, $singleRecord->ccEmails, $singleRecord->scheduleName);
//                    }
                    $response = TRUE;
                } else {
                    Log::info('cronEnablingPausingActiveDays = campaigns Error against schedule Name  = '. $singleRecord->scheduleName);
                    $scheduleData['isCronRunning'] = 0;
                    $scheduleData['isCronSuccess'] = 0;
                    $scheduleData['isCronError'] = 1;
                    PfCampaignSchedule::updateSchedule($singleRecord->id, $scheduleData);
                    Log::info('cronEnablingPausingActiveDays = Error Email sent against schedule Name  = '. $singleRecord->scheduleName);
                    _sendEmailForErrorCampaign($singleRecord->managerEmail, $singleRecord->ccEmails, $singleRecord->scheduleName);
                    $response = FALSE;
                }
            }
        } elseif ($singleRecord->isCronRunning === 2) {
            Log::info('cronEnablingPausingActiveDays = if cron were running = '.$singleRecord->isCronRunning. ' whole day but today is active so all campaigns will pause now against schedule Name = '.$singleRecord->scheduleName);
            $return = $this->pausingEnablingPortfolios($pausePortfolioList);
            if ($return == TRUE) {
                Log::info('cronEnablingPausingActiveDays = campaigns paused successfully against schedule Name  = '. $singleRecord->scheduleName);
                $scheduleData['isCronRunning'] = 0;
                $scheduleData['isCronSuccess'] = 1;
                $scheduleData['isCronError'] = 0;
                PfCampaignSchedule::updateSchedule($singleRecord->id, $scheduleData);
//                if ($singleRecord->emailReceiptEnd == 1) {
//                    Log::info('cronEnablingPausingActiveDays = Email paused which was enabled against schedule Name  = '. $singleRecord->scheduleName);
//                    _sendEmailForPausedCampaign($singleRecord->managerEmail, $singleRecord->ccEmails, $singleRecord->scheduleName);
//                }
                $response = TRUE;
            } else {
                Log::info('cronEnablingPausingActiveDays = campaigns Error against schedule Name  = '. $singleRecord->scheduleName);
                $scheduleData['isCronRunning'] = 0;
                $scheduleData['isCronSuccess'] = 0;
                $scheduleData['isCronError'] = 1;
                PfCampaignSchedule::updateSchedule($singleRecord->id, $scheduleData);
                Log::info('cronEnablingPausingActiveDays = Error Email sent against schedule Name  = '. $singleRecord->scheduleName);
                _sendEmailForErrorCampaign($singleRecord->managerEmail, $singleRecord->ccEmails, $singleRecord->scheduleName);
                $response = FALSE;
            }
        }

        return $response;
    }

    /**
     * @param $singleRecord
     * @param $enablePortfolioList
     * @return bool
     * @throws \ReflectionException
     */
    private function cronEnablingPausingInActiveDays($singleRecord,  $enablePortfolioList)
    {
        $response = TRUE;
        if ($singleRecord->isCronRunning == 0) {
            Log::info('cronEnablingPausingInActiveDays = if cron were running = '.$singleRecord->isCronRunning);
            $return = $this->pausingEnablingPortfolios($enablePortfolioList);
            if ($return == TRUE) {
                Log::info('cronEnablingPausingInActiveDays = campaigns enabled successfully against schedule Name  = '. $singleRecord->scheduleName);
                $scheduleData['isCronRunning'] = 2;
                $scheduleData['isCronSuccess'] = 1;
                $scheduleData['isCronError'] = 0;
                PfCampaignSchedule::updateSchedule($singleRecord->id, $scheduleData);
//                if ($singleRecord->emailReceiptStart == 1) {
//
//                    Log::info('cronEnablingPausingInActiveDays = Email enabled on start time against schedule Name  = '. $singleRecord->scheduleName);
//                    _sendEmailForEnabledCampaign($singleRecord->managerEmail, $singleRecord->ccEmails, $singleRecord->scheduleName);
//                }
                $response = TRUE;
            } else {
                Log::info('cronEnablingPausingInActiveDays = campaigns Error against schedule Name  = '. $singleRecord->scheduleName);
                $scheduleData['isCronRunning'] = 0;
                $scheduleData['isCronSuccess'] = 0;
                $scheduleData['isCronError'] = 1;
                PfCampaignSchedule::updateSchedule($singleRecord->id, $scheduleData);
                Log::info('cronEnablingPausingInActiveDays = Error Email sent against schedule Name  = '. $singleRecord->scheduleName);
                _sendEmailForErrorCampaign($singleRecord->managerEmail, $singleRecord->ccEmails, $singleRecord->scheduleName);
                $response = FALSE;
            }
        }
        return $response;
    }

    /**
     * @param $postData
     * @return bool
     */
    private function pausingEnablingPortfolios($postData)
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
                            'portfolioId' => $postData[$i]['portfolioId'],
                            'state' => $postData[$i]['state']
                        ];
                        // Create a client with a base URI
                        $apiUrl = getApiUrlForDiffEnv(env('APP_ENV'));
                        $url = $apiUrl . '/' . Config::get('constants.apiVersion') . '/' . Config::get('constants.amsPortfolioUrl');

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
                            dd($responseBody);
                            if (!empty($responseBody) && !is_null($responseBody)) {
                                $storeDataArray = [];
                                $storeDataArray['portfolioId'] = $responseBody[0]->portfolioId;
                                $storeDataArray['state'] = $postData[$i]['state'];
                                $storeDataArray['updated_at'] = date("Y-m-d H:i:s");
                                array_push($storeDataArrayUpdate, $storeDataArray);
                            }
                        } catch (\Exception $ex) {
                            if ($ex->getCode() == 401) {
                                Log::error('Refresh Access token. In file filePath:Commands\Ams\Portfolio\SP\updateCampaignList');
                                Artisan::call('getaccesstoken:amsauth');
                                $obAccessToken = new AMSModel();
                                $dataAccessTaken['accessToken'] = $obAccessToken->getAMSToken();
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
                    } // End For Loop
                    // Update Campaign Records
                    dd($storeDataArrayUpdate);
                    if (!empty($storeDataArrayUpdate)) {
                        Portfolios::updatePortfolio($storeDataArrayUpdate);
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
    /**
     * @param $singleRecord
     * @return array
     */
    private function getPausePortfoliosData($singleRecord)
    {
        $apiVarData = [];
        $apiVarDataToSend = [];
        $portfolios = $singleRecord->portfolios;
        if (!$portfolios->isEmpty()) {
            foreach ($portfolios as $singlePortfolio) {
                $apiVarData['portfolioId'] = intval($singlePortfolio->portfolioId);
                $apiVarData['profileId'] = intval($singlePortfolio->profileId);
                $apiVarData['state'] = "paused";
                $apiVarData['id'] = intval($singlePortfolio->id);
                array_push($apiVarDataToSend, $apiVarData);
            }
        }
        return $apiVarDataToSend;
    }

    /**
     * @param $singleRecord
     * @return array
     */
    private function getEnablePortfoliosData($singleRecord)
    {
        $apiVarData = [];
        $apiVarDataToSend = [];
        $portfolios = $singleRecord->portfolios;
        if (!$portfolios->isEmpty()) {
            foreach ($portfolios as $singlePortfolio) {
                $apiVarData['portfolioId'] = intval($singlePortfolio->portfolioId);
                $apiVarData['profileId'] = intval($singlePortfolio->profileId);
                $apiVarData['state'] = "enabled";
                $apiVarData['id'] = intval($singlePortfolio->id);
                array_push($apiVarDataToSend, $apiVarData);
            }
        }
        return $apiVarDataToSend;
    }
}
