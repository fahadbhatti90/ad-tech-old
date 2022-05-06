<?php

namespace App\Models\ProductPreviewModels;

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;
use App\Models\AccountModels\AccountModel;
use App\Models\Inventory\InventoryBrandsModel;
use App\Models\ProductPreviewModels\UserActionsModel;

class ProductPreviewModel extends Model
{
    public $table = "tbl_event_logs";
    public static $tableName = "tbl_event_logs";
    public $timestamps = false;

    public function __construct()
    {
        $this->table = getDbAndConnectionName("db1") . "." . $this->table;
    } //end constructor

    public static function getCompleteTableName()
    {
        return getDbAndConnectionName("db1") . "." . self::$tableName;
    }

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'fkAccountId',
        'asin',
        'fkEventId',
        'occurrenceDate',
        'notes',
    ];

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
        DB::transaction(function () use ($eventData) {
            foreach (array_chunk($eventData, 50) as $data) {
                ProductPreviewModel::insert($data);
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
    }


    /*****************************Relation Ships***************************/

    /**
     * Get the client for the product view.
     */

    /**
     * Get the client for the product view.
     */
    public function account()
    {
        return $this->belongsTo('App\Models\ProductPreviewModels\AllAsinsDetailsModel', 'fkAccountId', "fk_account_id");
    }//end relationship

    public function accounts()
    {
        return $this->belongsTo(AccountModel::class, 'fkAccountId');
    }//end relationship

    /**
     * Get the event that owns the ProductPreview.
     */
    public function events()
    {
        return $this->belongsTo(EventsModel::class, "fkEventId");
    }//end relationship

    public function brand_alias()
    {
        return $this->setConnection(\getDbAndConnectionName("c2"))->hasMany(InventoryBrandsModel::class, "fkAccountId", "fkAccountId");
    }//end relationship

    /*****************************Relation Ships***************************/

}//end class
