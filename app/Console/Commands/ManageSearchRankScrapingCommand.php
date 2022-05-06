<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Events\SendNotification;
use Illuminate\Support\Facades\Log;
use App\Libraries\ScrapingController;
use Symfony\Component\Process\Process;
use Graze\ParallelProcess\PriorityPool;
use Graze\ParallelProcess\Display\Table;
use App\Libraries\DailyScrapingController;
use App\Models\ScrapingModels\SettingsModel;
use App\Models\ScrapingModels\SearchTermModel;
use App\Models\ScrapingModels\TempSearchRankModel;
use App\Models\ScrapingModels\ActivityTrackerModel;
use Symfony\Component\Console\Output\ConsoleOutput;
use App\Models\ScrapingModels\SearchRankCrawlerModel;
use App\Models\ScrapingModels\SearchRankFailStatuses;
use App\Http\Controllers\SearchRankScrapingController;

class ManageSearchRankScrapingCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'ManageSearchRankScrapingCommand:sr';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command for getting valid schedule and executing schedule of search rank scraper';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }
    public function init($crawler_id){
       
    }
    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {

        if(SearchRankCrawlerModel::isAnySchedulRunning()){
            $this->info("All Ready running");
            return;
        }

        //ScheduleTime Check
        if(!$this->checkScheduleTime()) return;

        $this->info("Time To Run Search Rank Schedule");
        $this->setActivity("Time To Run Search Rank Schedule", "info");

        $settings = SettingsModel::where("name","sr_threads");
        if(!$settings->exists()){
            $this->error("No Thread Count Found Settings Found For Search Rank");
            $this->setActivity("Settings Table: No Thread Count Found For Search Rank", "errorSettings");
            return;
        }
        
        $sleepTimeSettings = SettingsModel::where("name","SrSleepTime");
        if(!$sleepTimeSettings->exists()){
            $this->error("No Sleep Time Settings Found");
            $this->setActivity("No Sleep Time Settings Found", "errorSettings");
            return;
        }
        
        $crawler_ids = ScrapingController::getValidDepartmentId();
        if(count($crawler_ids) > 0){
            $srController = new SearchRankScrapingController();
            //Temp Url Creation

            $this->createTempUrlsOfAllSchedules($srController, $crawler_ids, $sleepTimeSettings);

            $totalNumberOfThread = $settings->select("value")->first()->value;
            do {
               $isCompleted = $this->processInitAndExe($totalNumberOfThread);
               $this->setActivity("Is Search Rank Finished=>".json_encode($isCompleted), "Cron Completion status");

               if($isCompleted){
                    $tempUrls = TempSearchRankModel::where("urlStatus","-5");
                    if($tempUrls->exists())
                    {
                        $this->info("Going to sleep for 1 minute");
                        $this->setActivity("Going to sleep for 1 minute", "info");
                        
                        sleep(60);

                        $this->info("Woke up");
                        $this->setActivity("Woke up", "info");

                        continue;
                    }
                    $this->info("No More Urls Found Search Rank Scraping Completed") ;
                    $this->setActivity("No More Urls Found Search Rank Scraping Completed", "Completed");
                    break;
               }
            } while (true);
            
            SearchRankCrawlerModel::ResetAllSchdule();            
            $this->_manageNotification($crawler_ids);
        } 
        else
        {
            $this->info("No Search Rank Schedule Found Schedule Count = ".count($crawler_ids));
            $this->setActivity("No Search Rank Schdule Found Schedule Count = ".count($crawler_ids), "info");  
        }
    }//end handle function
    private function createTempUrlsOfAllSchedules($srController, $crawler_ids, $sleepTimeSettings){

        $totalNumberOfUrls = 0;
        for ($i=0; $i < count($crawler_ids); $i++) 
        {
            $this->setActivity( "Initializing Scraping Craw ler ID = ".$crawler_ids[$i]["crawler_id"]." Starting Cron.", "info");
   
            $crawler_id = $crawler_ids[$i]["crawler_id"];

            //Varify that crawler_id contains searchTerms
            if(!SearchTermModel::where("crawler_id",$crawler_id)->exists()){
                $this->broadcastNoSearchTermFoundNotificaitons($crawler_id);
                continue;   
            }

            $urls = SearchTermModel::where("crawler_id",$crawler_id)->with("crawler")->get();
            
            $sleepTime = $sleepTimeSettings->select("value")->first()->value;

            $this->tempUrlsCreation($urls, $srController,  $crawler_id, $totalNumberOfUrls, $sleepTime);
        }//end for loop
    }
    private function setActivity($activity, $activity_type){
        ActivityTrackerModel::setActivity(
            $activity, 
            $activity_type, 
            "ManageSearchRankScrapingCommand:sr",
            "App\Console\Commands",
            date('Y-m-d H:i:s')
        );
    }//end function
    private function broadcastNoSearchTermFoundNotificaitons($crawler_id){
        $this->info("No Search Terms Found");
        $this->setActivity("No Search Terms Found Crawler Id ".$crawler_id, "Error DB");
        $err = array(
            "Table Name:" => "Search Rank Crawler, Search Terms",
            "Item:" => 'Search Terms',
            "Crawler_id"=>$crawler_id,
            "Status:" => 'Missing',
            "Created At:" => date("Y-m-d H:i"),
        );
        broadcast(new SendNotification(
                    2,
                    "Search Rank Error",
                    "No Search Term Found Against Crawler ID:#".$crawler_id,json_encode($err),
                    null,
                    date("Y-m-d H:i")
                )
            )->toOthers();
        // $details = array(
        //     "Table Name:" => "Search Rank Crawler, Search Terms",
        //     "Item:" => 'Search Terms',
        //     "Crawler_id"=>$crawler_id,
        //     "Status:" => 'Missing',
        //     "Created At:" => date("Y-m-d H:i"),
        // );
    
        // broadcast(new SendNotification(2,"Search Rank Error","No Search Terms Found Crawler Id ".json_encode($err),json_encode( $details),date("Y-m-d H:i")))->toOthers();
    }//end function
    private function checkScheduleTime(){
        $current_hour = date("H",strtotime(date("H:i")));
        $schduleTime = SettingsModel::where("name","SrScheduleTime");
        if(!$schduleTime->exists()){
            $this->error("No SR schedule time found in settings table");
            $this->setActivity("No SR schedule time found in settings table","Error");
            // $details = array(
            //     "Table Name:" => "Settings",
            //     "Item:" => 'Thread Count',
            //     "Status:" => 'Missing',
            //     "Created At:" => date("Y-m-d H:i"),
            // );
        
            // broadcast(new SendNotification(2,"Thread Count Missing","Settings Table: No Thread Count Found For Search Rank",json_encode( $details),date("Y-m-d H:i")))->toOthers();
            return false;
        }
        
        $schduleTime = $schduleTime->select("value")->first()->value;
        
        $this->info("Settings Time ".date("H",strtotime($schduleTime)));
        $this->info("Current Hour ".$current_hour);

        return !(date("H",strtotime($schduleTime)) != $current_hour);//true = not schedule time, false = schedule time
        
    }//end function
    private function readySearchRankNoti( $fail_data, $crawlersNoti ) {
                    $notiDetails = array();
                    $crawlerId = $crawlerName = $searchTermsTotal = "";
                    foreach ($crawlersNoti as $key => $value) {
                         $crawlerId .= $value->id.", ";
                         $crawlerName .=$value->c_name.", ";
                         $searchTermsTotal .=$value->searchTerm->count().", ";
                    }
                    $crawlerId = str_replace_last(", ","",$crawlerId);
                    $crawlerName = str_replace_last(", ","",$crawlerName);
                    $searchTermsTotal = str_replace_last(", ","",$searchTermsTotal);

                    $notiDetails["crawler Ids"] = $crawlerId;
                    $notiDetails["crawler Names"] = $crawlerName;
                    $notiDetails["Total Search Terms"] = $searchTermsTotal;
                    $notiDetails["Details Download Link"] = 'Download Black List Search Terms File';
                    $notiDetails["Completed At"] = date("Y-m-d H:i");
                    return [
                        "notiDetails"=>$notiDetails,
                    ];
    } //end function
    private function _manageNotification($crawler_ids){
        $crawler_idIn = array();
        for ($i=0; $i < count($crawler_ids); $i++) {
            array_push($crawler_idIn,$crawler_ids[$i]["crawler_id"]);
        }
        $crawlersNoti = SearchRankCrawlerModel::with("searchTerm")->whereIn("id",$crawler_idIn);
        $fail = SearchRankFailStatuses::where("isNew",1)->select("failed_data","failed_reason");
        $fail_data = null;

        if($fail->exists())
        {
            if(!$crawlersNoti->exists()){
                $this->setActivity("Fail fetch crawlers Notification Not Sent", "errorNoti");
                return;
            }
            $fail_data = SearchRankFailStatuses::getDailyScrapingAsinFailStatus();
            $crawlersNoti = $crawlersNoti->get();
            try {
                
                $noti = $this->readySearchRankNoti($crawlersNoti);
                foreach ($fail_data as $manageId => $accounts) {
                    broadcast(new SendNotification(
                        null,
                        $accounts,//accounts holded by manager
                        2,
                        "Search Rank Error", 
                        "Some of the search terms are Black Listed", 
                        json_encode( $noti["notiDetails"]),
                        null,
                        date("Y-m-d H:i"))
                    )->toOthers();
                }//endforeach

                $this->setActivity("Notificaiton BroadCasted of Search Rank Errors", "success");
                SearchRankFailStatuses::UpdateNewFailStatues();
                
            } catch (\Throwable $th) {
                
                Log::error($th->getMessage());
                $this->setActivity("Fail To broadcast Notification But Saved To DB Reasone =>".str_limit($th->getMessage(), 200)."For Complete reason see log File of Date ".date('Y-m-d H:i:s'), "errorNoti");
            }
        }
        else
        {
            $this->setActivity("Nothing Fail", "info");
          
        }
        
    }//end function
    private function tempUrlsCreation($urls, $srController,  $crawler_id, $totalNumberOfUrls, $sleepTime){
        foreach ($urls as $key => $url) {
            $tries = 1;
            $isPageCountFetched = false;
            do {
                $this->setActivity("Try $tries For Fetching Total Number Of Pages", "info");
                if($srController->setTotalNumberOfPages(($url->st_url))){
                    $this->setActivity("Try $tries Success for search term url =>".$url->st_url, "Success");
                    $isPageCountFetched = true;
                    break;
                }
                sleep($sleepTime);
                if($tries>=3){
                    break;
                }
                $tries++;
            } while (true);
            if(!$isPageCountFetched){
                $this->setActivity("Search Term With Url $url->st_url is Skipped After $tries Tries Fails", "Error St Skip");
                $err = array(
                    "id"=>$url->id,
                    "searchTerm"=>$url->st_term,
                    "url"=>$url->st_url,
                    "departmentId"=>isset($url->crawler->department) ? $url->crawler->d_id:0,
                    "departmentName"=>isset($url->crawler->department) ? $url->crawler->department->d_name:"No Department Found",
                    "departmentAlias"=>isset($url->crawler->department) ? $url->crawler->department->d_alias:"No Department Found",
                    "crawlerId"=>isset($url->crawler_id) ? $url->crawler_id:0,
                    "crawlerName"=>isset($url->crawler) ? $url->crawler->c_name:"No Crawler Found",
                );
                $crawler_idF = isset($url->crawler_id) ? $url->crawler_id:0;
                
                $failReasons = array(
                    "Search Term is not right against the department",
                    "Their are no results found on amazon against this search term",
                    "Amazon servers were not available for service at the time of request(503)",
                );
                $srController->_set_fail_status(
                    json_encode($err),
                    json_encode($failReasons),
                    $crawler_idF
                );
               
                // $details = $err;
            
                // broadcast(new SendNotification(2,"Search Rank Error","Fail To Fetch Total Pages",json_encode($details),date("Y-m-d H:i")))->toOthers();
                continue;
            }
            
            $this->setActivity('Success Total Pages => '.$srController->totalPages, "info");

            //Limits the total number of page to 40
            if($srController->totalPages > 31)
                $srController->totalPages = 31;
            
                $this->setActivity('Total Pages Filtered => '.$srController->totalPages, "info");
                
            $temp_sr_urls = array();
            for ($i=1; $i < $srController->totalPages ; $i++) { 
                $totalNumberOfUrls++;
                $temp_sr_url = array(
                    "department_id"=>$url->crawler->d_id,
                    "searchTerm_id"=>$url->id,
                    "searchRankUrl"=>"$url->st_url&page=$i",
                    "pageNo"=>$i,
                    "urlStatus"=>"0",
                    "created_at"=>date('Y-m-d')
                );
                array_push($temp_sr_urls,$temp_sr_url);
            }
            if(count($temp_sr_urls)<=0){
                $this->error('Not Able to create temprary URLS');
                $this->setActivity('Not Able to create temprary URLS', "Error DB");  
                return;  
            }
            TempSearchRankModel::insert($temp_sr_urls);
            $this->setActivity('Next Url', "info");
        }//end foreach loop

    }//end function
    private function processInitAndExe($totalNumberOfThread){
        $tempUrls = TempSearchRankModel::where("urlStatus","0");
        if(!$tempUrls->exists()) return true;

        $tempUrls = $tempUrls->select("id")->get();

        $totalNumberOfUrls = count($tempUrls);

        if($totalNumberOfUrls < $totalNumberOfThread){
            $totalNumberOfThread = $totalNumberOfUrls;
            $pagesPerThread = 1;
        }
        else
        {
            $pagesPerThread = round($totalNumberOfUrls/$totalNumberOfThread);
        }


        $this->info("totalNumberOfThread=>". $totalNumberOfThread );
        $this->setActivity("totalNumberOfThread=>". $totalNumberOfThread, "info");
        $this->info("Total Urls=>".$totalNumberOfUrls);
        $this->setActivity("Total Urls=>".$totalNumberOfUrls, "info");
        $this->info("Per Thread=>".$pagesPerThread);
        $this->setActivity("Per Thread=>".$pagesPerThread, "info");
        
       
        $chunked = array_chunk($tempUrls->toArray(),$pagesPerThread);
        
        $tn = 1;
        foreach ($chunked as $value) { //1 =>2, 2=>3, 3=>4, 4=>5, 5=>6, 6=>7 breakl
            TempSearchRankModel::whereIn("id",$value)
                ->update(["allocatedThread"=>"T$tn"]);
                    $this->info("Temp Ids=>".json_encode($value));
                    
                    $this->setActivity("Temp Ids=>".json_encode($value), "info");
            $tn++;
        }//end for loop
        $this->info("Threads Allocated Success");
        
        $pool = new PriorityPool();
        $startPage = 1;
        for ($i=1; $i <= $totalNumberOfThread; $i++) { 
            # code...
            if(str_contains(url('/'), 'http://localhost'))
                $pool->add(new Process("php artisan RunSearchRankScrapingCommand:cron T$i"));
            else
                $pool->add(new Process("php /var/www/html/helo-tek/artisan RunSearchRankScrapingCommand:cron T$i"));
          
        }
        $output = new ConsoleOutput();
        $table = new Table($output, $pool);
        $table->run();


        TempSearchRankModel::ResetSmallErrors();
        return false;
    }//end function
}//end class
