<?php

namespace App\Models\ProductSegments;
use App\Models\CustomModel;
use Illuminate\Database\Eloquent\Model;

class InventoryAllDetails extends CustomModel
{
    protected $connection = 'mysqlDb2';
    public $table = "tbl_inventory_all_details";
    public static $tableName = "tbl_inventory_all_details";
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