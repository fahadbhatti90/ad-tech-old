<?php

namespace App\Models\ScrapingModels;

use App\Models\ScrapingModels\UserHierarchy\AccountsAsinModel;
use Illuminate\Database\Eloquent\Model;
use App\Http\Controllers;

use DB;
use App\Quotation;

class ScrapModel extends Model
{
    protected $table = "tbl_asins_result";
    public $timestamps = false;

    public static function deleteCollectionScrapResult($c_id){
        $scrapModel = ScrapModel::where("c_id",$c_id);
        if($scrapModel->exists()){
            return $scrapModel->delete();
        }
        return true;
    }

    public function InsertAsins($data){
       return DB::table('c26s_temp_delta_asins')->insert($data);
    }


    public function InsertResults($data){
        if($data != null){
            if(count($data) > 0 ){
                DB::table('tbl_asins_result')->insert($data);
            }        
        }
    }

    public function asin_result_collection()
    {
        return $this->belongsTo('App\Models\ScrapingModels\CollectionsModel','c_id');
    }//end function

    /**
     * Get Asins Account.
     */
    public function getAsinAccounts()
    {
        return $this->hasMany(AccountsAsinModel::class,"fkAsinId","fkAsinId");
    }
}
