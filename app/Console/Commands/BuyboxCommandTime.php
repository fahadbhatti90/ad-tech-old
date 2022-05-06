<?php

namespace App\Console\Commands;

use App\Models\BuyBoxModel;
use App\Libraries\BroadCastNotification;
use App\Models\mynotification;
use Illuminate\Console\Command;
use App\Events\SendNotification;
use App\Events\AdminNotification;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\File;
use Rap2hpoutre\FastExcel\FastExcel;
use App\Mail\BuyBoxEmailAlertMarkdown;
use Symfony\Component\Process\Process;
use App\Notifications\BuyBoxEmailAlert;
use Graze\ParallelProcess\PriorityPool;
use Graze\ParallelProcess\Display\Lines;
use Vluzrmos\SlackApi\Facades\SlackFile;
use App\Models\AccountModels\AccountModel;
use App\Models\BuyBoxModels\BuyBoxAsinListModel;
use App\Models\BuyBoxModels\BuyBoxTempUrlsModel;
use App\Models\BuyBoxModels\BuyBoxFailStatusModel;
use App\Models\BuyBoxModels\BuyBoxScrapResultModel;
use Symfony\Component\Console\Output\ConsoleOutput;
use App\Models\BuyBoxModels\BuyBoxActivityTrackerModel;
use App\Models\BuyBoxModels\UserHierarchy\BuyBoxAccountsAsinModel;

