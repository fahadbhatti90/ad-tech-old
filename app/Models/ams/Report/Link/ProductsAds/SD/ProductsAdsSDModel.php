<?php

namespace App\Models\ams\Report\Link\ProductsAds\SD;

use Illuminate\Database\Eloquent\Model;

class ProductsAdsSDModel extends Model
{
    protected $table = "tbl_ams_productsads_reports_download_links_sd";
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
