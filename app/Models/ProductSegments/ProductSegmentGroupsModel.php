<?php

namespace App\models\ProductSegments;

use Illuminate\Database\Eloquent\Model;
use App\Models\ProductSegments\ProductSegments;

class ProductSegmentGroupsModel extends Model
{
    public $table = "tbl_product_segment_groups";
    public static $tableName = "tbl_product_segment_groups";
    public static function getCompleteTableName(){
      return getDbAndConnectionName("db1").".".self::$tableName;
    }
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'groupName'
    ];
    public function segments()
    {
        return $this->hasMany(ProductSegments::class, 'fkGroupId');
    } //end function
}