class BuyboxCommandTime extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'buyboxcommandtime:buybox';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'This Command will run every minute for scraping creating process for buy box scraping';

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
    public function handle(){
        if(BuyBoxModel::checkRunningCrons()){
            $this->info("Already Running");
            return;
        }//end if
        
        $validCrons = $this->_updateBuyBoxValidCronStuts();
        if($validCrons)
        {   
            BuyBoxActivityTrackerModel::setActivity("Buy Box Cron starts execution","info","BuyboxCommandTime"," App\Console\Commands",date('Y-m-d H:i:s'));
            $validCrons = BuyBoxModel::getRunningCrons();
           
            $this->_initializeCron($validCrons);
            do {
                
                if(!BuyBoxTempUrlsModel::checkValidUrls()){
                    BuyBoxActivityTrackerModel::setActivity("No More ASIN's Remaining Checking 503","info","BuyboxCommandTime"," App\Console\Commands",date('Y-m-d H:i:s'));
       
                    if(BuyBoxTempUrlsModel::check503ValidUrls()){
                        BuyBoxActivityTrackerModel::setActivity("503 Url Found, Going to sleep for 1 minute","info","BuyboxCommandTime"," App\Console\Commands",date('Y-m-d H:i:s'));
       
                        $this->info("Going to sleep for 1 minute");
                        sleep(60);
                        $this->info("Woke up");
                        BuyBoxActivityTrackerModel::setActivity("Woke up","info","BuyboxCommandTime"," App\Console\Commands",date('Y-m-d H:i:s'));
                        continue;
                    }//end if
                    
                    $this->info("NO URL WITH 503 STATUS FOUND, BREAKING");
                    BuyBoxActivityTrackerModel::setActivity("NO URL WITH 503 STATUS FOUND, BREAKING","info","BuyboxCommandTime"," App\Console\Commands",date('Y-m-d H:i:s'));
                       
                    break; 
                }//end if

                $tempUrls = BuyBoxTempUrlsModel::getValidUrls();
                
                $totalNumberOfThread = 25;

                if(count($tempUrls) > $totalNumberOfThread){
                    $asinPerThread = round(count($tempUrls)/$totalNumberOfThread);
                }//end if
                else
                {
                    $totalNumberOfThread = count($tempUrls);
                    $asinPerThread = 1;
                }//end else
                $this->info(count($tempUrls));
                $this->info($totalNumberOfThread);
                $this->info($asinPerThread);

                $chunked = array_chunk($tempUrls->toArray(),$asinPerThread);
                $tn = 1;
                foreach ($chunked as $value)
                {
                    BuyBoxTempUrlsModel::whereIn("id",$value)
                    ->update(["allocatedThread"=>"T$tn"]);
                    $this->info("ASIN_IDs=>".json_encode($value));
                    $tn++;
                }//end foreach loop
              
                $pool = new PriorityPool();
                for ($threadNumber=1; $threadNumber <= $totalNumberOfThread; $threadNumber++) 
                { 
                    # code...
                    if(str_contains(url('/'), 'http://localhost'))
                        $pool->add(new Process("php artisan asinscraper:buybox T$threadNumber $threadNumber"));
                    else
                        $pool->add(new Process("php /var/www/html/helo-tek/artisan asinscraper:buybox T$threadNumber $threadNumber"));
                }//end for loop


                //***********Changing temp Urls scrapStatus From 0 to 1**********\\
                BuyBoxTempUrlsModel::setProcessingStatus();
                //***********Changing temp Urls scrapStatus From 0 to 1**********\\

                $output = new ConsoleOutput();
                $lines = new Lines($output, $pool);
                $lines->run();
                
                $this->info("Thread Execution Completed checking remaining Urls");
                BuyBoxActivityTrackerModel::setActivity("Thread Execution Completed checking remaining Urls","info","BuyboxCommandTime"," App\Console\Commands",date('Y-m-d H:i:s'));

                if(!BuyBoxTempUrlsModel::checkValidUrls()){
                    $this->info("No Valid Urls Found Checking 503");
                    BuyBoxActivityTrackerModel::setActivity("No Valid Urls Found Checking 503","info","BuyboxCommandTime"," App\Console\Commands",date('Y-m-d H:i:s'));
    
                    if(BuyBoxTempUrlsModel::check503ValidUrls()){
                        BuyBoxActivityTrackerModel::setActivity("503 Url Found, Going to sleep for 1 minute","info","BuyboxCommandTime"," App\Console\Commands",date('Y-m-d H:i:s'));
                        $this->info("Going to sleep for 1 minute");
                        sleep(60);
                        $this->info("Woke up");
                        BuyBoxActivityTrackerModel::setActivity("Woke up","info","BuyboxCommandTime"," App\Console\Commands",date('Y-m-d H:i:s'));
                        continue;
                    }//end if
                    
                    $this->info("NO URL WITH 503 STATUS FOUND, BREAKING");
                    BuyBoxActivityTrackerModel::setActivity("NO URL WITH 503 STATUS FOUND, BREAKING","info","BuyboxCommandTime"," App\Console\Commands",date('Y-m-d H:i:s'));
                    break;    
                }//end if
                BuyBoxTempUrlsModel::updateValidUrls();
            } while (true);

            //alert send code here/
            $this->_manageNotification($validCrons);
            print_r($this->_sendAlerts($validCrons));

        }//end if
        else
        {
            $this->info("No Valid Cron Found");    
        }//end else


    }//end handle function
    private function _readySearchRankNoti( $accountsFailed, $CronsNoti ) { 
        $accountNoti = [];
        foreach ($accountsFailed as $key => $value) {
            $fail_data = $value->fail_status;
            $details = array();
            foreach ($fail_data as $key => $value) {
                $details["item".($key+1)] = json_encode($value);
            }
            $accountNoti[$value->fkAccountId] = $details;
        }//end foreach
        $notiDetails = array();
        $CronId = $CronName = $asinTotal = "";
        foreach ($CronsNoti as $key => $value) {
                $CronId .= $value->id.", ";
                $CronName .=$value->cNameBuybox.", ";
                $asinTotal .=BuyBoxModel::getTotalAsinCount($value->cNameBuybox).", ";
        }

        $CronId = str_replace_last(", ","",$CronId);
        $CronName = str_replace_last(", ","",$CronName);
        $asinTotal = str_replace_last(", ","",$asinTotal);
        
        $notiDetails = array();
        $notiDetails["Crawler Ids"] = $CronId;
        $notiDetails["Crawler Names"] = $CronName;
        $notiDetails["Details Download Link"] = "Download Black Listed Alert Details";
        $notiDetails["Completed At"] = date("Y-m-d H:i");

        return [
            "details"=>$accountNoti,
            "notiDetails"=>$notiDetails,
        ];
    } //end function
    private function _manageNotification($CronsNoti){
        //get distinc account id's of failed data 
        // $fail = BuyBoxFailStatusModel::where("isNew",1)->select("failed_data","failed_reason");
        $fail = BuyBoxAccountsAsinModel::with("fail_status")
        ->select("fkAccountId")
        ->groupBy("fkAccountId");
        //get distinct managers id's of failed data by using accout_id's array 
        $fail_data = null;

        if($fail->exists())
        {
            $fail_data = $fail->get();
            try {
                //pass distinct account id's array of failed data to this function as well
                $noti = $this->_readySearchRankNoti($fail_data,$CronsNoti);
                //loop through managers id's array and generate notificaiton for each managers id seperatly and broadcast it
                $fkAccountIDs = array_keys($noti["details"]);
                
                
                // $managers = AccountModel::getHirarchyBaseBuyBoxFailStatus();
                $managers = BuyBoxFailStatusModel::getBuyBoxFailStatus();
                
                //pass account id's to the SendNotificaiton Class
                foreach ($managers as $manageId => $accounts) {
                    broadcast(new SendNotification(
                        null,
                        $accounts,//accounts with data of manager
                        2,
                        "Buy Box Error",
                        "Some of the asins are black listed",
                        json_encode( $noti["notiDetails"]),
                        null,
                        date("Y-m-d H:i"))
                    )->toOthers();
                }//endforeach
                
                BuyBoxActivityTrackerModel::setActivity(
                    "Notificaiton BroadCasted of Buy Box",
                    "success",
                    "BuyboxCommandTime",
                    "app\Console\Commands",
                    date('Y-m-d H:i:s')
                );
                BuyBoxFailStatusModel::UpdateNewFailStatues();
            } catch (\Throwable $th) {
                BuyBoxActivityTrackerModel::setActivity(
                    "Fail To broadcast Notification =>".str_limit($th->getMessage(), 200)."For Complete reason see log File of Date ".date('Y-m-d H:i:s'),
                    "errorNoti", 
                    "BuyboxCommandTime",
                    "app\Console\Commands",
                    date('Y-m-d H:i:s')
                );
            }
        }
        else
        {
            BuyBoxActivityTrackerModel::setActivity(
                "Nothing Fail",
                "info",
                "BuyboxCommandTime",
                "app\Console\Commands",
                date('Y-m-d H:i:s')
            );
          
        }//end else
        
    }//end function
    private function _updateBuyBoxValidCronStuts(){
        // checkValidCronForBuyBox
        $currnet_date = date("Y-m-d");
        $current_hour = intval(date("H",strtotime(date("H:i"))));
    
        if(!BuyBoxModel::checkValidCronForBuyBox($currnet_date,$current_hour))
        return FALSE;

        $validCrons = BuyBoxModel::getValidCronForBuyBox($currnet_date,$current_hour);

        foreach ($validCrons as $cron) 
        {
            $runData = $this->_getRunDetilsUsingHours($cron->nextRunTime,$cron->hoursToAdd);
            $cron->nextRun = $runData["nextRun"];
            $cron->nextRunTime = $runData["nextRunTime"];
            $cron->currentFrequency = $cron->currentFrequency-1 < 0?0:$cron->currentFrequency-1;
            $cron->cronStatus = 1;
            $cron->save();
          
    
         }//end foreach
         
         return TRUE;
    }//end function Step 1
    private function _initializeCron($validCrons){
        foreach ($validCrons as $validCron) {
            if(!BuyBoxAsinListModel::checkAsinExists($validCron->cNameBuybox))
            {
                $this->warn("No Asin Found against Cron Name => $validCron->cNameBuybox" );
                continue;
            }//end if


            //updating valid collection asin status 
            $asinCodes =  BuyBoxAsinListModel::getAsin($validCron->cNameBuybox);

            $tempUrls = array();
            foreach ($asinCodes as $key => $value) 
            {
                $url = array(
                    "fk_bbc_id"=>$validCron->id,
                    "fk_bb_asin_list_id"=>$value->id,
                    "frequency"=>$validCron->frequency,
                    "asinCode"=>$value->asinCode,
                    "scrapStatus"=>"0",
                    "allocatedThread"=>"NA",
                    "createdAt"=>date("Y-m-d H:i:s")
                );
                array_push($tempUrls,$url);
            }//end foreach

            $tempUrlsASINS = array_chunk($tempUrls,1000);
            foreach ($tempUrlsASINS as $tempUrlsASINkey => $tempUrlsASIN) {
                BuyBoxTempUrlsModel::insert($tempUrlsASIN);
            }
           
        }//end for loop
    }//end function Step 2
    private function _getRunDetilsUsingHours($nextRunTime, $hours){
        $nextCronTime = date("H", strtotime("$nextRunTime:00 +" . $hours . " hours"));
        $data["nextRunTime"] = $nextCronTime;
        //For Checking Date Passing to next date
        $currentCronTimeDiffer = date("Y-m-d");
        $nextCronTimeDiffer = date("Y-m-d", strtotime("$nextRunTime:00 +" . $hours . " hours"));
        
        $data["nextRun"] = $currentCronTimeDiffer;
        $data["isNextDay"] = false;
        if($nextCronTimeDiffer > $currentCronTimeDiffer){
            $data["nextRun"] = $nextCronTimeDiffer;
            $data["isNextDay"] = true;
        }
        return $data;
    }//end function Step 1.1
    private function _sendPushNotification($managers,$cron,$notiType){
        $notiDetails = array();
        $notiDetails["Crawler Ids"] = $cron->id;
        $notiDetails["Crawler Names"] = $cron->cNameBuybox;
        $notiDetails["Crawler Email"] = $cron->email;
        // $notiDetails["Total ASINS"] = BuyBoxModel::getTotalAsinCount($cron->cNameBuybox);
        // $notiDetails["Total In $notiType Category"] = count($managers);
        $notiDetails["Details Download Link"] = "Download $notiType Alert Details";
        $notiDetails["Completed At"] = date("Y-m-d H:i");
            foreach ($managers as $manageId => $accounts) {
                broadcast(new SendNotification(
                    $manageId == "null"?null:$manageId,
                    $accounts,//accounts holded by manager
                    1,
                    "$notiType",
                    "Some of the Asins are in $notiType category further details are mentioned below", 
                    json_encode( $notiDetails),
                    null,
                    date("Y-m-d H:i")
                    )
                )->toOthers();
                if($manageId != "null")
                broadcast(new AdminNotification(
                        SendNotification::$notiId,
                        1,
                        "$notiType",
                        "Some of the Asins are in $notiType category further details are mentioned below", 
                        date("Y-m-d H:i"))
                )->toOthers();
            }//end foreach

    }//end function
    private function _sendAlerts($validCrons){
        
        $notes = new BroadCastNotification();
        foreach ($validCrons as $cron) 
        {
            try 
            {
                if(BuyBoxScrapResultModel::checkSoldBuyAlerts($cron))
                {
                    $fileTempPath = public_path('buybox/SoldByAlert.csv');   
                    $bbResult = BuyBoxScrapResultModel::getSoldBuyAlerts($cron);
                    // $soldByUserSpecific = BuyBoxScrapResultModel::getSoldByAlertDataUserSpecific($cron);
                    try {
                        $notes->sendPushNotification($cron,"Sold By");
                        // $this->_sendPushNotification($soldByUserSpecific,$cron,"Sold By");
                        // $this->_sendPushNotification($bbResult,$cron,"Sold By");
                        BuyBoxActivityTrackerModel::setActivity(
                            "Notificaiton BroadCasted of Buy Box",
                            "success",
                            "BuyboxCommandTime",
                            "app\Console\Commands",
                            date('Y-m-d H:i:s')
                        );
                    } catch (\Throwable $th) {
                        Log::error($th->getMessage());
                        BuyBoxActivityTrackerModel::setActivity(
                            "Fail To broadcast Notification Reasone =>".str_limit($th->getMessage(), 200)."For Complete reason see log File of Date ".date('Y-m-d H:i:s'),
                            "errorNoti",
                            "BuyboxCommandTime",
                            "app\Console\Commands",
                            date('Y-m-d H:i:s')
                        );
                    }
                    BuyBoxActivityTrackerModel::setActivity(
                        "cron => ".json_encode($cron),
                        "info",
                        "BuyboxCommandTime"
                        ," App\Console\Commands",date('Y-m-d H:i:s')
                    );
                    BuyBoxActivityTrackerModel::setActivity(
                        "Generating sold by csv file",
                        "info",
                        "BuyboxCommandTime",
                        " App\Console\Commands",
                        date('Y-m-d H:i:s')
                    );
                    
                    $this->_generateSoldByCSV($bbResult,$fileTempPath);

                    BuyBoxActivityTrackerModel::setActivity(
                        "Sold by csv File Generated Succesfully",
                        "SUCCESS",
                        "BuyboxCommandTime",
                        " App\Console\Commands",
                        date('Y-m-d H:i:s')
                    );
                    BuyBoxActivityTrackerModel::setActivity(
                        "Sending sold by  Slack Alert",
                        "info",
                        "BuyboxCommandTime",
                        " App\Console\Commands",
                        date('Y-m-d H:i:s')
                    );
                    $this->_sendSoldBySlackAlert($fileTempPath);
                    BuyBoxActivityTrackerModel::setActivity(
                        "Sold by Slack Alert Sent Successfully",
                        "SUCCESS",
                        "BuyboxCommandTime",
                        " App\Console\Commands",
                        date('Y-m-d H:i:s')
                    );
                    for ($i=1; $i <=3; $i++) { 
                        try {
                            BuyBoxActivityTrackerModel::setActivity(
                                "Try $i for sending email alert of Sold by",
                                "info",
                                "BuyboxCommandTime",
                                " App\Console\Commands",
                                date('Y-m-d H:i:s')
                            );
                            $status = $this->_sendSoldByEmailAlert($fileTempPath, "SoldByAlert.csv", $cron);
                            BuyBoxActivityTrackerModel::setActivity(
                                "Sold by Email alert Sent in Try $i status = ".json_encode($status),
                                "SUCCESS",
                                "BuyboxCommandTime",
                                " App\Console\Commands",
                                date('Y-m-d H:i:s')
                            );
                            break;
                        } catch (\Throwable $th) {
                            BuyBoxActivityTrackerModel::setActivity(
                                "Try $i FAILED retrying with error messge =>".str_limit($th->getMessage(), 300),
                                "ERROR",
                                "BuyboxCommandTime",
                                " App\Console\Commands",
                                date('Y-m-d H:i:s')
                            );
                            if($i==3)
                            {
                                $errors = array(
                                    "Cron Id"=>$cron->id,
                                    "Alert Type"=>"Sold By Email Alert",
                                    "Cron Email"=>$cron->email,
                                    "Cron Name"=>$cron->cNameBuybox,
                                    "Cron Frequency"=>$cron->currentFrequency+1,
                                );
                            $this->_set_buybox_fail_status($cron->id,($errors),json_encode(["Fail To Send Sold by Email In 3 Tries".str_limit($th->getMessage(), 300)]), $cron->id);
                            }
                        }
                    }
                   

                    if(File::exists($fileTempPath))
                    File::delete($fileTempPath);
                }//end if

                if(BuyBoxScrapResultModel::checkOutOfStockAlerts($cron))
                {
                    $bbResult = BuyBoxScrapResultModel::getOutOfStockAlerts($cron);
                    // $soldByUserSpecific = BuyBoxScrapResultModel::getOutOfStockAlertDataUserSpecific($cron);
                    try {
                        // $this->_sendPushNotification($soldByUserSpecific,$cron,"Out Of Stock");
                        $notes->sendPushNotification($cron,"Out Of Stock");
                        BuyBoxActivityTrackerModel::setActivity(
                            "Notificaiton BroadCasted of Search Rank Errors",
                            "success",
                            "BuyboxCommandTime",
                            "app\Console\Commands",
                            date('Y-m-d H:i:s')
                        );
                    } catch (\Throwable $th) {
                        Log::error($th->getMessage());
                        BuyBoxActivityTrackerModel::setActivity(
                            "Fail To broadcast Notification Reasone =>".str_limit($th->getMessage(), 200)."For Complete reason see log File of Date ".date('Y-m-d H:i:s'),
                            "errorNoti",
                            "BuyboxCommandTime",
                            "app\Console\Commands",
                            date('Y-m-d H:i:s')
                        );
                    
                    }
                    $fileTempPath = public_path('buybox/OutOfStock.csv');
                    BuyBoxActivityTrackerModel::setActivity("cron => ".json_encode($cron),"info","BuyboxCommandTime"," App\Console\Commands",date('Y-m-d H:i:s'));
                    BuyBoxActivityTrackerModel::setActivity("Generating OutOfStock csv file","info","BuyboxCommandTime"," App\Console\Commands",date('Y-m-d H:i:s'));
                    
                    $this->_generateOutOfStockCSV($bbResult,$fileTempPath);

                    BuyBoxActivityTrackerModel::setActivity("OutOfStock csv File Generated Succesfully Sending OutOfStock Slack Alert","info","BuyboxCommandTime"," App\Console\Commands",date('Y-m-d H:i:s'));
                    
                    $this->_sendOutOfStockSlackAlert($fileTempPath);

                    BuyBoxActivityTrackerModel::setActivity("OutOfStock Slack Alert Sent Successfully","SUCCESS","BuyboxCommandTime"," App\Console\Commands",date('Y-m-d H:i:s'));
                    for ($i=1; $i <=3; $i++) { 
                        try {
                            BuyBoxActivityTrackerModel::setActivity("Try $i for sending email alert of OutOfStock","info","BuyboxCommandTime"," App\Console\Commands",date('Y-m-d H:i:s'));

                            $status = $this->_sendOutOfStockEmailAlert($fileTempPath, "OutOfStock.csv", $cron);

                            BuyBoxActivityTrackerModel::setActivity(
                            "OutOfStock Email alert Sent in Try $i status = ".json_encode($status),
                            "SUCCESS",
                            "BuyboxCommandTime",
                            " App\Console\Commands",
                            date('Y-m-d H:i:s'));
                            break;
                        } catch (\Throwable $th) {
                            BuyBoxActivityTrackerModel::setActivity("Try $i FAILED retrying with error messge =>".str_limit($th->getMessage(), 300),"ERROR","BuyboxCommandTime"," App\Console\Commands",date('Y-m-d H:i:s'));
                            if($i==3)
                            {
                                $errors = array(
                                    "Cron Id"=>$cron->id,
                                    "Alert Type"=>"OutOfStock Email Alert",
                                    "Cron Email"=>$cron->email,
                                    "Cron Name"=>$cron->cNameBuybox,
                                    "Cron Frequency"=>$cron->currentFrequency+1,
                                );
                            $this->_set_buybox_fail_status($cron->id,($errors),json_encode(["Fail To Send OutOfStock Email In 3 Tries".str_limit($th->getMessage(), 300)]), $cron->id);
                            }
                        }
                    }
                    if(File::exists($fileTempPath))
                    File::delete($fileTempPath);
                }//enf if
               
            }//end try
            catch (\Throwable $th) 
            {
                BuyBoxScrapResultModel::updateIsNewStatus();
                
                Log::error('Error:'.$th->getMessage());
                
                BuyBoxActivityTrackerModel::setActivity("Their's been an error while sending alerts ","ERROR","BuyboxCommandTime"," App\Console\Commands",date('Y-m-d H:i:s'));
                $errors = array(
                    "Cron Id"=>$cron->id,
                    "Cron Email"=>$cron->email,
                    "Cron Name"=>$cron->cNameBuybox,
                    "Cron Frequency"=>$cron->currentFrequency+1,
                );
                $this->_set_buybox_fail_status($cron->id,json_encode($errors),"Their's been an error while sending alerts ".str_limit($th->getMessage(), 300), $cron->id);
            }
            if($cron->nextRun != date("Y-m-d")){
                Log::error('Updating Cron Status');
                BuyBoxActivityTrackerModel::setActivity("Updating Cron Status","info","BuyboxCommandTime"," App\Console\Commands",date('Y-m-d H:i:s'));
                $cron->currentFrequency = $cron->frequency;
            }
            $cron->cronStatus = 0;
            $cron->save();
        }//end foreach loop
        
        BuyBoxScrapResultModel::updateIsNewStatus();
        Log::error('Alerts Sent');
        BuyBoxActivityTrackerModel::setActivity("Alerts Sent Process Completed","SUCCESS","BuyboxCommandTime"," App\Console\Commands",date('Y-m-d H:i:s'));
        return "Alerts Sent"."\n";
    }//end function Step 3
    private function _set_buybox_fail_status($asin, $data, $reason, $crawler_id= null){
        $notiDetails = array();
        $failData["Crawler Ids"] = $notiDetails["Crawler Ids"] = $crawler_id;
        $failData["Crawler Email"] = $notiDetails["Crawler Email"] = $data["Cron Email"];
        $failData["Crawler Name"] = $notiDetails["Crawler Name"] = $data["Cron Name"];
        $failData["Crawler Frequency"] = $notiDetails["Crawler Frequency"] = $data["Frequency"];
        $notiDetails["Details Download Link"] = "Download Details";
        $failData["Completed At"] = $notiDetails["Completed At"] = date("Y-m-d H:i");
        $accounts = [];
        $accounts["null"][] = [
            "failed_data"=>json_encode($failData),
            "fkAccountId"=>NULL,
            "failed_reason"=>$reason,
            "failed_at"=>date('Y-m-d H:i:s'),
            "crawler_id"=>is_null($crawler_id)?0:$crawler_id,
            "created_at"=>date('Y-m-d H:i:s')
        ];
        broadcast(new SendNotification(
            null,
            $accounts,//accounts holded by manager
            2,
            $data["Alert Type"],
            $reason, 
            json_encode($notiDetails),
            null,
            date("Y-m-d H:i")
            )
        )->toOthers();
        // BuyBoxFailStatusModel::create(array(
        //     "failed_data"=>$data,
        //     "fkAccountId"=>NULL,
        //     "failed_reason"=>$reason,
        //     "failed_at"=>date('Y-m-d H:i:s'),
        //     "crawler_id"=>is_null($crawler_id)?0:$crawler_id,
        //     "created_at"=>date('Y-m-d H:i:s'),
        // ));
    }
    private function _generateSoldByCSV($bbResult, $fileTempPath){

        (new FastExcel(collect($bbResult)))->export("$fileTempPath", function ($result) {
            return  [
                    "ASIN's" => $result->asinCode,
                    "Message" => "Alert Type Sold By"
                ];
        });//end
    }//end function Step 3.1
    private function _sendSoldByEmailAlert($fileTempPath, $newFileName, $cron){
        $messages = array();
        $messages[0] = "<p>This email notification is to inform you that some of your products are in Sold by alert category.</p>";
        $messages[1] = "<p>Schedule Collection Name : ".$cron->cNameBuybox."</p>";
        $messages[2] = "<p>Frequency : ".($cron->currentFrequency+1)."</p>";
        $messages[3] = "<p>Please see the attach file for further details.</p>";
        $bodyHTML =  ((new BuyBoxEmailAlertMarkdown("Sold By Alert",$messages))->render());

            $data = [];
            $data["toEmails"] = array(
                $cron->email,
            );

            $data["subject"] = "Buy Box Sold By Alert";
            $data["bodyHTML"] = $bodyHTML;
            
            $data["attachments"] = array(
                    array(
                        "path"=>$fileTempPath,
                        "name"=> $newFileName
                    ),
                );
            
            return SendMailViaPhpMailerLib($data);

    }//end function Step 3.2
    private function _sendSoldBySlackAlert($fileTempPath){
            SlackFile::upload([
                'filename' =>"SoldByAlert.csv", 
                'title'  => 'Sold Buy Alerts File', 
                'initial_comment'  => 'Buy Box Scrpaing Complete. You have some SOLD BY alerts for more details see the attach file', 
                'content' => File::get($fileTempPath),
                'channels' => '#projects' //can be channel, users, or groups ID
            ]);
    }//end function Step 3.3
    private function _generateOutOfStockCSV($bbResult, $fileTempPath){
            (new FastExcel(collect($bbResult)))->export("$fileTempPath", function ($result) {
            return  [
                    "ASIN's" => $result->asinCode,
                    "Message" => "Out Of Stock"
                ];
            });//end
            
    }//end function Step 3.4
    private function _sendOutOfStockSlackAlert($fileTempPath){
        SlackFile::upload([
            'filename' =>"OutOfStock.csv", 
            'title'  => 'Out Of Stock Alerts File', 
            'initial_comment'  => "Buy Box Scrpaing Complete. Some ASIN'S are out of stock for more details see the attach file", 
            'content' => File::get($fileTempPath),
            'channels' => '#projects' //can be channel, users, or groups ID
        ]);
        
    }//end function Step 3.5
    private function _sendOutOfStockEmailAlert($fileTempPath, $newFileName, $cron){
        $messages = array();
        $messages[0] = "<p>This email notification is to inform you that some of your products are out of stock.</p>";
        $messages[1] = "<p><b>Schedule Collection Name :</b> ".$cron->cNameBuybox."</p>";
        $messages[2] = "<p><b>Frequency :</b> ".($cron->currentFrequency+1)."</p>";
        $messages[3] = "<p>Please see the attach file for further details.</p>";
        $bodyHTML =  ((new BuyBoxEmailAlertMarkdown("Out Of Stock Alert",$messages))->render());

        $data = [];
        $data["toEmails"] = array(
            $cron->email,
        );

        $data["subject"] = "Buy Box Out Of Stock Alert";
        $data["bodyHTML"] = $bodyHTML;
        
        $data["attachments"] = array(
                array(
                    "path"=>$fileTempPath,
                    "name"=> $newFileName
                ),
            );
        
        return SendMailViaPhpMailerLib($data);
        
    }//end function Step 3.6
}//end class
