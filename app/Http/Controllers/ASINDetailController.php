<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Rap2hpoutre\FastExcel\FastExcel;
use App\Models\ScrapingModels\asinModel;
use App\Models\ScrapingModels\CronModel;
use App\Models\ScrapingModels\ProxyModel;
use Illuminate\Support\Facades\Validator;
use App\Libraries\InstantScrapingController;
use App\Models\ScrapingModels\SettingsModel;
use App\Models\ScrapingModels\CollectionsModel;
use App\Models\ScrapingModels\InstantASINTempModel;
use App\Libraries\DataTableHelpers\DataTableHelpers;
use Artisan;
use App\Http\Controllers\InstantAsinTempSchedulesController;

class ASINDetailController extends Controller
{

    public function __construct()
    {
        //$this->middleware('auth');
        $this->middleware('auth.super_admin');
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function getAsinCollections(Request $request)
    {
        $options = $request->options;
        $data = $this->getCollections();
        
        return [
            "status" => true,
            "data" => $data,
        ];
    }//end function
    private function getCollections(){
        return CollectionsModel::select("id","c_name", "c_type", "created_at")
        ->orderBy("id","DESC")
        ->withCount("asin")
        ->get()->map(function($item, $index){
            return [
                "sr" => $index + 1,
                "id" => $item->id,
                "c_name" => $item->c_name,
                "c_type" => $item->c_type == 1 ? "Daily" : "Instant",
                "asinCount" => $item->asin_count == 0 ? "NA" : $item->asin_count,
                "created_at" => $item->created_at,
            ];
        });
    }
    public function uploadFile(Request $request)
    {
        $created_collection = null;
        $collection = null;
        try {
            $respon = array();
            $respon["status"] = false;
            if($request->hasFile('collectionFile'))
            {
                $file = $request->file('collectionFile');
                $fileExt = $file->getClientOriginalExtension();
                if ($fileExt != 'xls' && $fileExt != 'xlsx' && $fileExt != 'csv') {
                        $respon["message"] = "Please Select A Valid File Type";
                        return json_encode($respon);
                }
        
                $fullFileName = $file->getClientOriginalName();//getting Full File Name
                $fileNameOnly = pathinfo($fullFileName,PATHINFO_FILENAME);//getting File Name With out extension
                $newFileName = $fileNameOnly .'_'.time().'.'.$fileExt;//Foramting New Name with Time stamp for avoiding any duplicated names in databese
                
                // $scrap_model= new ScrapModel();
        
                $inputFileName =  public_path('uploads/'). $newFileName ;
                
                request()->collectionFile->move(public_path('uploads'), $newFileName );
        
                $collection = (new FastExcel)->import($inputFileName);
                if (!isset($collection[0]['asin']) || empty(trim($collection[0]['asin']))) {
                    $respon["message"] = "Please Select A Valid File";
                    if (File::exists($inputFileName)) {
                        File::delete($inputFileName);
                    }
                    return json_encode($respon);
                }
                
                $new_collection = array(
                    "c_name"=>$request->collectionName,
                    "c_type"=>$request->collectionType == "d",//if scrap type is d=>daily then true or 1 else false or 0
                    "created_at"=>date('Y-m-d H:i:s'),
                );
              
                
                $created_collection = CollectionsModel::create($new_collection);
                $data = array();
                $distinctAsin = [];
                foreach ($collection as $row) {
                    if(!isset($row['asin']) || empty($row['asin'])){
                        if(!is_null($created_collection));
                        {
                            CollectionsModel::find($created_collection->id)->delete();
                            if (File::exists($inputFileName)) {
                                File::delete($inputFileName);
                            }
                        }
                        $respon["message"] = "Please Select A Valid Fileee ";
                        return json_encode($respon);
                    }
                    $tempASIN = $row['asin'];
                    $distinctAsin["$tempASIN"] = $tempASIN;
                    // $single = array(
                    //     'c_id'=>$created_collection->id,
                    //     'asin_code'=>$row['asin'],
                    //     'created_on'=>date('Y-m-d H:i:s')
                    // );
                    // array_push($data,$single);
                }
                if(count($distinctAsin)>0){
                    foreach ($distinctAsin as $key => $value) {
                        $single = array(
                            'c_id'=>$created_collection->id,
                            'asin_code'=>$value,
                            'created_on'=>date('Y-m-d H:i:s')
                        );
                        array_push($data,$single);
                    }
                }
                else
                {
                    $respon["message"] = "No Distinct Record found";
                    return json_encode($respon);
                }
                if(count($data)>0){
                    if($request->collectionType == "d"){
                        $dailyASINS = array_chunk($data,1000);
                        foreach ($dailyASINS as $dailyASINkey => $dailyASIN) {
                            asinModel::insert($dailyASIN);
                        }
                    }
                    else {
                        $instantASINS = array_chunk($data,1000);
                        foreach ($instantASINS as $instantASINkey => $instantASIN) {
                            InstantASINTempModel::insert($instantASIN);
                        }
                        InstantAsinTempSchedulesController::addInstantAsinTempSchedule( $created_collection->id );
                    }
                }
                if (File::exists($inputFileName)) {
                    File::delete($inputFileName);
                }
                $respon["status"] = true;
                $respon["message"] = "Collection Added Successfully";
                $respon["tableData"] = $this->getCollections();
                $respon["collection_type"] = $request->collectionType == "i"? $created_collection->id : null ;
                return  json_encode($respon);//return's the success status
                }
                else
                {
                    $respon["message"] = "Fail To Upload File, File Not Found Try Again";
                    return json_encode($respon);
                }
    
        } catch (\Throwable $th) {
            if(!is_null($created_collection))
            {
                CollectionsModel::find($created_collection->id)->delete();
            }
            $respon["status"] = false;
            $respon["message"] = $th->getMessage();
            return json_encode($respon);
        }
      
    }
    public function showProxyForm(){
        $data['pageTitle'] = 'Proxy';
        $data['pageHeading'] = 'Upload Proxy';
        $data['proxies'] = ProxyModel::all();
        return view("subpages.scrapper.addProxy")
        ->with($data);
    }//end function

    public function deleteAllProxies(){
        ProxyModel::truncate();
        return back()->with("message","Proxies Deleted Successfully");
    }//end function
    public function uploadProxy(Request $request){
        $respon = array();
            $respon["status"] = false;
            if($request->hasFile('proxy'))
            {
                    $file = $request->file('proxy');
                    $fileExt = $file->getClientOriginalExtension();
                    
                    if ($fileExt != 'xls' && $fileExt != 'xlsx' && $fileExt != 'csv') {
                            $respon["message"] = "Please Select A Valid File Type";
                            return json_encode($respon);
                    }

                    $fullFileName = $file->getClientOriginalName();//getting Full File Name
                    $fileNameOnly = pathinfo($fullFileName,PATHINFO_FILENAME);//getting File Name With out extension
                    $newFileName = $fileNameOnly .'_'.time().'.'.$fileExt;//Foramting New Name with Time stamp for avoiding any duplicated names in databese
                    $inputFileName =  public_path('uploads/'). $newFileName ;

                    $file->move(public_path('uploads'), $newFileName);
                    $collection = (new FastExcel)->import($inputFileName);

                    // if (
                    //     (!isset(trim($collection[0]["proxy_ip"])) || empty(trim($collection[0]["proxy_ip"]))) &&
                    //     ((!isset(trim($collection[0]["proxy_auth"]))) || empty(trim($collection[0]["proxy_auth"])))
                    //  ) {
                    //     $respon["message"] = "Please Select A Valid File 1";
                    //     if (File::exists($inputFileName)) {
                    //         File::delete($inputFileName);
                    //     }
                    //     return json_encode($respon);
                    // }

                    $proxy = array();
                    foreach ($collection as $row) {
                        // if(
                        //     (
                        //         !isset(trim($row["proxy_ip"])) || 
                        //         empty(trim($row["proxy_ip"]))
                        //     ) 
                        //     &&
                        //     (
                        //         !isset(trim($row["proxy_auth"])) || 
                        //         empty(trim($row["proxy_auth"]))
                        //     )
                        // ){
                        //     $respon["message"] = "Please Select A Valid Fileee ";
                        //     return json_encode($respon);
                        // }
                        $single = array(
                            "proxy_ip"=>$row["proxy_ip"],
                            'proxy_auth'=>$row["proxy_auth"]
                        );
                        array_push($proxy,$single);
                    }

                    if(count($proxy)<=0){
                        $respon["message"] = "Fail to crate proxy array for DB insertion";
                        return json_encode($respon);
                    }

                    if(!ProxyModel::insert($proxy)){
                        $respon["message"] = "Fail to insert record in DB";
                        return json_encode($respon);
                    }

                    $respon["status"] = true;
                    $respon["message"] = "Process Successful";
                    $respon["message"] = "Process Successful";
                    return  json_encode($respon);//return's the success status

            }
            else
            {
                $respon["message"] = "Fail To Upload File, File Not Found Try Again";
                return json_encode($respon);
            }
    }
    public function converProxyTxtToCsv(Request $request){

        $respon = array();
        $respon["status"] = false;
        if($request->hasFile('proxy'))
        {
                $file = $request->file('proxy');
                $fileExt = $file->getClientOriginalExtension();
                
                if ($fileExt != 'txt') {
                        $respon["message"] = "Please Select A Valid File Type";
                        return json_encode($respon);
                }//end if

                $fullFileName = $file->getClientOriginalName();//getting Full File Name
                $fileNameOnly = pathinfo($fullFileName,PATHINFO_FILENAME);//getting File Name With out extension
                $newFileName = $fileNameOnly .'_'.time().'.'.$fileExt;//Foramting New Name with Time stamp for avoiding any duplicated names in databese
                $inputFileName =  public_path('uploads/'). $newFileName ;
                $file->move(public_path('uploads'), $newFileName);

                if (!File::exists($inputFileName)) {
                    $respon["message"] = "File Not Exist";
                    return json_encode($respon);
                } //end if
                
                $content = File::get($inputFileName);
                File::delete($inputFileName);
                $content = explode(',',$content);
                $l = array();
                if($content != false){
                    foreach($content as $line) {
                        array_push($l,$line);
                    }
                }//end if

                $proxy = array();
                if(count($l)<=0){
                    $respon["message"] = "Fail to extract content from file";
                    return json_encode($respon);
                }//end if

                foreach ($l as $key => $value) {
                    $r =  explode(';',$value);
                    if(count($r)>0){
                    $r = [
                        "proxy_ip" =>trim($r[0]),
                        "proxy_auth" => trim($r[1]),
                    ];
                    array_push($proxy,$r);
                    }
                }

                if(count($proxy)<=0){
                    $respon["message"] = "Fail to crate proxy array for DB insertion";
                    return json_encode($respon);
                }
                $list = collect($proxy);
                (new FastExcel(($list)))->export("proxy.csv");
                
                
                $respon["status"] = true;
                $respon["message"] = "Process Successful";
                $respon["message"] = "Process Successful";
                return  json_encode($respon);//return's the success status

            }
            else
            {
                $respon["message"] = "Fail To Upload File, File Not Found Try Again";
                return json_encode($respon);
            }

    }//end function
    /**
     * Instant Scraping Call.
     *
     * @return \Illuminate\Http\Response
     */
    public function ScrapData(Request $request)
    {
        $srController = new InstantScrapingController();
        $srController->ScrapDataInstant($request->c_id);
        return "Done"; 
    }

    public function testModel(){
        $curr_time = "2019-09-02 05:32:22"; 
  
        // The strtotime() function converts 
        // English textual date-time 
        // description to a UNIX timestamp. 
        $time_ago = strtotime($curr_time); 
          
     return $this->_to_time_ago($time_ago);
   
    }

    private function _to_time_ago( $time ) { 
      
        // Calculate difference between current 
        // time and given timestamp in seconds 
        $diff = time() - $time; 
          
        if( $diff < 1 ) {  
            return 'less than 1 second ago';  
        } 
          
        $time_rules = array (  
                    12 * 30 * 24 * 60 * 60 => 'year', 
                    30 * 24 * 60 * 60       => 'month', 
                    24 * 60 * 60           => 'day', 
                    60 * 60                   => 'hour', 
                    60                       => 'minute', 
                    1                       => 'second'
        ); 
      
        foreach( $time_rules as $secs => $str ) { 
              
            $div = $diff / $secs; 
      
            if( $div >= 1 ) { 
                  
                $t = round( $div ); 
                  
                return $t . ' ' . $str .  
                    ( $t > 1 ? 's' : '' ) . ' ago'; 
            } 
        } 
    } 
    private function _getInstantASINScrap(){
        if(CollectionsModel::where("c_type",0)->exists()){
          $colModel =  CollectionsModel::where("c_type",0)->with("asin")->get();
          $asinCount = 0;
          foreach ($colModel as $value) {
            $asinCount += $colModel->asin->count();
          }
          return $asinCount;
        }
        return 0;
    }
    public function Scrapboard()
    {
        $data['pageTitle'] = 'Scrapboard Dashboard';
        $data['pageHeading'] = 'Scrapboard';
        $data['schedules'] = CronModel::all()->count();
        $data['schedulesStoped'] = CronModel::where("cronStatus","stop")->count();
        $data['schedulesRunning'] = CronModel::where("cronStatus","run")->count();

        $schedule = CronModel::all()->sortByDesc("id")->first();
        $lastScheduleUploaded ="No Schedule Found";
        if(($schedule) != null){
            $lastScheduleUploaded = $this->_to_time_ago(strtotime($schedule->created_at));   
        }
        $data['lastScheduleUploaded']=  $lastScheduleUploaded;

        $lastCollectionUploaded = $data['lastCollectionAddedType'] = "No Collection Found";
        $data['totalCollection'] = CollectionsModel::all()->count();
        $data['totalCollectionDaily'] = CollectionsModel::where("c_type",1)->count();
        $data['totalCollectionInstant'] =CollectionsModel::where("c_type",0)->count();
        
        if(CollectionsModel::all()->count() > 0){
            $data['lastCollectionAddedType'] = CollectionsModel::all()->sortByDesc("id")->first()->c_type == 1?"Daily":"Instant";
            $lastCollectionUploaded = $this->_to_time_ago(strtotime( CollectionsModel::all()->sortByDesc("id")->first()->created_at));   
        }
        $data['lastCollectionUploaded'] =  $lastCollectionUploaded;
      


        $asin = asinModel::all()->sortByDesc("asin_id")->first();
        $lastAsinUploaded ="No ASIN Found";
        if(($asin) != null){
            $lastAsinUploaded = $this->_to_time_ago(strtotime($asin->created_on));   
        }
        $data['lastAsinUploaded']=  $lastAsinUploaded;
        
        return view('subpages.scrapper.scrapboard')->with($data);
    }

    /**
     * Show the form for editing the Schedule time for cron job.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function edit()
    {
        $data['status'] = true;
        $data['data'] = $this->GetAllSchedules();
        $data['collections'] = CollectionsModel::where("c_type",true)
        ->select("id","c_name as name")
        ->get(); 
        $data['scheduleTime'] = SettingsModel::where("name","scheduleTime")
        ->select("id","value")
        ->first();
        return $data;
        // return view('subpages.scrapper.scheduling')->with($data); 
    }
    private function GetAllSchedules(){
        return CronModel::with("asin_collection")
        ->orderBy("id","desc")
        ->get()
        ->map(function($item, $index){
            return [
                "sr"=> $index+1,
                "id"=> $item->id,
                "c_name"=> $item->asin_collection->c_name,
                "cronStatus"=> $item->cronStatus,
                "lastRun"=> $item->lastRun ? $item->lastRun : "Not ran yet",
                "cronDuration"=> $item->cronDuration == "0000-00-00" ? "Daily" : $item->cronDuration,
                "isRunning"=> $item->isRunning ? "Yes" : "No",
                "created_at"=> $item->created_at,
            ];
        });
    }//end function
    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function AddCron(Request $request)
    {  
        $response = array();

        $type = $request->input('collectionId');
        $titleTypeName = str_replace('_', ' ', $type);
        
        return $this->_addSchedual($request,  $titleTypeName);

    }//end function
     /**
     * Delete the specified Schedule Cron from DB.
     *
     * @param  int $id
     */
    public function deleteSchedual($id){
        try {
            $cronModel = CronModel::find($id);
            if($cronModel ==null){
                return json_encode($response = array(
                    'status' => false,
                    'message' => "Sorry, No Such Record Found",
                ));
            }
            if(!$cronModel->delete()){
                return json_encode($response = array(
                    'status' => false,
                    'message' => "Sorry, Fail To Delete Record TryAgain",
                ));
            }
            return json_encode($response = array(
                'status' => true,
                'message' => "Schedule cron deleted successfully",
                'tableData' => $this->GetAllSchedules(),
            ));
        } catch (\Throwable $th) {
            return json_encode($response = array(
                'status' => false,
                'message' =>  $th->getMessage(),
            ));
        }
        
    }
   

    private function _addSchedual(Request $request, $titleTypeName){
        // Store Cron record into DB
        $addedCron  = null;
        $isEdited = false;
        $cronDuration = "";
        $daysToAdd = 0;
        switch ($request->input('cronduration')) {
            case '1w':
            $daysToAdd = 7;
            $cronDuration = date('Y-m-d', strtotime("+$daysToAdd days"));
                break;
            case '2w':
            $daysToAdd = 14;
            $cronDuration = date('Y-m-d', strtotime("+$daysToAdd days"));
                break;
            case '3w':
            $daysToAdd = 21;
            $cronDuration = date('Y-m-d', strtotime("+$daysToAdd days"));
                break;
            case '1m':
                $daysToAdd = 30;
                $cronDuration = date('Y-m-d', strtotime("+$daysToAdd days"));
            default:
                $cronDuration = "0000-00-00";
                break;
        }
        
        if(CollectionsModel::find($request->input('collectionId'))->asin_cron == null){
            $RequestData['c_id'] = $request->input('collectionId');
            $RequestData['cronStatus'] = $request->input('cronstatus');
            $RequestData['cronDuration'] = $cronDuration;
            $RequestData['created_at'] = date('Y-m-d H:i:s');
            $addedCron = CronModel::create($RequestData);
        }
        else 
        {
            $isEdited = true;
            $addedCron =  CollectionsModel::find($request->input('collectionId'))->asin_cron; 
            $addedCron->cronStatus = $request->input('cronstatus');
            $addedCron->cronDuration = $cronDuration;
            $addedCron->created_at = date('Y-m-d H:i:s');
            $addedCron->save();
        }

        return $response = array(
            'status' => 'success',
            'message' => $isEdited?"Schedule Updated Successfully":"Schedule Added Successfully",
            'isEdited'=>$isEdited,
            'tableData' => $this->GetAllSchedules(),
        );
    }//function ends

    //Not In Use Edit Schedual Functionality 
    private function _editSchedual(Request $request,$titleTypeName){
            $cronModel = CronModel::find($request->id);
            if($cronModel ==null){
                return $response = array(
                    'status' => 'fail',
                    'title' => "Editing Record Operation Failed",
                    'message' => "Sorry, No Such Record Found",
                );
            }

            $cronModel->cronType = $request->crontype;
            $cronModel->cronTime = $request->crontime;
            $cronModel->cronStatus = $request->cronstatus;

            if(!$cronModel->save()){
                return $response = array(
                    'status' => 'fail',
                    'title' => "Internal Server Error",
                    'message' => "Not Able To Save Record",
                );
            }
            $newRowForFrontend  ='<td><i class="fa fa-trash"></i> | ';
            $newRowForFrontend .='<i class="fa fa-edit"></i></td>';
            $newRowForFrontend .='<td>'.$request["crontype"].'</td>';
            $newRowForFrontend .='<td>'.$request["crontime"].'</td>';
            $newRowForFrontend .='<td>'.$request["frequency"].'</td>';
            $newRowForFrontend .='<td>'.$request["cronstatus"].'</td>';
            return $response = array(
                'status' => 'success',
                'action_type'=>$request->action_type,
                'title' => $titleTypeName,
                'message' => "Schedule Edited Successfully",
                'newRow'=>$newRowForFrontend
            );
    }//function ends
    

}//end class
