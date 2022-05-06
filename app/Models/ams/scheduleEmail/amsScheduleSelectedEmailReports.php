<?php

namespace App\Models\ams\scheduleEmail;

use Illuminate\Database\Eloquent\Model;

class amsScheduleSelectedEmailReports extends Model
{
    public $table = "tbl_ams_schedule_selected_email_reports";
    protected $fillable = [
        'fkReportScheduleId',
        'fkSponsordTypeId',
        'fkReportId',
        'fkParameterType'
    ];
}
