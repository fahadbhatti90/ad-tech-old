<?php

namespace App\Http\Controllers\ClientAuth;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Models\ProductPreviewModels\GraphDataModels\AsinDailyModels;

class ProductTableController extends Controller
{ 
    public function __construct()
    {
        $this->middleware('auth.manager');
    }//end constructor
    public function productTable(){ 
        // return "wqork";
        return AsinDailyModels::getDailyCompCardData(3760911, 16479981011, "B003YCF47U","20191231");
        return view("client.productTable")
        ->with("asins",$asins);
    }
}
