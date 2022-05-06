<?php

namespace App\Models\ProductSegments;

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;
use App\Models\ProductSegments\ProductSegments;
use App\models\ProductSegments\ProductSegmentGroupsModel;

class AsinSegments extends Model
{
    public $table = "tbl_asin_segments";
    public static $tableName = "tbl_asin_segments";
    public function __construct()
    {
    } //end constructor
    public static function getCompleteTableName(){
      return getDbAndConnectionName("db1").".".self::$tableName;
    }
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'id',
        'fkAccountId',
        'fkSegmentId',
        'fkTagId',
        'asin',
        'occurrenceDate',
        'uniqueColumn',
        'createdAt',
        'updatedAt'
    ];
    public $timestamps = false;
    /*insert or update code starts*/
    public function insertOrUpdate($rows, array $exclude = [])
    {
        // We assume all rows have the same keys so we arbitrarily pick one of them.
        $columns = array_keys($rows[0]);

        $columnsString = implode('`,`', $columns);
        $values = $this->buildSQLValuesFrom($rows);
        $updates = $this->buildSQLUpdatesFrom($columns, $exclude);
        $params = array_flatten($rows);

        $query = "insert into {$this->table} (`{$columnsString}`) values {$values} on duplicate key update {$updates}";
        DB::beginTransaction();
        try {
            DB::statement($query, $params);
            DB::commit();
            return $query;
        } catch (\Exception $e) {
            DB::rollback();
            return false;
        }
    }
    public static function insertEventCronData($eventData)
    {
        DB::transaction(function () use ($eventData){
            foreach (array_chunk($eventData, 50) as $data) {
                AsinSegments::insert($data);
            }
        });
    }
    /**
     * Build proper SQL string for the values.
     *
     * @param array $rows
     * @return string
     */
    protected function buildSQLValuesFrom(array $rows)
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
    protected function buildSQLUpdatesFrom($columns, array $exclude)
    {
        $updateString = collect($columns)->reject(function ($column) use ($exclude) {
            return in_array($column, $exclude);
        })->reduce(function ($updates, $column) {
            return $updates .= "`{$column}`=VALUES(`{$column}`),";
        }, '');

        return trim($updateString, ',');
    }                                                            /*insert or update code ends*/
    public function segment_details()
    {
        return $this->belongsTo(ProductSegments::class, 'fkSegmentId');
    } //end function
    public function segment_group()
    {
        return $this->belongsTo(ProductSegmentGroupsModel::class, 'fkGroupId');
    } //end function
}
