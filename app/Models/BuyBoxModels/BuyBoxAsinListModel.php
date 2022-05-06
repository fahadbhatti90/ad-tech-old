<?php

namespace App\Models\BuyBoxModels;


use Illuminate\Database\Eloquent\Model;
use App\Models\BuyBoxModels\BuyBoxScrapResultModel;
use App\Models\BuyBoxModels\UserHierarchy\BuyBoxAccountsAsinModel;

class BuyBoxAsinListModel extends Model
{
   
    protected $table = "tbl_buybox_asin_list";
    
    public $timestamps = false;
    
    public static function checkAsinExists($cronName){
       return BuyBoxAsinListModel::where("cNameBuybox",$cronName)
        ->exists();
    }//end function
    public static function getAsin($cronName){
       return BuyBoxAsinListModel::has("getAsinAccounts")->where("cNameBuybox",$cronName)
        ->select("id",'asinCode')
        ->get();
    }//end function
    public static function getAsinCount($cronName){
        return BuyBoxAsinListModel::where("cNameBuybox",$cronName)
         ->count();
     }//end function
    /**
     * Relationships
     */
    public function crons()
    {
        return $this->belongsTo('App\Models\BuyBoxModel','cNameBuybox','cNameBuybox');
    }//end function
    public function accounts()
    {
        return $this->setConnection('mysqlDb2')->hasMany('App\Models\ScrapingModels\UserHierarchy\ALLManagerAsinsModel', 'asin',"asinCode");
    }//end function
    /**
     * Get the comments for the blog post.
     */
    public function getAsinAccounts()
    {
        return $this->hasMany(BuyBoxAccountsAsinModel::class,"fkAsinId");
    }
    /**
     * Get the phone record associated with the user.
     */
    public function temp_url()
    {
        return $this->hasOne('App\Models\BuyBoxModels\BuyBoxTempUrlsModel',"fk_bb_asin_list_id","id");
    }
    /**
     * Get the phone record associated with the user.
     */
    public function result()
    {
        return $this->hasOne(BuyBoxScrapResultModel::class,'fkAsinId');
    }
}//end class
