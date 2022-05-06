<?php

namespace App\Models\ProductPreviewModels;

use Illuminate\Database\Eloquent\Model;

class ProductPreviewTypeModel extends Model
{
    public $table = "tbl_product_preview_type";
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'typeTitle',
    ];

    /**
     * boot
     *
     * @return void
     */
    public static function boot() {
        parent::boot();

        static::deleting(function($pptModel) { // before delete() method call this
            // do the rest of the cleanup...
        });
    }//end boot function


    /*****************************Relation Ships***************************/

    /**
     * Get the action for the product view.
     */
    public function prodcutPreview()
    {
        return $this->hasMany('App\Models\ProductPreviewModels\ProductPreviewModel',"fkProductPreviewTypeId");
    }

    /*****************************Relation Ships***************************/



}//end class
