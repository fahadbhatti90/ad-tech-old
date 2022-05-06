<?php

namespace App;
//namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\SoftDeletes;

class User extends Authenticatable
{

    use Notifiable;
    use SoftDeletes;
    public $type = "superAdmin";
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

    public static function checkUserAvaiable($id)
    {
        return User::where("id", $id)->exists();
    }//end function

    public function isEmailUnique($email)
    {
        return !User::where("email", $email)->withTrashed()->exists();
    }//end function

    /**
     * The users that belong to the roles.
     */
    public function roles()
    {
        return $this->belongsToMany('App\Models\UserRolesModels\Role', 'tbl_user_roles', 'userId', 'roleId');
    }

    public function managers()
    {
        return $this->belongsToMany('App\Models\UserRolesModels\Role', 'tbl_user_roles', 'userId', 'roleId')->where('tbl_roles.id', 3)->where('users.id', '!=', 1)->orderBy('users.id', 'desc');
    }

    public function admins()
    {
        return $this->belongsToMany('App\Models\UserRolesModels\Role', 'tbl_user_roles', 'userId', 'roleId')->where('tbl_roles.id', 2)->where('users.id', '!=', 1)->orderBy('users.id', 'desc');
    }

    public function hasAnyRoles($roles)
    {
        return null !== $this->roles()->whereIn('tbl_roles.id', $roles)->first();
    }

    public function hasAnyRole($role)
    {
        return null !== $this->roles()->where('tbl_roles.id', $role)->first();
    }

    public function accounts()
    {
        return $this->hasMany('App\Models\AccountModels\AccountModel', 'fkManagerId');
    }//end function
    /* public function brandManager()
     {
         return $this->hasMany('App\Models\brandAssociation\brandAssociation','fkManagerId');
     }*///end function
    public function brands()
    {
        return $this->hasMany('App\Models\ClientModels\ClientModel', 'fkManagerId');
    }//end function

    public function userAssignedbrands()
    {
        return $this->belongsToMany('App\Models\ClientModels\ClientModel', 'App\Models\Brands\brandAssociation', 'fkManagerId', 'fkBrandId')->withTimestamps();
    }
}
