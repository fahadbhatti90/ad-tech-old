<?php
namespace App\Libraries;

use App\Models\NotificationModel;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Models\NotificationDetailsModel;
use App\Events\SendNotificationUserSpecific;
use App\Models\BuyBoxModels\BuyBoxScrapResultModel;

class BroadCastNotification {

    public function sendPushNotification($cron, $notiType){

        ini_set('memory_limit', '2048M');
        ini_set('max_execution_time', 0);

        $mangersArray=[];
        $accounts=[];
        
        $broadcastNotificationData = array(
            "type"=> 1,
            "title"=>$notiType,
            "message"=>  "Some of the Asins are in $notiType category further details are mentioned below",
            "host" => getHostForNoti(),
            "created_at"=> date("Y-m-d H:i"),
        );


        if($notiType == "Sold By"){
             $data = $this->GetUserSpecificSoldByData($cron, $managersArray, $accounts);
        }
        else
        {
             $data = $this->GetUserSpecificOutOfStockData($cron, $managersArray, $accounts);
        }
        $this->InsertAccountBasedNotificationInDatabase($cron, $data["accountsViseMangers"], $data["accountViseData"], $broadcastNotificationData);
        return "BuyBox alerts sent successfully";
    }
      /**
     * This function is responsible for extracting out of stock alert data from tbl_buybox_asin_scraped table and re-arranging that data for 
     * braodcasting and saving in to database as NOTIFICATIONS
     * 
     * This function takes last two parameters as "Pass By Reference"
     * 
     * @param object $cron
     * @param array &$managersArray
     * @param array &$accounts
     * @return array
     */
    private function GetUserSpecificOutOfStockData($cron, &$managersArray, &$accounts){
        $results = BuyBoxScrapResultModel::has("getResultAccounts")
        ->with("getResultAccounts.accounts")
        ->with("getResultAccounts.accounts:id,fkManagerId")
        ->with("getResultAccounts:fkAccountId,fkAsinId")
        ->with("getResultAccounts.accounts.brand.brandManagers")
        ->where("fkCollection",$cron->cNameBuybox)
        ->where("isNew",1)
        ->where("stockAlert",1)
        ->get();
        foreach ($results as $resultkey => $result) {
            $resultArray = [];
            $resultArray["id"] =  $result->id;
            $resultArray["fkCollection"] =  $result->fkCollection;
            $resultArray["isNew"] =  $result->isNew;
            $resultArray["brand"] =  $result->brand;
            $resultArray["soldBy"] =  $result->soldBy;
            $resultArray["soldByAlert"] =  $result->soldByAlert;
            $resultArray["price"] =  $result->price;
            $resultArray["priceOrignal"] =  $result->priceOrignal;
            $resultArray["primeDesc"] =  $result->primeDesc;
            $resultArray["prime"] =  $result->prime;
            $resultArray["stock"] =  $result->stock;
            $resultArray["stockAlert"] =  $result->stockAlert;
            $resultArray["url"] =  $result->url;
            $resultArray["asinCode"] =  $result->asinCode;
            $resultArray["createdAt"] =  $result->createdAt;
            // return $result->getResultAccounts;
            foreach ($result->getResultAccounts as $ResultAccountkey => $resultAccount) {
                if($resultAccount->accounts == null){
                    $managersArray["null"] = null;   
                    $accounts["null"][] = $resultArray;
                    continue;
                }
                $acountId = $resultAccount->accounts->id;
                $accounts["$acountId"][] = $resultArray;
                if($resultAccount->accounts->brand == null){
                    $managersArray["$acountId"] = "null";
                    continue;
                }//endif
                foreach ($resultAccount->accounts->brand->brandManagers as $managerkey => $manager) {
                    if( isset($managersArray["$acountId"]) && in_array( $manager->id, $managersArray["$acountId"] ))
                    {
                    continue;
                    }//endif
                    $managersArray["$acountId"][] = $manager->id;
                }//end foreach
    
            }//end foreach
            
        }//end foreach
        return [
            "accountViseData"=>$accounts,
            "accountsViseMangers"=>$managersArray,
         ];
    }
    /**
     * This function is responsible for extracting sold by alert data from tbl_buybox_asin_scraped table and re-arranging that data for 
     * braodcasting and saving in to database as NOTIFICATIONS
     * 
     * This function takes last two parameters as "Pass By Reference"
     * 
     * @param object $cron
     * @param array &$managersArray
     * @param array &$accounts
     * @return array
     */
    private function GetUserSpecificSoldByData($cron, &$managersArray, &$accounts){
        $results = BuyBoxScrapResultModel::has("getResultAccounts")
        ->with("getResultAccounts.accounts")
        ->with("getResultAccounts.accounts:id,fkManagerId")
        ->with("getResultAccounts:fkAccountId,fkAsinId")
        ->with("getResultAccounts.accounts.brand.brandManagers")
        ->where("fkCollection",$cron->cNameBuybox)
        ->where("isNew",1)
        ->where("soldByAlert",1)
        ->get();
        foreach ($results as $resultkey => $result) {
            $resultArray = [];
            $resultArray["id"] =  $result->id;
            $resultArray["fkCollection"] =  $result->fkCollection;
            $resultArray["isNew"] =  $result->isNew;
            $resultArray["brand"] =  $result->brand;
            $resultArray["soldBy"] =  $result->soldBy;
            $resultArray["soldByAlert"] =  $result->soldByAlert;
            $resultArray["price"] =  $result->price;
            $resultArray["priceOrignal"] =  $result->priceOrignal;
            $resultArray["primeDesc"] =  $result->primeDesc;
            $resultArray["prime"] =  $result->prime;
            $resultArray["stock"] =  $result->stock;
            $resultArray["stockAlert"] =  $result->stockAlert;
            $resultArray["url"] =  $result->url;
            $resultArray["asinCode"] =  $result->asinCode;
            $resultArray["createdAt"] =  $result->createdAt;
            // return $result->getResultAccounts;
            foreach ($result->getResultAccounts as $ResultAccountkey => $resultAccount) {
                if($resultAccount->accounts == null){
                    $managersArray["null"] = null;   
                    $accounts["null"][] = $resultArray;
                    continue;
                }
                $acountId = $resultAccount->accounts->id;
                $accounts["$acountId"][] = $resultArray;
                if($resultAccount->accounts->brand == null){
                    $managersArray["$acountId"] = "null";
                    continue;
                }//endif
                foreach ($resultAccount->accounts->brand->brandManagers as $managerkey => $manager) {
                    if( isset($managersArray["$acountId"]) && in_array( $manager->id, $managersArray["$acountId"] ))
                    {
                    continue;
                    }//endif
                    $managersArray["$acountId"][] = $manager->id;
                }//end foreach
    
            }//end foreach
            
        }//end foreach
        return [
            "accountViseData"=>$accounts,
            "accountsViseMangers"=>$managersArray,
         ];
    }
    /**
     * This function is responsible for saving notification and its details into database and for braodcasting notifications to their
     * respective users by calling first SaveNotficationInDatabase and then BroadcastNotificationsToUsers function for each notification
     *
     * @param object $cron
     * @param array $accountsViseMangers
     * @param array $accountViseData
     * @param array $broadcastNotificationData
     * @return void
     */
    private function InsertAccountBasedNotificationInDatabase($cron, $accountsViseMangers, $accountViseData, $broadcastNotificationData){
        $notiType = $broadcastNotificationData["type"];
        $accounts = $accountViseData;
        $notiDetails = array();
        $notiDetails["Crawler Ids"] = $cron->id;
        $notiDetails["Crawler Names"] = $cron->cNameBuybox;
        $notiDetails["Crawler Email"] = $cron->email;
        $notiDetails["Details Download Link"] = "Download $notiType Alert Details";
        $notiDetails["Completed At"] = date("Y-m-d H:i");
        //add foreach loop on account id here and make a multidimensional array for mass insertion
        foreach ($accounts as $accountId => $accountData) {
            # code...
            $DBnoti = array(
            "type"=>  $broadcastNotificationData["type"],
            "title"=> $broadcastNotificationData["title"],
            "message"=> $broadcastNotificationData["message"],
            "details"=> json_encode($notiDetails),
            "fkAccountId"=> $accountId == "null"? NULL : $accountId,
            "created_at"=> date("Y-m-d H:i"),
            );
            $addedNotification = $this->SaveNotificationInDatabase($DBnoti, $accountData);
            
            if($addedNotification == null)
            {
                continue;
            }
            
            $broadcastNotificationData["id"] = $addedNotification->id;
            $this->BroadcastNotificationsToUsers($broadcastNotificationData, $accountsViseMangers[$accountId]);

        }
    }
    /**
     * This function only inserts single notification and its details in database and returns the added notification
     * In case of any errors this function will rollback all the database quries and return NULL
     *
     * @param array $DBnoti
     * @param array $accountData
     * @return object|null
     */
    private function SaveNotificationInDatabase($DBnoti, $accountData){
        DB::beginTransaction();
        try {
            //change create to insert function for mass insertion
            $addNotificaiton = NotificationModel::create($DBnoti);
            $DBnotiDetails = [];
            foreach ($accountData as $key => $value) {
                $DBnotiDetail = array(
                    "n_id"=> $addNotificaiton->id,
                    "details"=> json_encode($value),
                    "created_at"=> date("Y-m-d H:i"),
                ); 
                array_push($DBnotiDetails,$DBnotiDetail);
                if(count($DBnotiDetails) >= 1000) {
                    NotificationDetailsModel::insert($DBnotiDetails);
                    $DBnotiDetails = [];
                }   
            }//end foreach
            if(count($DBnotiDetails) > 0) {
                NotificationDetailsModel::insert($DBnotiDetails);
            }
            DB::commit();
            return $addNotificaiton;
        } catch (\Exception $e) {
            DB::rollback();
            Log::error("Error Saving Notificaiton In DB");
            Log::error($e->getMessage());
            Log::error($e->getTrace());
        }
        return null;
    }
    
    /**
     * Only boradcast BuyBox Notifications to managers and admin on the bases of account ids
     *
     * @param array $broadcastNotificationData
     * @param array $mangers
     * @return void
     */
    private function BroadcastNotificationsToUsers($broadcastNotificationData, $mangersArray){
        $managers = $mangersArray;
        $notiType="sold by";
        if($mangersArray == null || $mangersArray == "null")
        {
            broadcast(new SendNotificationUserSpecific(
                null,
                $broadcastNotificationData
                ))->toOthers();
            return;
        }
       foreach ($managers as $key => $manageId) {
            /**
             * To Managers and in case no managers id found then broadcast to admin
             */
            broadcast(new SendNotificationUserSpecific(
                $manageId == "null"?null:$manageId,
                $broadcastNotificationData
                ))->toOthers();
            
        }//end foreach
        /**
         * Broad Cast to admin if and only if managerId is not null
         */
        broadcast(new SendNotificationUserSpecific(
            null,
            $broadcastNotificationData
            ))->toOthers();
    }
    
}
?>