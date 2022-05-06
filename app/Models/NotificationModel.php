<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NotificationModel extends Model
{
    protected $table="tbl_notification";
    protected $fillable = [
       'title', 'type', 'message', 'details', 'created_at',"fkAccountId"
    ];
    public $timestamps = false;
    public function notiDetails()
    {
        return $this->hasMany('App\Models\NotificationDetailsModel','n_id');
    }//end function
    public function account()
    {
        return $this->belongsTo('App\Models\AccountModels\AccountModel', 'fkAccountId');
    }//end function
    /**
    * Retrieve the model for a bound value.
    *
    * @param  mixed  $value
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function resolveRouteBinding($value)
    {
        // $noti = $this->with("account")->where('id', $value)->first();
        // if($noti->account->fkManagerId == auth()->user()->id){
        //     return $noti;
        // }
        return $this->with("account")->where('id', $value)->first() ?? null;
    }//end function
}//end class
