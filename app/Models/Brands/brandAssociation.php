<?php

namespace App\Models\Brands;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class brandAssociation extends Model
{
    use SoftDeletes;
    public $table = "tbl_brand_association";
    protected $fillable = [
        'fkBrandId',
        'fkManagerId'
    ];

    public function brand()
    {
        return $this->belongsTo('App\Models\ClientModels\ClientModel', 'fkBrandId');
    }//end function
}
