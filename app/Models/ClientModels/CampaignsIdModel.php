<?php

namespace App\Models\ClientModels;

use Illuminate\Database\Eloquent\Model;
use App\Models\AccountModels\AccountModel;
use App\Models\ClientModels\CampaignTagsAssignmentModel;

class CampaignsIdModel extends Model
{
    public $table = "tbl_rtl_ams_campaign_list";
    public static $tableName = "tbl_rtl_ams_campaign_list";
    public $timestamps = false;
    public static $ordering = false;
    public static $dir = "false";
    public static $search = null;
    public function __construct()
    {
        $this->table = getDbAndConnectionName("db1").".".$this->table;
        $this->connection = getDbAndConnectionName("c1");
    } //end constructor
    public static function getCompleteTableName()
    {
        return getDbAndConnectionName("db1").".".self::$tableName;
    }
    /**
    * Get the tag that owns the Product.
    */
    public function tag()
    {
        return $this->setConnection('mysqlDb2')->hasMany(CampaignTagsAssignmentModel::class, "campaignId", "campaignId");
    }//end relationship
    public function accounts()
    {
        return $this->setConnection(\getDbAndConnectionName("c1"))->belongsTo(AccountModel::class, 'fkAccountId', "id");
    }//end function

    /**
     * The "booting" method of the model.
     *
     * @return void
     */
     
    // protected static function boot()
    // {
    //     parent::boot();

    //     static::addGlobalScope('enabledOnly',function (Builder $builder) {
    //         $builder->where("state","=","enabled");
    //     });
    // }
    // public function scopeEnabled($query)
    // {
      
    //     return $query->whereIn(\DB::raw("$tName.campaignId"),$campainIds);
    // }
}
