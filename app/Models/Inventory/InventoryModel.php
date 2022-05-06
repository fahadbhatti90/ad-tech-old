<?php

namespace App\Models\Inventory;

use Illuminate\Database\Eloquent\Model;
use App\Models\Inventory\InventoryBrandsModel;
use App\Models\Inventory\InventoryProductModel;
use App\Models\Inventory\InventoryCategoryModel;
use App\Models\Inventory\InventorySubCategoryModel;

class InventoryModel extends Model
{
    protected $connection = 'mysqlDb2';
    public $table = "tbl_inventory_all_details";
    public static $tableName = "tbl_inventory_all_details";
    public function __construct()
    {
        $this->table = \getDbAndConnectionName("db2").".".$this->table;
        $this->connection = \getDbAndConnectionName("c2");
    } //end constructor

    public static function getTableName(){
      return \getDbAndConnectionName("db2").".".self::$tableName;
    }//end funciton
    public static function getInentory($groupByColumn = null){
        $inventory = self::getFullInventory();
        $inventroyTN = self::$tableName;
        if($groupByColumn != null){
          switch ($groupByColumn) {
            case 'ASIN':
                $inventory = self::getInventoryOnlyWithASIN();
                break;
            case 'subcategory_id':
                $inventory = self::getInventoryOnlyWithSubCategory();
                break;
            case 'category_id':
                $inventory = self::getInventoryOnlyWithCategory();
                break;
            case 'fk_account_id':
                $inventory = self::getInventoryOnlyWithBrand();
                break;
          }
          $inventory["query"] = $inventory["query"]->groupBy($inventroyTN.".".$groupByColumn);
        }
        return $inventory;
    }//end funciton 
    public static function getColumnsToSearch(){
      $inventroyBrandTN = InventoryBrandsModel::$tableName;
      $inventroyProductsTN = InventoryProductModel::$tableName;
      $inventroyCategoryTN = InventoryCategoryModel::$tableName;
      $inventroySubCategoryTN = InventorySubCategoryModel::$tableName;
      $inventroyTN = self::$tableName;
      
      return [
        "$inventroyTN.ASIN",
        "$inventroyTN.product_title",
        "$inventroyProductsTN.overrideLabel",
        "$inventroyTN.accountName",
        "$inventroyBrandTN.overrideLabel",
        "$inventroyTN.category_name",
        "$inventroyCategoryTN.overrideLabel",
        "$inventroyTN.subcategory_name",
        "$inventroySubCategoryTN.overrideLabel"
      ];
    }
    public static function getFullInventory(){
      $inventroyBrandTN = InventoryBrandsModel::$tableName;
      $inventroyProductsTN = InventoryProductModel::$tableName;
      $inventroyCategoryTN = InventoryCategoryModel::$tableName;
      $inventroySubCategoryTN = InventorySubCategoryModel::$tableName;
      $inventroyTN = self::$tableName;
      $inventory = self::selectRaw(
        "$inventroyTN.fk_account_id,
        $inventroyTN.accountName,
        $inventroyBrandTN.fkAccountId,
        $inventroyBrandTN.overrideLabel overrideLabelBrand,
        $inventroyTN.ASIN,
        $inventroyTN.product_title,
        $inventroyProductsTN.asin as inventoryAsin,
        $inventroyProductsTN.overrideLabel overrideLabelProduct,
        $inventroyTN.category_id,
        $inventroyTN.category_name,
        $inventroyCategoryTN.fkCategoryId,
        $inventroyCategoryTN.overrideLabel overrideLabelCategory,
        $inventroyTN.subcategory_id,
        $inventroyTN.subcategory_name,
        $inventroySubCategoryTN.fkSubCategoryId,
        $inventroySubCategoryTN.overrideLabel overrideLabelSubCategory
        "
        )
        ->leftJoin("$inventroyBrandTN", "$inventroyTN.fk_account_id", '=', "$inventroyBrandTN.fkAccountId")
        ->leftJoin("$inventroyProductsTN", "$inventroyTN.ASIN", '=', "$inventroyProductsTN.asin")
        ->leftJoin("$inventroyCategoryTN", "$inventroyTN.category_id", '=', "$inventroyCategoryTN.fkCategoryId")
        ->leftJoin("$inventroySubCategoryTN", "$inventroyTN.subcategory_id", '=', "$inventroySubCategoryTN.fkSubCategoryId");
        
      return [
        "query" => $inventory,
        "columns" => self::getColumnsToSearch(),
      ];;
    }
    private static function getInventoryOnlyWithASIN(){
      $inventroyProductsTN = InventoryProductModel::$tableName;
      $inventroyTN = self::$tableName;
      $inventory = self::selectRaw(
        "
        $inventroyTN.ASIN,
        $inventroyTN.product_title,
        $inventroyProductsTN.asin as inventoryAsin,
        $inventroyProductsTN.overrideLabel overrideLabelProduct
        "
        )
        ->leftJoin("$inventroyProductsTN", "$inventroyTN.ASIN", '=', "$inventroyProductsTN.asin");
        return [
          "query" => $inventory,
          "sortColumn"=>[
            "$inventroyTN.ASIN",
            "$inventroyTN.product_title",
          ],
          "columns" => [
            "$inventroyTN.ASIN",
            "$inventroyTN.product_title",
            "$inventroyProductsTN.overrideLabel",
          ],
        ];
    }//end function
    private static function getInventoryOnlyWithSubCategory(){
      $inventroySubCategoryTN = InventorySubCategoryModel::$tableName;
      $inventroyTN = self::$tableName;
      $inventory = self::selectRaw(
        "
        $inventroyTN.subcategory_id,
        $inventroyTN.subcategory_name,
        $inventroySubCategoryTN.fkSubCategoryId,
        $inventroySubCategoryTN.overrideLabel overrideLabelSubCategory
        "
        )
        ->leftJoin("$inventroySubCategoryTN", "$inventroyTN.subcategory_id", '=', "$inventroySubCategoryTN.fkSubCategoryId");
        return [
          "query" => $inventory,
          "sortColumn"=>[
            "$inventroyTN.subcategory_id",
            "$inventroyTN.subcategory_name",
          ],
          "columns" => [
            "$inventroyTN.subcategory_name",
            "$inventroySubCategoryTN.overrideLabel"
          ],
        ];
    }//end function
    private static function getInventoryOnlyWithCategory(){
      $inventroyCategoryTN = InventoryCategoryModel::$tableName;
      $inventroyTN = self::$tableName;
      $inventory = self::selectRaw(
        "
        $inventroyTN.category_id,
        $inventroyTN.category_name,
        $inventroyCategoryTN.fkCategoryId,
        $inventroyCategoryTN.overrideLabel overrideLabelCategory
        "
        )
        ->leftJoin("$inventroyCategoryTN", "$inventroyTN.category_id", '=', "$inventroyCategoryTN.fkCategoryId");
        return [
          "query" => $inventory,
          "sortColumn"=>[
            "$inventroyTN.category_id",
            "$inventroyTN.category_name",
          ],
          "columns" => [
            "$inventroyTN.category_name",
            "$inventroyCategoryTN.overrideLabel",
          ],
        ];
    }//end function
    private static function getInventoryOnlyWithBrand(){
      $inventroyBrandTN = InventoryBrandsModel::$tableName;
      $inventroyTN = self::$tableName;
      $inventory = self::selectRaw(
        "
        $inventroyTN.fk_account_id,
        $inventroyTN.accountName,
        $inventroyBrandTN.fkAccountId,
        $inventroyBrandTN.overrideLabel overrideLabelBrand
        "
        )
        ->leftJoin("$inventroyBrandTN", "$inventroyTN.fk_account_id", '=', "$inventroyBrandTN.fkAccountId");
        return [
          "query" => $inventory,
          "sortColumn"=>[
            "$inventroyTN.fk_account_id",
            "$inventroyTN.accountName",
          ],
          "columns" => [
            "$inventroyTN.accountName",
            "$inventroyBrandTN.overrideLabel",
          ],
        ];
    }//end function
}//end class