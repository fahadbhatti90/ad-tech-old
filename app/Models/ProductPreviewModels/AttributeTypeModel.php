<?php

namespace App\Models\ProductPreviewModels;

use Illuminate\Database\Eloquent\Model;

class AttributeTypeModel extends Model
{
    public $table = "tbl_attribute_type";
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'attributeTypeName',
    ];

    public static function boot() {
        parent::boot();

        static::deleting(function($attributeTypeModel) { // before delete() method call this
            // do the rest of the cleanup...
        });
    }//end boot function

    /**
     * Relation Ships
     */

    public function prodcutPreview()
    {
        return $this->hasMany('App\Models\ProductPreviewModels\ProductPreviewModel',"fkAttributeTypeId");
    }
}//end class

