<?php

namespace App\models;

use App\Models\AccountModels\AccountModel;
use App\Models\ClientModels\ClientModel;
use App\User;
use DB;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use stdClass;
use function GuzzleHttp\Psr7\get_message_body_summary;

class BiddingRule extends Model
{
    private static $tbl_ams_bidding_rules = 'tbl_ams_bidding_rules';
    private static $tbl_ams_campaign_list = 'tbl_ams_campaign_list';
    private static $tbl_ams_bidding_rules_portfolio_campaign_data_cron = 'tbl_ams_bidding_rules_portfolio_campaign_data_cron';
    private static $tbl_ams_bidding_rule_invalid_profile = 'tbl_ams_bidding_rule_invalid_profile';
    private static $tbl_ams_bidding_rule_keywordId_list = 'tbl_ams_bidding_rule_keywordId_list';
    private static $tbl_ams_bidding_rule_cron = 'tbl_ams_bidding_rule_cron';
    private static $tbl_ams_bidding_rule_preset = 'tbl_ams_bidding_rule_preset';

    /**
     * This function is used to store Rule into DB
     *
     * @param $dataArray
     * @param $type
     * @return bool
     */
    public static function storeBiddingRule($dataArray, $type)
    {
        if ($type == 'add') {
            DB::beginTransaction();
            try {
                DB::table(BiddingRule::$tbl_ams_bidding_rules)->insertGetId($dataArray);
                DB::commit();
                return true;
            } catch (\Exception $e) {
                DB::rollback();
                return $dataArray;
                // something went wrong
            }
        } else if ($type == 'edit') {

            DB::beginTransaction();
            try {
                $recordDataCron = DB::table(BiddingRule::$tbl_ams_bidding_rules_portfolio_campaign_data_cron)
                    ->select('*')
                    ->where('fkBiddingRuleId', $dataArray['id'])
                    ->get()
                    ->first();
                if ($recordDataCron) {
                    DB::table(BiddingRule::$tbl_ams_bidding_rules_portfolio_campaign_data_cron)->where('fkBiddingRuleId', $dataArray['id'])->delete();
                    DB::commit();
                }
                $recordKeywordCron = DB::table(BiddingRule::$tbl_ams_bidding_rule_keywordId_list)
                    ->select('*')
                    ->where('fkBiddingRuleId', $dataArray['id'])
                    ->get()
                    ->first();
                if ($recordKeywordCron) {
                    DB::table(BiddingRule::$tbl_ams_bidding_rule_keywordId_list)->where('fkBiddingRuleId', $dataArray['id'])->delete();
                    DB::commit();
                }
                //$recordBiddingRuleCron = DB::table(BiddingRule::$tbl_ams_bidding_rule_cron)
                //    ->select('*')
                //    ->where('fkBiddingRuleId', $dataArray['id'])
                //    ->get()
                //    ->first();
                //if ($recordBiddingRuleCron) {
                //    DB::table(BiddingRule::$tbl_ams_bidding_rule_cron)->where('fkBiddingRuleId', $dataArray['id'])->delete();
                //    DB::commit();
                //}

                DB::table(BiddingRule::$tbl_ams_bidding_rules)
                    ->where('id', $dataArray['id'])
                    ->update($dataArray);
                DB::commit();
                return true;
            } catch (\Exception $e) {
                DB::rollback();
                // something went wrong
                return false;
            }
        } else if ($type == 'delete') {
            DB::beginTransaction();
            try {
                DB::table(BiddingRule::$tbl_ams_bidding_rules)->where('id', $dataArray)->delete();
                DB::commit();
                return true;
            } catch (\Exception $e) {
                DB::rollback();
                // something went wrong
                return false;
            }
        }

    }

