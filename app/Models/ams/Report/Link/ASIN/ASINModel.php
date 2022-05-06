<?php

namespace App\Models\ams\Report\Link\ASIN;

use Illuminate\Database\Eloquent\Model;

class ASINModel extends Model
{
    protected $table = "tbl_ams_asin_reports_download_links";
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
