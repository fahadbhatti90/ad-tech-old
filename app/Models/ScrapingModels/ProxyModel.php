<?php

namespace App\Models\ScrapingModels;

use Illuminate\Database\Eloquent\Model;

class ProxyModel extends Model
{
    protected $table = "tbl_proxy";
    public $timestamps = false;
    public static function getRandom(){
        $proxy_ip = ProxyModel::where("is_blocked",false);
        if(!$proxy_ip->exists()){
           return "NA";
        }
        return $proxy_ip->inRandomOrder()->first();
    }
    public static function checkProxyExists(){
        $proxy_ip = ProxyModel::where("is_blocked",false);
        if(!$proxy_ip->exists()){

           return $proxy_ip->first();
        }
    }
    public static function markProxyBlock($p_id){
       $proxy =  ProxyModel::where("id",$p_id);
       if($proxy->exists()){
           $proxy = $proxy->get()[0];
           $proxy->is_blocked = 1;
           $proxy->save();
       }
    }
}
