<?php

namespace App\Models\ScrapingModels;

use Illuminate\Database\Eloquent\Model;

class DepartmentModel extends Model
{
    protected $table = "tbl_search_rank_department";

    public $timestamps = false;

        public static function getSearchTerms($d_id){
            
        }

       //Relationships

       public function crawler()
       {
           return $this->hasOne('App\Models\ScrapingModels\SearchRankCrawlerModel','d_id');
       }//end function

       public function tempUrls()
       {
           return $this->hasMany('App\Models\ScrapingModels\TempSearchRankModel','department_id');
       }//end function
}//END CLASS
