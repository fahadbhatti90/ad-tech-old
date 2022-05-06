<?php

namespace App\Models\ams\Report\Link\Adgroup\SB;

use Illuminate\Database\Eloquent\Model;

class AdgroupSBModel extends Model
{
    protected $table = "tbl_ams_adgroup_reports_download_links_sb";
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
