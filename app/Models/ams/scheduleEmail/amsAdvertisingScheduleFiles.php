<?php

namespace App\Models\ams\scheduleEmail;

use Illuminate\Database\Eloquent\Model;

class amsAdvertisingScheduleFiles extends Model
{
    public $table = "tbl_ams_advertising_schedule_files";
    protected $fillable = [
        'fkScheduleId',
        'fkParameterTypeId',
        'parameterTypeName',
        'time',
        'date',
        'fileName',
        'filePath',
        'completeFilePath',
        'devServerLink',
        'apiServerLink',
        'isDataFound',
        'isFileDeleted'
    ];
}
