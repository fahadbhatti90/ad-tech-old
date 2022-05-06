<?php

namespace App\Models\BuyBoxModels;

use Illuminate\Database\Eloquent\Model;
use App\Models\BuyBoxModels\BuyBoxAsinListModel;
use App\Models\BuyBoxModels\UserHierarchy\BuyBoxAccountsAsinModel;

class BuyBoxScrapResultModel extends Model
{
    protected $table = "tbl_buybox_asin_scraped";
    
    public $timestamps = false;

    public static function checkSoldBuyAlerts($cron){
        return BuyBoxScrapResultModel::where("fkCollection",$cron->cNameBuybox)
        ->where("isNew",1)
        ->where("soldByAlert",1)
        ->exists();
    }
    public static function getSoldBuyAlerts($cron){
        return BuyBoxScrapResultModel::where("fkCollection",$cron->cNameBuybox)
        ->where("isNew",1)
        ->where("soldByAlert",1)
        ->get();
    }
    public static function checkOutOfStockAlerts($cron){
        return BuyBoxScrapResultModel::where("fkCollection",$cron->cNameBuybox)
        ->where("isNew",1)
        ->where("stockAlert",1)
        ->exists();
    }   

    public static function getOutOfStockAlerts($cron){
        return BuyBoxScrapResultModel::where("fkCollection",$cron->cNameBuybox)
        ->where("isNew",1)
        ->where("stockAlert",1)
        ->get();
    }
    public static function updateIsNewStatus(){
        BuyBoxScrapResultModel::where("isNew",1)
        ->update(["isNew"=>0]);
    }
    public static function getSoldByAlertDataUserSpecific($cron){
        $dataAccountBased = [];
        $data = BuyBoxScrapResultModel::has("getResultAccounts")
        ->with("getResultAccounts.accounts")
        ->with("getResultAccounts.accounts:id,fkManagerId")
        ->with("getResultAccounts:fkAccountId,fkAsinId")
        ->where("fkCollection",$cron->cNameBuybox)
        ->where("isNew",1)
        ->where("soldByAlert",1)
        ->get();
        foreach ($data as $key => $value) {
            $fkManagerID = $value->getResultAccounts[0]->fkAccountId==null?
                                "null":
                                $value->getResultAccounts[0]->accounts==null?
                                "null":
                                $value->getResultAccounts[0]->accounts->fkManagerId;
            $dataAccountBased[$fkManagerID == null ? "null":$fkManagerID]
            [$value->getResultAccounts[0]->fkAccountId==null?"null":$value->getResultAccounts[0]->fkAccountId]
            [] = $value;
        }
        return $dataAccountBased;
    }
    public static function getOutOfStockAlertDataUserSpecific($cron){
        $dataAccountBased = [];
        $data = BuyBoxScrapResultModel::has("getResultAccounts")
        ->with("getResultAccounts.accounts")
        ->with("getResultAccounts.accounts:id,fkManagerId")
        ->with("getResultAccounts:fkAccountId,fkAsinId")
        ->where("fkCollection",$cron->cNameBuybox)
        ->where("isNew",1)
        ->where("stockAlert",1)
        ->get();
        foreach ($data as $key => $value) {
            $fkManagerID = $value->getResultAccounts[0]->fkAccountId==null?
            "null":
            $value->getResultAccounts[0]->accounts==null?
            "null":
            $value->getResultAccounts[0]->accounts->fkManagerId;
            $dataAccountBased[$fkManagerID == null ? "null":$fkManagerID]
            [$value->getResultAccounts[0]->fkAccountId==null?"null":$value->getResultAccounts[0]->fkAccountId]
            [] = $value;
        }
        return $dataAccountBased;
    }
    /**
     * Relationships
     */
    public function crons()
    {
        return $this->belongsTo('App\Models\BuyBoxModel','fkCollection','cNameBuybox');
    }//end function
    /**
     * Relationships
     */
    public function asin()
    {
        return $this->belongsTo(BuyBoxAsinListModel::class,'fkAsinId');
    }//end function
    /**
     * Get the comments for the blog post.
     */
    public function getResultAccounts()
    {
        return $this->hasMany(BuyBoxAccountsAsinModel::class,"fkAsinId","fkAsinId");
    }

}
