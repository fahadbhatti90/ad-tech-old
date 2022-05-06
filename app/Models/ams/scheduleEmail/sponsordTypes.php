<?php

namespace App\Models\ams\scheduleEmail;

use Illuminate\Database\Eloquent\Model;

class sponsordTypes extends Model
{
    public $table = "tbl_ams_sponsored_types";
    protected $fillable = [
        'sponsordTypenName',
        'isActive'
    ];
   public function schedule()
    {
        return $this->belongsToMany('App\Models\ams\scheduleEmail\scheduleAdvertisingReports','tbl_ams_schedule_selected_email_sponsored_types','fkReportScheduleId','fkSponsordTypeId');
       
    }
}
