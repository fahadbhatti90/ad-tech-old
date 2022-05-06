<?php

namespace App\Models\ClientModels;

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;
use App\Models\AccountModels\AccountModel;
use App\Models\ProductPreviewModels\GraphDataModels\AsinDailyModels;

class AsinTagsModel extends Model
{
    protected $connection = 'mysql';
    public $table = "tbl_asin_tags";
    public static $tableName = "tbl_asin_tags";
    public $timestamps = false;
    protected $fillable = [
        "asin",
        'fkTagId', 
        'tag', 
        "fullFillmentChannel",
        "createdAt", 
        "updatedAt", 
    ];
    public function __construct()
    {
        $this->table = getDbAndConnectionName("db1").".".$this->table;
        $this->connection = getDbAndConnectionName("c1");
    } //end constructor
    public static function getCompleteTableName(){
      return getDbAndConnectionName("db1").".".self::$tableName;
    }
    public static function insertOrUpdate($rows, array $exclude = [])
    {
        // We assume all rows have the same keys so we arbitrarily pick one of them.
        $columns = array_keys($rows[0]);

        $columnsString = implode('`,`', $columns);
        $values = self::buildSQLValuesFrom($rows);
        $updates = self::buildSQLUpdatesFrom($columns, $exclude);
        $params = array_flatten($rows);
        $tableName = self::$tableName;
        $query = "insert into {$tableName} (`{$columnsString}`) values {$values} on duplicate key update {$updates}";

        DB::statement($query, $params);

        return $query;
    }

    /**
     * Build proper SQL string for the values.
     *
     * @param array $rows
     * @return string
     */
    protected static function buildSQLValuesFrom(array $rows)
    {
        $values = collect($rows)->reduce(function ($valuesString, $row) {
            return $valuesString .= '(' . rtrim(str_repeat("?,", count($row)), ',') . '),';
        }, '');

        return rtrim($values, ',');
    }

    /**
     * Build proper SQL string for the on duplicate update scenario.
     *
     * @param       $columns
     * @param array $exclude
     * @return string
     */
    protected static function buildSQLUpdatesFrom($columns, array $exclude)
    {
        $updateString = collect($columns)->reject(function ($column) use ($exclude) {
            return in_array($column, $exclude);
        })->reduce(function ($updates, $column) {
            return $updates .= "`{$column}`=VALUES(`{$column}`),";
        }, '');

        return trim($updateString, ',');
    }

    /**
     * Get the tag that owns the Product.
     */
    public function products()
    { 
        $tableName = AsinDailyModels::getCompleteTableName();
        return $this->belongsTo('App\Models\ProductPreviewModels\GraphDataModels\AsinDailyModels',"asin","ASIN")->select(DB::raw("$tableName.fk_account_id, sum($tableName.shipped_units) as shipped_units, $tableName.ASIN, $tableName.product_title, $tableName.fullfillment_channel"))->groupBy(["ASIN"]);
    }//end relationship
}
