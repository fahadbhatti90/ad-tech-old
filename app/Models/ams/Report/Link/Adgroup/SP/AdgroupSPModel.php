<?php

namespace App\Models\ams\Report\Link\Adgroup\SP;

use Illuminate\Database\Eloquent\Model;

class AdgroupSPModel extends Model
{
    protected $table = "tbl_ams_adgroup_reports_download_links_sp";
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
