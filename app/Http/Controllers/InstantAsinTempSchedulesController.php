<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Libraries\InstantScrapingController;
use App\Models\ScrapingModels\InstantScrapingTempScheduleModel;

class InstantAsinTempSchedulesController extends Controller
{
    private static function anySchedulesExist(){
        return InstantScrapingTempScheduleModel::count() > 0;
    }
    private static function checkSchedulesRunning(){
        return InstantScrapingTempScheduleModel::where("isRunning", 1)->exists();
    }
    public static function shouldProceed(){
        return (self::anySchedulesExist() && !self::checkSchedulesRunning());
    }
    public static function updateScheduleStatusToRunning(){
        return InstantScrapingTempScheduleModel::where("isRunning", 0)->update(["isRunning"=> 1]);
    }
    public static function getActiveScheduleCollectionIds(){
        InstantAsinTempSchedulesController::updateScheduleStatusToRunning();
        return InstantScrapingTempScheduleModel::select("fkCollectionId")
        ->where("isRunning", 1)
        ->get()
        ->map(function($item, $index){
            return $item->fkCollectionId;
        });//[1,2,4]
    }
    public static function startScraping(){
        $scraper = new InstantScrapingController();
        return $scraper->ScrapDataInstant();
    }
    public static function removeRunningSchedule(){
        return InstantScrapingTempScheduleModel::where("isRunning", 1)->delete();
    }
    public static function addInstantAsinTempSchedule($fkCollectionId){
        InstantScrapingTempScheduleModel::insert([
            "fkCollectionId"=>$fkCollectionId
        ]);
    }
}
