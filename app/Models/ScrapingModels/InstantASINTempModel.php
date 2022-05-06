<?php

namespace App\Models\ScrapingModels;

use Illuminate\Database\Eloquent\Model;

class InstantASINTempModel extends Model
{
   public $table = 'tbl_asins_intant_temp_urls';
   protected $primaryKey = null;
   public $timestamps = false;

   public static function isAlreadyRunning(){
       return (InstantASINTempModel::where('allocatedThread',"<>","NA")->count() > 0);
   }
   public static function DeleteTempASIN($id){
    $tempASIN = InstantASINTempModel::where("asin_id",$id);
    if($tempASIN->exists()){
      return  $tempASIN->delete();
    }
    return false;
}//end function
   public function collection()
   {
       return $this->belongsTo('App\Models\ScrapingModels\CollectionsModel','c_id');
   }//end function
}
