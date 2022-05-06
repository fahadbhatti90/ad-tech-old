<?php

namespace App\Models\ClientModels;

use App\Models\CustomModel;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;
use App\Models\AccountModels\AccountModel;
use App\Models\ClientModels\CampaignsIdModel;

class CampaignTagsAssignmentModel extends Model
{   
    protected $connection= 'mysqlDb2';
    public $table = "tbl_campaign_tags_assigned";
    public static $tableName = "tbl_campaign_tags_assigned";
    public $timestamps = false;
    protected $fillable = [
        "campaignId",
        'fkTagId',
        'fkAccountId',
        'tag',
        'type',
        'uniqueColumn',
        "createdAt",
        "updatedAt",
    ];
    public function __construct()
    {
        $this->table = getDbAndConnectionName("db2").".".$this->table;
        $this->connection = getDbAndConnectionName("c2");
    } //end constructor
    public static function getCompleteTableName(){
      return getDbAndConnectionName("db2").".".self::$tableName;
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

        DB::connection("mysqlDb2")->statement($query, $params);

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
    public function tag()
    {
        return $this->belongsTo('App\Models\ClientModels\CampaignTagsModel', "fkTagId");
    } //end relationship
    public function strategyCompaigns()
    {
        $accounts = AccountModel::where("fkManagerId", auth()->user()->id)
        ->select("id","fkId")
        ->where("fkBrandId",getBrandId())
        ->where("fkAccountType",1)
        ->get()
        ->map(function($item,$value){
            return $item->fkId;
        });
        $table1 = CampaignsIdModel::getCompleteTableName();
        
        return $this->setConnection('mysql')->belongsTo(CampaignsIdModel::class, "campaignId", "campaignId")
        ->whereIn("fkProfileId",$accounts) 
        ->join('tbl_account', "$table1.fkProfileId", "=", "tbl_account.fkId")
        ->selectRaw("$table1.campaignId,name,brandName,$table1.createdAt,tbl_account.fkId,tbl_account.id")
        ;

    } //end function
}
