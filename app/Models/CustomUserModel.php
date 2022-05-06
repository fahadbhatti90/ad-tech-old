<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;

class CustomUserModel extends Authenticatable
{


    use Notifiable;
    public $table='users';
    public $type="superAdmin";
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function roles(){
        return $this->belongsToMany('App\Models\UserRolesModels\Role');
    }

    public function hasAnyRoles($roles){
        return null!==$this->roles()->whereIn('name',$roles)->first();
    }

    public function hasAnyRole($role){
        return null!==$this->roles()->where('name',$role)->first();
    }
}
