<?php

namespace App\Models\Inventory;

use App\Models\CustomModel;
use Illuminate\Database\Eloquent\Model;

class InventoryBrandsModel extends CustomModel
{
    protected $connection = 'mysqlDb2';
    public $table = "tbl_inventory_brands_override";
    public static $tableName = "tbl_inventory_brands_override";
    public $timestamps = false;

    public function __construct()
    {
        $this->table = \getDbAndConnectionName("db2").".".$this->table;
        $this->connection = \getDbAndConnectionName("c2");
    } //end constructor

    public static function getTableName(){
      return \getDbAndConnectionName("db2").".".self::$tableName;
    }//end funciton

}//end class
