<?php

namespace App\Models\DayPartingModels;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;
use DB;

class Portfolios extends Model
{
    public $table = 'tbl_ams_day_parting_portfolios';
    protected $primaryKey = "id";

    public static function insertPortfolioList($data)
    {
        foreach ($data as $row) {
            $existData = Portfolios::where([
                'portfolioId' => $row['portfolioId'],
                'fkProfileId' => $row['fkProfileId']
            ])->get();

            if ($existData->isEmpty()) {
                try {
                    Portfolios::insertGetId($row);
                    Log::info('Portfolio Id = '.$row['portfolioId'].' List Updated');
                } catch (\Illuminate\Database\QueryException $ex) {
                    Log::error($ex->getMessage());
                }
            }else{
                Portfolios::where('portfolioId', $row['portfolioId'])
                    ->update($row);
                Log::info('Already exist in DB. ProfileId :' . $row['fkProfileId']);
            }
        }
    }

    /**
     * This Function is used to update Portfolio List
     * @param $data
     */
    public static function updatePortfolio($data)
    {
        foreach ($data as $row){
            $existData = Portfolios::where('portfolioId', $row['portfolioId'])->exists();
            if ($existData){
                try {
                    Portfolios::where('portfolioId', $row['portfolioId'])
                        ->update([
                            'state'=> $row['state'],
                            'updated_at' => $row['updated_at']
                        ]);
                    Log::info('Portfolio Id = '.$row['portfolioId'].' List Updated');
                } catch (\Illuminate\Database\QueryException $ex) {
                    Log::error($ex->getMessage());
                }
            }
        }
    }

    /**
     * The schedule that belong to the portfolio.
     */
    public function schedules()
    {
        return $this->belongsToMany(PfCampaignSchedule::class, 'tbl_ams_day_parting_portfolio_schedule_ids', 'fkCampaignId','fkScheduleId');
    }

    /**
     * This schedule is used to get only active campaigns
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function scheduleCampaigns()
    {
        return $this->belongsToMany(PfCampaignSchedule::class, 'tbl_ams_day_parting_portfolio_schedule_ids', 'fkPortfolioId','fkScheduleId')
            ->wherePivot('enablingPausingStatus', NULL);
    }

    public function campaigns()
    {
        return $this->hasMany(PortfolioAllCampaignList::class, 'portfolioId', 'portfolioId');
    }

    /**
     * This function is used to store Portfolio data
     *
     * @param $storeArray
     */
    public static function storePortfolios($storeArray)
    {
        Log::info('Start Insert data into DB.');
        DB::transaction(function () use ($storeArray) {
            foreach ($storeArray as $row) {
                $existData = DB::table('tbl_ams_portfolios')->where([
                    'portfolioId' => $row['portfolioId'],
                    'fkProfileId' => $row['fkProfileId']
                ])->get();
                if ($existData->isEmpty()) {
                    try {
                        $id = DB::table('tbl_ams_portfolios')->insertGetId($row);
                        Log::info('Insert Record id = ' . $id);
                    } catch (\Illuminate\Database\QueryException $ex) {
                        Log::error($ex->getMessage());
                    }
                } else {
                    DB::table('tbl_ams_portfolios')
                        ->where([
                            'portfolioId' => $row['portfolioId'],
                            'fkProfileId' => $row['fkProfileId']
                        ])->update($row);
                    Log::info('Already exist in DB. ProfileId :' . $row['fkProfileId']);
                }
            }
        }, 3);
        Log::info('End Insertion query.');
    }

    /**
     * This function is used to get specific profile portfolios list
     *
     * @param $array
     * @return mixed
     */
    public static function getPortfoliosList($array)
    {
        return DB::table('tbl_ams_portfolios')->where([
            'fkProfileId' => $array['fkProfileId'],
            'state' => 'enabled'
        ])->get();
    }
}
