<?php

namespace App\Models;

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;

class CustomModel extends Model
{
    
    /**
     * static insertOrUpdate()
     *
     * param mixed $rows
     * param mixed $tableName
     * param mixed array (optional)
     *
     * return string $query
     *
     * @param mixed $rows
     * @param mixed $tableName
     * @param mixed array (optional)
     * @return string $query
     */
    public static function insertOrUpdate($rows ,$tableName, array $exclude = [])
    {
        // We assume all rows have the same keys so we arbitrarily pick one of them.
        $columns = array_keys($rows[0]);

        $columnsString = implode('`,`', $columns);
        $values = self::buildSQLValuesFrom($rows);
        $updates = self::buildSQLUpdatesFrom($columns, $exclude);
        $params = array_flatten($rows);
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
}
