<?php

namespace App\Models\ams\scheduleEmail;

use Illuminate\Database\Eloquent\Model;

class amsParameterTypes extends Model
{
    public $table = "tbl_Ams_Parameter_Types";
    protected $fillable = [
        'parameterName',
        'isSd',
        'isSp',
        'isSb',
        'isActive'
    ];
}
