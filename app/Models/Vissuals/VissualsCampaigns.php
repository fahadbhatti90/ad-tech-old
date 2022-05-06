<?php

namespace App\Models\Vissuals;

use Illuminate\Database\Eloquent\Model;

class VissualsCampaigns extends Model
{
    public $connection ="mysql";
    public $table = "tbl_rtl_ams_campaign_list";
    
    // public function scopeEnabled($query)
    // {   
    //     return $query->whereIn("campaignId",$campainIds);
    // }
}
