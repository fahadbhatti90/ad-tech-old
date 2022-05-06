<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AMSApiModel extends Model
{
    protected $connection = 'mysql';
    public $table = "tb_ams_api";
    public static $tableName = "tb_ams_api";
    public $timestamps = false;
    public static $grantType = 'refresh_token';
    protected $fillable = [
        "grant_type", 
        'refresh_token', 
        'client_id', 
        "client_secret",
        "created_at",
        "updated_at"
    ];
    public function __construct()
    {
        $this->table = getDbAndConnectionName("db1").".".$this->table;
        $this->connection = getDbAndConnectionName("c1");
    } //end constructor
    public static function getCompleteTableName(){
      return getDbAndConnectionName("db1").".".self::$tableName;
    }
}
