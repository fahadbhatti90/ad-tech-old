<?php

namespace App\Console\Commands\SearchRankCommands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use App\Models\ScrapingModels\SearchTermModel;
use App\Models\ScrapingModels\ActivityTrackerModel;
use App\Models\ScrapingModels\SearchRankCrawlerModel;
use App\Http\Controllers\SearchRankScrapingController;
use App\Models\ScrapingModels\SearchRankScrapedResultModel;
use App\Models\ScrapingModels\TempSearchRankModel;

class RunSearchRankScrapingCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'RunSearchRankScrapingCommand:cron {sr_data*}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'This will scrap all the search ranks';

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
        try {

            $arguments = $this->argument("sr_data");
       
            $tName =  $arguments[0];

            $currentThreadTempUrls =  TempSearchRankModel::where("allocatedThread", $tName)->get();
           
            $srController = new SearchRankScrapingController();
            foreach ($currentThreadTempUrls as $key => $value) 
            {
                try {
                    
                        $srController->scrapSearchRank(
                            $value, 
                            $value->searchTerm_id, 
                            $value->pageNo, 
                            $value->department->crawler->id, 
                            $value->id
                        );
                    

                } catch (\Throwable $th) {
                        Log::info('SOme Error Occured'. $th->getMessage());
                        $err = array(
                            "id"=>$value->searchTerm_id,
                            "url"=>$value->searchRankUrl,
                            "departmentId"=>isset($value->department_id) ? $value->department_id:0,
                            "departmentName"=>isset($value->department) ? $value->department->d_name:"No Department Found",
                            "departmentAlias"=>isset($value->department) ? $value->department->d_alias:"No Department Found",
                            "crawlerId"=>isset($value->department->crawler) ? $value->department->crawler->id:0,
                            "crawlerName"=>isset($value->department->crawler) ? $value->department->crawler->c_name:"No Crawler Found",
                        );
                        $failReasons = array(
                            "Exception: Some Error Occured Error Message:- ".str_limit($th->getMessage(),300),
                        );
                        $crawler_id = isset($value->department->crawler) ? $value->department->crawler->id:0;
                       
                        $srController->_set_fail_status(
                            json_encode($err),
                            json_encode($failReasons),
                            $crawler_id
                        );
                        TempSearchRankModel::DeleteTempUrl($value->id);
                        ActivityTrackerModel::setActivity(
                            'SOme Error Occured'. $th->getMessage(),
                            "Error 500",
                            "RunSearchRankScrapingCommand:cron",
                            "App\Console\Commands\SearchRankCommands;",date('Y-m-d H:i:s'));
                        continue;
                }//end catch
            }//end for each
                    
         

        } catch (\Throwable $th) {
            Log::error("filePath:app\Console\Commands\SearchRankCommands\RunSearchRankScrapingCommand:cron Their's been an error while scraping Error Message".str_limit($th->getMessage(),300));
            ActivityTrackerModel::setActivity(
                "Their's been an error while scraping Error Message ". str_limit($th->getMessage(),200),
                "Error",
                "RunSearchRankScrapingCommand:cron",
                "app\Console\Commands\SearchRankCommands",
                date('Y-m-d H:i:s')
            );
            // $this->error("filePath:app\Console\Commands\SearchRankCommands\RunSearchRankScrapingCommand:cron Their's been an error while scraping Department ID = ".$crawler_id);
            // $this->line("Error Message ". $th->getMessage());
            // /**
            //  * Reseting Department schedule to be scrap again with deleting its scrap data till error
            //  */
            // $cronModel = SearchRankCrawlerModel::where("id",$crawler_id);
            // if($cronModel->exists()){
            //     $cronModel =  $cronModel->get()[0]; 
            //     $cronModel->c_lastRun = "0000-00-00";
            //     $cronModel->c_nextRun = date('Y-m-d');
            //     $cronModel->save();

            //       $st_ids = SearchTermModel::getDepartmentSearchTermsIdArray($crawler_id);
            //       SearchRankScrapedResultModel::deleteSearchTermScrapResult($st_ids);
            // }
            // /**
            //  * Reseting Department schedule to be scrap again with deleting its scrap data till error
            //  */
           
            

        }//end catch
    }//end handle function
}//end class
