<?php

namespace App\Models\ClientModels;

use Illuminate\Database\Eloquent\Model;

class CampaignTagsModel extends Model
{
    public $table = "tbl_campaign_tags";
    public $timestamps = false;
    protected $fillable = [
        'tag',
        "fkManagerId"
    ];
    /**
     * isTagAlreadyExists
     *
     * @param mixed $tagName
     * @return void
     */
    public static function isDuplicateTag($tagId, $tagName)
    {
        return self::where("tag", $tagName)->where("fkManagerId", auth()->user()->id)->where("id", "<>", $tagId)->exists();
    }
    public function compaigns()
    {
        return $this->hasMany('App\Models\ClientModels\CampaignTagsAssignmentModel', 'fkTagId');

    } //end function

    /**
    * Retrieve the model for a bound value.
    *
    * @param  mixed  $value
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function resolveRouteBinding($value)
    {
        return $this->with("compaigns")->where('id', $value)->first() ?? ["status"=>false,"message"=>"No Such Tag Found"];
    }
}
