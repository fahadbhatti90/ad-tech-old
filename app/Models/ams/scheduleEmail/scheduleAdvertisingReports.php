<?php

namespace App\Models\ams\scheduleEmail;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
class scheduleAdvertisingReports extends Model
{
    use SoftDeletes;
    public $table = "tbl_ams_schedule_advertising_reports";
    protected $fillable = [
        'reportName',
        'amsProfileId',
        'fkProfileId',
        'fkSponsordTypeId',
        'fkReportTypeId',
        'granularity',
        'allMetricsCheck',
        'timeFrameType',
        'timeFrame',
        'time',
        'scheduleDate',
        'cronLastRunDate',
        'cronLastRunTime',
        'addCC',
        'mon',
        'tue',
        'wed',
        'thu',
        'fri',
        'sat',
        'sun',
        'createdBy'
    ];

    /**
     * @return $data
     * @uses in app\Models\ams\scheduleEmail\scheduledEmailAdvertisingReportsMetrics
     * relationship with selected metrics in ams scheduled reports
     */
     public function selectedReportTypes()
    {
        return $this->belongsToMany('App\Models\ams\scheduleEmail\sponsordReports','tbl_ams_schedule_selected_email_reports','fkReportScheduleId','fkReportId');
    }
    public function selectedSponsoredTypes()
    {
        return $this->belongsToMany('App\Models\ams\scheduleEmail\sponsordTypes','tbl_ams_schedule_selected_email_sponsored_types','fkReportScheduleId','fkSponsordTypeId');
    }
    public function selectedReportsMetrics()
    {
        return $this->belongsToMany('App\Models\ams\scheduleEmail\amsReportsMetrics','tbl_ams_scheduled_email_reports_metrics','fkReportScheduleId','fkReportMetricId');
    }
}
