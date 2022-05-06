<?php

namespace App\Console\Commands\EventTracking;

use App\models\ActiveAsin;
use Illuminate\Console\Command;
use App\Models\EventTracking\CronEvent;
use App\Models\ProductPreviewModels\EventsModel;
use App\Models\ProductPreviewModels\ProductPreviewModel;
use App\Models\ScrapingModels\asinModel;
use App\Providers\EventServiceProvider;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use App\Models\ScrapingModels\ScrapModel;
use mysql_xdevapi\Exception;

class eventTrackingOfferNotFound extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'eventTrackingCron:offerNotFound';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'This Command is used to log event of offer not found..';

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

        Log::info('Event Tracking Start ' . $this->description);
        try {
            $cronEventData = EventsModel::select('id', 'eventName')->where('isEventAuto', 1)->where('id',11)->first();
            if (!empty($cronEventData)) {
                    $logDataToInsertDb = [];
                    $eventId = $cronEventData->id;
                    $capturedDate = date('Y-m-d');
                    $previousDayDate = date('Y-m-d', strtotime($capturedDate . ' -1 day'));
                    // Fetch data which scraped today
                    $todayData = $this->findTodayData($capturedDate, $eventId);
                Log::info('Offer Not Found Case Start');
                if (!$todayData->isEmpty()) {
                    Log::info('Offer Not Found.Data found for today');
                    foreach ($todayData as $key => $td) {
                        $asinValue = $td->asin;
                        $asinAccounts = ActiveAsin::select('accountId')->where('asin', $asinValue)->where('accountId', '!=', NULL)->get();
                    if (!$asinAccounts->isEmpty()) {
                    foreach ($asinAccounts as $asinAccount) {
                        $asinAccountId = $asinAccount->accountId;
                        if (!empty($asinAccountId)) {
                        $logData = $this->offerNotFound($td, $eventId,$asinAccountId);
                        (!empty($logData)) ? array_push($logDataToInsertDb, $logData) : '';
                    }
                    }
                    }
                    }// End Foreach Loop
                    } else {
                        Log::info('Offer Not Found.No data found for today');
                    }
                    if (!empty($logDataToInsertDb)) {
                        Log::info('Offer Not Found.Data Ready For Insertion');
                            $ppm = new ProductPreviewModel();
                            $ppm->insertOrUpdate($logDataToInsertDb);
                            //ProductPreviewModel::insertEventCronData($logDataToInsertDb);
                            unset($logDataToInsertDb);
                            unset($logData);
                        Log::info('Data Insertion Completed');
                        }else{
                        Log::info('Offer Not Found.Data Not Ready For Insertion');
                    }// End If Statement
            } // End Cron Event Data
        } catch (\Exception $ex) {
            Log::info('Event Tracking' . $ex->getMessage());
        }
    }

    /**
     * This function is used to log event when offer not found event occur
     * @param $td
     * @param $scd
     * @return array
     */
    private function offerNotFound($todayData, $eventId, $asinAccountId)
    {
        $logData = [];
        try {
            $newOfferPrice = '';
            $newListPrice = '';
            $newAvailabilityMessage = '';
            if (!empty($todayData)) {
                $newOfferPrice = $todayData->offerPrice;
                $newListPrice = $todayData->listPrice;
                $newAvailabilityMessage = $todayData->availabilityMessage;
            }
            /*logic for old data data not found ends*/
            /*logic for current data data not found starts*/
            $newPriceFound = 0;
            $newAvailabilityMessageFound = 0;
            if ($newOfferPrice == '0.00' && $newListPrice == '0.00') {
                $newPriceFound = 1;
            }
            if ($newAvailabilityMessage == 'Currently unavailable') {
                $newAvailabilityMessageFound = 1;
            }
            //check if price and avability message condition is true then log offer not found event
            $newConditionTrue = 0;
            if ($newPriceFound == 1 || $newAvailabilityMessageFound == 1) {
                $logData = $this->eventLoggingData($todayData, $eventId, $asinAccountId);
            }
            /*logic for current data data not found ends*/
            return $logData;
        } catch (Throwable $e) {
            report($e);
            return $logData;
        }
    }

    /**
     * @param $capturedDate
     * @return \Illuminate\Support\Collection
     */
    private function findTodayData($capturedDate, $eventType)
    {
            return ScrapModel::with('getAsinAccounts.accounts:id,accountName')
                ->groupBy('asin')
                ->where('capturedAt', $capturedDate)
                ->get();
    }

    /**
     * @param $asin
     * @param $key
     * @return mixed
     */
    private function eventLoggingData($dataAgainstAsin, $eventId,$asinAccountId)
    {
        $data = [];
        try{
            $data['fkAccountId'] = $asinAccountId ;
            $data['asin'] =  $dataAgainstAsin->asin;
            $data['fkEventId'] = $eventId;
            $data['occurrenceDate'] = date('Y-m-d');
            $data['notes'] = 'Offer not found event.';
            $data['uniqueColumn'] = $dataAgainstAsin->asin . '|' . $asinAccountId . '|' . $eventId . '|' . date('Y-m-d');
            $data['createdAt'] = date('Y-m-d H:i:s');
            $data['updatedAt'] = date('Y-m-d H:i:s');
        return $data;
            } catch (Throwable $e) {
        report($e);
        return $data;
        }
    }

}
