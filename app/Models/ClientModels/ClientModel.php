<?php
/*we are using this model for brand table it was previously named as client*/
namespace App\Models\ClientModels;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\SoftDeletes;

class ClientModel extends Authenticatable
{
    use Notifiable;
    use SoftDeletes;
    public $table = "tbl_brands";

    protected $guard = "client";

    public $type = "client";
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'fkAgencyId', 'name', 'email', 'password','fkManagerId','isParentBrand',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];
    public static function boot() {
        parent::boot();

        static::deleting(function($client) { // before delete() method call this
            $client->accounts()->delete();
            // do the rest of the cleanup...
        });
    }
    public function isLoggedIn($id)
    {
        if (!auth()->guard("client")->check())
            return false;
        return auth()->guard("client")->user()->id == $id;
    }//end function

    public static function checkClientAvaiable($id)
    {
        return ClientModel::where("id", $id)->exists();
    }//end function

    public function isEmailUnique($email)
    {
        return !ClientModel::where("email", $email)->withTrashed()->exists();
    }//end function

    /*******************Relationships*********************/

    public function agency()
    {
        return $this->belongsTo('App\Models\AgencyModels\AgencyModel', 'fkAgencyId');

    }//end function
    public function accounts()
    {
        return $this->hasMany('App\Models\AccountModels\AccountModel','fkBrandId');
    }//end function
    /*******************Relationships*********************/
     public function brandName()
    {
        return $this->hasMany('App\Models\brandAssociation\brandAssociation','fkBrandId');
    }//end function
     public function manager()
    {
        return $this->belongsTo('App\User', 'fkManagerId');
    }

    /**
     * The users that belong to the roles.
     */
    public function brandManagers()
    {
        return $this->belongsToMany('App\User','tbl_brand_association','fkBrandId','fkManagerId');
    }
    /*public function hasAnyManager(){
          return null!==$this->brandManagers();
    }*/
     public function brandAssignedUsers(){
         return $this->belongsToMany('App\User', 'tbl_brand_association', 'fkBrandId', 'fkManagerId')->withTimestamps();
      }

    /**
     * Get the associatedBrandwith ams child brand for the blog post.
     */
    public function comments()
    {
        return $this->hasMany('App\Comment');
    }




}//end class
