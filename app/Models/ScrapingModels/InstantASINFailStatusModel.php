<?php

namespace App\Models\ScrapingModels;

use Illuminate\Database\Eloquent\Model;

class InstantASINFailStatusModel extends Model
{
    public $table = 'tbl_asins_instant_fail_statuses';
    public $timestamps = false;

    protected $fillable = [
        'failed_data', 'failed_reason', 'failed_at', 'c_id'
    ];

    public static function getDailyScrapingAsinFailStatus(){
        $dataAccountBased = [];
        $data = self::select("failed_data","failed_reason")
        ->where("isNew",1)
        ->get();
        foreach ($data as $key => $value) {
            $dataAccountBased["null"]["null"][] = $value;
        }
        return $dataAccountBased;
    }
    public static function UpdateNewFailStatues(){
        InstantASINFailStatusModel::where("isNew",1)
        ->update([
            "isNew" => 0
        ]);
    }//end function
}
