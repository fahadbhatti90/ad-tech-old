<?php

namespace App\Models\ScrapingModels;

use Illuminate\Database\Eloquent\Model;

class CronModel extends Model
{
    protected $table="tbl_schedule_cron";
    protected $fillable = [
       'c_id', 'cronStatus', 'cronDuration','created_at'
    ];
    public $timestamps = false;

    public static function getScheduleds($current_date){
        return CronModel::where("cronStatus","run")   
        ->where("lastRun", "<>", [$current_date])
            ->where(function ($query) use ($current_date) {
                $query->where('cronDuration', ">=", $current_date)->orWhere('cronDuration', '0000-00-00');
            })
        ->get();
    }
    public static function checkAvailableSchedules($current_date){
        return CronModel::where("cronStatus","run")
        ->where("lastRun", "<>", [$current_date])
                ->where(function ($query) use ($current_date) {
                    $query->where('cronDuration', ">=", $current_date)->orWhere('cronDuration', '0000-00-00');
        })
        ->count() > 0;
    }
    public static function isAnySchedulRunning(){
       return CronModel::where("isRunning",1)
       ->count() <= 0;
    }
    public function asin_collection()
    {
        return $this->belongsTo('App\Models\ScrapingModels\CollectionsModel','c_id');
    }//end function
    
}//end model class
