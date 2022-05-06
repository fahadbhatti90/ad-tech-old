<?php

namespace App\Models\ProductPreviewModels;

use Illuminate\Database\Eloquent\Model;
use App\Models\Inventory\InventoryBrandsModel;
use App\Models\Inventory\InventoryProductModel;

class AllAsinsDetailsModel extends Model
{
    protected $connection = 'mysqlDb2';
    public $table = "prst_tbl_asins_detail";
    public static $tableName = "prst_tbl_asins_detail";
    public static function getTableName(){
        return \getDbAndConnectionName("db2").".".self::$tableName;
      }//end funciton
    /**
     * Get the prodcutPreview for the event.
     *
     * @return void
     */
    public function prodcutPreview()
    {
        return $this->hasMany('App\Models\ProductPreviewModels\ProductPreviewModel',"fkAccountId","fk_account_id");
    }
    public function brand_alias()
    {
        return $this->setConnection(\getDbAndConnectionName("c2"))->hasMany(InventoryBrandsModel::class,"fkAccountId","fk_account_id");
    }//end relationship

    public function product_alias()
    {
        return $this->setConnection(\getDbAndConnectionName("c2"))->hasMany(InventoryProductModel::class,"asin","ASIN");
    }//end relationship

}//end class
