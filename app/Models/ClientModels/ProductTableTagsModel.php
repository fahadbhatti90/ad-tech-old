<?php

namespace App\Models\ClientModels;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class ProductTableTagsModel extends Model
{
    protected $connection = 'mysql';
    public $table = "tbl_product_table_tags";
    public $timestamps = false;
    protected $fillable = [
        'fkManagerId',
        'tag',
    ];
    /**
     * isTagAlreadyExists
     *
     * @param mixed $tagName
     * @return void
     */
    public static function isDuplicateTag($tagId, $tagName)
    {
        return self::where("tag", $tagName)->where("fkManagerId", auth()->user()->id)->where("id", "<>", $tagId)->exists();
    }
    // public function productss()
    // {
    //     return $this->setConnection('mysqlDb2')->hasMany('App\Models\ProductPreviewModels\GraphDataModels\AsinDailyModels', 'fkTagId');
    // }//end function
    public function products()
    {
        return $this->hasMany('App\Models\ClientModels\AsinTagsModel', 'fkTagId');
    } //end function
    public function products_count()
    {
        return $this->setConnection('mysqlDb2')->hasMany('App\Models\ProductPreviewModels\GraphDataModels\AsinDailyModels', 'fkTagId')->select(DB::raw("count(*) as count"));

    } //end function
    /**
    * Retrieve the model for a bound value.
    *
    * @param  mixed  $value
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function resolveRouteBinding($value)
    {
        return $this->with("products")->where('id', $value)->first() ?? ["status"=>false,"message"=>"No Such Tag Found"];
    }
} //end class
