<?php

namespace App\Models\ProductPreviewModels;

use Illuminate\Database\Eloquent\Model;

class UserActionsModel extends Model
{
    public $table = "tbl_user_actions";
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'actionName',
    ];

    public static function boot() {
        parent::boot();

        static::deleting(function($userAction) { // before delete() method call this
            
            // do the rest of the cleanup...
        });
    }//end boot
    /**
     * Relation Ships
     */

    public function prodcutPreview()
    {
        return $this->hasMany('App\Models\ProductPreviewModels\ProductPreviewModel',"fkEventId");
    }
}//end class
