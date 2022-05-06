<?php

namespace App\Models\DayPartingModels;


use DB;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Log;
use App\Models\DayPartingModels\Portfolios;
use App\Models\DayPartingModels\PortfolioAllCampaignList;


class PfCampaignSchedule extends Model
{
    public $table = 'tbl_ams_day_parting_pf_campaign_schedules';
    protected $primaryKey = "id";

    public static function insertPfCampaignSchedule($data)
    {
        try {
            Log::info('Schedule Inserted');
            pfCampaignSchedule::insert($data);
            return DB::getPDO()->lastInsertId();
        } catch (\Illuminate\Database\QueryException $ex) {
            Log::error($ex->getMessage());
        }
    }

    public static function updateSchedule($id, $data)
    {
        return PfCampaignSchedule::where('id', $id)->update($data);
    }

    /**
     *  The campaigns that belong to many schedule.
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function campaigns()
    {
        return $this->belongsToMany(
            PortfolioAllCampaignList::class,
            'tbl_ams_day_parting_campaign_schedule_ids',
            'fkScheduleId',
            'fkCampaignId'
        );
          //  ->wherePivot('enablingPausingStatus', NULL);
    }

    /**
     * The portfolios that belong to many schedule.
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function portfolios()
    {
        return $this->belongsToMany(Portfolios::class, 'tbl_ams_day_parting_portfolio_schedule_ids', 'fkScheduleId', 'fkPortfolioId')
            ->wherePivot('enablingPausingStatus', NULL);
    }

    /**
     * The schedule that belong to the sponsored Products.
     */
    public function sponsoredProduct()
    {
        return $this->belongsToMany(PortfolioAllCampaignList::class, 'tbl_ams_day_parting_campaign_schedule_ids', 'fkScheduleId', 'fkCampaignId')
            ->where(
                'reportType', Config::get('constants.portfolioSponsoredProduct'
                ))
            ->wherePivot(
                'enablingPausingtime', NULL
            );
    }

    /**
     * The schedule that belong to the sponsored Products.
     */
    public function sponsoredProductPivot()
    {
        return $this->belongsToMany(PortfolioAllCampaignList::class, 'tbl_ams_day_parting_campaign_schedule_ids', 'fkScheduleId', 'fkCampaignId')
            ->where(
                'reportType', Config::get('constants.portfolioSponsoredProduct'
            ))
            ->withPivot([
                'userSelection', 'enablingPausingtime', 'enablingPausingStatus', 'isEnablingPausingDone'
            ]);
    }

    /**
     * The schedule that belong to the sponsored Products.
     */
    public function sponsoredProductPivotForCrons()
    {
        return $this->belongsToMany(PortfolioAllCampaignList::class, 'tbl_ams_day_parting_campaign_schedule_ids', 'fkScheduleId', 'fkCampaignId')
            ->where(
                'reportType', Config::get('constants.portfolioSponsoredProduct'
            ))
            ->wherePivot(
                'enablingPausingtime', '!=', NULL
            )
            ->wherePivot(
                'isEnablingPausingDone', '!=', 1
            )
            ->withPivot([
                'userSelection', 'enablingPausingtime', 'enablingPausingStatus', 'isEnablingPausingDone'
            ]);
    }

    /**
     * The schedule that belong to the sponsored Products.
     */
    public function sponsoredBrand()
    {
        return $this->belongsToMany(PortfolioAllCampaignList::class, 'tbl_ams_day_parting_campaign_schedule_ids', 'fkScheduleId', 'fkCampaignId')
            ->where(
                'reportType', Config::get('constants.portfolioSponsoredBrand'
            ))
            ->wherePivot(
                'enablingPausingtime', NULL
            );
    }

    /**
     * The schedule that belong to the sponsored Products.
     */
    public function sponsoredBrandPivot()
    {
        return $this->belongsToMany(PortfolioAllCampaignList::class, 'tbl_ams_day_parting_campaign_schedule_ids', 'fkScheduleId', 'fkCampaignId')
            ->where(
                'reportType', Config::get('constants.portfolioSponsoredBrand'
            ))
            ->withPivot([
                'userSelection', 'enablingPausingtime', 'enablingPausingStatus', 'isEnablingPausingDone'
            ]);
    }

    /**
     * The schedule that belong to the sponsored Products.
     */
    public function pauseCampaignPivotForCrons()
    {
        return $this->belongsToMany(PortfolioAllCampaignList::class, 'tbl_ams_day_parting_campaign_schedule_ids', 'fkScheduleId', 'fkCampaignId')
            ->wherePivot(
                'enablingPausingtime', '!=', NULL
            )
            ->wherePivot(
                'isEnablingPausingDone', '!=', 1
            )
            ->withPivot([
                'userSelection', 'enablingPausingtime', 'enablingPausingStatus', 'isEnablingPausingDone'
            ]);
    }

    /**
     * The schedule that belong to the sponsored Products.
     */
    public function sponsoredBrandPivotForCrons()
    {
        return $this->belongsToMany(PortfolioAllCampaignList::class, 'tbl_ams_day_parting_campaign_schedule_ids', 'fkScheduleId', 'fkCampaignId')
            ->where(
                'reportType', Config::get('constants.portfolioSponsoredBrand'
            ))
            ->wherePivot(
                'enablingPausingtime', '!=', NULL
            )
            ->wherePivot(
                'isEnablingPausingDone', '!=', 1
            )
            ->withPivot([
                'userSelection', 'enablingPausingtime', 'enablingPausingStatus', 'isEnablingPausingDone'
            ]);
    }
    /**
     * The schedule that belong to the sponsored Products.
     */
    public function sponsoredDisplay()
    {
        return $this->belongsToMany(PortfolioAllCampaignList::class, 'tbl_ams_day_parting_campaign_schedule_ids', 'fkScheduleId', 'fkCampaignId')
            ->where(
                'reportType', Config::get('constants.portfolioSponsoredDisplay'
            ))
            ->wherePivot(
                'enablingPausingtime', NULL
            );
    }

    /**
     * The schedule that belong to the sponsored Products.
     */
    public function sponsoredDisplayPivot()
    {
        return $this->belongsToMany(PortfolioAllCampaignList::class, 'tbl_ams_day_parting_campaign_schedule_ids', 'fkScheduleId', 'fkCampaignId')
            ->where(
                'reportType', Config::get('constants.portfolioSponsoredDisplay'
            ))
            ->withPivot([
                'userSelection', 'enablingPausingtime', 'enablingPausingStatus', 'isEnablingPausingDone'
            ]);
    }

    /**
     * The schedule that belong to the sponsored Products.
     */
    public function sponsoredDisplayPivotForCrons()
    {
        return $this->belongsToMany(PortfolioAllCampaignList::class, 'tbl_ams_day_parting_campaign_schedule_ids', 'fkScheduleId', 'fkCampaignId')
            ->where(
                'reportType', Config::get('constants.portfolioSponsoredDisplay'
            ))
            ->wherePivot(
                'enablingPausingtime', '!=', NULL
            )
            ->wherePivot(
                'isEnablingPausingDone', '!=', 1
            )
            ->withPivot([
                'userSelection', 'enablingPausingtime', 'enablingPausingStatus', 'isEnablingPausingDone'
            ]);
    }
}
