<?php

namespace App\Models\ScrapingModels\UserHierarchy;

use App\Models\CustomModel;
use App\Models\AccountModels\AccountModel;

class AccountsAsinModel extends CustomModel
{
    public $table = "tbl_accounts_asins";
    public static $tableName = "tbl_accounts_asins";
    public $timestamps = false;
    public function fail_status()
    {
        return $this->hasMany('App\Models\FailStatus',"fkAccountId","fkAccountId")->where("isNew",1);
    }

    public function accounts(){
        return $this->belongsTo(AccountModel::class,'fkAccountId');
    }
}
