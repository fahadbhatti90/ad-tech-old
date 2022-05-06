<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Console\Command;
use App\Models\ams\scheduleEmail\scheduleAdvertisingReports;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\File;
use App\Mail\BuyBoxEmailAlertMarkdown;
use Symfony\Component\Process\Process;
use App\Notifications\BuyBoxEmailAlert;
use App\Models\ams\scheduleEmail\amsScheduleSelectedEmailReports;
use App\Models\ams\scheduleEmail\scheduledEmailAdvertisingReportsMetrics;
use App\Models\ams\scheduleEmail\amsReportsMetrics;
use App\User;
use Rap2hpoutre\FastExcel\FastExcel;
use Rap2hpoutre\FastExcel\SheetCollection;

class generateAmsCompaignReportExcel extends Controller
{
    /**
     * @return view
     */
    public function downloadReports($scheduleId)
    {
    	$data['pageTitle'] = 'Download Advertising Reports';
        $data['pageHeading'] = 'Download Advertising Reports';
        $data['scheduleId'] = $scheduleId;
        //return view("subpages.historicalData.schistory")->with($data);
        return view('subpages.ams.advertisingReportsEmail.downloadReports')->with($data);
        //return view('subpages.mws.schistory')->with($data);

    }

    /**
     * @return csv
     */
    public function downloadCompaignReport($scheduleId)
    {

        scSetMemoryLimitAndExeTime();

                  
       $scheduleId = $scheduleId;
        $fileName='Compaigns-'.date('YmdHis').'-'.$scheduleId.'.xlsx';
        $report_type='Compaigns';
        if ($report_type=='Compaigns'){

      $scheduledReports = scheduleAdvertisingReports::find($scheduleId);
      //$scheduleCount = $scheduledReports->count();
      if ($scheduledReports !== null) {
          $scheduleId = $scheduledReports->id;
          $reportName = $scheduledReports->reportName;
          $amsProfileId = $scheduledReports->amsProfileId;
          $granularity = $scheduledReports->granularity;
          $addCC = $scheduledReports->addCC;
          $createdBy = $scheduledReports->createdBy;
          $GetManagerId = User::where('id', $createdBy)->first();
          $managerEmail = $GetManagerId->email;
          $timeFrame = $scheduledReports->timeFrame;
          $getSelectedSponsordTypes = amsScheduleSelectedEmailReports::distinct()->where('fkReportScheduleId',$scheduleId)->get(['fkSponsordTypeId']);

        $getSelectedSponsorTypesArray=array();
        foreach ($getSelectedSponsordTypes as $getSelectedSponsordType) {
            $getSelectedSponsorTypesArray[] = $getSelectedSponsordType->fkSponsordTypeId;
        } //end foreach
        $commaSeprateSponsordTypeArray=[];
        if (in_array(1, $getSelectedSponsorTypesArray))
  {
        $commaSeprateSponsordTypeArray[]='SD';
  }
          if (in_array(2, $getSelectedSponsorTypesArray))
  {
        $commaSeprateSponsordTypeArray[]='SP';
  }
   if (in_array(3, $getSelectedSponsorTypesArray))
  {
        $commaSeprateSponsordTypeArray[]='SB';
  }
        if (empty($commaSeprateSponsordTypeArray)) {
        $commaSeprateSponsordType = 'SP,SD,SB'; 
        }else{
        $commaSeprateSponsordTypeImplode = implode(",",$commaSeprateSponsordTypeArray);
        $commaSeprateSponsordType = "'$commaSeprateSponsordTypeImplode'";
        }   
        //echo $commaSeprateSponsordType;
        //exit;
         /*** Get selected metrics against parameter type  ends ***/

         /*** Get selected metrics against parameter type  starts ***/
     $selectCompaignReportsSelectedMetrics = scheduledEmailAdvertisingReportsMetrics::where("fkReportScheduleId",$scheduleId)->where("fkParameterType",1)->get();

     $compaignReportsSelectedMetricsCount = $selectCompaignReportsSelectedMetrics->count();
if ($compaignReportsSelectedMetricsCount>0) {
   /******** Get column name against metric id starts ***********/
   $compaignMetricsArray=[];
   $compaignMetricsArray = [
                                'brand_name',
                                'accouint_id',
                                'account_name',
                                'profile_id',
                                'profile_name',
                                'date_key',
                                'year_',
                                'week_'
                            ];
   foreach ($selectCompaignReportsSelectedMetrics as  $selectCompaignReportsSelectedMetric) {

     $currentComapaignMetricId = $selectCompaignReportsSelectedMetric->fkReportMetricId;
    
     /*************** Get column name against metric id starts ***********/
     $GetCompaignReportMetricsNames = amsReportsMetrics::where('id', $currentComapaignMetricId)->first();
     $GetCompaignReportMetricsNamesCount = $GetCompaignReportMetricsNames->count();
     if($GetCompaignReportMetricsNamesCount > 0){
      $tblCompaignColumnName = $GetCompaignReportMetricsNames->tblColumnName;
     $compaignMetricsArray[] = trim($tblCompaignColumnName);
     }//end if
     }//end foreach
    }//end if
   

   /* echo '<pre>';
    print_r($compaignMetricsArray);
    exit;
   */

    switch ($granularity) {
     case 'Daily':
     echo 'Daily';
       $DB2 = 'mysqlDb2';
   $parameters = array($amsProfileId,$commaSeprateSponsordType,$timeFrame);
   $storedProceduresData = \DB::connection($DB2)->select('call spCalculateDailyCampaignSchedulingReport(?,?,?)',$parameters);
   \DB::disconnect($DB2); 
    if (!empty($storedProceduresData)) {
      Log::info("filePath:app\Console\Commands\Ams\AmsAdvertisingEmailSchedule, Module Name: Advertising Reports to e-mail. Message :Data found against this schedule.");
       $storeNew = [];
  foreach ($storedProceduresData as $key => $value) {
    foreach ($compaignMetricsArray as $key1 ) {
      
      if (array_key_exists($key1, $value)) {
        //$this->info($key1);
          $matchesColumns[$key1] = $value->$key1;

      }
    }
    array_push($storeNew, $matchesColumns);
  }

       //$fileName='Compaigns-'.date('YmdHis').'-'.$scheduleId.'.xlsx';
       $fileTempPath = public_path('files/advertisingEmail/sd-compaigns-'.date('YmdHis').'-'.$scheduleId.'.csv'); 
       //$this->_generateSoldByCSV($storeNew,$fileTempPath);
       $fileNames[]=$fileTempPath;
   //    $status = $this->_sendSoldByEmailAlert($fileTempPath, $fileName, $cronData);
        }else{
          $noDATAEamil = $this->_noDataEmailAlert($cronData);
          Log::info("filePath:app\Console\Commands\Ams\AmsAdvertisingEmailSchedule, Module Name: Advertising Reports to e-mail. Message :No data found against this schedule.");
        }
       break;
       //weekely start
       case 'Weekly':
        echo 'Weekly';
       $DB2 = 'mysqlDb2';
   $parameters = array($amsProfileId,$commaSeprateSponsordType,$timeFrame);
   $storedProceduresData = \DB::connection($DB2)->select('call spCalculateWeeklyCampaignSchedulingReport(?,?,?)',$parameters);
   \DB::disconnect($DB2); 
      if (!empty($storedProceduresData)) {

          $storeNew = [];
  foreach ($storedProceduresData as $key => $value) {
    foreach ($compaignMetricsArray as $key1 ) {
      
      if (array_key_exists($key1, $value)) {
          $matchesColumns[$key1] = $value->$key1;

      }
    }
    array_push($storeNew, $matchesColumns);
  }
      
       //$fileName='Compaigns-'.date('YmdHis').'-'.$scheduleId.'.csv';
       $fileTempPath = public_path('files/advertisingEmail/Compaigns-'.date('YmdHis').'-'.$scheduleId.'.csv'); 
       //$this->_generateSoldByCSV($storeNew,$fileTempPath); 
       $fileNames[]=$fileTempPath;
       
     //  $status = $this->_sendSoldByEmailAlert($fileTempPath, $fileName, $cronData);
       }
       break;
       //monthly start
       case 'Monthly':
       echo 'Monthly';
       $DB2 = 'mysqlDb2';
   $parameters = array($amsProfileId,$commaSeprateSponsordType,$timeFrame);
   $storedProceduresData = \DB::connection($DB2)->select('call spCalculateMonthlyCampaignSchedulingReport(?,?,?)',$parameters);
   \DB::disconnect($DB2); 
      if (!empty($storedProceduresData)) {
        $storeNew = [];
  foreach ($storedProceduresData as $key => $value) {
    foreach ($compaignMetricsArray as $key1 ) {
      
      if (array_key_exists($key1, $value)) {
          $matchesColumns[$key1] = $value->$key1;

      }
    }
    array_push($storeNew, $matchesColumns);
  }
       //$fileName='Compaigns-'.date('YmdHis').'-'.$scheduleId.'.csv';
       $fileTempPath = public_path('files/advertisingEmail/Compaigns-'.date('YmdHis').'-'.$scheduleId.'.csv'); 
       //echo '<pre>';
       //print_r($storeNew);
       //dd($storeNew);
      // $this->_generateSoldByCSV($storeNew,$fileTempPath); 
       $fileNames[]=$fileTempPath;

       //$status = $this->_sendSoldByEmailAlert($fileTempPath, $fileName, $cronData);
     }
       break;
   }  



       }else{
        echo 'no record found';
       }






            $sheets = new SheetCollection([
                //'Users1' => MWSModel::get_report_excel(),
                'Compaign' => $storeNew
            ]);
            return (new FastExcel($sheets))->download($fileName);
        }


    }
    private function _generateSoldByCSV($bbResult, $fileTempPath){

        //return (new FastExcel(collect($bbResult)))->export($fileTempPath);
       //return (new FastExcel($bbResult))->download('file.csv');

      
            //return (new FastExcel($sheets))->download('file.csv');
        //$bbResult = array();

    }//end function Step 3.1
}
