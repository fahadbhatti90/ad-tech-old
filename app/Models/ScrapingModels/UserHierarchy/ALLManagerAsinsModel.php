<?php

namespace App\Models\ScrapingModels\UserHierarchy;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class ALLManagerAsinsModel extends Model
{
    protected $connection = "mysqlDb2";
    public $table = "prst_vew_product_table";
    public $timestamps = false;

    public function ownerasin()
    {
        return $this->setConnection('mysql')->belongsTo('App\Models\ScrapingModels\asinModel', 'asin','asin_code');
    }//end function



    /**
     * The "booting" method of the model.
     *
     * @return void
     */
    protected static function boot()
    {
        parent::boot();

        static::addGlobalScope('distinctAsin',function (Builder $builder) {
            $builder->selectRaw("distinct fk_account_id,asin");
        });
    }
}
