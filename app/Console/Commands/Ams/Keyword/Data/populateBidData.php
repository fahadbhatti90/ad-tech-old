<?php

namespace App\Console\Commands\Ams\Keyword\Data;

use Artisan;
use DB;
use App\models\BiddingRule;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class populateBidData extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'keywordData:bidding_rule';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'This command is used to data of campaign to get keyword list of specific type.';

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
        Log::info("filePath:App\Console\Commands\Ams\Keyword\Data\populateBidData. Start Cron.");
        Log::info($this->description);
        $biddingDataArray = BiddingRule::getBiddingRulesList();
        if (count($biddingDataArray) > 0) {
            $finalDataArray = array(); // define array for store
            foreach ($biddingDataArray as $singleValue) {
                $dataArray = array(); // define array
                if ($singleValue->type === 'Portfolio') { // if type is Portfolio
                    $dataArray['fkBiddingRuleId'] = $singleValue->id;
                    $dataArray['sponsoredType'] = $singleValue->sponsoredType;
                    $dataArray['type'] = $singleValue->type;
                    $dataArray['frequency'] = $singleValue->frequency;
                    $dataArray['listOfCampaign'] = $this->getCampaignPortfolioListData($singleValue->pfCampaigns, $singleValue->sponsoredType);
                } else { // if type is campaign
                    $dataArray['fkBiddingRuleId'] = $singleValue->id;
                    $dataArray['sponsoredType'] = $singleValue->sponsoredType;
                    $dataArray['type'] = $singleValue->type;
                    $dataArray['frequency'] = $singleValue->frequency;
                    $dataArray['listOfCampaign'] = $this->getCampaignList($singleValue->pfCampaigns, $singleValue->sponsoredType);
                    $dataArray['profile'] = $singleValue->profileId;
                }// end if else
                array_push($finalDataArray, $dataArray);
            }// end foreach
            $response = BiddingRule::storeDataForBiddingRuleCorn($finalDataArray);
            if ($response) {
                // successfully store into DB
            } else {
                // no store into DB
            }
        } else {
            // no data found in bidding rule table
        } // end if else
        Log::info("filePath:App\Console\Commands\Ams\Keyword\Data\populateBidData. End Cron.");
    }

    /**
     * This function is used to get campaign list of specific Portfolio
     *
     * @param $portfolioId
     * @param $sponsoredType
     * @return array
     */
    private function getCampaignPortfolioListData($portfolioId, $sponsoredType)
    {
        $returnArray = array(); // define array
        $portfolioArray = explode(',', $portfolioId);
        foreach ($portfolioArray as $array) {
            $response = BiddingRule::getCampaignId($array, $sponsoredType);
            if ($response != NULL) { //check is not null
                array_push($returnArray, $response);
            }// end if
        }// end foreach
        return $returnArray;
    }

    /**
     * @param $campaignId
     * @param $sponsoredType
     * @return array
     */
    private function getCampaignList($campaignId, $sponsoredType)
    {
        $returnArray = array(); // define array
        $portfolioArray = explode(',', $campaignId);
        foreach ($portfolioArray as $array) {
            $response = BiddingRule::getCampaignList($array, $sponsoredType);
            if ($response != NULL) { //check is not null
                array_push($returnArray, $response);
            }// end if
        }// end foreach
        return $returnArray;
    }
}
