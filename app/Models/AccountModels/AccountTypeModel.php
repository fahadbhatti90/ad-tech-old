<?php

namespace App\Models\AccountModels;

use Illuminate\Database\Eloquent\Model;

class AccountTypeModel extends Model
{
    public $table = "tbl_account_type";

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name'
    ];

    public function accounts()
    {
        
        return $this->hasMany('App\Models\AccountModels\AccountModel','fkAccountType');

    }//end function
}