    /**
     * This function is used to get All Bidding Rule
     *
     * @return bool
     */
    public static function getBiddingRulesList()
    {
        $returnArray = array();
        $responseData = DB::table('tbl_ams_bidding_rules')
            ->select('*')
            ->get();
        if ($responseData->isNotEmpty()) {
            foreach ($responseData as $single) {
                $GetManagerId = AccountModel::where('fkId', $single->profileId)->where('fkAccountType', 1)->first();
                if (!empty($GetManagerId)) {
                    $brandId = $GetManagerId->fkBrandId;
                } else {
                    $brandId = '';
                }
                if (!empty($brandId) || $brandId != 0) {
                    $managerEmailArray = new stdClass();
                    $managerEmailArray->id = $single->id;
                    $managerEmailArray->fkUserId = $single->fkUserId;
                    $managerEmailArray->fKPreSetRule = $single->fKPreSetRule;
                    $managerEmailArray->ruleName = $single->ruleName;
                    $managerEmailArray->sponsoredType = $single->sponsoredType;
                    $managerEmailArray->type = $single->type;
                    $managerEmailArray->lookBackPeriod = $single->lookBackPeriod;
                    $managerEmailArray->lookBackPeriodDays = $single->lookBackPeriodDays;
                    $managerEmailArray->pfCampaigns = $single->pfCampaigns;
                    $managerEmailArray->profileId = $single->profileId;
                    $managerEmailArray->frequency = $single->frequency;
                    $managerEmailArray->metric = $single->metric;
                    $managerEmailArray->condition = $single->condition;
                    $managerEmailArray->integerValues = $single->integerValues;
                    $managerEmailArray->thenClause = $single->thenClause;
                    $managerEmailArray->bidBy = $single->bidBy;
                    $managerEmailArray->andOr = $single->andOr;
                    $managerEmailArray->ccEmails = $single->ccEmails;
                    $managerEmailArray->createdAt = $single->createdAt;
                    $managerEmailArray->updatedAt = $single->updatedAt;
                    $managerEmailArray->fkBrandId = $single->fkBrandId;
                    array_push($returnArray, $managerEmailArray);
                }
            }
            if (!empty($returnArray)) {
                return $returnArray;
            }
            return false;
        }
        return false;
    }

    /**
     * This function is used to get All Bidding Rule
     *
     * @return bool
     */
    public static function getBiddingRulesListSpecificUser()
    {
        $data = [];
        $accounts = AccountModel::where("fkBrandId", getBrandId())
            ->select("id", "fkId")
            ->where("fkAccountType", 1)
            ->get()
            ->map(function ($item, $value) {
                return $item->fkId;
            });

        if ($accounts->isNotEmpty()) {
            $accountsList = collect($accounts)->all();
            DB::raw('SET @rownum=0; ');
            $responseData = DB::select('SELECT tbl_ams_bidding_rules.*, @rownum := @rownum + 1 AS rownum, presetName FROM tbl_ams_bidding_rules LEFT JOIN tbl_ams_bidding_rule_preset ON tbl_ams_bidding_rule_preset.id = tbl_ams_bidding_rules.fKPreSetRule WHERE SUBSTRING_INDEX(tbl_ams_bidding_rules.`profileId`,\'|\',1) IN (' . (implode(',', $accountsList)) . ')');
            return $responseData;
        }

    }

    /**
     * This function is used to get bidding rule preset rules
     *
     * @return bool
     */
    public static function getBiddingRulesPresetList()
    {
        $responseData = DB::table('tbl_ams_bidding_rule_preset')->select('*')->get();
        return $responseData;
    }

    /**
     * This function is used to get specific Bidding Rule
     *
     * @param $id
     * @return bool
     */
    public static function getSpecificBiddingRule($id)
    {
        $responseData = DB::table('tbl_ams_bidding_rules')
            ->select('*')
            ->where('id', '=', $id)
            ->first();
        return $responseData;
    }

    /**
     * This function is used to get campaign id of specific portfolio
     *
     * @param $portfolioId
     * @param $sponsoredType
     * @return bool
     */
    public static function getCampaignId($portfolioId, $sponsoredType)
    {

        return DB::table(BiddingRule::$tbl_ams_campaign_list)
            ->select(BiddingRule::$tbl_ams_campaign_list . '.*')
            ->join('tbl_ams_portfolios as tbl_port', 'tbl_port.portfolioId', '=', BiddingRule::$tbl_ams_campaign_list . '.portfolioId')
            ->where('tbl_port.id', $portfolioId)
            ->get()
            ->all();
    }

    /**
     * @param $portfolioId
     * @param $sponsoredType
     * @return bool
     */
    public static function getCampaignList($Id, $sponsoredType)
    {
        return DB::table(BiddingRule::$tbl_ams_campaign_list)
            ->select('*')
            ->where('id', $Id)
            ->where('campaignType', $sponsoredType)
            ->get()
            ->first();
    }

