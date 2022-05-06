<?php

namespace App\Models\Vissuals;

use Illuminate\Database\Eloquent\Model;
use App\Models\AccountModels\AccountModel;

class VissualsProfile extends Model
{
    public $table = "tbl_ams_profiles";
    public function accounts()
    {
        return $this->setConnection(\getDbAndConnectionName("c1"))->belongsTo(AccountModel::class, 'id',"fkId")->where ("fkAccountType",1);
    }//end function
}
