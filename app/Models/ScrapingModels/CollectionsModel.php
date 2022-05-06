<?php

namespace App\Models\ScrapingModels;

use Illuminate\Database\Eloquent\Model;
use App\Models\ScrapingModels\asinModel;

class CollectionsModel extends Model
{
    
    protected $table="tbl_asin_collection";
    protected $fillable = [
        'c_name', 'c_type', 'created_at'
    ];
    // protected $with = ['asin','asin_cron'];

    public $timestamps = false;


    //custom function
    public static function getScheduledCollections(){
        return CollectionsModel::where("c_type",1)->get();
        
    }
    public static function checkAvailableCollections(){
        return CollectionsModel::where("c_type",1)->count()>0;
    }
    public static  function updateInstantASINNewStatus(){
        CollectionsModel::where('c_type',0)->where('isNew',1)
        ->update([
            "isNew"=>0
        ]);
    }//end function
    //Relationships
    public function asin()
    {
        return $this->hasMany('App\Models\ScrapingModels\asinModel','c_id');
    }//end function
    public function asinCount()
    {
        return $this->hasMany(asinModel::class,'c_id')->get()->count();
    }//end function
    public function asin_result()
    {
        return $this->hasMany('App\Models\ScrapingModels\ScrapModel','c_id');
    }//end function

    public function asin_cron()
    {
        return $this->hasOne('App\Models\ScrapingModels\CronModel','c_id');
    }//end function


}//end model
