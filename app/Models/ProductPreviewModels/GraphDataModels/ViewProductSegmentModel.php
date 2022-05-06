<?php

namespace App\Models\ProductPreviewModels\GraphDataModels;

use Illuminate\Database\Eloquent\Model;

class ViewProductSegmentModel extends Model
{
    public $table = "view_product_asin_segments";
    public static $tableName =  "view_product_asin_segments";
    public $timestamps = false;
    public static function getCompleteTableName(){
        return getDbAndConnectionName("db1").".".self::$tableName;
    }
}
