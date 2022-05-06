<?php

namespace App\Models\ams\Report\Link\Adgroup\SD;

use Illuminate\Database\Eloquent\Model;

class AdgroupSDModel extends Model
{
    protected $table = "tbl_ams_adgroup_reports_download_links_sd";
    protected $primaryKey = 'id';
    protected $fillable = [
        'fkBatchId',
        'fkAccountId',
        'profileID',
        'fkConfigId',
        'reportId',
        'status',
        'statusDetails',
        'location',
        'fileSize',
        'reportDate',
        'creationDate',
        'isDone'
    ];
    public $timestamps = false;
}
