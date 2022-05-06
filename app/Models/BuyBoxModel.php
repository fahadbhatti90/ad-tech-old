<?php

namespace App\Models;

use App\Notifications\BuyBoxAlert;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use App\Models\BuyBoxModels\BuyBoxAsinListModel;

/**
 * Class BuyBoxModel
 * @package App\Models
 */
class BuyBoxModel extends Model
{
    use Notifiable;
    public $table = "tbl_buybox_cron";
    
    public $timestamps = false;
    public static function boot() {
        parent::boot();

        static::deleting(function($buyboxCollect) { // before delete() method call this
            $buyboxCollect->tempUrls()->delete();
            $buyboxCollect->asins()->delete();
            // do the rest of the cleanup...
        });
    }
    /**
     * Relationships
     */
    public function tempUrls()
    {
        return $this->hasMany('App\Models\BuyBoxModels\BuyBoxTempUrlsModel','fk_bbc_id');
    }//end function
    public function asins()
    {
        return $this->hasMany('App\Models\BuyBoxModels\BuyBoxAsinListModel','cNameBuybox','cNameBuybox');
    }//end function
    public function results()
    {
        return $this->hasMany('App\Models\BuyBoxModels\BuyBoxScrapResultModel','fkCollection','cNameBuybox');
    }//end function
    public function getSoldByAlerts()
    {
        return $this->hasMany('App\Models\BuyBoxModels\BuyBoxScrapResultModel','fkCollection','cNameBuybox')
        ->where("soldByAlert",1);
    }//end function
    public function getOutOfStockAlerts()
    {
        return $this->hasMany('App\Models\BuyBoxModels\BuyBoxScrapResultModel','fkCollection','cNameBuybox')
        ->where("stockAlert",1);
    }//end function




    /**
     * Relationships
     */
    
    /**
     * Route notifications for the Slack channel.
     *
     * @param  \Illuminate\Notifications\Notification  $notification
     * @return string
     */
    public function routeNotificationForSlack($notification)
    {
        return 'https://hooks.slack.com/services/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';
    }

    public static function getTotalAsinCount($cronName){
        if(BuyBoxAsinListModel::checkAsinExists($cronName))
        {
            return BuyBoxAsinListModel::getAsinCount($cronName);
        }
        return 0;
    }//end function

    public static function checkValidCronForBuyBox($current_date, $current_hour){
        return BuyBoxModel::where("cronStatus",0)   
         ->where("nextRun", "=", [$current_date])
         ->where("duration", ">=",$current_date) 
         ->where("nextRunTime", "=", [$current_hour])
         ->exists();
     }
    public static function getValidCronForBuyBox($current_date, $current_hour){
        return BuyBoxModel::where("cronStatus",0)   
         ->where("nextRun", "=", [$current_date])
         ->where("duration", ">=",$current_date) 
         ->where("nextRunTime", "=", [$current_hour])
        ->get();
    }
    public static function getRunningCrons()
    {
        return BuyBoxModel::where("cronStatus",1) 
        ->get();
    }
    public static function checkRunningCrons()
    {
        return BuyBoxModel::where("cronStatus",1) 
        ->exists();
    }
    public static function getRunningCronsWithAlerts()
    {
        return BuyBoxModel::with([
            "getSoldByAlerts" => function($query){
              $query->where("frequency",2);
            }, 
            "getOutOfStockAlerts" => function($query){
                $query->where("frequency",2);
            }
          ])->get();
    }
 
    /**
     * @return mixed
     */
    public static function getFrequnecyList()
    {
        return DB::table('tbl_buybox_cron')
            ->get();
    }

    /**
     * @param $collection_name
     * @return bool
     */
    public static function deleteRecord($collection_name)
    {
        DB::table('tbl_buybox_cron')->where('cNameBuybox', '=', $collection_name)->delete();
        DB::table('tbl_buybox_asin_list')->where('cNameBuybox', '=', $collection_name)->delete();
        return TRUE;
    }

    /**
     * @param $asin
     * @return mixed
     */
    public static function getASINStatus($asin)
    {
        return DB::table('tbl_buybox_asin_list')
            ->where('asinCode', $asin)
            ->where('scrapStatus', 0)
            ->get();
    }

    /**
     * @param $limit
     * @param $offset
     * @return mixed
     */
    public function getASINForThreading($collectionName, $limit, $offset)
    {
        return DB::table('tbl_buybox_asin_list')
            ->where('cNameBuybox', $collectionName)
            ->where('scrapStatus', 0)
            ->orWhere('scrapStatus', 2)
            ->offset($offset)
            ->limit($limit)
            ->get();
    }

