<?php

namespace App\Models\ScrapingModels;

use App\models\ActiveAsin;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;
use App\Models\ScrapingModels\UserHierarchy\AccountsAsinModel;

class asinModel extends Model
{
    protected $connection = "mysql";
    protected $table = "tbl_asins";
    protected $primaryKey = "asin_id";
    public $timestamps = false;

    public static function checkASINData($startDate, $endDate)
    {
        return DB::select('SELECT count(tbl_asins_result.id) as totalData FROM tbl_asin_collection
        join tbl_asins_result
        On tbl_asin_collection.id = tbl_asins_result.c_id
        where tbl_asins_result.capturedAt between "' . $startDate . '" AND "' . $endDate . '"');
    }
    public static function getASINData($startDate, $endDate)
    {
        return DB::select('SELECT * FROM tbl_asin_collection
        join tbl_asins_result
        On tbl_asin_collection.id = tbl_asins_result.c_id
        where tbl_asins_result.capturedAt between "' . $startDate . '" AND "' . $endDate . '"');
    }

    public static function isNotUniqueASIN($asin)
    {
        return (asinModel::where('asin_code', $asin)->count() > 0);
    }

    public function collection()
    {
        return $this->belongsTo('App\Models\ScrapingModels\CollectionsModel', 'c_id');
    }//end function

    public function accounts()
    {
        return $this->setConnection('mysqlDb2')->hasMany('App\Models\ScrapingModels\UserHierarchy\ALLManagerAsinsModel', 'asin', "asin_code");
    }//end function

    /**
     * Get the comments for the blog post.
     */
    public function getAsinAccounts()
    {
        return $this->hasMany(AccountsAsinModel::class, "fkAsinId", "asin_id");
    }

    public function activeAsin()
    {
        return $this->hasOne(ActiveAsin::class, "asin", "asin_code");
    }
}//end class
