<?php

namespace App\Models\ams\Report\Link\Target\SB;

use Illuminate\Database\Eloquent\Model;

class TargetAdsSBModel extends Model
{
    protected $table = "tbl_ams_targets_reports_download_links_sb";
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
