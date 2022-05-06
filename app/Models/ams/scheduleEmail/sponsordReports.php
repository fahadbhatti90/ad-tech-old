<?php

namespace App\Models\ams\scheduleEmail;

use Illuminate\Database\Eloquent\Model;

class sponsordReports extends Model
{
    public $table = "tbl_ams_sponsored_reports";
    protected $fillable = [
        'reportName',
        'fkSponsordTypeId',
        'fkParameterType',
        'isActive'
    ];

     public function schedule()
    {
        return $this->belongsToMany('app\Models\ams\scheduleEmail\scheduleAdvertisingReports','tbl_ams_schedule_selected_email_reports','fkReportScheduleId','fkReportId');
       
    }
 }
