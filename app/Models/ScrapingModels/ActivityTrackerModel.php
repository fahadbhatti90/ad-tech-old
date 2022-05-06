<?php

namespace App\Models\ScrapingModels;

use Illuminate\Database\Eloquent\Model;

class ActivityTrackerModel extends Model
{
    protected $table="tbl_scraping_activity_tracker";
    protected $fillable =[
        'activity','activity_type','cron_type','file_path','activity_time',
    ];
    public static function setActivity($activity,$activity_type,$cron_type,$file_path,$activity_time){
        $data = [
           /* 'activity'=> preg_replace("/[^a-zA-Z0-9 ]+/", "", $activity),*/'activity'=> $activity,'activity_type'=>$activity_type,'cron_type'=>$cron_type,'file_path'=>$file_path,'activity_time'=>$activity_time,
        ];
        ActivityTrackerModel::create($data);
    }
    public $timestamps = false;

}//end class model

