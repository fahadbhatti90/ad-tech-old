<?php

namespace App\Models\ProductPreviewModels\GraphDataModels;

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;
use App\Models\AccountModels\AccountModel;
use App\Models\ClientModels\AsinTagsModel;
use App\Models\ProductSegments\AsinSegments;
use App\Models\Inventory\InventoryProductModel;
use App\Models\Inventory\InventoryCategoryModel;
use App\Models\Inventory\InventorySubCategoryModel;

class ProductTableGraphModel extends Model
{
    protected $connection = 'mysqlDb2';
    public $table = "prst_vew_product_table";
    public static $tableName =  "prst_vew_product_table";
    public $timestamps = false;
    public function __construct()
    {
        $this->table = getDbAndConnectionName("db2").".".$this->table;
        $this->connection = getDbAndConnectionName("c2");
    } //end constructor
    public static function getCompleteTableName(){
      return getDbAndConnectionName("db2").".".self::$tableName;
    }
    /**
     * getDailyCompCardData
     *
     * @param mixed $cat_id
     * @param mixed $subcat_id
     * @param mixed $asin
     * @param mixed $year
     * @param mixed $date
     * @return void
     */
    public static function getDailyCompCardData($cat_id, $subcat_id, $asin, $year, $date){
        $accounts = AccountModel::where("fkBrandId",getBrandId())
        ->select("id")
        ->get()
        ->map(function($item,$value){
            return $item->id;
        });
        $accounts = implode(',',$accounts->toArray());
        $date = date('Ymd', strtotime($date));
        return DB::connection("mysqlDb2")->select("CALL spShowPresentationDailyCompCardTable('$asin',$cat_id, $subcat_id,$date,'$accounts')");
    }
    /**
     * getMonthlyCompCardData
     *
     * @param mixed $cat_id
     * @param mixed $subcat_id
     * @param mixed $asin
     * @param mixed $year
     * @param mixed $month
     * @return void
     */
    public static function getMonthlyCompCardData($cat_id, $subcat_id, $asin, $year, $startDate){
        $accounts = AccountModel::where("fkBrandId",getBrandId())
        ->select("id")
        ->get()
        ->map(function($item,$value){
            return $item->id;
        });
        $accounts = implode(',',$accounts->toArray());
        $date = date('Ymd', strtotime($startDate));
        return DB::connection("mysqlDb2")->select("CALL spShowPresentationMonthlyCompCardTable('$asin',$cat_id, $subcat_id,$date,'$accounts')");
    }
    /**
     * getWeeklyCompCardData
     *
     * @param mixed $cat_id
     * @param mixed $subcat_id
     * @param mixed $asin
     * @param mixed $year
     * @param mixed $week
     * @return void
     */
    public static function getWeeklyCompCardData($cat_id, $subcat_id, $asin,  $year, $startDate){
        $accounts = AccountModel::where("fkBrandId",getBrandId())
        ->select("id")
        ->get()
        ->map(function($item,$value){
            return $item->id;
        });
        $accounts = implode(',',$accounts->toArray());
        $date = date('Ymd', strtotime($startDate));
        return DB::connection("mysqlDb2")->select("CALL spShowPresentationWeeklyCompCardTable('$asin',$cat_id, $subcat_id,$date,'$accounts')");
    }//end function
    /**
     * Get the tag that owns the Product.
     */
    public function tag()
    {
        $accounts = AccountModel::where("fkBrandId",getBrandId())
        ->select("id")
        ->get()
        ->map(function($item,$value){
            return $item->id;
        });
        return $this->setConnection('mysql')->hasMany(AsinTagsModel::class,"asin","ASIN")->whereIn("fkAccountId",$accounts);
    }//end relationship
    /**
     * Get the segment that owns the Product.
     */
    public function segment()
    {
        $accounts = AccountModel::where("fkBrandId",getBrandId())
        ->select("id")
        ->get()
        ->map(function($item,$value){
            return $item->id;
        });
        return $this->setConnection('mysql')->hasMany(AsinSegments::class,"asin","ASIN")->whereIn("fkAccountId",$accounts);
    }//end relationship
    public function category_alias()
    {
        return $this->setConnection(\getDbAndConnectionName("db2"))->hasMany(InventoryCategoryModel::class,"fkCategoryId","category_id");
    }//end relationship
    public function sub_category_alias()
    {
        return $this->setConnection(\getDbAndConnectionName("db2"))->hasMany(InventorySubCategoryModel::class,"fkSubCategoryId","subcategory_id");
    }//end relationship
    public function product_alias()
    {
        return $this->setConnection(\getDbAndConnectionName("db2"))->hasMany(InventoryProductModel::class,"asin","ASIN");
    }//end relationship
    
}//enc class
