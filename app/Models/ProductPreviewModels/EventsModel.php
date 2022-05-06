<?php

namespace App\Models\ProductPreviewModels;

use Illuminate\Database\Eloquent\Model;

class EventsModel extends Model
{
    public $table = "tbl_events";
    public static $tableName = "tbl_events";
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'eventName',
    ];

    public static function boot() {
        parent::boot();

        static::deleting(function($eventModel) { // before delete() method call this
            // do the rest of the cleanup...
        });
    }//end boot function

    
    /**
     * Get the prodcutPreview for the event.
     *
     * @return void
     */
    public function prodcutPreview()
    {
        return $this->hasMany('App\Models\ProductPreviewModels\ProductPreviewModel',"fkEventId")->orderBy("occurrenceDate");
    }
}//end class
