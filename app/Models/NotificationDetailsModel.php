<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NotificationDetailsModel extends Model
{
    protected $table="tbl_notification_details";
    protected $fillable = [
       'n_id', 'details', 'created_at'
    ];
    public $timestamps = false;
    public function notification()
    {
        return $this->belongsTo('App\Models\NotificationModel','n_id');
    }//end function
   
}//end class