    /**
     * This function is used to store data for cron
     *
     * @param $dataArray
     * @return bool
     */
    public static function storeDataForBiddingRuleCorn($dataArray)
    {

        DB::beginTransaction();
        try {
            foreach ($dataArray as $singleArray) {
                if ($singleArray['type'] == 'Portfolio') { // if type is Portfolio
                    $storeDB = array();
                    if (count($singleArray['listOfCampaign']) > 0) {
                        foreach ($singleArray['listOfCampaign'] as $index) {
                            foreach ($index as $innerValue) {
                                $record = DB::table(BiddingRule::$tbl_ams_bidding_rules_portfolio_campaign_data_cron)->where([
                                    ['fkBiddingRuleId', '=', $singleArray['fkBiddingRuleId']],
                                    ['sponsoredType', '=', $singleArray['sponsoredType']],
                                    ['type', '=', $singleArray['type']],
                                    ['campaignId', '=', $innerValue->campaignId],
                                    ['portfolioId', '=', $innerValue->portfolioId],
                                ])->get()->first();
                                if (!$record) {
                                    $array = array();
                                    $array['fkBiddingRuleId'] = $singleArray['fkBiddingRuleId'];
                                    $array['sponsoredType'] = $singleArray['sponsoredType'];
                                    $array['type'] = $singleArray['type'];
                                    $array['frequency'] = $singleArray['frequency'];
                                    $array['profileId'] = $innerValue->profileId;
                                    $array['campaignId'] = $innerValue->campaignId;
                                    $array['portfolioId'] = $innerValue->portfolioId;
                                    $array['reportType'] = $singleArray['sponsoredType'];
                                    $array['fkConfigId'] = $innerValue->fkConfigId;
                                    $array['createdAt'] = date('Y-m-d H:i:s');
                                    $array['updatedAt'] = date('Y-m-d H:i:s');
                                    DB::table(BiddingRule::$tbl_ams_bidding_rules_portfolio_campaign_data_cron)->insertGetId($array);
                                } else {
                                    // if data found
                                }
                            }// end foreach
                        } // end foreach
                    }
                } else if ($singleArray['type'] == 'Campaign') { // if type is campaign
                    for ($j = 0; $j < count($singleArray['listOfCampaign']); $j++) {
                        $record = DB::table(BiddingRule::$tbl_ams_bidding_rules_portfolio_campaign_data_cron)->where([
                            ['fkBiddingRuleId', '=', $singleArray['fkBiddingRuleId']],
                            ['sponsoredType', '=', $singleArray['sponsoredType']],
                            ['campaignId', '=', $singleArray['listOfCampaign'][$j]->campaignId],
                            ['type', '=', $singleArray['type']],
                        ])->get()->first();
                        if (!$record) {
                            $array = array();
                            $array['fkBiddingRuleId'] = $singleArray['fkBiddingRuleId'];
                            $array['sponsoredType'] = $singleArray['sponsoredType'];
                            $array['type'] = $singleArray['type'];
                            $array['frequency'] = $singleArray['frequency'];
                            $array['profileId'] = $singleArray['listOfCampaign'][$j]->profileId;
                            $array['campaignId'] = $singleArray['listOfCampaign'][$j]->campaignId;
                            $array['portfolioId'] = $singleArray['listOfCampaign'][$j]->portfolioId;
                            $array['reportType'] = $singleArray['sponsoredType'];
                            $array['fkConfigId'] = $singleArray['listOfCampaign'][$j]->fkConfigId;
                            $array['createdAt'] = date('Y-m-d H:i:s');
                            $array['updatedAt'] = date('Y-m-d H:i:s');
                            DB::table(BiddingRule::$tbl_ams_bidding_rules_portfolio_campaign_data_cron)->insertGetId($array);
                        }
                    }
                } else {
                    // if data found in DB
                }
            }// end foreach
            DB::commit();
            return true;
        } catch (\Exception $e) {
            // got error add to log file
            //$e->getMessage();
            DB::rollback();
            // something went wrong
            return false;
        }
    }

