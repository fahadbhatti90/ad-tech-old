<?php

namespace App\Console\Commands\BiddingRule;

use App\Mail\BuyBoxEmailAlertMarkdown;
use App\Models\AccountModels\AccountModel;
use App\Models\BiddingRule;
use App\Models\ClientModels\ClientModel;
use App\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Log;
use Rap2hpoutre\FastExcel\FastExcel;
use Rap2hpoutre\FastExcel\SheetCollection;

class cron extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'biddingRule:cronjob';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'This command is used to check bidding rule.';

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
     * @throws \Box\Spout\Common\Exception\IOException
     * @throws \Box\Spout\Common\Exception\InvalidArgumentException
     * @throws \Box\Spout\Common\Exception\UnsupportedTypeException
     * @throws \Box\Spout\Writer\Exception\WriterNotOpenedException
     * @throws \ReflectionException
     */
    public function handle()
    {
        Log::info("filePath:App\Console\Commands\BiddingRule\cron. Start Cron.");
        Log::info($this->description);
        $CronDataArray = array(); // define array for Cron data Collection
        $responseData = BiddingRule::getBiddingRulesList();
        if ($responseData != FALSE) {
            foreach ($responseData as $singleArray) {
                $array = array();
                $array['fkBiddingRuleId'] = $singleArray->id;
                $array['sponsoredType'] = $singleArray->sponsoredType;
                $array['lookBackPeriodDays'] = $singleArray->lookBackPeriodDays;
                $array['frequency'] = $singleArray->frequency;
                $array['runStatus'] = 0;
                $array['isActive'] = 1;
                $array['currentRunTime'] = '0000-00-00 00:00:00';
                $array['lastRunTime'] = '0000-00-00 00:00:00';
                $array['nextRunTime'] = '0000-00-00 00:00:00';
                $array['createdAt'] = date('Y-m-d H:i:s');
                $array['updatedAt'] = date('Y-m-d H:i:s');
                array_push($CronDataArray, $array);
            }
            BiddingRule::storeBiddingRuleCron($CronDataArray);
        }//endif
        $responseDataCron = BiddingRule::getBiddingRuleCron();
        if ($responseDataCron != FALSE) {
            foreach ($responseDataCron as $singleData) {
                $id = $singleData->id;
                $lookBackPeriodDays = $singleData->lookBackPeriodDays;
                $sponsoredType = $singleData->sponsoredType;
                $fkBiddingRuleId = $singleData->fkBiddingRuleId;
                $frequency = $singleData->frequency;
                $currentTimeNow = date('Y-m-d H');
                $currentDayNow = date('Y-m-d');
                $hourlyCheckCurrentTimeNow = date('H');
                $CronTime = $singleData->currentRunTime;
                $hourlyCheckCronTime = '';
                if ($CronTime == '0000-00-00 00:00:00') {
                    $CronTime = date('Y-m-d H');
                    $hourlyCheckCronTime = date('H');
                } else {
                    $CronTime = date('Y-m-d H', strtotime($singleData->currentRunTime));
                    $hourlyCheckCronTime = date('H', strtotime($singleData->currentRunTime));
                }
                switch ($frequency) {
                    case "once_per_day":
                        $CronLastRun = $singleData->lastRunTime;
                        $CronLastRunHourMinuteSec = '';
                        // check last cron time
                        if ($CronLastRun == '0000-00-00 00:00:00') {
                            $CronLastRunHourMinuteSec = date('Y-m-d H:i:s');
                            $CronLastRun = date('Y-m-d H', strtotime($CronLastRunHourMinuteSec));
                        } else {
                            $CronLastRunHourMinuteSec = date('Y-m-d H:i:s', strtotime($CronLastRun));
                            $CronLastRun = date('Y-m-d H', strtotime($CronLastRunHourMinuteSec));
                        }
                        // next cron run time
                        $nextRunTime = $singleData->nextRunTime;
                        $nextRunTimeHourMinuteSec = '';
                        $TodayNextRun = '';
                        if ($nextRunTime == '0000-00-00 00:00:00') {
                            $nextRunTimeHourMinuteSec = date('Y-m-d H:i:s', strtotime('+1 day', time()));
                            $TodayNextRun = date('Y-m-d', time());
                            $nextRunTime = date('Y-m-d H', strtotime($nextRunTimeHourMinuteSec));
                        } else {
                            $nextRunTimeHourMinuteSec = date('Y-m-d H:i:s', strtotime($nextRunTime));
                            $nextRunTime = date('Y-m-d H', strtotime($nextRunTimeHourMinuteSec));
                            $TodayNextRun = date('Y-m-d', strtotime($nextRunTimeHourMinuteSec));
                            // if next time is greater than last time
                            if ($CronLastRun > $nextRunTime) {
                                $nextRunTimeHourMinuteSec = date('Y-m-d H:i:s', strtotime('+1 day', time()));
                                $nextRunTime = date('Y-m-d H', strtotime($nextRunTimeHourMinuteSec));
                                $TodayNextRun = date('Y-m-d', strtotime($nextRunTimeHourMinuteSec));
                            }
                        }
                        if ($CronLastRun < $nextRunTime && $singleData->runStatus == 0 && $hourlyCheckCurrentTimeNow == $hourlyCheckCronTime && $TodayNextRun == $currentDayNow) {
                            $this->RunCronFrequencyVise($id, $lookBackPeriodDays, $sponsoredType, $fkBiddingRuleId, 1);
                        } elseif ($singleData->runStatus == 1 && $CronTime < $currentTimeNow) { // change cronRun status again 0
                            // tracker code
                            Log::info('start update bidding rule query for update CronRun status to 0');
                            $updateArray = array(
                                'updatedAt' => date('Y-m-d H:i:s'),
                                'runStatus' => '0',
                                'checkRule' => '0',
                                'ruleResult' => '0',
                                'emailSent' => '0',
                            );
                            BiddingRule::updateCronBiddingRuleStatus($id, $updateArray);
                            Log::info('end update bidding rule query for update CronRun status to 0');
                        }
                        break;
                    case "every_day":
                        $CronLastRun = $singleData->lastRunTime;
                        $CronLastRunHourMinuteSec = '';
                        // check last cron time
                        if ($CronLastRun == '0000-00-00 00:00:00') {
                            $CronLastRunHourMinuteSec = date('Y-m-d H:i:s');
                            $CronLastRun = date('Y-m-d H', strtotime($CronLastRunHourMinuteSec));
                        } else {
                            $CronLastRunHourMinuteSec = date('Y-m-d H:i:s', strtotime($CronLastRun));
                            $CronLastRun = date('Y-m-d H', strtotime($CronLastRunHourMinuteSec));
                        }
                        // next cron run time
                        $nextRunTime = $singleData->nextRunTime;
                        $nextRunTimeHourMinuteSec = '';
                        $TodayNextRun = '';
                        if ($nextRunTime == '0000-00-00 00:00:00') {
                            $nextRunTimeHourMinuteSec = date('Y-m-d H:i:s', strtotime('+2 day', time()));
                            $TodayNextRun = date('Y-m-d', time());
                            $nextRunTime = date('Y-m-d H', strtotime($nextRunTimeHourMinuteSec));
                        } else {
                            $nextRunTimeHourMinuteSec = date('Y-m-d H:i:s', strtotime($nextRunTime));
                            $nextRunTime = date('Y-m-d H', strtotime($nextRunTimeHourMinuteSec));
                            $TodayNextRun = date('Y-m-d', strtotime($nextRunTimeHourMinuteSec));
                            // if next time is greater than last time
                            if ($CronLastRun > $nextRunTime) {
                                $nextRunTimeHourMinuteSec = date('Y-m-d H:i:s', strtotime('+2 day', time()));
                                $nextRunTime = date('Y-m-d H', strtotime($nextRunTimeHourMinuteSec));
                                $TodayNextRun = date('Y-m-d', strtotime($nextRunTimeHourMinuteSec));
                            }
                        }

                        if ($CronLastRun < $nextRunTime && $singleData->runStatus == 0 && $hourlyCheckCurrentTimeNow == $hourlyCheckCronTime && $TodayNextRun == $currentDayNow) {
                            $this->RunCronFrequencyVise($id, $lookBackPeriodDays, $sponsoredType, $fkBiddingRuleId, 2);
                        } elseif ($singleData->runStatus == 1 && $CronTime < $currentTimeNow) { // change cronRun status again 0
                            // tracker code
                            Log::info('start update bidding rule query for update CronRun status to 0');
                            $updateArray = array(
                                'updatedAt' => date('Y-m-d H:i:s'),
                                'runStatus' => '0',
                                'checkRule' => '0',
                                'ruleResult' => '0',
                                'emailSent' => '0',
                            );
                            BiddingRule::updateCronBiddingRuleStatus($id, $updateArray);
                            Log::info('end update bidding rule query for update CronRun status to 0');
                        }
                        break;
                    case "w":
                        $CronLastRun = $singleData->lastRunTime;
                        $CronLastRunHourMinuteSec = '';
                        // check last cron time
                        if ($CronLastRun == '0000-00-00 00:00:00') {
                            $CronLastRunHourMinuteSec = date('Y-m-d H:i:s');
                            $CronLastRun = date('Y-m-d H', strtotime($CronLastRunHourMinuteSec));
                        } else {
                            $CronLastRunHourMinuteSec = date('Y-m-d H:i:s', strtotime($CronLastRun));
                            $CronLastRun = date('Y-m-d H', strtotime($CronLastRunHourMinuteSec));
                        }
                        // next cron run time
                        $nextRunTime = $singleData->nextRunTime;
                        $nextRunTimeHourMinuteSec = '';
                        $TodayNextRun = '';
                        if ($nextRunTime == '0000-00-00 00:00:00') {
                            $nextRunTimeHourMinuteSec = date('Y-m-d H:i:s', strtotime('+7 day', time()));
                            $TodayNextRun = date('Y-m-d', time());
                            $nextRunTime = date('Y-m-d H', strtotime($nextRunTimeHourMinuteSec));
                        } else {
                            $nextRunTimeHourMinuteSec = date('Y-m-d H:i:s', strtotime($nextRunTime));
                            $nextRunTime = date('Y-m-d H', strtotime($nextRunTimeHourMinuteSec));
                            $TodayNextRun = date('Y-m-d', strtotime($nextRunTimeHourMinuteSec));
                            // if next time is greater than last time
                            if ($CronLastRun > $nextRunTime) {
                                $nextRunTimeHourMinuteSec = date('Y-m-d H:i:s', strtotime('+7 day', time()));
                                $nextRunTime = date('Y-m-d H', strtotime($nextRunTimeHourMinuteSec));
                                $TodayNextRun = date('Y-m-d', strtotime($nextRunTimeHourMinuteSec));
                            }
                        }
                        if ($CronLastRun < $nextRunTime && $singleData->runStatus == 0 && $hourlyCheckCurrentTimeNow == $hourlyCheckCronTime && $TodayNextRun == $currentDayNow) {
                            $this->RunCronFrequencyVise($id, $lookBackPeriodDays, $sponsoredType, $fkBiddingRuleId, 7);
                        } elseif ($singleData->runStatus == 1 && $CronTime < $currentTimeNow) { // change cronRun status again 0
                            // tracker code
                            Log::info('start update bidding rule query for update CronRun status to 0');
                            $updateArray = array(
                                'updatedAt' => date('Y-m-d H:i:s'),
                                'runStatus' => '0',
                                'checkRule' => '0',
                                'ruleResult' => '0',
                                'emailSent' => '0',
                            );
                            BiddingRule::updateCronBiddingRuleStatus($id, $updateArray);
                            Log::info('end update bidding rule query for update CronRun status to 0');
                        }
                        break;
                    case "m":
                        $CronLastRun = $singleData->lastRunTime;
                        $CronLastRunHourMinuteSec = '';
                        // check last cron time
                        if ($CronLastRun == '0000-00-00 00:00:00') {
                            $CronLastRunHourMinuteSec = date('Y-m-d H:i:s');
                            $CronLastRun = date('Y-m-d H', strtotime($CronLastRunHourMinuteSec));
                        } else {
                            $CronLastRunHourMinuteSec = date('Y-m-d H:i:s', strtotime($CronLastRun));
                            $CronLastRun = date('Y-m-d H', strtotime($CronLastRunHourMinuteSec));
                        }
                        // next cron run time
                        $nextRunTime = $singleData->nextRunTime;
                        $nextRunTimeHourMinuteSec = '';
                        $TodayNextRun = '';
                        if ($nextRunTime == '0000-00-00 00:00:00') {
                            $nextRunTimeHourMinuteSec = date('Y-m-d H:i:s', strtotime('+30 day', time()));
                            $TodayNextRun = date('Y-m-d', time());
                            $nextRunTime = date('Y-m-d H', strtotime($nextRunTimeHourMinuteSec));
                        } else {
                            $nextRunTimeHourMinuteSec = date('Y-m-d H:i:s', strtotime($nextRunTime));
                            $nextRunTime = date('Y-m-d H', strtotime($nextRunTimeHourMinuteSec));
                            $TodayNextRun = date('Y-m-d', strtotime($nextRunTimeHourMinuteSec));
                            // if next time is greater than last time
                            if ($CronLastRun > $nextRunTime) {
                                $nextRunTimeHourMinuteSec = date('Y-m-d H:i:s', strtotime('+30 day', time()));
                                $nextRunTime = date('Y-m-d H', strtotime($nextRunTimeHourMinuteSec));
                                $TodayNextRun = date('Y-m-d', strtotime($nextRunTimeHourMinuteSec));
                            }
                        }
                        if ($CronLastRun < $nextRunTime && $singleData->runStatus == 0 && $hourlyCheckCurrentTimeNow == $hourlyCheckCronTime && $TodayNextRun == $currentDayNow) {
                            $this->RunCronFrequencyVise($id, $lookBackPeriodDays, $sponsoredType, $fkBiddingRuleId, 30);
                        } elseif ($singleData->runStatus == 1 && $CronTime < $currentTimeNow) { // change cronRun status again 0
                            // tracker code
                            Log::info('start update bidding rule query for update CronRun status to 0');
                            $updateArray = array(
                                'updatedAt' => date('Y-m-d H:i:s'),
                                'runStatus' => '0',
                                'checkRule' => '0',
                                'ruleResult' => '0',
                                'emailSent' => '0',
                            );
                            BiddingRule::updateCronBiddingRuleStatus($id, $updateArray);
                            Log::info('end update bidding rule query for update CronRun status to 0');
                        }
                        break;
                    default:
                        // not found
                }// switch
            }// end foreach
        }// endif

        Log::info("filePath:App\Console\Commands\BiddingRule\cron. End Cron.");
    }

    /**
     * This function is used to frequency Changes
     *
     * @param $id
     * @param $lookBackPeriodDays
     * @param $sponsoredType
     * @param $fkBiddingRuleId
     * @param $nextRunDay
     * @throws \Box\Spout\Common\Exception\IOException
     * @throws \Box\Spout\Common\Exception\InvalidArgumentException
     * @throws \Box\Spout\Common\Exception\UnsupportedTypeException
     * @throws \Box\Spout\Writer\Exception\WriterNotOpenedException
     * @throws \ReflectionException
     */
    private function RunCronFrequencyVise($id, $lookBackPeriodDays, $sponsoredType, $fkBiddingRuleId, $nextRunDay)
    {
        $updateArray = array(
            'currentRunTime' => date('Y-m-d H:i:s'),
            'lastRunTime' => date('Y-m-d H:i:s'),
            'nextRunTime' => date('Y-m-d H:i:s', strtotime('+' . $nextRunDay . ' day', time())),
            'updatedAt' => date('Y-m-d H:i:s'),
            'runStatus' => '1',
        );
        BiddingRule::updateCronBiddingRuleStatus($id, $updateArray);
        Artisan::call('keywordData:bidding_rule');
        Artisan::call('keywordlist:amsKeywordlist'.' '.$fkBiddingRuleId);
        $excelFileArray = array(); //  create array for cvs file data managing
        $ruleResultStatus = FALSE;
        $slug = '';
        if ($sponsoredType == 'sponsoredBrands') {
            $slug = 'SB';
        } elseif ($sponsoredType == 'sponsoredProducts') {
            $slug = 'SP';
        } else if ($sponsoredType == 'sponsoredDisplay') {
            $slug = 'SD';
        }
        $responseData = BiddingRule::getKeywordData($fkBiddingRuleId, $slug);
        if (!empty($responseData)) {
            foreach ($responseData as $singleData) {
                $profileId = $singleData->profileId;
                $fkConfigId = $singleData->fkConfigId;
                $client_id = $singleData->client_id;
                $campaignId = $singleData->campaignId;
                $adGroupId = $singleData->adGroupId;
                $keywordId = $singleData->keywordId;
                $state = $singleData->state;
                $reportType = $singleData->reportType;
                $bid = $singleData->bid;
                $Rule = BiddingRule::getSpecificBiddingRule($singleData->fkBiddingRuleId);
                if ($fkBiddingRuleId == $singleData->fkBiddingRuleId) { // bidding rule id will same
                    $parameter = array(
                        $campaignId,
                        $keywordId,
                        $reportType,
                        (int)$lookBackPeriodDays);
                    $DB1 = 'mysql'; // layer 0 database
                    $KeywordReportData = \DB::connection($DB1)->select("CALL spCalculateKeywordBiddingRule(?,?,?,?)", $parameter);
                    if (!empty($KeywordReportData)) {
                        $updateArray = array(
                            'checkRule' => '1',
                        );
                        BiddingRule::updateCronBiddingRuleStatus($id, $updateArray);
                        $arrayReportData = (array)$KeywordReportData[0];
                        if (!empty($Rule)) {
                            $conditionArray = array();
                            $metricList = explode(',', $Rule->metric);
                            $conditionList = explode(',', $Rule->condition);
                            $integerValuesList = explode(',', $Rule->integerValues);
                            for ($i = 0; $i < count($metricList); $i++) {
                                $condition = '<'; // default less
                                $and = '';
                                if ($conditionList[$i] == 'greater') {
                                    $condition = '>';
                                }
                                if ($Rule->andOr != 'NA') {
                                    if ($Rule->andOr == 'and') {
                                        $and = '&&';
                                    } else if ($Rule->andOr == 'or') {
                                        $and = '||';
                                    }
                                }
                                // Database value condition User input value, e.g (db >/< userValue)
                                $conditionText = '(' . $arrayReportData[$metricList[$i]] . ' ' . $condition . ' ' . $integerValuesList[$i] . ')' . (($i == 1) ? '' : $and);
                                array_push($conditionArray, $conditionText);
                            }// end for loop
                            $Result = eval('return (' . implode('', $conditionArray) . ');');
                            $ruleCheckStatus = 'FALSE'; // eval return true
                            $increaseBidValue = 0.0;
                            if ($Result) {
                                $ruleResultStatus = TRUE;
                                $updateArray = array(
                                    'ruleResult' => '1',
                                );
                                $ruleCheckStatus = 'TRUE';
                                //BiddingRule::updateCronBiddingRuleStatus($id, $updateArray);
                                //$this->emailSent($Rule, $arrayReportData);
                                $bidBy = $Rule->bidBy;
                                if ($Rule->thenClause == 'raise') {
                                    $increaseBidValue = round(abs((($bidBy / 100) * $bid) + $bid), 2);
                                } else {
                                    if ($bidBy <= 100 && $bidBy >= 0) {
                                        $increaseBidValue = round(abs((($bidBy / 100) * $bid) - $bid), 2);
                                    } else {
                                        $increaseBidValue = round(abs(((100 / 100) * $bid) - $bid), 2);
                                    }
                                }
                                $data['data'] = array(
                                    'profileId' => $profileId,
                                    'fkConfigId' => $fkConfigId,
                                    'clientId' => $client_id,
                                    'campaignId' =>  $campaignId,
                                    'adGroupId' =>  $adGroupId,
                                    'keywordId' =>  $keywordId,
                                    'state' =>  $state,
                                    'reportType' =>  $reportType,
                                    'oldbid' => $bid,
                                    'newbid' => $increaseBidValue
                                );
                                Artisan::call('updateKeywordbid:updatebid', $data);
                                print($bidBy);
                            } else {
                                //$updateArray = array(
                                //  'ruleResult' => '0',
                                //);
                                //BiddingRule::updateCronBiddingRuleStatus($id, $updateArray);
                                echo 'failed';
                            }
                            $dataArray = array(
                                'id' => $id,
                                'updateArray' => $updateArray,
                                'rule' => $Rule,
                                'ruleCheckStatus' => $ruleCheckStatus,
                                'arrayReportData' => $arrayReportData,
                                'keywordBid' => $bid,
                                'increaseKeywordBid' => $increaseBidValue
                            );
                            array_push($excelFileArray, $dataArray);
                        }// end if
                    }// end if
                }// end if
            }// end foreach
            if ($ruleResultStatus) {// check if rule status true then sent email
                $this->emailSent($excelFileArray);
            }
        } else {
            Log::info('keyword data not found.');
        }
    }

    /**
     * This function is used to Sent Email to User
     *
     * @param $excelFileArray
     * @return int
     * @throws \Box\Spout\Common\Exception\IOException
     * @throws \Box\Spout\Common\Exception\InvalidArgumentException
     * @throws \Box\Spout\Common\Exception\UnsupportedTypeException
     * @throws \Box\Spout\Writer\Exception\WriterNotOpenedException
     * @throws \ReflectionException
     */

    private function emailSent($excelFileArray)
    {
        $id = $excelFileArray[0]['id']; // tbl_ams_bidding_rule_cron 'id'
        $emailStatus = BiddingRule::getCronBiddingRuleEmailStatus($id);
        if ($emailStatus->emailSent == 0 && $emailStatus->ruleResult == 0 && !empty($emailStatus)) {
            $ruleCheckData = array();
            $ruleStatementData = array();
            $updateArray = array(
                'ruleResult' => '1',
            );
            BiddingRule::updateCronBiddingRuleStatus($id, $updateArray);
            foreach ($excelFileArray as $singleArray) {
                $conditionArray = array();
                $metricList = explode(',', $singleArray['rule']->metric);
                $conditionList = explode(',', $singleArray['rule']->condition);
                $integerValuesList = explode(',', $singleArray['rule']->integerValues);
                $conditionTextCsvData = '';
                for ($i = 0; $i < count($metricList); $i++) {
                    $metricValue = $metricList[$i];
                    if ($metricValue == 'cost') {
                        $metricValue = 'spend';
                    }
                    if ($metricValue == 'revenue') {
                        $metricValue = 'sales';
                    }
                    $and = '';
                    if ($singleArray['rule']->andOr != 'NA') {
                        if ($singleArray['rule']->andOr == 'and') {
                            $and = 'AND';
                        } else if ($singleArray['rule']->andOr == 'or') {
                            $and = 'OR';
                        }
                    }
                    $conditionTextCsvData .= ' if ' . $metricValue . ' ' . $conditionList[$i] . ' ' . $integerValuesList[$i] . ' ' . (($i == 1) ? '' : $and);
                    array_push($conditionArray, $conditionTextCsvData);
                }// end for loop
                $conditionTextCsvData .= 'Then ' . $singleArray['rule']->thenClause . ' bid by ' . $singleArray['rule']->bidBy . ' %';
                array_push($ruleStatementData, $conditionTextCsvData);
                $ruleCheckDataArray = array(
                    "Campaign Id" => isset($singleArray['arrayReportData']['campaignId']) ? $singleArray['arrayReportData']['campaignId'] : '0',
                    "Campaign Name" => isset($singleArray['arrayReportData']['campaignName']) ? $singleArray['arrayReportData']['campaignName'] : 'NA',
                    "AdGroup Id" => isset($singleArray['arrayReportData']['adGroupId']) ? $singleArray['arrayReportData']['adGroupId'] : '0',
                    "AdGroup Name" => isset($singleArray['arrayReportData']['adGroupName']) ? $singleArray['arrayReportData']['adGroupName'] : 'NA',
                    "Keyword Id" => isset($singleArray['arrayReportData']['keywordId']) ? $singleArray['arrayReportData']['keywordId'] : '0',
                    "Keyword Text" => isset($singleArray['arrayReportData']['keywordText']) ? $singleArray['arrayReportData']['keywordText'] : 'NA',
                    "Match Type" => isset($singleArray['arrayReportData']['matchType']) ? $singleArray['arrayReportData']['matchType'] : 'NA',
                    "Impression" => isset($singleArray['arrayReportData']['impression']) ? $singleArray['arrayReportData']['impression'] : '0',
                    "Clicks" => isset($singleArray['arrayReportData']['clicks']) ? $singleArray['arrayReportData']['clicks'] : '0',
                    "Spend" => isset($singleArray['arrayReportData']['cost']) ? $singleArray['arrayReportData']['cost'] : '0',
                    "Sales" => isset($singleArray['arrayReportData']['revenue']) ? $singleArray['arrayReportData']['revenue'] : '0',
                    "ROAS" => isset($singleArray['arrayReportData']['roas']) ? $singleArray['arrayReportData']['roas'] : '0',
                    "ACOS" => isset($singleArray['arrayReportData']['acos']) ? $singleArray['arrayReportData']['acos'] : '0',
                    "CPC" => isset($singleArray['arrayReportData']['cpc']) ? $singleArray['arrayReportData']['cpc'] : '0',
                    "CPA" => isset($singleArray['arrayReportData']['cpa']) ? $singleArray['arrayReportData']['cpa'] : '0',
                    "keywordBid" => isset($singleArray['keywordBid']) ? $singleArray['keywordBid'] : '0.0',
                    "Bid" => isset($singleArray['increaseKeywordBid']) ? $singleArray['increaseKeywordBid'] : '0.0',
                    "Check Status" => isset($singleArray['ruleCheckStatus']) ? $singleArray['ruleCheckStatus'] : 'NA',
                );
                array_push($ruleCheckData, $ruleCheckDataArray);
            }

            // define sheet and assign structure array of data
            $list = collect([
                array(
                    'Rule Name' => $excelFileArray[0]['rule']->ruleName,
                    'Frequency' => $excelFileArray[0]['rule']->frequency,
                    'Bidding Rule Conditions ' => $ruleStatementData[0])
            ]);
            // make sheets
            $sheets = new SheetCollection([
                'Rule Detail' => $list,
                'Rule Check Data' => $ruleCheckData
            ]);
            $fileName = $excelFileArray[0]['rule']->ruleName . '.xlsx';
            $fileNameWithPath = public_path('ams/bidding-rule/' . $fileName);
            (new FastExcel($sheets))->export($fileNameWithPath);
            $messages = array();
            $messages[0] = "<p>This email notification is to inform you about bidding rule.</p>";
            $messages[1] = "<p>Please see the attach file for further details.</p>";
            $bodyHTML = ((new BuyBoxEmailAlertMarkdown("Bidding Rule", $messages))->render());
            $data = [];
            $getBrandId = AccountModel::where('fkId', $excelFileArray[0]['rule']
                ->profileId)
                ->where('fkAccountType', 1)
                ->first();
            if (!empty($getBrandId)) {
                $brandId = $getBrandId->fkBrandId;
            } else {
                $brandId = '';
            }
            if (!empty($brandId) || $brandId != 0) {
                $getBrandAssignedUsers = ClientModel::with("brandAssignedUsers")->find($brandId);
                $managerEmailArray = [];
                foreach ($getBrandAssignedUsers->brandAssignedUsers as $getBrandAssignedUser) {
                    $brandAssignedUserId = $getBrandAssignedUser->pivot->fkManagerId;
                    $GetManagerEmail = User::where('id', $brandAssignedUserId)->first();
                    $managerEmailArray[] = $GetManagerEmail->email;
                }
                $data["toEmails"] = $managerEmailArray;
                if (isset($excelFileArray[0]['rule']->ccEmails) && $excelFileArray[0]['rule']->ccEmails != '') {
                    $cc = explode(',', $excelFileArray[0]['rule']->ccEmails);
                    $data["cc"] = $cc;
                }

                $data["subject"] = "Bidding Rule";
                $data["bodyHTML"] = $bodyHTML;
                $data["attachments"] = array(
                    array(
                        "path" => $fileNameWithPath,
                        "name" => $fileName
                    ),
                );
                $responseEmail = SendMailViaPhpMailerLib($data);
                if (empty($responseEmail['errors'])) {
                    $updateArray = array(
                        'emailSent' => '1',
                    );
                    $id = $excelFileArray[0]['id']; // tbl_ams_bidding_rule_cron 'id'
                    BiddingRule::updateCronBiddingRuleStatus($id, $updateArray);
                    if (file_exists($fileNameWithPath)) {
                        unlink($fileNameWithPath);
                    }
                }
            } else {
                // if email and rule checked
                Log::info('Email and Rule checked.');
            }
        } else {
            // if email and rule checked
            Log::info('Email and Rule checked.');
        }
        sleep(3);
    }
}
