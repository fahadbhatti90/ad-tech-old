<?php

namespace App\Models\ams\Report\Link\ProductsAds\SP;

use Illuminate\Database\Eloquent\Model;

class ProductsAdsSPModel extends Model
{
    protected $table = "tbl_ams_productsads_reports_download_links";
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
