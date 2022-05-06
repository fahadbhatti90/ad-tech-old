<?php

namespace App\Models\UserRolesModels;

use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    public $table = "tbl_roles";

    public function users()
    {
        return $this->belongsToMany('App\User','tbl_user_roles','userId','roleId');
    }

   /* public function roles(){
        return $this->belongsToMany('App\Models\User');
    }*/
}
