<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BiddingRuleTracker extends Model
{
    protected $table = 'tbl_ams_bidding_tracker';
    protected $primaryKey = 'id';
    protected $fillable = ['profileId','adGroupId','campaignId','state','reportType','oldBid','bid','keywordId','code','creationDate'];
    public $timestamps = false;
}