    /**
     * This function is used to get stored date of campaign with detail data
     *
     * @return bool
     */
    public static function getDataForBiddingRuleCorn($fkBiddingRuleId)
    {
        DB::beginTransaction();
        try {
            $responseData = DB::table(BiddingRule::$tbl_ams_bidding_rules_portfolio_campaign_data_cron . ' as tbl_camp')
                ->select('tbl_camp.id', 'tbl_camp.fkBiddingRuleId', 'tbl_camp.sponsoredType', 'tbl_camp.type', 'tbl_camp.profileId', 'tbl_camp.campaignId', 'tbl_camp.portfolioId','tbl_camp.portfolioId', 'tbl_camp.reportType','tbl_camp.fkConfigId','tb_ams_api.client_id')
                ->join('tbl_ams_profiles as tbl_pro', 'tbl_pro.profileId', '=', 'tbl_camp.profileId')
                ->join('tb_ams_api', 'tbl_camp.fkConfigId', '=', 'tb_ams_api.id')
                ->where('tbl_pro.isSandboxProfile', 0)
                ->where('tbl_camp.fkBiddingRuleId', '=', $fkBiddingRuleId)
                ->where('tbl_pro.isActive', 1)
                ->get();
            DB::commit();
            if (count($responseData) > 0) {
                return $responseData;
            }
        } catch (\Exception $e) {
            DB::rollback();
            // something went wrong
            return false;
        }
    }

    /**
     * This function is used to store in valid profile record
     *
     * @param $fkId
     * @param $fkBiddingRuleId
     * @param $profileId
     * @param $campaignId
     * @return bool
     */
    public static function inValidProfile($fkId, $fkBiddingRuleId, $profileId, $campaignId)
    {
        DB::beginTransaction();
        try {
            $record = DB::table(BiddingRule::$tbl_ams_bidding_rule_invalid_profile)->where([
                ['fkId', '=', $fkId],
                ['fkBiddingRuleId', '=', $fkBiddingRuleId],
                ['profileId', '=', $profileId],
                ['campaignId', '=', $campaignId],
            ])->get()->first();
            if (!$record) {
                $array = array(
                    'fkId' => $fkId,
                    'fkBiddingRuleId' => $fkBiddingRuleId,
                    'profileId' => $profileId,
                    'campaignId' => $campaignId,
                    'createdAt' => date('Y-m-d H:i:s'),
                    'updatedAt' => date('Y-m-d H:i:s'),
                );
                DB::table(BiddingRule::$tbl_ams_bidding_rule_invalid_profile)->insert($array);
            } else {
                // if data found
            }
            DB::commit();
        } catch (\Exception $e) {
            // got error add to log file
            // $e->getMessage()
            DB::rollback();
            // something went wrong
            return false;
        }
    }

    /**
     * This function is used to store keyword data
     *
     * @param $DataArray
     * @return bool
     */
    public static function storeKeywordData($DataArray)
    {
        DB::beginTransaction();
        try {
            foreach ($DataArray as $single) {
                log::info('model');
                $record = DB::table(BiddingRule::$tbl_ams_bidding_rule_keywordId_list)->where([
                    // ['fkId', '=', $single['fkId']],
                    ['fkBiddingRuleId', '=', $single['fkBiddingRuleId']],
                    //['profileId', '=', $single['profileId']],
                    // ['campaignId', '=', $single['campaignId']],
                    ['keywordId', '=', $single['keywordId']],
                    //['adGroupId', '=', $single['adGroupId']],
                    /// ['reportType', '=', $single['reportType']],
                ])->get()->first();
                if (!$record) {
                    DB::table(BiddingRule::$tbl_ams_bidding_rule_keywordId_list)->insert($single);
                    DB::commit();
                } else {
                    // if data found
                    DB::table(BiddingRule::$tbl_ams_bidding_rule_keywordId_list)
                        ->where([
                            'fkBiddingRuleId' => $single['fkBiddingRuleId'],
                            'keywordId' => $single['keywordId']
                        ])->update($single);
                    DB::commit();
                }
            }// end foreach
        } catch (\Exception $e) {
            // got error add to log file
            // $e->getMessage()
            DB::rollback();
            // something went wrong
            return false;
        }
    }