    /**
     * @return mixed
     */
    public static function geBuyBoxCronList()
    {
        return DB::table("tbl_buybox_cron")
            ->where('cronStatus', 0)
            ->get();
    }

    /**
     * @return mixed
     */
    public function getAsinBatch()
    {
        return DB::table("tbl_buybox_asin_list")
            ->where('scrapStatus', 0)
            ->orWhere('scrapStatus', 2)
            ->get();
    }

    /**
     * @param $email
     * @param $c_name_buybox
     * @param $frequency
     * @param $duration
     * @param $collection
     * @return bool
     */
    public static function insertRecord($email, $c_name_buybox, $frequency, $duration, $collection,$runData)
    {
        $InsertArray = [];
        $cronData = [];
        $Record = DB::table("tbl_buybox_asin_list")
            ->where('cNameBuybox', '=', $c_name_buybox)
            ->exists();
        if (!$Record) {
            // record doesn't exist
            $nextRun = date("Y-m-d");
            $nextRunTime = date("H", strtotime("+ 1 hours"));
            $hoursToAdd = $runData["hoursToAdd"];
            $CronDuration = date("Y-m-d", strtotime("+" . $duration . " week"));
            $cronData = [
                'email' => trim($email),
                'cNameBuybox' => trim($c_name_buybox),
                'frequency' => $frequency,
                'currentFrequency' => $frequency,
                'duration' => $CronDuration,
                'nextRun' => $nextRun,
                'nextRunTime' => $nextRunTime,
                'hoursToAdd' => $hoursToAdd,
                'cronStatus' => 0,
                'createdAt' => date('Y-m-d H:i:s'),
                'updatedAt' => date('Y-m-d H:i:s')
            ];
            
            try {
                DB::beginTransaction();
                DB::table('tbl_buybox_cron')->insert($cronData);
                //insert buybox scraper detail
                foreach ($collection->unique() as $row) {
                    $single=[];
                    $single['cNameBuybox'] = $c_name_buybox;
                    $single['frequency'] = $frequency;
                    $single['asinCode'] = $row['asin'];
                    $single['duration'] = $duration;
                    $single['scrapStatus'] = 0;
                    $single['createdAt'] = date('Y-m-d H:i:s');
                    $single['updatedAt'] = date('Y-m-d H:i:s');
                    array_push($InsertArray, $single);
                    if($InsertArray >=1000)
                    {
                        BuyBoxAsinListModel::insert($InsertArray);
                        $InsertArray = [];
                    }
                }
                if(count($InsertArray) > 0){
                    BuyBoxAsinListModel::insert($InsertArray);
                }
                DB::commit();
                return TRUE;
            } catch (\Illuminate\Database\QueryException $ex) {
                DB::rollBack();
                Log::error($ex->getMessage());
                Log::error($ex->getTrace());
            }
        }
        else 
        {
            Log::error("No Record ".json_encode($Record));
            return FALSE;
        }
    }

    /**
     * @return mixed
     * This function is used to export data into excel file
     *
     */
    public static function allScrapData()
    {
        return DB::table("tbl_buybox_asin_scraped")
            ->select('fkCollection as collectionName', 'brand', 'soldBy', 'price', 'prime', 'stock', 'asinCode', 'stockAlert', 'soldByAlert')
            ->get();
    }

    /**
     * This function is used to data for slack alert
     */
    public static function getAlertScrapeData()
    {
        return DB::table("tbl_buybox_asin_scraped")
            ->get();
    }

    /**
     * @param $Asin
     */
    public static function updateAsinStatus($Asin, $status)
    {
        try {
            DB::table('tbl_buybox_asin_list')
                ->where('asinCode', $Asin)
                ->update(['scrapStatus' => $status]);
        } catch (\Illuminate\Database\QueryException $ex) {
            Log::error($ex->getMessage());
        }
    }

    /**
     * @param $DBArray
     */
    public static function insertScrapedRecord($DBArray)
    {
        try {
            DB::table("tbl_buybox_asin_scraped")
            ->insert($DBArray);
            BuyBoxModel::updateAsinStatus($DBArray['asinCode'], 1);
        } catch (\Illuminate\Database\QueryException $ex) {
            dd($ex->getMessage());
            Log::error($ex->getMessage());
        }
    }
}