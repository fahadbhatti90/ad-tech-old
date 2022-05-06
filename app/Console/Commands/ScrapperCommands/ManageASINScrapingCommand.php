<?php

namespace App\Console\Commands\ScrapperCommands;

use App\Libraries\DailyScrapingController;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use App\Models\ScrapingModels\ActivityTrackerModel;
class ManageASINScrapingCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'ManageASINScrapingCommand:asin {reqData*}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command For Intializing and Startin Threads For Daily ASIN Scraping';

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
            try {
               
                ActivityTrackerModel::setActivity("Starting Scraping Thread => ".$threadName ,"info","ManageASINScrapingCommand:cron","app\Console\ScrapperCommands\Commands",date('Y-m-d H:i:s'));

                $sc = new DailyScrapingController();
                $sc->cok = $cokieeval;
               
                $sc->ScrapData($threadName); 
              
                $this->info("Thread Execution Completed Successfully");
            } catch (\Throwable $th) {
                Log::error("filePath:app\Console\Commands\ScrapperCommands\ScheduleCronCommand:cron Their's been an error while scraping Thread = ".$threadName."Error Message ".($th->getMessage()));
               
                ActivityTrackerModel::setActivity("Their's been an error while scraping Thread = ".$threadName."Error Message ". str_limit($th->getMessage(),500) ,"info","ManageASINScrapingCommand:cron","app\Console\ScrapperCommands\Commands",date('Y-m-d H:i:s'));

                $this->error("filePath:app\Console\Commands\ScrapperCommands\ScheduleCronCommand:cron Their's been an error while scraping");
                $this->line("Error Message ". str_limit($th->getMessage(),500));

            }
        
     
    }
}
