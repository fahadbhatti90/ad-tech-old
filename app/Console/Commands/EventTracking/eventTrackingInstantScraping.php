<?php

namespace App\Console\Commands\EventTracking;

use App\Models\ProductPreviewModels\EventsModel;
use App\Models\ProductPreviewModels\ProductPreviewModel;
use App\Models\ScrapingModels\asinModel;
use App\Models\ScrapingModels\ScrapModel;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use App\Models\ActiveAsin;
use App\Models\ScrapingModels\InstantASINFailStatusModel;

class eventTrackingInstantScraping extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'eventTrackingInstantScraping:cron {collectionIds*}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'This Command is used to log instant scraping event of price change, review change for instant scraping etc..';

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

        ini_set('memory_limit', '-1');
        Log::info('Event Tracking Start ' . $this->description);
        try {
            $argumants = $this->argument('collectionIds');
            if (!empty($argumants)){
                $collectionIds = $argumants;
            $cronEventData = EventsModel::where('isEventAuto', 1)->get(['id', 'eventName']);
            if (!empty($cronEventData)) {
                foreach ($cronEventData as $scd) {
                    $logDataToInsertDb = [];
                    $capturedDate = date('Y-m-d');
                    $previousDayDate = date('Y-m-d', strtotime($capturedDate . ' -1 day'));
                    // Fetch data which scraped today
                    $todayData = $this->findTodayData($capturedDate, $scd->id, $collectionIds);

                    if (!empty($todayData)) {

                        switch ($scd->id) {
                            case '1':
                                foreach ($todayData as $key => $td) {
                                    $asinValue = $td->asin;
                                    $asinAccounts = ActiveAsin::select('accountId')->where('asin', $asinValue)->get();
                                    if (!$asinAccounts->isEmpty()) {
                                        foreach ($asinAccounts as $asinAccount) {
                                            $asinAccountId = $asinAccount->accountId;
                                            if (!empty($asinAccountId)) {
                                                // Review Change function to check data with previous scrap data if Review change occurs event will be log
                                                $logData = $this->reviewChange($td, $previousDayDate, $scd, $asinAccountId);
                                                (!empty($logData)) ? array_push($logDataToInsertDb, $logData) : '';
                                            }
                                        }
                                    }
                                }// End Foreach Loop
                                break;
                            case '2':
                                Log::info('Content Performance Start');
                                foreach ($todayData as $key => $td) {
                                    $asinValue = $td->asin;
                                    $asinAccounts = ActiveAsin::select('accountId')->where('asin', $asinValue)->get();
                                    if (!$asinAccounts->isEmpty()) {
                                        foreach ($asinAccounts as $asinAccount) {
                                            $asinAccountId = $asinAccount->accountId;
                                            if (!empty($asinAccountId)) {
                                                $logData = $this->contentPerformance($td, $scd, $asinAccountId);
                                                (!empty($logData)) ? array_push($logDataToInsertDb, $logData) : '';
                                            }
                                        }
                                    }
                                }// End Foreach Loop
                                break;
                            case '3':
                                Log::info('Product not found Start');
                                foreach ($todayData as $key => $td) {
                                    // chekc 404 from table "tbl_asins_instant_fail_statuses",if record found then log event
                                    $failedData = $td->failed_data;
                                    if (!empty(trim($failedData))) {
                                        $jsonDecode = json_decode($failedData);
                                        if (isset($jsonDecode->asin)) {
                                            $asinValue = $jsonDecode->asin;
                                            if (!empty(trim($asinValue))) {
                                                $asinAccounts = ActiveAsin::select('accountId')->where('asin', $asinValue)->get();
                                                if (!$asinAccounts->isEmpty()) {
                                                    foreach ($asinAccounts as $asinAccount) {
                                                        $asinAccountId = $asinAccount->accountId;
                                                        if (!empty($asinAccountId)) {
                                                            $logData = $this->productNotFound($asinValue, $scd, $asinAccountId);
                                                            (!empty($logData)) ? array_push($logDataToInsertDb, $logData) : '';
                                                        }
                                                    }
                                                }
                                            }
                                        }
                                    }
                                }
                                break;
                            case '7':
                                Log::info('Price Change Case Start');
                                foreach ($todayData as $key => $td) {
                                    $asinValue = $td->asin;
                                    $asinAccounts = ActiveAsin::select('accountId')->where('asin', $asinValue)->get();
                                    if (!$asinAccounts->isEmpty()) {
                                        foreach ($asinAccounts as $asinAccount) {
                                            $asinAccountId = $asinAccount->accountId;
                                            if (!empty($asinAccountId)) {
                                                // Price Change function to check data with previous scrap data if price change occurs event will be log.
                                                $logData = $this->priceChange($td, $previousDayDate, $scd, $key, $asinAccountId);
                                                (!empty($logData)) ? array_push($logDataToInsertDb, $logData) : '';
                                            }
                                        }
                                    }
                                }// End Foreach Loop
                                break;
                            case '8':
                                Log::info('Seller Change Case Start');
                                foreach ($todayData as $key => $td) {
                                    $asinValue = $td->asin;
                                    $asinAccounts = ActiveAsin::select('accountId')->where('asin', $asinValue)->get();
                                    if (!$asinAccounts->isEmpty()) {
                                        foreach ($asinAccounts as $asinAccount) {
                                            $asinAccountId = $asinAccount->accountId;
                                            if (!empty($asinAccountId)) {
                                                // Seller Change function to check data with previous scrap data if seller change occurs event will be log.
                                                $logData = $this->sellerChange($td, $previousDayDate, $scd, $asinAccountId);
                                                (!empty($logData)) ? array_push($logDataToInsertDb, $logData) : '';
                                            }
                                        }
                                    }
                                }// End Foreach Loop
                                break;
                            case '9':
                                Log::info('Content Change Start');
                                foreach ($todayData as $key => $td) {
                                    $asinValue = $td->asin;
                                    $asinAccounts = ActiveAsin::select('accountId')->where('asin', $asinValue)->get();
                                    if (!$asinAccounts->isEmpty()) {
                                        foreach ($asinAccounts as $asinAccount) {
                                            $asinAccountId = $asinAccount->accountId;
                                            if (!empty($asinAccountId)) {
                                                $logData = $this->contentChange($td, $previousDayDate, $scd, $asinAccountId);
                                                (!empty($logData)) ? array_push($logDataToInsertDb, $logData) : '';
                                            }
                                        }
                                    }
                                }// End Foreach Loop
                                break;
                            default:
                                Log::info('no event tracking report name');
                                break;
                        }// End switch Statement

                        if (!empty($logDataToInsertDb)) {
                            $ppm = new ProductPreviewModel();
                            foreach (array_chunk($logDataToInsertDb, 50) as $data) {
                                $ppm->insertOrUpdate($data);
                            }
                            //ProductPreviewModel::insertEventCronData($logDataToInsertDb);
                            unset($logDataToInsertDb);
                            unset($logData);
                        }// End If Statement
                    } // End If Statement
                } // End foreach
            } // End Cron Event Data
        }
        } catch (\Exception $ex) {
            Log::info('Event Tracking' . $ex->getMessage());
        }
    }
    /**
     * This function is used to log event when price changes to previous price change
     * @param $td
     * @param $previousDayDate
     * @param $scd
     * @param $key
     * @return array
     */
    public function priceChange($td, $previousDayDate, $scd, $key, $asinAccountId)
    {
        $logData = [];
        $previousDayData = $this->findPreviousDayData($td->asin, $previousDayDate, 0);
        if (!empty($previousDayData)) {
            $newPrice = $td->offerPrice;
            $oldPrice = $previousDayData->offerPrice;
            if ($oldPrice != 0.00 || $newPrice != 0.00) {
                $promoOrNot = ($td->isPromo == true) ? 'Promotional' : 'Non Promotional';
                // This check if price change decrease
                if ($oldPrice != 0) { // oldPrice was 0
                    $percentage = getPercentage($oldPrice, $newPrice);
                    if ($percentage < -5) {
                        $logData = $this->eventLoggingData($td, $scd->id, $asinAccountId);
                        $logData['notes'] = 'price change decrease ' . $percentage . '%' . PHP_EOL . ' Old Price ' . $oldPrice . ' New Price ' . $newPrice . PHP_EOL . $promoOrNot;
                    }// End if
                    // This check if price change increase
                    if ($percentage > 5) {
                        $logData = $this->eventLoggingData($td, $scd->id, $asinAccountId);
                        $logData['notes'] = 'price change increase ' . $percentage . '%' . PHP_EOL . ' Old Price ' . $oldPrice . ' New Price ' . $newPrice . PHP_EOL . $promoOrNot;
                    }// End if
                }// End if
            } // End if
        }
        return $logData;
    }

    /**
     * This Function is used to log event if content Performance
     * @param $td
     * @param $previousDayDate
     * @param $scd
     * @return array
     */
    public function contentPerformance($td, $scd, $asinAccountId)
    {
        $logData = [];
            $todayBulletCount = $td->bulletCount;
            $todayImagesCount = $td->imageCount;  // Are there 5 images?
            $todayCheckAPlusCheck = $td->aplusModule;// Is there A+ content active on page?
            $todayVideoAvailable = $td->videoCount; // Is there video?
            // flags
            $bulletCountStatus = 'NO.';
            $imagesCountStatus = 'NO.';
            $CheckAPlusCheckStatus = 'NO.';
            $todayVideoCheckStatus = 'NO.';
            if ($todayBulletCount == 5) {//Are there 5 bulletpoints?
                $bulletCountStatus = 'Yes.';
            }
            if ($todayImagesCount == 5) { //Are there 5 images?
                $imagesCountStatus = 'Yes.';
            }
            if ($todayCheckAPlusCheck == 1) { //Is there A+ content active on page?
                $CheckAPlusCheckStatus = 'Yes.';
            }
            if ($todayVideoAvailable == 1) { //Is there video?
                $todayVideoCheckStatus = 'Yes.';
            }
            if ($bulletCountStatus == 'NO.' || $imagesCountStatus == 'NO.' || $CheckAPlusCheckStatus == 'NO.' || $todayVideoCheckStatus == 'NO.') {
                $logData = $this->eventLoggingData($td, $scd->id, $asinAccountId);
                $logData['notes'] = 'Content Performance Status ';
                $logData['notes'] .= PHP_EOL . ' Are there 5 bulletpoints? ' . $bulletCountStatus;
                $logData['notes'] .= PHP_EOL . ' Are there 5 images? ' . $imagesCountStatus;
                $logData['notes'] .= PHP_EOL . ' Is there A+ content active on page? ' . $CheckAPlusCheckStatus;
                $logData['notes'] .= PHP_EOL . ' Is there video? ' . $todayVideoCheckStatus;
            }
        return $logData;
    }

    /**
     * @param $td
     * @param $previousDayDate
     * @param $scd
     * @return array|mixed
     */
    public function productNotFound($asinValue, $scd,$asinAccountId)
    {
        $logData = [];
        // chekc 404 from table "tbl_asins_instant_fail_statuses",if record found then log event
        $logData = $this->eventLoggingData($asinValue, 3, $asinAccountId);
        $logData['notes'] = 'Product Not Found Status ';
        $logData['notes'] .= PHP_EOL . ' AISN : ' . $asinValue;

        return $logData;
    }

    /**
     * @param $td
     * @param $previousDayDate
     * @param $scd
     * @return array|mixed
     */
    public function contentChange($td, $previousDayDate, $scd, $asinAccountId)
    {
        Log::info('Content Change ASIN : ' . $td->asin);
        $logData = [];
        $previousDayData = $this->findPreviousDayData($td->asin, $previousDayDate, 0);
        if (!empty($previousDayData)) {
            $todayProductTitle = $td->title; // Did product title change?
            $todayImagesCount = $td->imageCount;  // Did number of images change?
            $todayVideoAvailable = $td->videoCount; // Is there video?
            $todayCategoryListChange = $td->breadcrumbs; // Did category listings change?
            $todayBulletsChange = $td->bullets; // Did bullet points change?
            // previous data
            $previousProductTitle = $previousDayData->title; // Did product title change?
            $previousImagesCount = $previousDayData->imageCount;  // Did number of images change?
            $previousCategoryListChange = $previousDayData->breadcrumbs; // Did category listings change?
            $previousVideoCountChange = $previousDayData->videoCount; // Did category listings change?
            $previousBulletsChange = $previousDayData->bullets; // Did bullet points change?
            // flags
            $bulletStatusChange = 'NO.';
            $productTitleCheckStatus = 'NO.';
            $categoryListChangeCheckStatus = 'NO.';
            $imageCountCurrentChange = 'NO.';
            $videoChangeCheckStatus = 'NO.';
            if (strcmp($todayProductTitle, $previousProductTitle) !== 0) {// Did product title change?
                $productTitleCheckStatus = 'Yes.';
            }
            if (strcmp($todayCategoryListChange, $previousCategoryListChange) !== 0) {// Did category listings change?
                $categoryListChangeCheckStatus = 'Yes.';
            }
            if ($previousImagesCount != $todayImagesCount) { // Did number of images change?
                $imageCountCurrentChange = 'Yes.';
            }
            if ($previousVideoCountChange != $todayVideoAvailable) {// Did video change?
                $videoChangeCheckStatus = 'Yes.';
            }
            if (strcmp($todayBulletsChange, $previousBulletsChange) !== 0) {// Did bullet points change?
                $bulletStatusChange = 'Yes.';
            }
            if ($productTitleCheckStatus == 'Yes.' || $categoryListChangeCheckStatus == 'Yes.' || $imageCountCurrentChange == 'Yes.' || $videoChangeCheckStatus == 'Yes.' || $bulletStatusChange == 'Yes.') {
                $logData = $this->eventLoggingData($td, $scd->id, $asinAccountId);
                $logData['notes'] = 'Content Change Status ';
                $logData['notes'] .= PHP_EOL . ' Did product title change? ' . $productTitleCheckStatus;
                $logData['notes'] .= PHP_EOL . ' Did category listings change? ' . $categoryListChangeCheckStatus;
                $logData['notes'] .= PHP_EOL . ' Did number of images change? ' . $imageCountCurrentChange;
                $logData['notes'] .= PHP_EOL . ' Did video change? ' . $videoChangeCheckStatus;
                $logData['notes'] .= PHP_EOL . ' Did bullet points change? ' . $bulletStatusChange;
            }// End If
        } // End If
        return $logData;
    }

    /**
     * This Function is used to log event if seller change on the bases of yesterday seller name
     * @param $td
     * @param $previousDayDate
     * @param $scd
     * @return array
     */
    public function sellerChange($td, $previousDayDate, $scd, $asinAccountId)
    {
        $logData = [];
        $previousDayData = $this->findPreviousDayData($td->asin, $previousDayDate, 0);
        if (!empty($previousDayData)) {
            $newSeller = $td->seller;
            $oldSeller = $previousDayData->seller;
            if ($newSeller != 'NA' || $oldSeller != 'NA') {
                $similarityPercentage = checkSimilarText($oldSeller, $newSeller);
                if ($similarityPercentage < 100) {
                    $logData = $this->eventLoggingData($td, $scd->id, $asinAccountId);
                    $logData['notes'] = 'seller change ' . PHP_EOL . ' Old Seller ' . $oldSeller . ' New Seller ' . $newSeller;
                }
            }
        } // End If
        return $logData;
    }

    /**
     * This function is used to log event when if review change on base of yesterday data
     * @param $td
     * @param $previousDayDate
     * @param $scd
     * @return array|mixed
     */
    public function reviewChange($td, $previousDayDate, $scd,$asinAccountId)
    {
        $logData = [];
        $previousDayData = $this->findPreviousDayData($td->asin, $previousDayDate, 0);
        if (!empty($previousDayData)) {
            $newReviewCount = intval(str_replace(",", "", $td->totalReviews));
            $oldReviewCount = intval(str_replace(",", "", $previousDayData->totalReviews));

           $reviewDifference = $newReviewCount - $oldReviewCount;
            // Event will be log if total review changes more than 15 and if those reviews change the average Review value.
            if ($newReviewCount > $oldReviewCount && $reviewDifference >= 15) {
                $newAverageReviewCount = $td->averageReview;
                 $oldAverageReviewCount = $previousDayData->averageReview;
                if ($newAverageReviewCount > $oldAverageReviewCount || $newAverageReviewCount < $oldAverageReviewCount) {
                    $logData = $this->eventLoggingData($td, $scd->id,$asinAccountId);
                    $logData['notes'] = 'Review change ' . PHP_EOL . ' Old Average Review ' . $oldAverageReviewCount . ' New Average Review ' . $newAverageReviewCount;
                } // End If statement Average Count
            } // End if  statement Total Review Count
        } // End If

        return $logData;
    }

    /**
     * @param $capturedDate
     * @return \Illuminate\Support\Collection
     */
    public function findTodayData($capturedDate, $eventType, $collectionIds)
    {
        switch ($eventType) {
            case 2:
                return ScrapModel::join('tbl_active_asins', 'tbl_asins_result.asin', '=', 'tbl_active_asins.asin')
                    ->select('tbl_asins_result.*', 'tbl_active_asins.accountId')
                    ->groupBy('tbl_asins_result.asin')
                    ->whereIn("c_id", $collectionIds)
                    ->get();
            break;
            case 3:
                if(!empty($collectionIds)){
                    return InstantASINFailStatusModel::select('failed_data')->whereIn("c_id", $collectionIds)->where('failed_reason','["404 Product Not Found"]')->get();

                }
                break;
            default:
                return ScrapModel::select('id','c_id','url','asin','asinExists','capturedAt','metaKeywords','brand','offerPriceOrignal','offerPrice','listPriceOrignal','listPrice',
                    'offerCount','modelNo','breadcrumbs','category','title','images','imageCount','videoCount','bullets','bulletCount','avgWordsPerBulitCount',
                    'aplus','aplusModule','averageReview','totalReviews','bestSellerRank','bestSellerCategory','isPrime','isAvailable',
                    'availabilityMessage','isAmazonChoice','amazonChoiceTerm','isPromo','seller','size','color','weight','length','width','height',
                    'shipWeight','dateFirstAvailable','fkAsinId')->with('getAsinAccounts.accounts:id,accountName')
                    ->groupBy('asin')
                    ->whereIn("c_id", $collectionIds)
                    ->get();
            break;
        }
    }

    /**
     * @param $asin
     * @param $capturedDate
     * @return \Illuminate\Database\Eloquent\Model|\Illuminate\Database\Query\Builder|object|null
     */
    public function findPreviousDayData($asin, $previousDate, $eventType)
    {
        $currentDate = date('Y-m-d');
                $previousDayData= ScrapModel::select('id','c_id','url','asin','asinExists','capturedAt','metaKeywords','brand','offerPriceOrignal','offerPrice','listPriceOrignal','listPrice',
                    'offerCount','modelNo','breadcrumbs','category','title','images','imageCount','videoCount','bullets','bulletCount','avgWordsPerBulitCount',
                    'aplus','aplusModule','averageReview','totalReviews','bestSellerRank','bestSellerCategory','isPrime','isAvailable',
                    'availabilityMessage','isAmazonChoice','amazonChoiceTerm','isPromo','seller','size','color','weight','length','width','height',
                    'shipWeight','dateFirstAvailable','fkAsinId')->where('asin', $asin)
                    ->where('capturedAt', $currentDate)
                    ->orderBy('id', 'desc')
                    ->skip(1)->take(1)->first();
                if (empty($previousDayData)) {
                    $previousDayData= ScrapModel::select('id','c_id','url','asin','asinExists','capturedAt','metaKeywords','brand','offerPriceOrignal','offerPrice','listPriceOrignal','listPrice',
                        'offerCount','modelNo','breadcrumbs','category','title','images','imageCount','videoCount','bullets','bulletCount','avgWordsPerBulitCount',
                        'aplus','aplusModule','averageReview','totalReviews','bestSellerRank','bestSellerCategory','isPrime','isAvailable',
                        'availabilityMessage','isAmazonChoice','amazonChoiceTerm','isPromo','seller','size','color','weight','length','width','height',
                        'shipWeight','dateFirstAvailable','fkAsinId')->where('asin', $asin)
                        ->where('capturedAt', $previousDate)
                        ->orderBy('id', 'desc')
                        ->first();
                }
                return $previousDayData;

    }

    /**
     * @param $asin
     * @param $key
     * @return mixed
     */
    public function eventLoggingData($dataAgainstAsin, $eventId,$asinAccountId)
    {
        switch($eventId){
        case 3:
        $data['asin'] = $asin = $dataAgainstAsin;
        break;
        default:
        $data['asin'] = $asin = $dataAgainstAsin->asin;
        break;
        }
        $data['fkAccountId'] = $asinAccountId;
        $data['fkEventId'] = $eventId;
        $data['occurrenceDate'] = date('Y-m-d');
        $data['uniqueColumn'] = $asin . '|' . $asinAccountId . '|' . $eventId . '|' . date('Y-m-d');
        $data['createdAt'] = date('Y-m-d H:i:s');
        $data['updatedAt'] = date('Y-m-d H:i:s');
        return $data;
    }
}
