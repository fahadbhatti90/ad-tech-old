<?php

namespace App\Http\Controllers\ClientAuth;

use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Models\NotificationModel;
use App\Http\Controllers\Controller;
use Rap2hpoutre\FastExcel\FastExcel;
use App\Libraries\NotificationHelper;
use Illuminate\Support\Facades\Artisan;
use App\Models\ClientModels\ClientModel;
use App\Models\NotificationDetailsModel;
use Illuminate\Support\Facades\Validator;
use App\Models\AccountModels\AccountModel;
use App\Models\Inventory\InventoryBrandsModel;
use App\Models\ProductPreviewModels\EventsModel;
use App\Libraries\DataTableHelpers\DataTableHelpers;
use App\Models\ProductPreviewModels\UserActionsModel;
use App\Models\ProductPreviewModels\ProductPreviewModel;
use App\Models\ProductPreviewModels\AllAsinsDetailsModel;
use App\Http\Controllers\Manager\GlobalBrandSwitcherController;

class NotesController extends Controller
{
        
    public function __construct()
    {
        $this->middleware('auth.manager');
    }//end constructor
    
    public function getNavigationData(Request $request){
        $data = null;
        $gbs = new GlobalBrandSwitcherController();
        $data = $gbs->getManagerBrands();
        $data["notiCount"] = NotificationHelper::getNotificaitonCount(session("activeRole"));
        $data["activeRole"] = session("activeRole");
        return $data;
    }
    public function getNotificaitons(Request $request){
        Artisan::call('cache:clear');
        //add currently login Manager Id in to where clause in each query
        $accountIds = AccountModel::where("fkBrandId", getBrandId())
                    ->select("id")
                    ->get()
                    ->map(function($item,$value){
                        return $item->id;
                });
        $data = [];
        $data['BuyBoxNotifications']["data"] = NotificationModel::whereIn("fkAccountId", $accountIds)
        ->where("type",1)
        ->orderBy('id', 'desc')
        ->get();
        $data['BuyBoxNotifications']["unseenCount"] = NotificationModel::whereIn("fkAccountId",$accountIds)
        ->where("type",1)
        ->where("status",0)
        ->count();
        $data['BlackListNotifications'] = $data['SettingsNotifications'] =  [];
        $data['accounts'] =  $accountIds;
                
        return $data;

    }//end function

