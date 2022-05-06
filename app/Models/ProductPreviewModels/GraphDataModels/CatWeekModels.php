<?php

namespace App\Models\ProductPreviewModels\GraphDataModels;

use Illuminate\Database\Eloquent\Model;

class CatWeekModels extends Model
{
    protected $connection = 'mysqlDb2';
    public $table = "tbl_cat_vew_weekly";
}
