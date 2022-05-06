<?php

namespace App\Console\Commands\EventTracking;

use App\models\ActiveAsin;
use App\Models\EventTracking\CronEvent;
use App\Models\ProductPreviewModels\EventsModel;
use App\Models\ProductPreviewModels\ProductPreviewModel;
use App\models\ScInventoryFbaHealthReport;
use App\Models\ScrapingModels\asinModel;
use App\models\VcDailyStageInventory;
use App\Providers\EventServiceProvider;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use App\Models\ScrapingModels\ScrapModel;
use mysql_xdevapi\Exception;

class eventTrackingOutOtStock extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'eventTrackingCron:OOS {type} {date?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'This Command is used to log event of out of stock.';

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
        ini_set('memory_limit', '-1');
        $type = $this->argument('type');
        $date = $this->argument('date'); // to get specific event date
        $logDataToInsertDb = [];
        $getActiveAsinList = ActiveAsin::get();
        if (!empty($getActiveAsinList)) {
            switch ($type) {
                case 'sc' :
                    $capturedDate = date('Y-m-d', strtotime(' -1 day'));
                    $dataArray = array();
                    foreach ($getActiveAsinList as $singleAsin) {
                        $scData = ScInventoryFbaHealthReport::where('asin', $singleAsin->asin)
                            ->where('reportRequestDate', $capturedDate)
                            ->first(['fkAccountId', 'asin', 'sellableQuantity as sellable']);
                        if (!empty($scData) && $scData->sellable == 0) {
                            array_push($dataArray, $scData);
                        }
                    }
                    if (!empty($dataArray)) {
                        Log::info('Out of Stock in Seller Central Report');
                        foreach ($dataArray as $key => $td) {
                            $logData = [];
                            $logData = $this->eventLoggingData($td->fkAccountId, $td->asin, 6, $capturedDate);
                            $logData['notes'] = 'sellable units on hand value is ' . $td->sellable . ' and the status is Active IN seller central';
                            (!empty($logData)) ? array_push($logDataToInsertDb, $logData) : '';
                        }
                        // End Foreach Loop
                        if (!empty($logDataToInsertDb)) {
                            $ppm = new ProductPreviewModel();
                            foreach (array_chunk($logDataToInsertDb, 50) as $data) {
                                $ppm->insertOrUpdate($data);
                            }
                            unset($logDataToInsertDb);
                            unset($logData);
                        }
                    }
                    break;
                case 'vc' :
                    $dataArray = array();
                    foreach ($getActiveAsinList as $singleAsin) {
                        // get vc data
                        $vcData = VcDailyStageInventory::where('asin', $singleAsin->asin)
                            ->where('rec_date', $date)
                            ->first(['fkAccountId', 'asin', 'sellable_on_hand_units as sellable', 'rec_date']);
                        if (!empty($vcData) && $vcData->sellable == 0) {
                            array_push($dataArray, $vcData);
                        }
                    }
                    if (!empty($dataArray)) {
                        Log::info('Out of Stock in Vendor Central Report');
                        foreach ($dataArray as $key => $td) {
                            $logData = [];
                            $logData = $this->eventLoggingData($td->fkAccountId, $td->asin, 10, $date);
                            $logData['notes'] = 'sellable units on hand value is ' . $td->sellable . ' and the status is Active IN vendor central';
                            (!empty($logData)) ? array_push($logDataToInsertDb, $logData) : '';
                        }
                        // End Foreach Loop
                        if (!empty($logDataToInsertDb)) {
                            $ppm = new ProductPreviewModel();
                            foreach (array_chunk($logDataToInsertDb, 50) as $data) {
                                $ppm->insertOrUpdate($data);
                            }
                            unset($logDataToInsertDb);
                            unset($logData);
                        }
                    }
                    break;
                default:
                    Log::info('no event tracking report name');
                    break;
            }
        }
    }

    /**
     * @param $fkAccountId
     * @param $asin
     * @param $eventId
     * @param $occurrenceDate
     * @return mixed
     */
    public function eventLoggingData($fkAccountId, $asin, $eventId, $occurrenceDate)
    {
        $data['fkAccountId'] = $fkAccountId;
        $data['asin'] = $asin;
        $data['fkEventId'] = $eventId;
        $data['occurrenceDate'] = $occurrenceDate;
        $data['uniqueColumn'] = $asin . '|' . $fkAccountId . '|' . $eventId . '|' . $occurrenceDate;
        $data['createdAt'] = date('Y-m-d H:i:s');
        $data['updatedAt'] = date('Y-m-d H:i:s');
        return $data;
    }

}