    public function previewNotificaiton(Request $request){
        $notification = NotificationModel::with("account")->where('id', $request->notification)->first();
        if(!isset($notification)){
            return [
                "status"=>false,
                "message"=>"No Such Notification"
            ];
        }
        if($notification->account != null && session("activeRole") == 3)//true if manager's notification
        {
            $accountIds = AccountModel::where("fkBrandId",getBrandId())
                    ->select("id")
                    ->get()
                    ->map(function($item,$value){
                        return $item->toArray()["id"];
                    });
                    $accountIds = @json_decode(json_encode($accountIds), true);
                    
            if( $notification->type != 1 || !in_array($notification->account->id, ($accountIds)))
            return [
                "status"=>false,
                "message"=> "No Such Notification"
            ] ;
        }
        else {//true if other than manager's notficaiton
            return [
                "status"=>false,
                "message"=>"No Such Notification"
            ] ;
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
                        $stdecoded = json_decode($failData->failed_data);
                        foreach ($stdecoded as $datakey => $data) {
                            $asin_collection[ucwords(str_replace("-"," ",Str::kebab(($datakey))))] = $data; 
                        }//end foreach
                        $stdecoded = json_decode($failData->failed_reason);
                        foreach ($stdecoded as $reasonkey => $reason) {
                            $asin_collection["Reason".($reasonkey+1)] = $reason; 
                        }//end foreach
                        
                        array_push($asin_collections,$asin_collection);
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

    /***********************************************************************************************************/
    /**                                  Notes MODULE                                              **/
    /*********************************************************************************************************/
    private function getAccountIds(){
        return AccountModel::where("fkBrandId",getBrandId())
        ->select("id")
        ->get()
        ->map(function($item,$value){
            return $item->id;
        });
    }
    public function events(Request $request)
    {
        $options = $request->options;
        $accounts = $this->getAccountIds();
        $accountTN = AccountModel::getTableName();
        $data = [];
        $data["status"] = true;
        $productPreviewTN = ProductPreviewModel::getCompleteTableName();
        $accountsTable = AllAsinsDetailsModel::getTableName();
        $InventoryBrandsModel = InventoryBrandsModel::getTableName();
        $ProductPreviewModel = ProductPreviewModel::$tableName;
        $EventsModel = EventsModel::$tableName;
        $columnsToSearch = [
            "$ProductPreviewModel.asin",
            "notes",
            "occurrenceDate",
            "$accountTN.accountName",
            "$EventsModel.eventName",
            "$InventoryBrandsModel.overrideLabel"
        ];
        $eventLogs = ProductPreviewModel::select(
            "$ProductPreviewModel.id",
            "$ProductPreviewModel.asin",
            "$ProductPreviewModel.fkAccountId",
            "fkEventId as eventId",
            "notes",
            "occurrenceDate",
            "$accountTN.accountName",
            "$EventsModel.eventName",
            "$InventoryBrandsModel.overrideLabel"
        )
        ->leftJoin($accountTN, "$ProductPreviewModel.fkAccountId", '=', "$accountTN.id")
        ->leftJoin("$EventsModel", "$ProductPreviewModel.fkEventId", '=', "$EventsModel.id")
        ->leftJoin("$InventoryBrandsModel", "$ProductPreviewModel.fkAccountId", '=', "$InventoryBrandsModel.fkAccountId")
        ->whereIn("$productPreviewTN.fkAccountId", $accounts);
       
        $pageNumber = json_decode($options)->pageNumber;
        $paginatedData = DataTableHelpers::GetManualEventPaginatedData(
            $eventLogs, 
            $options, 
            $columnsToSearch, 
            "DATE($productPreviewTN.createdAt)", 
            "DESC"
        );

            $data["total"] = $paginatedData["total"];
            $data["data"] = ($paginatedData["data"]
            ->map(function($item,$value) use ($pageNumber){
                return [
                    "id" => $pageNumber > 1 ? $value+1+(($pageNumber-1)*10) : $value + 1 ,
                    "orignalId" => $item->id,
                    "asin" => $item->asin,
                    "accountName" =>  $item->overrideLabel != null ? $item->overrideLabel : $item->accountName ,
                    "eventName" => $item->eventName,
                    "eventId" => $item->eventId,
                    "notes" => $item->notes,
                    "occurrenceDate" => $item->occurrenceDate,
                    "createdAt" => $item->createdAt,
                    "fkAccountId" => $item->fkAccountId,
                ];
            }));
            return $data;
    }//end functon
    public function eventsData()
    {
        $data = [];
        $accounts = $this->getAccountIds();
        $data["accounts"] = AllAsinsDetailsModel::with("brand_alias:fkAccountId,overrideLabel")
        ->select("fk_account_id","fk_account_id as attr1", "accountName as attr2")
        ->distinct()
        ->whereIn("fk_account_id",$accounts)
        ->get();
        $data["status"] = true;
        return $data;
    }//end functon
    public function eventsLogs(Request $request){
        $eventLog = ProductPreviewModel::find($request->id);
        $accounts = $this->getAccountIds();
       if($eventLog){
        return array(
            'status' => true,
            'data' => $eventLog,
            'accounts' => AllAsinsDetailsModel::with("brand_alias:fkAccountId,overrideLabel")
            ->select("fk_account_id","fk_account_id as attr1", "accountName as attr2")
            ->distinct()
            ->whereIn("fk_account_id",$accounts)
            ->get(),
            'asins'=>$this->getAsin($eventLog->fkAccountId)["result"]
        );
       }
       return array(
            'status' => false,
            'message' => "No Such Event Log Found",
        );
    }
    public function manageNotes(Request $request){
        if($request->operationType == "edit"){
            return $this->editEventLog($request);
        }
        $occurrenceDates = $request->occurrenceDates;
        $eventsNotes = $request->eventsNotes;
        $fkEventIds = explode(",",$request->fkEventIds);
        $data =[];
        foreach ($fkEventIds as $key => $eventId) {
            for ($i=0; $i < count($occurrenceDates); $i++) { 
                $uniqueColumn = "$request->asin|$request->childBrand|".$eventId."|".$occurrenceDates[$i];
                array_push($data,[
                    'fkAccountId' =>$request->childBrand,
                    'asin' => $request->asin,
                    'fkEventId' => $eventId,
                    'occurrenceDate' => $occurrenceDates[$i],
                    'notes' => $request->eventsNotes[$key],
                    'uniqueColumn' => $uniqueColumn,
                    'createdAt' => date('Y-m-d H:i:s'),
                    'updatedAt' => date('Y-m-d H:i:s'),
                ]);
                
            }//end for
        }//end foreach
        if(count($data) < 0){
            return $response = array(
                'status' => false,
                'message' => "No Data Found",
            );
        }
        $message = "";
        
        $ppm = new ProductPreviewModel();
        $ppm->insertOrUpdate($data,["createdAt"]);//on update exclude createdAt
        return $response = array(
            'status' => true,
            'title' => "Success",
            'message' => "Event Log Added Successfully"
        );
    }//end function
    public function addEventLogs(Request $request){
        
        $dateRange = explode(" - ",$request->occurrenceDates);
        $startDate = $dateRange[0];
        $endDate = $dateRange[1];
        $startMonth = date('m', strtotime($startDate) );
        $endMonth = date('m', strtotime($endDate));
        $eventMonths = [];
        for ($i=$startMonth; $i <= $endMonth; $i++) { 
            $eventMonths[] = (int) $i;
        }
        $eventMonths = \implode(",", $eventMonths);
        $childBrand = $request->childBrand;
        $eventsNotes = ($request->eventsNotes);
        $fkEventIds = explode(",",$request->fkEventIds);
        $data =[];
        foreach ($fkEventIds as $key => $eventId) {
            $uniqueColumn = "$request->asin|$request->childBrand|".$eventId."|".$request->occurrenceDates;
             
            array_push($data,[
                'fkAccountId' =>$request->childBrand,
                'asin' => $request->asin,
                'fkEventId' => $eventId,
                'notes' => $request->eventsNotes[$key],
                'startDate' => $startDate,
                'endDate' => $endDate,
                'eventMonths' => $eventMonths,
                'uniqueColumn' => $uniqueColumn,
            ]);
        }//end foreach

        if(count($data) < 0){
            return $response = array(
                'status' => false,
                'title' => "Error Invalid Data",
                'message' => ["No Data Found"],
            );
        }
        $eventLog = new ProductPreviewModel();
        $eventLog->insertOrUpdate($data);
        return $response = array(
            'status' => true,
            'title' => "Success",
            'message' => "Event Log Added Successfully"
        );
    }//end function
    public function editEventLog($request){
       $eventLog = ProductPreviewModel::find($request->id);
       if( $eventLog ){
            $eventLog->notes = $request->fkEventIds == 4 ? $request->eventsNotes[0] : $request->eventsNotes[1];
            $eventLog->save();
            return array(
                'status' => true,
                'message' => "Event Log Updated Successfully"
            );
        }
        return array(
            'status' => false,
            'message' => "No Such Event Log Found",
        );
    }
    public function getRequiredData(Request $request){
        return $this->getAsin($request->accountId);
    }
    public function deleteEventLog(Request $request){
       $eventLog = ProductPreviewModel::find($request->id);
       if($eventLog ){
            $eventLog->delete();
            return array(
                'status' => true,
                'message' => "Event log removed successfully"
            );
       }
       else{
        return array(
            'status' => false,
            'message' => "No Such Event Log Found",
        );
       }
    }
    /************************************Private funcitons for Notes MODULE *************************************/
    
    private function getAsin($accountId){
        $data = AllAsinsDetailsModel::with("product_alias:asin,overrideLabel")
        ->where("fk_account_id",$accountId)
        ->whereNotNull("ASIN")
        ->whereNotNull("product_title")
        ->select("ASIN","ASIN as attr1", "product_title as attr2")
        ->distinct("ASIN")
        ->get();
        return array(
            'status' => true,
            'dropDownName' => "asin",
            'result' => $data ,
        );
    }//end function

    /************************************Private funcitons for Notes MODULE ENDS*************************************/

}
