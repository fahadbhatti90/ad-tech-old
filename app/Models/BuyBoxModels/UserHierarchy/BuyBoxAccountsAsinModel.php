<?php

namespace App\Models\BuyBoxModels\UserHierarchy;

use App\Models\CustomModel;
use App\Models\AccountModels\AccountModel;

class BuyBoxAccountsAsinModel extends CustomModel
{
    public $table = "tbl_buybox_accounts_asins";
    public static $tableName = "tbl_buybox_accounts_asins";
    public $timestamps = false;
    public static function getHirarchyBaseFailStatus(){
        $dataAccountBased = [];
        $data = AccountModel::with("fail_status:failed_data,failed_reason,fkAccountId")->get();
        foreach ($data as $dataKey => $dataValue) {
            foreach ($dataValue->fail_status as $key => $value) {
                $dataAccountBased[$dataValue->fkManagerId==null?"null":$dataValue->fkManagerId][$dataValue->id][] = $value;
            }
        }
        return $dataAccountBased;
    }
    public function fail_status()
    {
        return $this->hasMany('App\Models\BuyBoxModels\BuyBoxFailStatusModel',"fkAccountId","fkAccountId")->where("isNew",1);
    }
    /**
     * Relationships
     */
    public function asin()
    {
        return $this->belongsTo('App\Models\BuyBoxModels\BuyBoxAsinListModel','fkAsinId');
    }//end function
    /**
     * Relationships
     */
    public function results()
    {
        return $this->belongsTo(BuyBoxScrapResultModel::class,'fkAsinId','fkAsinId');
    }//end function
    public function accounts(){
        return $this->belongsTo(AccountModel::class,'fkAccountId');
    }
}