    /**
     * This function is used to get keyword data of specific type
     * @param $fkBiddingRuleId
     * @param $type
     * @return bool
     */
    public static function getKeywordData($fkBiddingRuleId, $type)
    {
        DB::beginTransaction();
        try {
            $responseData = DB::table(BiddingRule::$tbl_ams_bidding_rule_keywordId_list)
                ->join('tb_ams_api', 'tbl_ams_bidding_rule_keywordId_list.fkConfigId', '=', 'tb_ams_api.id')
                ->select('tbl_ams_bidding_rule_keywordId_list.id','tbl_ams_bidding_rule_keywordId_list.fkId','tbl_ams_bidding_rule_keywordId_list.fkBiddingRuleId','tbl_ams_bidding_rule_keywordId_list.fkConfigId','tbl_ams_bidding_rule_keywordId_list.profileId','tbl_ams_bidding_rule_keywordId_list.reportType','tbl_ams_bidding_rule_keywordId_list.keywordId','tbl_ams_bidding_rule_keywordId_list.adGroupId','tbl_ams_bidding_rule_keywordId_list.campaignId','tbl_ams_bidding_rule_keywordId_list.keywordText','tbl_ams_bidding_rule_keywordId_list.matchType','tbl_ams_bidding_rule_keywordId_list.matchType','tbl_ams_bidding_rule_keywordId_list.state','tbl_ams_bidding_rule_keywordId_list.bid','tbl_ams_bidding_rule_keywordId_list.servingStatus','tbl_ams_bidding_rule_keywordId_list.creationDate','tbl_ams_bidding_rule_keywordId_list.creationDate','tbl_ams_bidding_rule_keywordId_list.lastUpdatedDate','tbl_ams_bidding_rule_keywordId_list.createdAt','tbl_ams_bidding_rule_keywordId_list.updatedAt','tb_ams_api.client_id')
                ->where('fkBiddingRuleId', '=', $fkBiddingRuleId)
                ->get();
            DB::commit();
            if (count($responseData) > 0) {
                return $responseData;
            }
        } catch (\Exception $e) {
            DB::rollback();
            // something went wrong
            return false;
        }
    }

    /**
     * This function is used to update campaign data status from api
     *
     * @param $id
     */
    public static function updateBiddingCampaignApiStatus($id)
    {
        DB::table(BiddingRule::$tbl_ams_bidding_rules_portfolio_campaign_data_cron)
            ->where('id', $id)
            ->update(['isDone' => 1]);
    }

    /**
     * This function is used to store data from CRON management of Bidding Rule
     *
     * @param $DataArray
     * @return bool
     */
    public static function storeBiddingRuleCron($DataArray)
    {
        DB::beginTransaction();
        DB::table(BiddingRule::$tbl_ams_bidding_rule_cron)
            ->update(['isActive' => 0]);
        try {
            foreach ($DataArray as $single) {
                $record = DB::table(BiddingRule::$tbl_ams_bidding_rule_cron)
                    ->where([['fkBiddingRuleId', '=', $single['fkBiddingRuleId']]
                    ])
                    ->get()
                    ->first();
                if (!$record) {
                    DB::table(BiddingRule::$tbl_ams_bidding_rule_cron)->insert($single);
                } else {
                    // if data found
                    $updateArray = array(
                        'sponsoredType' => $single['sponsoredType'],
                        'lookBackPeriodDays' => $single['lookBackPeriodDays'],
                        'frequency' => $single['frequency'],
                        'isActive' => 1,
                    );
                    DB::table(BiddingRule::$tbl_ams_bidding_rule_cron)
                        ->where('fkBiddingRuleId', $single['fkBiddingRuleId'])
                        ->update($updateArray);
                }
                DB::commit();
            }// end foreach
        } catch (\Exception $e) {
            // got error add to log file
            // $e->getMessage()
            DB::rollback();
            // something went wrong
            return false;
        }
    }

