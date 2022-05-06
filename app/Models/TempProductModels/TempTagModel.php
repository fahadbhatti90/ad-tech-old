<?php

namespace App\Models\TempProductModels;

use App\Models\CustomModel;
use Illuminate\Database\Eloquent\Model;

class TempTagModel extends CustomModel
{
    protected $connection = 'mysql';
    public $table = "temp_tbl_product_tags";
    public static $tableName = "temp_tbl_product_tags";
    public $timestamps = false;
    public function __construct()
    {
        $this->table = \getDbAndConnectionName("db1").".".$this->table;
        $this->connection = \getDbAndConnectionName("c1");
    } //end constructor
    
}
