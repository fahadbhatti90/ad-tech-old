<?php

namespace App\Models\BuyBoxModels;

use Illuminate\Database\Eloquent\Model;

class BuyBoxActivityTrackerModel extends Model
{
    protected $table="tbl_buybox_activity_tracker";
    protected $fillable =[
        'activity','activity_type','cron_type','file_path','activity_time',
    ];
    public static function setActivity($activity,$activity_type,$cron_type,$file_path,$activity_time){
        $data = [
           /* 'activity'=> preg_replace("/[^a-zA-Z0-9 ]+/", "", $activity),*/'activity'=> $activity,'activity_type'=>$activity_type,'cron_type'=>$cron_type,'file_path'=>$file_path,'activity_time'=>$activity_time,
        ];
        BuyBoxActivityTrackerModel::create($data);
    }
    public $timestamps = false;

}//end class model

