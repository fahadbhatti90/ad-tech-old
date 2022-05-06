<?php

namespace App\Models\BatchIdModels;

use Illuminate\Database\Eloquent\Model;

class BatchIdModel extends Model
{
    public $table = "tbl_batch_id";

    public function accounts()
    {
        return $this->belongsTo('App\Models\AccountModels\AccountModel','fkAccountId');
    }//end function
}
