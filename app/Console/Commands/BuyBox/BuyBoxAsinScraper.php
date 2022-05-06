<?php

namespace App\Console\Commands\BuyBox;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\BuyBoxScrapingController;
use App\Models\BuyBoxModels\BuyBoxTempUrlsModel;
use App\Models\BuyBoxModels\BuyBoxFailStatusModel;
use App\Models\BuyBoxModels\BuyBoxActivityTrackerModel;

class BuyBoxAsinScraper extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'asinscraper:buybox {argument*}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Single Thread Command';

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
        $ArrayArgument = $this->argument('argument');
        $threadName = $ArrayArgument[0];
        $threadNumber = $ArrayArgument[1];

        BuyBoxActivityTrackerModel::setActivity("Thread $threadName Started","info","BuyBoxAsinScraper"," App\Console\Commands",date('Y-m-d H:i:s'));

        $this->info("Thread $threadName Started");

         $currentThreadTempUrls =  BuyBoxTempUrlsModel::with("asin:id","asin.getAsinAccounts:fkAccountId,fkAsinId")->where("allocatedThread", $threadName)->where("scrapStatus","1")->with("crons")->get();

                $sc = new BuyBoxScrapingController();
                
                foreach ($currentThreadTempUrls as $tempUrl) {
                    try {
                        $sc->Scraper($tempUrl);
                    } 
                    catch (\Throwable $th) 
                    {
                        BuyBoxActivityTrackerModel::setActivity("\nException $threadName => ".str_limit($th->getMessage(),300),"info","BuyBoxAsinScraper"," App\Console\Commands",date('Y-m-d H:i:s'));
                        $this->warn("\nException $threadName => ".$th->getMessage());
                        Log::error("\nException $threadName => ".$th->getMessage());
                        $err = array(
                            "Asin Id"=>$tempUrl->fk_bb_asin_list_id,
                            "Asin"=>$tempUrl->asinCode,
                            "Cron Id"=>$tempUrl->fk_bbc_id,
                            "Cron Title"=>isset($tempUrl->crons) ? $tempUrl->crons->cNameBuybox:"NA"
                        );
                        $failReasons = array(
                            $th->getMessage(),
                        );
                        $crawler_id = $tempUrl->fk_bbc_id;
                        $this->_set_buybox_fail_status(
                                $tempUrl,
                                json_encode($err),
                                json_encode($failReasons),
                                $crawler_id
                            );
                    }//end catch
                    
                }//end foreach

            
        $this->info("Thread $threadName Completed");
        BuyBoxActivityTrackerModel::setActivity("Thread $threadName Completed","info","BuyBoxAsinScraper"," App\Console\Commands",date('Y-m-d H:i:s'));

    }//end handle function
    private function _set_buybox_fail_status($asin, $data, $reason, $crawler_id= null){
        $accounts = $asin->asin->getAsinAccounts;
        $fData = [];
        foreach ($accounts as $key => $value) {
            $fData[] = [
            'fkAccountId'=>$value->fkAccountId,
            "failed_data"=>$data,
            "failed_reason"=>$reason,
            "failed_at"=>date('Y-m-d H:i:s'),
            "crawler_id"=>is_null($crawler_id)?0:$crawler_id,
            "created_at"=>date('Y-m-d H:i:s'),
            ];
        } 
        BuyBoxFailStatusModel::insert($fData);
        BuyBoxTempUrlsModel::deleteTempUrl($asin->id);
    }//end function
   
}//end class
