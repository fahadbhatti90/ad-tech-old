<?php

namespace App\Http\Controllers;

use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Models\NotificationModel;
use Rap2hpoutre\FastExcel\FastExcel;
use App\Libraries\NotificationHelper;
use Illuminate\Support\Facades\Artisan;
use App\Models\NotificationDetailsModel;
use App\Http\Controllers\Manager\GlobalBrandSwitcherController;

class SuperAdminNotificationController extends Controller
{
    
    public function __construct()
    {
        $this->middleware('auth.super_admin');
    }
    
    public function getNavigationData(Request $request){
        $data = null;
        $data["notiCount"] = NotificationHelper::getNotificaitonCount(session("activeRole"));
        $data["activeRole"] = session("activeRole");
        return $data;
    }
    public function getNotificaitons(Request $request){
        Artisan::call('cache:clear');
        return NotificationHelper::getNotificaitons($request->userType);
        
    }//end function

    public function previewNotificaiton(Request $request){ 
        $notification = NotificationModel::with("account")->where('id', $request->notification)->first();
        if(!isset($notification )|| session("activeRole") == 3 || (session("activeRole") == 1 && $notification->type == 1)
          ){
             // when super admin tries to access buybox noti
            return array(
              "status"=>false,
              "message"=>"No Such Notification Found"  
            );
        }
        if($notification->status==0){
            $notification->status = 1;
            $notification->save();
        }
        $notiType = $notification->type;
        switch ($notification->type) {
            case 1:
                $type="BuyBox";
                break;
            case 2:
                $type="Black List";
                break;
            
            default:
            $type="Settings";
                break;
        }
        $message =  Str::title($notification->message);
        $details = json_decode($notification->details);
        $notification = array(
           "ID #:" => $notification->id,
           "Type:" => $type,
           "Title:" => Str::title($notification->title),
           "Status:" => "Seen",
           "Time:" => to_time_ago($notification->created_at),
        );
        
        return [
            "status"=>true,
            "message" => $message,
            "details" => $details,
            "notiType" => $notiType,
            "notification" => $notification,
            "unseenNotiCount" => NotificationHelper::getNotificaitonCount(session("activeRole"))
        ];
    }//end function

    public function UpdateNotificationsStatus(Request $request){
        $notiIds = $request->ids;
        if(empty($notiIds)){
            return [
                "status"=> false,
                "message"=> "No new notification ids found"
            ];
        }
        $notificaitonsToUpdate = NotificationModel::whereIn("id",$notiIds);
        if($notificaitonsToUpdate->where("status", 0 )->exists()){
            return [
               "status" => ($notificaitonsToUpdate->update(["status"=>1]) > 0) 
            ];
        }
        else{
            return ["status"=>true];
        }
    }//end function

    /**
     * DownloadNotificationDetailsImprovised
     *
     * @param NotificationDetailsModel $notification
     * @return void
     */
    public function DownloadNotificationDetailsImprovised($notiDetailId)
    {
        
        ini_set('memory_limit', '2048M');
        ini_set('max_execution_time', 0);
        $notification = NotificationDetailsModel::with('notification')->where('n_id', $notiDetailId)->get() ?? abort(404);
        $details = $notification;
        $notificationParent = $notification[0]->notification;
        $asin_collections = [];
        if($notificationParent->type == 1)
        {
            foreach ($details as $detailkey => $detail) {
                
                $asin_collection = [];
                $notiDet = json_decode($detail->details);
               
                foreach ($notiDet as $datakey => $data) {
                    $asin_collection[ucwords(str_replace("-"," ",Str::kebab(($datakey))))] = $data; 
                }//end foreach
                array_push($asin_collections,$asin_collection);
            }   
            $fileName = $notificationParent->title."_Notification_#".$notificationParent->id."_".$notificationParent->created_at.".csv"; 
        }
        else
        {
            foreach ($details as $detailkey => $detail) {
                        $asin_collection = [];
                        $failData = json_decode($detail->details);
                        
                        if($notificationParent->type == 2){
                            $stdecoded = json_decode($failData->failed_data);
                            foreach ($stdecoded as $datakey => $data) {
                                $asin_collection[ucwords(str_replace("-"," ",Str::kebab(($datakey))))] = $data; 
                            }//end foreach
                            $stdecoded = json_decode($failData->failed_reason);
                            foreach ($stdecoded as $reasonkey => $reason) {
                                $asin_collection["Reason".($reasonkey+1)] = $reason; 
                            }//end foreach
                            
                            array_push($asin_collections,$asin_collection);
                        }//end if
                        else{
                            foreach ($failData as $datakey => $data) {
                                $asin_collection[ucwords(str_replace("-"," ",Str::kebab(($datakey))))] = $data; 
                            }//end foreach
                            array_push($asin_collections,$asin_collection);
                        }
                        
            }//end foreach
            $fileName = "Notification_#".$notificationParent->id."_".$notificationParent->created_at.".csv"; 
        }
        $list = collect($asin_collections);
        return (new FastExcel(($list)))->download($fileName);
    }

    public function DownloadNotificationDetails(NotificationDetailsModel $notification)
    {
        
        ini_set('memory_limit', '2048M');
        ini_set('max_execution_time', 0);

        $details =json_decode($notification->details);
         
        $asin_collections = array();
        if($notification->notification->type == 1)
        {
            $asin_collections = $this->_getCollectionForBuyBox($details);    
            $fileName = $notification->notification->title."_Notification_#".$notification->n_id."_".$notification->notification->created_at.".csv"; 
        }
        else
        {
        $asin_collections = $this->_getCollectionForBlackList($details);
        $fileName = "Notification_#".$notification->n_id."_".$notification->notification->created_at.".csv"; 
        }
        $list = collect($asin_collections);
        return (new FastExcel(($list)))->download($fileName);
    }

    private function _getCollectionForBuyBox($details){
        $asin_collections = [];
        foreach ($details as $key => $value) {
            $sts = (json_decode($value));
            // dd($sts);
            $i=0;
            if(is_object($sts)){
                $asin_collection = [];
                foreach ($sts as $datakey => $data) {
                    $asin_collection[ucwords(str_replace("-"," ",Str::kebab(($datakey))))] = $data; 
                }//end foreach
                array_push($asin_collections,$asin_collection);
            }//end if
        }//end foreach
        return $asin_collections;
    }//end function
    private function _getCollectionForBlackList($details){
        $asin_collections = [];
        foreach ($details as $key => $value) {
            $sts = (json_decode($value));
            $i=0;
            if(is_object($sts)){
                $asin_collection = [];
                foreach ($sts as $key => $st) {
                    $stdecoded = json_decode($st);
                    if($key == "failed_data")
                    {
                        foreach ($stdecoded as $datakey => $data) {
                            $asin_collection[ucwords(str_replace("-"," ",Str::kebab(($datakey))))] = $data; 
                        }//end foreach
                    }//end if
                    if($key == "failed_reason")
                    {
                        foreach ($stdecoded as $reasonkey => $reason) {
                            $asin_collection["Reason".($reasonkey+1)] = $reason; 
                        }//end foreach
                    }//end if
                }//end foreach
                array_push($asin_collections,$asin_collection);
            }//end if
        }//end foreach
        return $asin_collections;
    }//end function

}//end class
