<?php

namespace App\Models\ams\Report\Link\Keyword\SB;

use Illuminate\Database\Eloquent\Model;

class KeywordSBModel extends Model
{
    protected $table = "tbl_ams_keyword_reports_download_links_sb";
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
