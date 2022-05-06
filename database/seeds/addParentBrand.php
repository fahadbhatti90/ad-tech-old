<?php

use App\Models\AccountModels\AccountModel;
use Illuminate\Database\Seeder;
use App\Models\ClientModels\ClientModel;
use App\User;
use Illuminate\Support\Facades\Hash;

class addParentBrand extends Seeder
{
    function __construct() {
        // copy your old constructor function code here
    }
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        /*$GetserId = ClientModel::where('email', $userEmail)->first();
        $brandId = $GetserId->id;
        */
        $getAdmin = User::has("admins")->where('deleted_at', NULL)->where('id','!=', 1)->first();
        if (!empty($getAdmin)) {
            $getAdminCount = $getAdmin->count();
            if ($getAdminCount > 0) {
                $adminId = $getAdmin->id;
            $getParentBrand = ClientModel::where('isParentBrand', 1)->withTrashed()->first();
            if (!empty($getParentBrand)) {
                $parentBrandCount = $getParentBrand->count();
                if ($parentBrandCount > 0) {
                    $parentBrandId = $getParentBrand->id;
                    $client = ClientModel::find($parentBrandId);
                    $client->name =  'Master Brand';
                    $client->fkAgencyId = 1;
                    $client->email = 'masterBrand@ad-tech.diginc.pk';
                    $client->save();
                    //$client->brandAssignedUsers()->wherePivot('fkManagerId', $adminId)->detach();
                    //$client->brandAssignedUsers()->attach($adminId);
                    $accountUpdate=AccountModel::where('deleted_at','!=', NULL)->withTrashed()
                        ->update(['fkBrandId' => $parentBrandId ,'deleted_at'=> NULL
                        ]);
                } else {
                    return $this->addParentBrand($adminId);
                }
            } else {
                    return $this->addParentBrand($adminId);
            }
            }
        }
    }
    public function addParentBrand($adminId)
    {
        $createBrand = ClientModel::create([
            'name' => 'Master Brand',
            'email' => 'masterBrand@ad-tech.diginc.pk',
            'fkAgencyId' => 1,
            'password' => Hash::make('123456'),
            'isParentBrand' => 1
        ]);//end create function
        $brandId = $createBrand->id;
        $clientModel = ClientModel::find($brandId);
        $clientModel->brandAssignedUsers()->attach($adminId);
        //at the end restore all deleted brands and assign it ot parent brand
        $accountUpdate=AccountModel::where('deleted_at','!=', NULL)->withTrashed()
            ->update(['fkBrandId' => $brandId ,'deleted_at'=> NULL
            ]);
    }
}
