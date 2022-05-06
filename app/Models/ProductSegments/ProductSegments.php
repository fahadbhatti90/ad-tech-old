<?php

namespace App\Models\ProductSegments;

use Illuminate\Database\Eloquent\Model;
use App\Models\ProductSegments\AsinSegments;
use App\models\ProductSegments\ProductSegmentGroupsModel;

class ProductSegments extends Model
{
    public $table = "tbl_product_segments";
    public static $tableName = "tbl_product_segments";
    public static function getCompleteTableName(){
      return getDbAndConnectionName("db1").".".self::$tableName;
    }
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'segmentName',
        'fkGroupId'
    ];
    public function products()
    {
        return $this->hasMany(AsinSegments::class, 'fkSegmentId');
    } //end function
    public function segment_group()
    {
        return $this->belongsTo(ProductSegmentGroupsModel::class, 'fkGroupId');
    } //end function
}
