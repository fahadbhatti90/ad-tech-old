<?php

namespace App\Models\ams\Report\Link\Campaign\SP;

use Illuminate\Database\Eloquent\Model;

class CampaignSPModel extends Model
{
    protected $table = "tbl_ams_campaigns_reports_download_links_sp";
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
