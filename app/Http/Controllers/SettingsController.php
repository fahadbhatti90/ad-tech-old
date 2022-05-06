<?php

namespace App\Http\Controllers;

use App\Models\ScrapingModels\SettingsModel;
use Illuminate\Http\Request;

class SettingsController extends Controller
{
    
    public function __construct()
    {
        //$this->middleware('auth');
        $this->middleware('auth.super_admin');
    }

    public function SetSchedulingTime(Request $request){
        $response = array();
        $response["status"] = false;
        try {
            if($request->has("scheduleTime")){
                $setting = SettingsModel::find($request->setting_id);
                $setting->value = $request->scheduleTime;
                $result = $setting->save();
                if($result){
                    $response["message"] = "Time Updated Successfully";
                    $response["status"] = true;
                    return $response;
                }
                
                $response["message"] = "Fail to update time";
                return $response;
            }
            else {
                $response["message"] = "Please select a valid time";
                $response["error500"] = false;
                return $response;
            }
        } catch (\Throwable $th) {
            
            $response["500"] = true;
            $response["message"] = "There is some thing wrong pleae provide valid information and try again";
            $response["exception"] = $th->getMessage();
            return $response;
        }

        
    }//end function
    public function SetSerachRankSchedulingTime(Request $request){
        $response = array();
        $response["status"] = false;
        try {
            if($request->has("scheduleTime")){
                $setting = SettingsModel::find($request->setting_id);
                $setting->value = $request->SrScheduleTime;
                $result = $setting->save();
                if($result){
                    $response["message"] = "Time Updated Successfully";
                    $response["status"] = true;
                    return $response;
                }
                
                $response["message"] = "Fail to update time";
                return $response;
            }
            else {
                $response["message"] = "Please select a valid time";
                $response["error500"] = false;
                return $response;
            }
        }//end try
        catch (\Throwable $th)
        {
            
            $response["500"] = true;
            $response["message"] = "There is some thing wrong pleae provide valid information and try again";
            $response["exception"] = $th->getMessage();
            return $response;
        }//end catch
    }//end function
}//end class
