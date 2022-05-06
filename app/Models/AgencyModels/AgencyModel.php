<?php

namespace App\Models\AgencyModels;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;

class AgencyModel extends Authenticatable
{
    use Notifiable;
    public $table = "tbl_agency";

    protected $guard = "agency";
    
    public $type = "agency";
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];
    public function clients()
    {
        return $this->hasMany('App\Models\ClientModels\ClientModel','fkAgencyId');
    }//end function
}//end class
