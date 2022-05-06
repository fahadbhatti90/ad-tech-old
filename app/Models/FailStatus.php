<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\AccountModels\AccountModel;
use App\Models\ScrapingModels\UserHierarchy\AccountsAsinModel;

class FailStatus extends Model
{
    protected $table="tbl_fail_statuses";
    protected $fillable = [
       'failed_data', 'failed_reason', 'failed_at', 'crawler_id','created_at'
    ];
    public $timestamps = false;
    public static function UpdateNewFailStatues(){
        FailStatus::where("isNew",1)
        ->update([
            "isNew" => 0
        ]);
    }//end function
    public function accounts()
    {
        return $this->belongsTo(AccountModel::class,"fkAccountId","id");
    }
    public static function getDailyScrapingAsinFailStatus(){
        $dataAccountBased = [];
        $data = self::select("failed_data","failed_reason","fkAccountId")
        ->where("isNew",1)
        ->get();
        foreach ($data as $key => $value) {
            $dataAccountBased["null"]["null"][] = $value;
        }
        return $dataAccountBased;
    }
    
}//end class
