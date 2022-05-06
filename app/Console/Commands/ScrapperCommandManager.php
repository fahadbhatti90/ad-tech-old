<?php

namespace App\Console\Commands;

use App\Models\FailStatus;
use Illuminate\Console\Command;
use App\Events\SendNotification;
use App\Events\AdminNotification;
use Illuminate\Support\Facades\Log;
use App\Models\ScrapingModels\asinModel;
use App\Models\ScrapingModels\CronModel;
use App\Libraries\DailyScrapingController;
use App\Models\AccountModels\AccountModel;
use App\Models\ScrapingModels\SettingsModel;
use App\Models\ScrapingModels\ActivityTrackerModel;
use App\Models\ScrapingModels\UserHierarchy\AccountsAsinModel;

class ScrapperCommandManager extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'ScrapperCommandManager:manage';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Manages the execution of all other commands';

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
        if (!CronModel::isAnySchedulRunning()) {
            $this->info("All Ready running");
            return;
        }
        $current_hour = date("H", strtotime(date("H:i")));
        $schduleTime = SettingsModel::where("name", "scheduleTime");
        if (!$schduleTime->exists()) {
            $this->error("No schedule time found in settings table");
            ActivityTrackerModel::setActivity(
                "No schedule time found in settings table",
                "Error",
                "ScrapperCommandManager:manage",
                "app\Console\Commands",
                date('Y-m-d H:i:s')
            );
            return;
        }

        $schduleTime = $schduleTime->select("value")->get()[0]->value;
        if (date("H", strtotime($schduleTime)) != $current_hour) {
            $this->info("Not a schedule time");
            $this->info(date("H", strtotime($schduleTime)));
            $this->info($current_hour);

            return;
        }

        $this->info("Schedule Time");
        ActivityTrackerModel::setActivity(
            "Schedule Time",
            "info",
            "ScrapperCommandManager:manage",
            "app\Console\Commands",
            date('Y-m-d H:i:s')
        );
        $selected_Collection_id = DailyScrapingController::getValidSchedule($current_hour);
        $this->info("Varifying Collection");
        if (count($selected_Collection_id) > 0) {

            $this->info("Collection Found");
            for ($i = 0; $i < count($selected_Collection_id); $i++) {

                ActivityTrackerModel::setActivity(
                    "Updating valid collection asin status in the tbl_daily_asin table and starting cron",
                    "info",
                    "ScrapperCommandManager:manage",
                    "app\Console\Commands",
                    date('Y-m-d H:i:s')
                );

                //updating valid collection asin status 
                asinModel::has("getAsinAccounts")->where("c_id", $selected_Collection_id[$i]["c_id"])
                    ->update(["asin_status" => "0"]);

            }//end for loop

            /*****************************************************************************************************/
            /*******************************Run Command That acctually Scraps*****************************/
            /*****************************************************************************************************/
            $this->call('ScheduleCronCommand:cron');
            /*****************************************************************************************************/
            /*******************************Run Command That acctually Scraps*****************************/
            /*****************************************************************************************************/
            //$activeCollections = CronModel::where("isRunning",1)->select("c_id")->get()->map(function($val){return $val->c_id;});
            CronModel::where("isRunning", true)->update(["isRunning" => false]);
            // Run Event Tracking Cron
            $commandArray = array();
            $commandArray['activeCollections'] = collect($selected_Collection_id)->map(function ($item, $key) {
                return $item['c_id'];
            });
            $this->call('eventTrackingCron:event', $commandArray);
            //Run Offer Not Found Event
            $this->call('eventTrackingCron:offerNotFound');
            // Run Segments Cron
            $this->call('asinSegmentsCron:cron', $commandArray);
            $this->call('refreshProductTableView:refresh');
            $this->_manageNotification($selected_Collection_id);

            ActivityTrackerModel::setActivity("ScheduleCronCommand:cron Command Executed Success fully", "info", "ScrapperCommandManager:manage", "app\Console\Commands", date('Y-m-d H:i:s'));
        } else {

            ActivityTrackerModel::setActivity("No Collection Found Collection Count = " . count($selected_Collection_id), "info", "ScrapperCommandManager:manage", "app\Console\Commands", date('Y-m-d H:i:s'));
            $this->info("No Collection Found" . count($selected_Collection_id));
        }


    }//end handle function

    private function _readyAsinDailyNoti($accountsFailed, $fail_data, $crawlersNoti)
    {
        $accountNoti = [];
        foreach ($accountsFailed as $key => $value) {
            $fail_data = $value->fail_status;
            $details = array();
            foreach ($fail_data as $key => $value) {
                $details["item" . ($key + 1)] = json_encode($value);
            }
            $accountNoti[$value->fkAccountId] = $details;
        }//end foreach
        $notiDetails = array();
        $crawlerId = $collecitonId = $collectionName = $TotalAsins = "";
        foreach ($crawlersNoti as $key => $value) {
            $crawlerId .= $value->id . ", ";
            $collecitonId .= $value->asin_collection->id . ", ";
            $collectionName .= $value->asin_collection->c_name . ", ";
            $TotalAsins .= $value->asin_collection->asin->count() . ", ";
        }
        $crawlerId = str_replace_last(", ", "", $crawlerId);
        $collecitonId = str_replace_last(", ", "", $collecitonId);
        $collectionName = str_replace_last(", ", "", $collectionName);
        $TotalAsins = str_replace_last(", ", "", $TotalAsins);

        $notiDetails["crawler Ids"] = $crawlerId;
        $notiDetails["Collection Ids"] = $collecitonId;
        $notiDetails["Collection Names"] = $collectionName;
        $notiDetails["Total ASINS"] = $TotalAsins;
        $notiDetails["Details Download Link"] = 'Download Black Listed ASINs File';
        $notiDetails["Completed At"] = date("Y-m-d H:i");

        // $details["Created At:"] = date("Y-m-d H:i");
        return [
            "details" => $accountNoti,
            "notiDetails" => $notiDetails,
        ];
    } //end function

    private function _manageNotification($crawler_ids)
    {
        $crawler_idIn = array();
        for ($i = 0; $i < count($crawler_ids); $i++) {
            array_push($crawler_idIn, $crawler_ids[$i]["crawler_id"]);
        }
        $crawlersNoti = CronModel::with(["asin_collection", "asin_collection.asin"])->whereIn("id", $crawler_idIn);
        $fail = FailStatus::where("isNew", 1)->select("failed_data", "failed_reason");
        $accountsFailed = AccountsAsinModel::with("fail_status")
            ->select("fkAccountId")
            ->groupBy("fkAccountId")->get();
        $fail_data = null;

        if ($fail->exists()) {
            if (!$crawlersNoti->exists()) {
                ActivityTrackerModel::setActivity(
                    "Fail to fetch crawlers, Notification Not Sent",
                    "errorNoti",
                    "ScrapperCommandManager:manage",
                    "app\Console\Commands",
                    date('Y-m-d H:i:s')
                );
                return;
            }
            $fail_data = $fail->get();
            $crawlersNoti = $crawlersNoti->get();
            try {

                $noti = $this->_readyAsinDailyNoti($accountsFailed, $fail_data, $crawlersNoti);
                // $managers = AccountModel::getHirarchyBaseAsinFailStatus();
                $managers = FailStatus::getDailyScrapingAsinFailStatus();
                foreach ($managers as $manageId => $accounts) {
                    broadcast(new SendNotification(
                            null,
                            $accounts,//accounts holded by manager
                            2,
                            "Daily Asin Scrap Error",
                            "Some of the asins are black listed",
                            json_encode($noti["notiDetails"]),
                            null,
                            date("Y-m-d H:i"))
                    )->toOthers();
                }//endforeach


                ActivityTrackerModel::setActivity(
                    "Successfull Notificaiton BroadCast of Daily Asin",
                    "success",
                    "ScrapperCommandManager:manage",
                    "app\Console\Commands",
                    date('Y-m-d H:i:s')
                );
                FailStatus::UpdateNewFailStatues();

            } catch (\Throwable $th) {

                Log::error($th->getMessage());
                ActivityTrackerModel::setActivity(
                    "Fail To broadcast Notification But Saved To DB Reasone =>" . str_limit($th->getMessage(), 200) . "For Complete reason see log File of Date " . date('Y-m-d H:i:s'),
                    "errorNoti",
                    "ScrapperCommandManager:manage",
                    "app\Console\Commands",
                    date('Y-m-d H:i:s')
                );
            }
        } else {
            ActivityTrackerModel::setActivity(
                "Nothing Fail",
                "info",
                "ScrapperCommandManager:manage",
                "app\Console\Commands",
                date('Y-m-d H:i:s')
            );

        }

    }//end function
}//end class
