<?php

namespace App\Http\Controllers\Manager;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Libraries\NotificationHelper;
use App\Models\Brands\brandAssociation;
use App\Models\ClientModels\ClientModel;

class GlobalBrandSwitcherController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth.manager');
    }
    public function getManagerBrands(){
        $brands = brandAssociation::has("brand")
            ->with("brand:id,name")
            ->select("fkBrandId")
            ->where("fkManagerId",auth()->user()->id)
            ->distinct()
            ->get();
        return [
            "status"=>true,
            "data"=>$brands,
            "selected"=>getBrandId(),
            "selectedBrandName"=>getBrandName(),
        ];
    }//end function
    public function switchActiveBrand($brandId){
        $brands = brandAssociation::has("brand")
        ->with("brand:id,name")
        ->select("fkBrandId","fkManagerId")
        ->where("fkManagerId",auth()->user()->id)
        ->where("fkBrandId", $brandId)
        ->distinct();
        if($brands->exists()){
            $brands = $brands->first();
            session(['m'.auth()->user()->id => $brands]);
        }
        return ["status"=>true, "unseenNotiCount" => NotificationHelper::getNotificaitonCount(session("activeRole"))];
    }
}//end class
