<?php

namespace App\Models\ScrapingModels;

use Illuminate\Database\Eloquent\Model;

class SettingsModel extends Model
{
    protected $table = "tbl_settings";
    protected $fillable = [
        'name', 'value', 'description', 'created_at'
    ];
    public $timestamps = false;

    public static function getHostName()
    {
        $setting = SettingsModel::where("name", "Host")->select("value");
        return $setting->exists() ? $setting->first()->value : "404";
    }
}//end class
