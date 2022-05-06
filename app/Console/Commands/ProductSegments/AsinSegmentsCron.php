<?php

namespace App\Console\Commands\ProductSegments;

use App\models\ActiveAsin;
use Illuminate\Console\Command;
use App\Models\ProductSegments\ProductSegments;
use App\Models\ScrapingModels\ScrapModel;
use Illuminate\Support\Facades\Log;
use App\Models\ProductSegments\AsinSegments;
use App\Models\ProductPreviewModels\ProductPreviewModel;
use App\Models\ProductSegments\InventoryAllDetails;
use App\Models\ScrapingModels\UserHierarchy\AccountsAsinModel;
use Carbon\Carbon;

class AsinSegmentsCron extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'asinSegmentsCron:cron {activeCollections*}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'This Command is used to generate asin segments..\'';

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
        Log::info('Product Segments Start ' . $this->description);
        /* refresh table everyday */
        try {
            $logDataToInsertDb = [];
            $capturedDate = date('Y-m-d');
            $previousDayDate = date('Y-m-d', strtotime($capturedDate . ' -1 day'));
            $argumants = $this->argument('activeCollections');
            if (!empty($argumants)){
            $collectionIds = $argumants;
            $cronProductSegmentsData = ProductSegments::select('id','segmentName','fkGroupId')->get();
            if (!empty($cronProductSegmentsData)) {
                foreach ($cronProductSegmentsData as $productSegment) {
                    $productSegmentId = $productSegment->id;
                    // Fetch data which scraped today
                    $todayData = $this->findTodayData($collectionIds, $productSegmentId, $capturedDate);
                    switch ($productSegmentId) {
                        case '1' :
                        //if isAmazonChoice is 1 then save segment
                        if (!$todayData->isEmpty()) {
                        Log::info('Amz Choice tag');
                        foreach ($todayData as $key => $td) {
                        $asinValue = $td->asin;
                        $asinAccounts = ActiveAsin::select('accountId')->where('asin', $asinValue)->get();
                        if (!$asinAccounts->isEmpty()) {
                        foreach ($asinAccounts as $asinAccount) {
                        $asinAccountId = $asinAccount->accountId;
                        if (!empty($asinAccountId)) {
                        $logData = $this->amzChoiceTag($productSegment, $asinAccountId, $asinValue);
                        (!empty($logData)) ? array_push($logDataToInsertDb, $logData) : '';
                        }
                        }
                        }
                        }
                        }
                            // End Foreach Loop
                        break;
                        case '2' :
                            //if isPrime is 1 then save segment
                            if (!$todayData->isEmpty()) {
                                Log::info('Prime Tag');
                                foreach ($todayData as $key => $td) {
                                    $asinValue = $td->asin;
                                    $asinAccounts = ActiveAsin::select('accountId')->where('asin', $asinValue)->get();
                                    if (!$asinAccounts->isEmpty()) {
                                        foreach ($asinAccounts as $asinAccount) {
                                            $asinAccountId = $asinAccount->accountId;
                                            if (!empty($asinAccountId)) {
                                                $logData = $this->primeTag($productSegment, $asinAccountId, $asinValue);
                                                (!empty($logData)) ? array_push($logDataToInsertDb, $logData) : '';
                                            }
                                        }
                                    }
                                }
                            }
                            // End Foreach Loop
                            break;
                        case '3' :
                            //if bestSellerRank integer value is between 1 to 10 then save segment
                            if (!$todayData->isEmpty()) {
                                Log::info('Best Seller Tag');
                                foreach ($todayData as $key => $td) {
                                    $asinValue = $td->asin;
                                    $asinAccounts = ActiveAsin::select('accountId')->where('asin', $asinValue)->get();
                                    if (!$asinAccounts->isEmpty()) {
                                        foreach ($asinAccounts as $asinAccount) {
                                            $asinAccountId = $asinAccount->accountId;
                                            if (!empty($asinAccountId)) {
                                                $logData = $this->bestSellerTag($td, $productSegment, $asinAccountId, $asinValue);
                                                (!empty($logData)) ? array_push($logDataToInsertDb, $logData) : '';
                                            }
                                        }
                                    }
                                }
                            }
                            // End Foreach Loop
                            break;
                        case '4' :
                        /*check if on of following event occure then log segment
                        1-product page not found
                        2-out of stock vc event occure
                        3-out of stock sc
                        4-seller change buy box event*/
                        Log::info('Buy Box Status');
                            $asinAccounts = ActiveAsin::select('accountId','asin')->get();
                            if (!$asinAccounts->isEmpty()) {
                                foreach ($asinAccounts as $asinAccount) {
                                    $asinAccountId = $asinAccount->accountId;
                                    $asinValue = $asinAccount->asin;
                                    if (!empty($asinAccountId) && !empty($asinValue)) {
                                        $logData = $this->buyBoxStatus($capturedDate, $productSegment, $asinAccountId, $asinValue);
                                        (!empty($logData)) ? array_push($logDataToInsertDb, $logData) : '';
                                    }
                                }
                            }
                            // End Foreach Loop
                            break;
                        default:
                            Log::info('No Product Segment Match');
                            break;
                    }
                    //update or insert data in db
                }
            }// End Cron Segment Data
                //Delete previous day data before new data is inserted
                $this->deletePreviousDayData($capturedDate);
                //Delete previous day data before new data is inserted
            //insert data into db
            if (!empty($logDataToInsertDb)) {
                $ppm = new AsinSegments();
                $ppm->insertOrUpdate($logDataToInsertDb);
                unset($logDataToInsertDb);
                unset($logData);
            }else{
                Log::info('No Data to insert against segments');
            }//insert or update bulk
        }
        }catch (\Exception $ex) {
            Log::info('Product Segments' . $ex->getMessage());
        }

    }
                                                     /*private functions starts*/
    /**
     * This function is used to log event when offer not found event occur
     * @param $td
     * @param $productSegment
     * @param $fkAccountId
     * @param $asin
     * @return array
     */
    public function amzChoiceTag($productSegment, $fkAccountId, $asin){
        $logData = [];
        try {
            $segmentId = $productSegment->id;
            $fkGroupId = $productSegment->fkGroupId;
            $logData = $this->segmentLoggingData($fkAccountId, $asin,$segmentId,$fkGroupId);
            return $logData;
        } catch (Throwable $e) {
            report($e);
            return $logData;
        }
    }
    /**
     * This function is used to log best prime tag segment
     * @param $td
     * @param $productSegment
     * @param $fkAccountId
     * @param $asin
     * @return array
     */
    public function primeTag($productSegment , $fkAccountId , $asin){
        $logData = [];
        try {
            $segmentId = $productSegment->id;
            $fkGroupId = $productSegment->fkGroupId;
            $logData = $this->segmentLoggingData($fkAccountId, $asin,$segmentId,$fkGroupId);
            return $logData;
        } catch (Throwable $e) {
            report($e);

            return $logData;
        }
    }
    /**
     * This function is used to log best seller tag segment
     * @param $td
     * @param $productSegment
     * @param $fkAccountId
     * @param $asin
     * @return array
     */
    public function bestSellerTag($td,$productSegment ,$fkAccountId,$asin){
        $logData = [];
        try {
            $segmentId = $productSegment->id;
            $fkGroupId = $productSegment->fkGroupId;
            $bestSellerRank = 0;
            if(!empty($td)){
                $bestSellerRankVal = str_replace( ',', '', $td->bestSellerRank);
                if (!empty($bestSellerRankVal)){
                    $getSellerRanksArray = $this->getSellerRanksArray($bestSellerRankVal);
                    if (isset($getSellerRanksArray[0])){
                        $getSellerRanksValues = $getSellerRanksArray[0];
                    if (!empty($getSellerRanksValues)){
                        foreach ($getSellerRanksValues as $getSellerRanksValue) {
                            if (!is_array($getSellerRanksValue)) {
                                $checkBestSellerRank = (int)$getSellerRanksValue;
                            if ($checkBestSellerRank >= 1 && $checkBestSellerRank <= 10) {
                                $bestSellerRank = 1;
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
            //if seller rank in string is between 1 to 10 then store segment
            if ($bestSellerRank == 1){
                $logData = $this->segmentLoggingData($fkAccountId, $asin,$segmentId,$fkGroupId);
            }
            return $logData;
        } catch (Throwable $e) {
            report($e);
            return $logData;
        }
    }
    /**
     * This function is used to log buy box segment
     * @param $capturedDate
     * @param $productSegment
     * @param $fkAccountId
     * @param $asin
     * @return array
     */
    public function buyBoxStatus($capturedDate, $productSegment,$fkAccountId,$asin)
    {
    $logData = [];
     try{
        /*initialize variables*/
        $segmentId = $productSegment->id;
        $fkGroupId = $productSegment->fkGroupId;
         /*check if on of following event occure then log segment
          1-product page not found
          2-out of stock vc event occure
          3-out of stock sc
          4-seller change buy box event*/
        $eventsCheckArray = ['3','6','8','10'];
        $checkAnyEventOccur = $this->checkEventOccur($fkAccountId, $asin, $capturedDate, $eventsCheckArray);
        if (!empty($checkAnyEventOccur)){
            $logData = $this->segmentLoggingData($fkAccountId, $asin,$segmentId,$fkGroupId);
            return $logData;
        }
        return $logData;
    }
    catch (Throwable $e) {
            report($e);
            return $logData;
        }
    }
    /**
     * @param $collectionIds
     * @param $productSegmentId
     * @param $capturedDate
     * @return \Illuminate\Support\Collection
     */
    public function findTodayData($collectionIds, $productSegmentId, $capturedDate)
    {
        switch ($productSegmentId) {
            case 1:
                return ScrapModel::select('id', 'c_id' , 'asin', 'offerPrice', 'listPrice', 'isPromo', 'capturedAt', 'seller', 'averageReview', 'totalReviews', 'fkAsinId','availabilityMessage','isAmazonChoice','isPrime','bestSellerRank')
                    ->with('getAsinAccounts.accounts:id,accountName')
                    ->whereIn("c_id", $collectionIds)
                    ->where("isAmazonChoice", 1)
                    ->where("capturedAt", $capturedDate)
                    ->get();
            break;
            case 2:
                return ScrapModel::select('id', 'c_id' , 'asin', 'offerPrice', 'listPrice', 'isPromo', 'capturedAt', 'seller', 'averageReview', 'totalReviews', 'fkAsinId','availabilityMessage','isAmazonChoice','isPrime','bestSellerRank')
                    ->with('getAsinAccounts.accounts:id,accountName')
                    ->whereIn("c_id", $collectionIds)
                    ->where("isPrime", 1)
                    ->where("capturedAt", $capturedDate)
                    ->get();
            break;
            default:
              return  ScrapModel::select('id', 'c_id' , 'asin', 'offerPrice', 'listPrice', 'isPromo', 'capturedAt', 'seller', 'averageReview', 'totalReviews', 'fkAsinId','availabilityMessage','isAmazonChoice','isPrime','bestSellerRank')
                    ->with('getAsinAccounts.accounts:id,accountName')
                    ->whereIn("c_id", $collectionIds)
                    ->where("capturedAt", $capturedDate)
                    ->get();
            break;
        }
    }
    /**
     * @param $asin
     * @param $key
     * @return mixed
     */
    public function segmentLoggingData($fkAccountId, $asin,$segmentId,$fkGroupId)
    {
        $currentDateTime = date('Y-m-d H:i:s');
        $currentDate = date('Y-m-d');
        $data['fkAccountId'] = $fkAccountId;
        $data['asin'] = $asin;
        $data['occurrenceDate'] = $currentDate;
        $data['fkSegmentId'] = $segmentId;
        $data['fkGroupId'] = $fkGroupId;
        $data['uniqueColumn'] = $asin . '|' . $fkAccountId . '|'. $segmentId;
        $data['createdAt'] = $currentDateTime;
        $data['updatedAt'] = $currentDateTime;
        return $data;
    }
    /**
     * @param $bestSellerRank
     * @return $array
     */
    public function getSellerRanksArray($bestSellerRank)
    {
        preg_match_all('!\d+!', $bestSellerRank, $matches);
        return $matches;
    }
    /**
     * This function is used to log buy box segment
     * @param $fkAccountId
     * @param $capturedDate
     * @param $eventid
     * @return array
     */
    private function  checkEventOccur($fkAccountId, $asin ,$capturedDate,$eventid){
       return $eventFount = ProductPreviewModel::where('occurrenceDate', $capturedDate)
                                        ->where('asin',$asin)
                                        ->where('fkAccountId',$fkAccountId)
                                        ->whereIn('fkEventId',$eventid)
                                        ->first();
    }
    /**
     * This function is used to delete previous day segments
     * @return array
     */
    private function  deletePreviousDayData($capturedDate)
    {
        $previousDayData = AsinSegments::select('id')->where('occurrenceDate', '!=' , $capturedDate)->first();
        //check if previous day recrods exist then run delete query
        if (!empty($previousDayData)) {
            AsinSegments::where('occurrenceDate', '!=' , $capturedDate)->delete();
        }
    }
}
