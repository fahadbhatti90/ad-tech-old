<?php

namespace App\Models\ams\scheduleEmail;

use Illuminate\Database\Eloquent\Model;

class amsScheduleSelectedEmailSponsordTypes extends Model
{
    public $table = "tbl_ams_schedule_selected_email_sponsored_types";
    protected $fillable = [
        'fkReportScheduleId',
        'fkSponsordTypeId'
    ];
}
