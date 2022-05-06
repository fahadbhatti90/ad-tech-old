<?php

namespace App\Models\ProductPreviewModels\GraphDataModels;

use Illuminate\Database\Eloquent\Model;

class SubCatWeekModels extends Model
{
    protected $connection = 'mysqlDb2';
    public $table = "tbl_cat_subcat_vew_weekly";
}
