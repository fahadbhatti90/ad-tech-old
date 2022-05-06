<?php

namespace App\Models\UserRoles;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

class UserRole extends Model
{
    use Notifiable;
    public $table = "tbl_user_roles";
    protected $fillable = [
        'roleId', 'userId'
    ];
}
