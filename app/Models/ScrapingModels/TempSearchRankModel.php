<?php

namespace App\Models\ScrapingModels;

use Illuminate\Database\Eloquent\Model;

class TempSearchRankModel extends Model
{
    protected $table="tbl_search_rank_temp_urls";
    public $timestamps = false;
    //custom function
    public static function DeleteTempUrl($id){
        $tempUrl = TempSearchRankModel::where("id",$id);
        if($tempUrl->exists()){
            $tempUrl->delete();
        }
        return true;
    }//end function
  
    public static function ResetSmallErrors(){
        TempSearchRankModel::where("urlStatus","<>","-5")
        ->update([
            "urlStatus"=>"0"
        ]);
    }
    public static function Reset503Error(){
        TempSearchRankModel::where("urlStatus","==","-5")
        ->update([
            "urlStatus"=>"0"
        ]);
    }

    /**
     * 
     * Relationships
     * 
     */
    public function department()
    {
        return $this->belongsTo('App\Models\ScrapingModels\DepartmentModel','department_id');
    }//end function
    public function searchTerm()
    {
        return $this->belongsTo('App\Models\ScrapingModels\SearchTermModel','searchTerm_id');
    }//end function
    /**
     * 
     * Relationships
     * 
     */
}//end class
