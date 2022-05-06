<?php

namespace App\Models\ams;

use Illuminate\Database\Eloquent\Model;

class ProfileModel extends Model
{
    protected $table = "tbl_ams_profiles";
    protected $fillable = [
        'fkConfigId',
        'profileId',
        'countryCode',
        'currencyCode',
        'timezone',
        'marketplaceStringId',
        'entityId',
        'type',
        'name',
        'adGroupSpSixtyDays',
        'aSINsSixtyDays',
        'campaignSpSixtyDays',
        'keywordSbSixtyDays',
        'keywordSpSixtyDays',
        'productAdsSixtyDays',
        'productTargetingSixtyDays',
        'creationDate',
        'SponsoredBrandCampaignsSixtyDays',
        'SponsoredDisplayCampaignsSixtyDays',
        'SponsoredDisplayAdgroupSixtyDays',
        'SponsoredDisplayProductAdsSixtyDays',
        'isActive',
        'isSandboxProfile',
        'SponsoredBrandAdgroupSixtyDays',
        'SponsoredBrandTargetingSixtyDays',
        'adGroupSdSixtyDays'
    ];
    public $timestamps = false;
}
