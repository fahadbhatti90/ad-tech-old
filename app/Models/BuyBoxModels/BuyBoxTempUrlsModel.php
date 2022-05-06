<?php

namespace App\Models\BuyBoxModels;

use Illuminate\Database\Eloquent\Model;

class BuyBoxTempUrlsModel extends Model
{
    protected $table = "tbl_buybox_temp_urls";
    
    public $timestamps = false;

    public static function checkValidUrls()
    {
        return BuyBoxTempUrlsModel::where("scrapStatus","<>","-5")
        ->exists();
    }//end function

    public static function check503ValidUrls()
    {
        return BuyBoxTempUrlsModel::where("scrapStatus","=","-5")
        ->exists();
    }//end function
    public static function getValidUrls()
    {
        return BuyBoxTempUrlsModel::where("scrapStatus","<>","-5")
        ->select("id", "fk_bb_asin_list_id")
        ->get();
    }//end function
    public static function setProcessingStatus(){
        BuyBoxTempUrlsModel::where("scrapStatus","0")
        ->update(["scrapStatus"=>"1"]);
    }
    public static function updateValidUrls(){
       return BuyBoxTempUrlsModel::where("scrapStatus","<>","-5")
        ->update(["scrapStatus"=>"0"]);
    }
    public static function deleteTempUrl($id){
        $tempUrl = BuyBoxTempUrlsModel::where("id","=",$id);
        if($tempUrl->exists()){
          return  $tempUrl->delete();
        }//end if
        return true;
     }//end function


    /**
     * Get the user that owns the phone.
     */
    public function asin()
    {
        return $this->belongsTo('App\Models\BuyBoxModels\BuyBoxAsinListModel',"fk_bb_asin_list_id");
    }
    /**
     * Relationships
     */
    public function crons()
    {
        return $this->belongsTo('App\Models\BuyBoxModel','fk_bbc_id');
    }//end function
}//end class
