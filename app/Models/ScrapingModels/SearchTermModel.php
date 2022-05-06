<?php

namespace App\Models\ScrapingModels;

use Illuminate\Database\Eloquent\Model;

class SearchTermModel extends Model
{
    protected $table = "tbl_search_terms";

    public $timestamps = false;

    public static function getDepartmentSearchTermsIdArray($crawler_id){
        $st = SearchTermModel::where("crawler_id",$crawler_id);
        if($st->exists()){
            $result = $st->select("id")->get();
            $st_id = array();
            $i = 0;
            foreach ($result as $key => $value) {
                $st_id[$i] = $value->id;
                $i++;
            }
            return ($st_id);
        }
        return "false";
    }
    
    //Relationship
    public function crawler()
    {
        return $this->belongsTo('App\Models\ScrapingModels\SearchRankCrawlerModel','crawler_id');
    }//end function
    public function srResult()
    {
        return $this->hasMany('App\Models\ScrapingModels\SearchRankScrapedResultModel','st_id');
    }//end function
    public function tempUrls()
    {
        return $this->hasMany('App\Models\ScrapingModels\TempSearchRankModel','searchTerm_id');
    }//end function
}//end class
