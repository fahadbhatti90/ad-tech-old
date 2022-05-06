<?php

namespace App\Models\ams\Report\Link\Keyword\SP;

use Illuminate\Database\Eloquent\Model;

class KeywordSPModel extends Model
{
    protected $table = "tbl_ams_keyword_reports_download_links_sp";
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
