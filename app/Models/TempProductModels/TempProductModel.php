<?php

namespace App\Models\TempProductModels;

use App\Models\CustomModel;
use Illuminate\Database\Eloquent\Model;

class TempProductModel extends CustomModel
{
    protected $connection = 'mysql';
    public $table = "temp_tbl_products";
    public static $tableName = "temp_tbl_products";
    public $timestamps = false;

    public function __construct()
    {
        $this->table = \getDbAndConnectionName("db1").".".$this->table;
        $this->connection = \getDbAndConnectionName("c1");
    } //end constructor
    public function segments()
    {
        return $this->belongsToMany('App\User');
    }
}
