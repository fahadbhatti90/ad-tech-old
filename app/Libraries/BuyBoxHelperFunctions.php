<?php
namespace App\Libraries;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\File;
use App\Notifications\BuyBoxEmailAlert;
use Vluzrmos\SlackApi\Facades\SlackFile;
use App\Models\AccountModels\AccountModel;
use App\Models\BuyBoxModels\BuyBoxAsinListModel;
use App\Models\BuyBoxModels\BuyBoxTempUrlsModel;
use App\Models\BuyBoxModels\BuyBoxFailStatusModel;
use App\Models\BuyBoxModels\BuyBoxScrapResultModel;
use Symfony\Component\Console\Output\ConsoleOutput;
use App\Http\Controllers\ClientAuth\NotesController;
use App\Models\BuyBoxModels\BuyBoxActivityTrackerModel;
use App\Models\BuyBoxModels\UserHierarchy\BuyBoxAccountsAsinModel;

class BuyBoxHelperFunctions {

    public function handleSoldByAlert(){
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

    }//end function
}//end class

?>