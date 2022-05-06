<?php

namespace App\Console\Commands\ScrapperCommands;

use Illuminate\Console\Command;
use App\Libraries\ScraperConstant;
use Symfony\Component\Process\Process;
use Graze\ParallelProcess\PriorityPool;
use App\Models\ScrapingModels\asinModel;
use App\Models\ScrapingModels\CronModel;
use Graze\ParallelProcess\Display\Lines;
use App\Models\ScrapingModels\SettingsModel;
use App\Models\ScrapingModels\ActivityTrackerModel;
use Symfony\Component\Console\Output\ConsoleOutput;

class ScheduleCronCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'ScheduleCronCommand:cron';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Threads Creation,ASIN allocation and Threads execution will be handled here';

    /**j
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

        
        
        // Here i need to set all asins status of valid collections to be at processing state; 
        $settings = SettingsModel::where("name","asin_threads");
        if(!$settings->exists()){
            $this->error(("No Settings Found"));
                
            ActivityTrackerModel::setActivity("No Settings Found","info","ScrapingController","App\Libraries\ScrapingController",date('Y-m-d H:i:s'));
            return ;
        }

        do {
                $crons  = CronModel::where("isRunning",1)
                    ;
                $c_id = null;
                if(!$crons->exists()){
                    $this->info("No Cron is Running Breaking Out");
                    ActivityTrackerModel::setActivity("No Cron is Running Breaking Out","info","ScrapingController","App\Libraries\ScrapingController",date('Y-m-d H:i:s'));
                 break;
                }
                $c_id = $crons->select("c_id")->get();
                $totalNumberOfThread = $settings->select("value")
                                    ->get()[0]->value;
                $asins = asinModel::where("asin_status","0")->whereIn("c_id",$c_id)->select("asin_id")->get();
            if(count($asins)<=0){
                $Check503 = asinModel::where("asin_status",ScraperConstant::ERROR_SERVICE_NOT_AVAILABLE)->whereIn("c_id",$c_id);
                if($Check503->exists()){
                    $this->info("Going to sleep for 1 minute");
                    ActivityTrackerModel::setActivity("Going to sleep for 1 minute","info","ScrapingController","App\Libraries\ScrapingController",date('Y-m-d H:i:s'));
                    sleep(60);
                    $this->info("Woke up");
                    ActivityTrackerModel::setActivity("Woke up","info","ScrapingController","App\Libraries\ScrapingController",date('Y-m-d H:i:s'));
                    continue;
                }
                $this->info("No More Asin Found ");
                ActivityTrackerModel::setActivity("No More Asin Found","info","ScrapingController","App\Libraries\ScrapingController",date('Y-m-d H:i:s'));
                break;
            }
            if(count($asins) > $totalNumberOfThread){
                $asinPerThread = round(count($asins)/$totalNumberOfThread);
            }
            else
            {
                $totalNumberOfThread = count($asins);
                $asinPerThread = 1;
            }
            
            $this->info(count($asins));
            $this->info($totalNumberOfThread);
            $this->info($asinPerThread);
            $chunked = array_chunk($asins->toArray(),$asinPerThread);
            $tn = 1; 
            foreach ($chunked as $value) { //1 =>2, 2=>3, 3=>4, 4=>5, 5=>6, 6=>7 breakl
              
                asinModel::whereIn("asin_id",$value)
                    ->update(["allocatedThread"=>"T$tn"]);
                        $this->info("ASIN_IDs=>".json_encode($value));
                        ActivityTrackerModel::setActivity("ASIN_IDs=>".json_encode($value),"info","ScheduleCronCommand:cron","App\Console\Commands\ScrapperCommands",date('Y-m-d H:i:s'));
                $tn++;
                
            }//end for loop
        
            $pool = new PriorityPool();
            for ($i=1; $i <= $totalNumberOfThread; $i++) { 
                # code...
                if(str_contains(url('/'), 'http://localhost'))
                    $pool->add(new Process("php artisan ManageASINScrapingCommand:asin T$i $i"));
                else
                    $pool->add(new Process("php /var/www/html/helo-tek/artisan ManageASINScrapingCommand:asin T$i $i"));
            }

            asinModel::where("asin_status","0")->whereIn("c_id",$c_id)
            ->update(["asin_status"=>"1"]);
            $this->info("Executing Cron");
            ActivityTrackerModel::setActivity("Executing Cron","info","ScrapingController","App\Libraries\ScrapingController",date('Y-m-d H:i:s'));
            $output = new ConsoleOutput();
            $lines = new Lines($output, $pool);
            $lines->run();

            
            $asinForScraping = asinModel::whereNotIn("asin_status",["2","i","-4","-404","-8","-5"])->whereIn("c_id",$c_id);
            if(!$asinForScraping->exists()){
                $this->info("NO ASIN FOUND CHECKING 503");
                ActivityTrackerModel::setActivity("NO ASIN FOUND CHECKING 503","info","ScrapingController","App\Libraries\ScrapingController",date('Y-m-d H:i:s'));
                $Check503 = asinModel::where("asin_status","-5")->whereIn("c_id",$c_id);
                if($Check503->exists()){
                    $this->info("Going to sleep for 1 minute From END of the loop");
                    ActivityTrackerModel::setActivity("Going to sleep for 1 minute From END of the loop","info","ScrapingController","App\Libraries\ScrapingController",date('Y-m-d H:i:s'));
                    sleep(60);
                    $this->info("Woke up minute From END of the loop");
                    ActivityTrackerModel::setActivity("Woke up minute From END of the loop","info","ScrapingController","App\Libraries\ScrapingController",date('Y-m-d H:i:s'));
                    continue;
                }
                
                $this->info("NO 503 FOUND BREAKING");
                ActivityTrackerModel::setActivity("NO 503 FOUND BREAKING","info","ScrapingController","App\Libraries\ScrapingController",date('Y-m-d H:i:s'));
                asinModel::where("allocatedThread","<>","NA")->whereIn("c_id",$c_id)->update(["allocatedThread"=>"NA"]);
                break;
            }//end if
            $this->info("Loop Continue");
            ActivityTrackerModel::setActivity("Loop Continue","info","ScrapingController","App\Libraries\ScrapingController",date('Y-m-d H:i:s'));
            $asinForScraping->update(["asin_status"=>"0"]);
      } while (true);
        
        
    
    }//end handle function 
}//end command class
