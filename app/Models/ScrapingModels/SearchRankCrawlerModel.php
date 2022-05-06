<?php

namespace App\Models\ScrapingModels;

use Illuminate\Database\Eloquent\Model;

class SearchRankCrawlerModel extends Model
{
    protected $table = "tbl_search_rank_crawler";
    protected $fillable = [
        'c_name', 'd_id', 'c_frequency', 'c_nextRun','created_at'
     ];
    public $timestamps = false;

    //Custom Funcitons

    public static function getCrawlers($current_date){
        return SearchRankCrawlerModel::where("c_lastRun", "<>", [$current_date])
        ->where("c_nextRun", "=",$current_date)
        ->get();
    }
    public static function checkAvailableCrawlers($current_date){
        return SearchRankCrawlerModel::where("c_lastRun", "<>", [$current_date])
        ->where("c_nextRun", "=",$current_date)
        ->count() > 0;
    }
    /**
     * if any schedule is in running state (isRunning is true) this function will return true
     * else return false
     *
     * @return void
     */
    public static function isAnySchedulRunning(){
        return SearchRankCrawlerModel::where("isRunning",1)
        ->count() > 0;
     }
    public static function ResetAllSchdule(){
        SearchRankCrawlerModel::where("isRunning",1)
        ->update(["isRunning"=>0]);
    }


    //Realtionships
    public function department()
    {
        return $this->belongsTo('App\Models\ScrapingModels\DepartmentModel','d_id');
    }//end function
    public function searchTerm()
    {
        return $this->hasMany('App\Models\ScrapingModels\SearchTermModel','crawler_id');
    }//end function
    public function failStatuses()
    {
        return $this->hasMany('App\Models\ScrapingModels\SearchRankFailStatuses','crawler_id');
    }//end function

}//end class
