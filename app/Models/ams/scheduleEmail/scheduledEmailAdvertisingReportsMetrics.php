<?php

namespace App\Models\ams\scheduleEmail;

use Illuminate\Database\Eloquent\Model;

class scheduledEmailAdvertisingReportsMetrics extends Model
{
    public $table = "tbl_ams_scheduled_email_reports_metrics";
    protected $fillable = [
        'fkReportScheduleId',
        'fkReportMetricId',
        'fkParameterType'
    ];
 /**
     * @return $data
     * @uses in 
     app\Models\ams\scheduleEmail\scheduleAdvertisingReports
     * relationship with ams scheduled reports based upon  selected metrics 
     */
     public function amsScheduledReport()
    {
    return $this->belongsTo('App\Models\ams\scheduleEmail\scheduleAdvertisingReports', 'fkReportMetricId');
    }
}
