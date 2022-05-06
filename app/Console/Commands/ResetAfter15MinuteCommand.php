<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\ScrapingModels\asinModel;
use App\Models\ScrapingModels\CronModel;
use App\Models\ScrapingModels\CollectionsModel;
use App\Models\ScrapingModels\TempSearchRankModel;
use App\Models\ScrapingModels\ActivityTrackerModel;
use App\Models\ScrapingModels\InstantASINTempModel;

class ResetAfter15MinuteCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'ResetEvery15Minute:rest';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'This command will hande the 503 error';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $crons  = CronModel::where("isRunning",1)
        ->select("c_id");
        if($crons->exists()){
            $c_id = $crons->get();
            $asinForScraping = asinModel::where("asin_status","=","-5")->whereIn("c_id",$c_id)->update(["asin_status"=>"0"]);
            ActivityTrackerModel::setActivity(
                "Daily ASIN -5 updated => ".json_encode($asinForScraping),
                "Commands",
                "ResetEvery15Minute:rest",
                "app\Console\Commands",
                date('Y-m-d H:i:s')
            );
        }

               $asinForScraping = InstantASINTempModel::where("asin_status","=","-5")
                ->update([
                    "asin_status" => "0"
                ]);
                ActivityTrackerModel::setActivity(
                    "Instant ASIN -5 updated => ".json_encode($asinForScraping),
                    "Commands",
                    "ResetEvery15Minute:rest",
                    "app\Console\Commands",
                    date('Y-m-d H:i:s')
                );
        // $instantCollectionIds = CollectionsModel::where("c_type",0)->select("id");
        // if($instantCollectionIds->exists()){
        //     $c_id = $instantCollectionIds->get();
        //     $asinForScraping = asinModel::where("asin_status","=","-5")
        //     ->where("allocatedThread","<>","NA")
        //     ->whereIn("c_id",$c_id)
        //     ->update(["asin_status"=>"0"]);
        //     ActivityTrackerModel::setActivity(
        //         "Instant ASIN -5 updated => ".json_encode($asinForScraping),
        //         "Commands",
        //         "ResetEvery15Minute:rest",
        //         "app\Console\Commands",
        //         date('Y-m-d H:i:s')
        //     );
        // }

        $asinForScraping = TempSearchRankModel::where("urlStatus","-5")
            ->update([
                "urlStatus" => "0"
            ]);
            ActivityTrackerModel::setActivity(
                "Search Rank -5 updated => ".json_encode($asinForScraping),
                "Commands",
                "ResetEvery15Minute:rest",
                "app\Console\Commands",
                date('Y-m-d H:i:s')
            );
     

        ActivityTrackerModel::setActivity("15 minut Command Run","Commands","ResetEvery15Minute:rest","app\Console\Commands",date('Y-m-d H:i:s'));
       
       
    }
}
