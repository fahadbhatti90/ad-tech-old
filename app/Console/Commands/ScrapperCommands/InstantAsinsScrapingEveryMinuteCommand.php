<?php

namespace App\Console\Commands\ScrapperCommands;

use Illuminate\Console\Command;
use App\Models\ScrapingModels\ActivityTrackerModel;
use App\Http\Controllers\InstantAsinTempSchedulesController;

class InstantAsinsScrapingEveryMinuteCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'InstantAsinsScrapingEveryMinuteCommand:instant';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'This command will scrap instant asins when user uploads them by fetching temprory schedules from tbl_asins_instant_temp_schedules';

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
        if(!InstantAsinTempSchedulesController::shouldProceed()) return;
        $this->setActivity("Some Schedule Found, Proceeding to update status", "everyMinute:info");
        $this->info("Some Schedule Found, Proceeding to update status");
        InstantAsinTempSchedulesController::updateScheduleStatusToRunning();

        $this->info("Schedule updated, scraping start");
        $this->setActivity("Schedule updated, scraping start", "everyMinute:info");
        $status = InstantAsinTempSchedulesController::startScraping();
        if($status["status"]){
            $this->info($status["message"]);
            $this->setActivity($status["message"], "everyMinute:info");
            $this->info("Scraping complete, getting collection id");
            $this->setActivity("Scraping complete, getting collection id", "everyMinute:info");
            
            $activeCollectionIds  = InstantAsinTempSchedulesController::getActiveScheduleCollectionIds();

            $this->info("Removing completed schedule ".json_encode($activeCollectionIds));
            \Log::info("Removing completed schedule ".json_encode($activeCollectionIds));
            InstantAsinTempSchedulesController::removeRunningSchedule();
            
            $this->info("Process complete Logging Event");
            
            //Event Logging Code 
                 $commandArray = array();
                 $commandArray['collectionIds'] = $activeCollectionIds;
                 \Artisan::call('offerNotFoundInstantScraping:offerNotFound', $commandArray);//fkCollectionId
                 \Artisan::call('eventTrackingInstantScraping:cron', $commandArray);
            //Event Logging Code
            $this->info("Process complete Logging Event");
            $this->info("Process start Logging Segments");
            $segmentCommandArray = array();
            $segmentCommandArray['activeCollections'] = $activeCollectionIds;
            //Segment Logging Code
            \Artisan::call('asinSegmentsCron:cron', $segmentCommandArray);
            $this->call('refreshProductTableView:refresh');
            //Segment Logging Code
            $this->info("Process complete Logging Segments");
        }
        
        $this->info($status["message"]);
        
    }
    private function setActivity($activity, $type){
        ActivityTrackerModel::setActivity(
            $activity, 
            $type, 
            "InstantAsinsScrapingEveryMinuteCommand", 
            "App\Console\Commands\ScrapperCommands", 
            date('Y-m-d H:i:s')
        );
    }
}