    /**
     * This function is used to get data from CRON management of Bidding Rule
     *
     * @return bool
     */
    public static function getBiddingRuleCron()
    {
        $returnArray = array();
        $arrayData = DB::table('tbl_ams_bidding_rule_cron')
            ->join('tbl_ams_bidding_rules', 'tbl_ams_bidding_rule_cron.fkBiddingRuleId', '=', 'tbl_ams_bidding_rules.id')
            ->select('tbl_ams_bidding_rule_cron.*', 'tbl_ams_bidding_rules.profileId')
            ->where('isActive', '=', 1)
            ->get();
        if ($arrayData->isNotEmpty()) {
            foreach ($arrayData as $single) {
                $getBrandId = AccountModel::where('fkId', $single->profileId)->where('fkAccountType', 1)->first();
                if (!empty($getBrandId)) {
                    $brandId = $getBrandId->fkBrandId;
                } else {
                    $brandId = '';
                }
                if (!empty($brandId) || $brandId != 0) {
                    $managerEmailArray = new stdClass();
                    $managerEmailArray->id = $single->id;
                    $managerEmailArray->fkBiddingRuleId = $single->fkBiddingRuleId;
                    $managerEmailArray->sponsoredType = $single->sponsoredType;
                    $managerEmailArray->lookBackPeriodDays = $single->lookBackPeriodDays;
                    $managerEmailArray->frequency = $single->frequency;
                    $managerEmailArray->runStatus = $single->runStatus;
                    $managerEmailArray->currentRunTime = $single->currentRunTime;
                    $managerEmailArray->lastRunTime = $single->lastRunTime;
                    $managerEmailArray->nextRunTime = $single->nextRunTime;
                    $managerEmailArray->isActive = $single->isActive;
                    $managerEmailArray->checkRule = $single->checkRule;
                    $managerEmailArray->ruleResult = $single->ruleResult;
                    $managerEmailArray->createdAt = $single->createdAt;
                    $managerEmailArray->updatedAt = $single->updatedAt;
                    $managerEmailArray->emailSent = $single->emailSent;
                    $managerEmailArray->profileId = $single->profileId;
                    array_push($returnArray, $managerEmailArray);
                }
            }// endforeach
            return $returnArray;
        }//endif
        return FALSE;
    }

    /**
     * @param $DataArray
     * @return bool
     */
    public static function storePresetRule($DataArray)
    {
        DB::beginTransaction();
        try {
            DB::table(BiddingRule::$tbl_ams_bidding_rule_preset)->insertGetId($DataArray);
            DB::commit();
            return true;
        } catch (\Exception $e) {
            // got error add to log file
            log::error($e->getMessage());
            DB::rollback();
            return FALSE;
        }
    }

    /**
     * @param $id
     * @return bool
     */
    public static function getPresetRule($id)
    {
        DB::beginTransaction();
        try {
            $responseData = DB::table(BiddingRule::$tbl_ams_bidding_rule_preset)
                ->select('*')
                ->where('id', '=', $id)
                ->get();
            DB::commit();
            if (count($responseData) > 0) {
                return $responseData;
            }
        } catch (\Exception $e) {
            DB::rollback();
            // something went wrong
            return false;
        }
    }

    /**
     * This function is used to truncate Preset Value from Database
     */
    public static function truncatePreset()
    {
        DB::table(BiddingRule::$tbl_ams_bidding_rule_preset)->truncate();
    }

    /**
     * This function is used to update bidding rule cron status
     *
     * @param $dataArray
     * @return bool
     */
    public static function updateCronBiddingRuleStatus($id, $dataArray)
    {
        DB::beginTransaction();
        try {
            // if data found
            DB::table(BiddingRule::$tbl_ams_bidding_rule_cron)
                ->where('id', $id)
                ->update($dataArray);
            DB::commit();
        } catch (\Exception $e) {
            // got error add to log file
            // $e->getMessage()
            DB::rollback();
            // something went wrong
            return false;
        }
    }

    /**
     * This function is used to get specific rule values
     * @param $id
     * @return mixed
     */
    public static function getCronBiddingRuleEmailStatus($id)
    {
        return DB::table(BiddingRule::$tbl_ams_bidding_rule_cron)
            ->select('*')
            ->where('id', '=', $id)
            ->get()
            ->first();
    }

    /**
     * This function is used to get data of specific campaign and potfoilio to show in datatable
     *
     * @param $type
     * @param $ruleId
     * @return mixed
     */
    public static function getBiddingRulesDatatableList($type, $ruleId)
    {
        if ($type == 'Campaign') {
            return DB::select("SELECT  tbl_camp.id , tbl_camp.name AS typeName
                                FROM tbl_ams_bidding_rules tbl_bid
                                INNER JOIN tbl_ams_campaign_list tbl_camp ON FIND_IN_SET(tbl_camp.id , tbl_bid.pfCampaigns) > 0
                                WHERE tbl_bid.`type` = 'Campaign'
                                AND tbl_bid.`id` = " . $ruleId);
        } else if ($type == 'Portfolio') {
            return DB::select("SELECT  tbl_port.id , tbl_port.name AS typeName
                                FROM tbl_ams_bidding_rules tbl_bid
                                INNER JOIN tbl_ams_portfolios tbl_port ON FIND_IN_SET(tbl_port.id , tbl_bid.pfCampaigns) > 0
                                WHERE tbl_bid.`type` = 'Portfolio'
                                AND tbl_bid.`id` = " . $ruleId);
        }
    }
}
