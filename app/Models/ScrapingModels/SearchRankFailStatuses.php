<?php

namespace App\Models\ScrapingModels;

use Illuminate\Database\Eloquent\Model;

class SearchRankFailStatuses extends Model
{
    protected $table="tbl_search_rank_fail_statuses";
    protected $fillable = [
       'failed_data', 'failed_reason', 'failed_at', 'crawler_id','created_at'
    ];
    public $timestamps = false;
    public static function UpdateNewFailStatues(){
        SearchRankFailStatuses::where("isNew",1)
        ->update([
            "isNew" => 0
        ]);
    }//end function
    public static function getDailyScrapingAsinFailStatus(){
        $dataAccountBased = [];
        $data = self::select("failed_data","failed_reason")
        ->where("isNew",1)
        ->get();
        foreach ($data as $key => $value) {
            $dataAccountBased["null"]["null"][] = $value;
        }
        return $dataAccountBased;
    }

    /**
     * Relationships
     */
    public function crawler()
    {
        return $this->belongsTo('App\Models\ScrapingModels\SearchRankCrawlerModel','crawler_id');
    }//end function



}//end class
