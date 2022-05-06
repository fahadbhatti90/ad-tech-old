<?php

namespace App\Console\Commands\ScrapperCommands;

use App\Libraries\InstantScrapingController;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use App\Models\ScrapingModels\ActivityTrackerModel;

class ManageInstantASINScrapingCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'ManageInstantASINScrapingCommand:instantAsin {reqData*}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command For Intializing and Startin Threads For Instant ASIN Scraping';

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
        $arguments = $this->argument("reqData");
            if( count($arguments) < 2 ){
                    $this->error(("Some of the reqired data missing"));
                    return;
            }
            $threadName = (($arguments[0]));
            $cokieeval = (($arguments[1]));
            $totalNumberOfThread = (($arguments[2]));
            $this->setActivity("Thread$threadName Starts", "Thread$threadName:info");
        
            try {
                
                ActivityTrackerModel::setActivity("Starting Scraping Thread => ".$threadName ,"instant","ManageASINScrapingCommand:cron","app\Console\ScrapperCommands\Commands",date('Y-m-d H:i:s'));
                
                $sc = new InstantScrapingController($totalNumberOfThread);
                $sc->cok = $cokieeval;
                
                $sc->ScrapDataInstantlyMultithread($threadName);

                $this->info("Thread Execution Completed Successfully");
            } catch (\Throwable $th) {
                Log::error("filePath:app\Console\Commands\ScrapperCommands\ScheduleCronCommand:cron Their's been an error while scraping Thread = ".$threadName."Error Message ". str_limit($th->getMessage(),500));
        
                $this->setActivity("Their's been an error while scraping Thread = ".$threadName."Error Message ". str_limit($th->getMessage(),500) ,"instant");
               
                $this->error("filePath:app\Console\Commands\ScrapperCommands\ScheduleCronCommand:cron Their's been an error while scraping");
                $this->line("Error Message ". str_limit($th->getMessage(),500));

            }//end catch
        
    }//end handle function
    
    private function setActivity($activity, $type){
        ActivityTrackerModel::setActivity(
            $activity, 
            $type, 
            "ManageInstantASINScrapingCommand", 
            "App\Console\Commands\ScrapperCommands", 
            date('Y-m-d H:i:s')
        );
    }
}
