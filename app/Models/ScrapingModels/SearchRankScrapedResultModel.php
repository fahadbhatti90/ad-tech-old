<?php

namespace App\Models\ScrapingModels;


use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;

class SearchRankScrapedResultModel extends Model
{
    protected $table="tbl_search_rank_scraped_result";
    public $timestamps = false;
    public static function checkSearchRankData($startDate, $endDate){
        return  DB::select('SELECT count(sr.id) as totalResults FROM tbl_search_rank_scraped_result sr
          join tbl_search_terms st
          On st.id = sr.st_id
          where sr.created_at between "' . $startDate . '" AND "' . $endDate . '"');
      }
    public static function getSearchRankData($startDate, $endDate){
      return  DB::select('SELECT st.st_term, sr.* FROM tbl_search_rank_scraped_result sr
        join tbl_search_terms st
        On st.id = sr.st_id
        where sr.created_at between "' . $startDate . '" AND "' . $endDate . '"');
    }
    public static function deleteSearchTermScrapResult($st_ids){
        $searchRankResultModel = SearchRankScrapedResultModel::whereIn('st_id', $st_ids);
        if($searchRankResultModel->exists()){
            return $searchRankResultModel->delete();
        }
        return true;
    }
    public static function checkCrawlerExists($st_id){
        return SearchRankScrapedResultModel::where('st_id', $st_id)->exists();
    }
    public static function getCrawlerByStId($st_id){
        return SearchRankScrapedResultModel::where('st_id', $st_id)->get();
    }
    public function searchTerm()
    {
        return $this->belongsTo('App\Models\ScrapingModels\SearchTermModel','st_id');
    }//end function
}
