<?php

namespace App\Models\ams\scheduleEmail;

use Illuminate\Database\Eloquent\Model;

class amsReportsMetrics extends Model
{
    public $table = "tbl_ams_reports_metrics";
    protected $fillable = [
        'metricName',
        'tblColumnName',
        'fkParameterType',
        'isActive'
    ];
    public function schedule()
    {
        return $this->belongsToMany('app\Models\ams\scheduleEmail\scheduleAdvertisingReports','tbl_ams_scheduled_email_reports_metrics','fkReportScheduleId','fkReportMetricId');
       
    }
}
