<?php

namespace App\Models\BuyBoxModels;

use Illuminate\Database\Eloquent\Model;

class BuyBoxFailStatusModel extends Model
{
    protected $table = "tbl_buybox_fail_statuses";
    public $timestamps = false;
    protected $fillable = [
        'failed_data', 'failed_reason', 'failed_at', 'crawler_id','created_at', 'fkAccountId'
     ];
     public static function UpdateNewFailStatues(){
        BuyBoxFailStatusModel::where("isNew",1)
        ->update([
            "isNew" => 0
        ]);
    }//end function
    public static function getBuyBoxFailStatus(){
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
