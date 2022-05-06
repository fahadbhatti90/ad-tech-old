<?php

namespace App\Models\ScrapingModels;

use Illuminate\Database\Eloquent\Model;

class InstantScrapingTempScheduleModel extends Model
{
   public $table = 'tbl_asins_instant_temp_schedules';
   protected $primaryKey = null;
   public $timestamps = false;
}
