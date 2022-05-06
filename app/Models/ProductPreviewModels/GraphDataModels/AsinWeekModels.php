<?php

namespace App\Models\ProductPreviewModels\GraphDataModels;

use Illuminate\Database\Eloquent\Model;

class AsinWeekModels extends Model
{
    protected $connection = 'mysqlDb2';
    public $table = "prst_tbl_sales_asin_weekly";
}
