<?php

namespace App\Models\TempProductModels;

use App\Models\CustomModel;
use Illuminate\Database\Eloquent\Model;
use App\Models\TempProductModels\TempProductModel;

class TempFilterAssignModel extends CustomModel
{
    protected $connection = 'mysql';
    public $table = "temp_tbl_product_assigned";
    public static $tableName = "temp_tbl_product_assigned";
    public $timestamps = false;
    public function __construct()
    {
        $this->table = \getDbAndConnectionName("db1").".".$this->table;
        $this->connection = \getDbAndConnectionName("c1");
    } //end constructor
    
}
