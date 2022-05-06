<?php

//namespace App\Console\Commands;
namespace App\Console\Commands\Mws\ScRequestReportList;
use Config;
use Illuminate\Console\Command;
use App\Models\MWSModel;
use Illuminate\Support\Facades\Log;
//use Sonnenglas\AmazonMws\AmazonReportRequestList;
use App\Libraries\mws\AmazonReportRequestList;


class MWSGetReportRequestList extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'mwsreportrequestlist:cron';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'MWS Report Request List';

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


        Log::info("filePath:app\Console\Commands\Mws\ScRequestReportList.Request Report List. Start Cron.");
        MWSModel::insert_mws_Activity('Start Cron','Request Report List','app\Console\Commands\Mws\ScRequestReportList');
        $APIParametr = new MWSModel();
        $api_data= $APIParametr->get_merchants();
        if ($api_data){
            Log::info("filePath:app\Console\Commands\Mws\ScRequestReportList.Request Report List. Merchants Found.");
            MWSModel::insert_mws_Activity('Merchants Found.','Request Report List','app\Console\Commands\Mws\ScRequestReportList');
            foreach ($api_data as $api_parameter)
            {
                $mws_config_id=trim($api_parameter->mws_config_id);
                Config::set('amazon-mws.store.store1.merchantId', trim($api_parameter->seller_id));
                //Config::set('amazon-mws.store.store1.marketplaceId', trim($api_parameter->marketplace_id));
                Config::set('amazon-mws.store.store1.marketplaceId','ATVPDKIKX0DER');
                Config::set('amazon-mws.store.store1.keyId', trim($api_parameter->mws_access_key_id));
                Config::set('amazon-mws.store.store1.secretKey', trim($api_parameter->mws_secret_key));
                Config::set('amazon-mws.store.store1.authToken', trim($api_parameter->mws_authtoken));
                $mws_submitted_request = MWSModel::get_mws_submitted_request($mws_config_id);


                if ($mws_submitted_request) {
                    Log::info("filePath:app\Console\Commands\Mws\ScRequestReportList.Request Report List. Submitted Requests Found.");
                    MWSModel::insert_mws_Activity('Submitted Requests Found.', 'Request Report List', 'app\Console\Commands\Mws\ScRequestReportList');

                    /*                    $req_id_array = array();
                                        $i = 0;
                                        foreach ($mws_submitted_request as $key) {
                                            $req_id_array[$i] = $key->ReportRequestId;
                                            $i++;
                                        }*/
                    foreach ($mws_submitted_request as $key) {
                        $amz = new AmazonReportRequestList("store1"); //store name matches the array key in the config file
                        //$amz->setRequestIds($req_id_array); //no Amazon-fulfilled orders
                        $ReportRequestId= $key->ReportRequestId;
                        $amz->setRequestIds($ReportRequestId); //no Amazon-fulfilled orders
                        $amz->fetchRequestList(); //no Amazon-fulfilled orders
                        Log::info("filePath:app\Console\Commands\Mws\ScRequestReportList.Request Report List. Api Call Starts.(Report Request Id:'.$ReportRequestId.')");
                        MWSModel::insert_mws_Activity('Api Call Starts.', 'Request Report List.(Report Request Id:'.$ReportRequestId.')', 'app\Console\Commands\Mws\ScRequestReportList');
                        $report_request_list_data = $amz->getList();
                       // echo '<pre>';
                       // print_r($report_request_list_data);
                        if ($report_request_list_data){
                            Log::info("filePath:app\Console\Commands\Mws\ScRequestReportList.Request Report List. Data Found In Api.(Report Request Id:'.$ReportRequestId.')");
                            MWSModel::insert_mws_Activity('Data Found In Api.', 'Request Report List(Report Request Id:'.$ReportRequestId.')', 'app\Console\Commands\Mws\ScRequestReportList');
                            for ($j = 0; $j < sizeof($report_request_list_data); $j++) {
                                $data = $report_request_list_data[$j];
                                $MwsReportRequestId = $report_request_list_data[$j]['ReportRequestId'];
                                $result = MWSModel::update_mws_report_request_status($data, $MwsReportRequestId);
                                if ($result) {
                                    Log::info("filePath:app\Console\Commands\Mws\salesReportsRequest.Request Sales Report.Request Report Status Updated.(Report Request Id:'.$ReportRequestId.')");
                                    MWSModel::insert_mws_Activity('Request Report Status Updated.','Sales Report Request(Report Request Id:'.$ReportRequestId.')','app\Console\Commands\Mws\catalogReportsRequest');
                                } else {
                                    Log::info("filePath:app\Console\Commands\Mws\salesReportsRequest.Request Sales Report.Request Report Status Not Updated.(Report Request Id:'.$ReportRequestId.')");
                                    MWSModel::insert_mws_Activity('Request Report Status Not Updated.','Sales Report Request(Report Request Id:'.$ReportRequestId.')','app\Console\Commands\Mws\catalogReportsRequest');
                                }
                            }
                        }else{
                            Log::info("filePath:app\Console\Commands\Mws\ScRequestReportList.Request Report List. Data Not Found In Api.(Report Request Id:'.$ReportRequestId.')");
                            MWSModel::insert_mws_Activity('Data Not Found In Api.', 'Request Report List(Report Request Id:'.$ReportRequestId.')', 'app\Console\Commands\Mws\ScRequestReportList');
                        }
                    }
                }else{
                    Log::info("filePath:app\Console\Commands\Mws\ScRequestReportList.Request Report List. Submitted Requests Not Found.");
                    MWSModel::insert_mws_Activity('Submitted Requests Not Found.','Request Report List','app\Console\Commands\Mws\ScRequestReportList');
                }

            }
        }else{
            Log::info("filePath:app\Console\Commands\Mws\ScRequestReportList.Request Report List. No Data Found In Api.");
            MWSModel::insert_mws_Activity('No Data Found In Api.','Request Report List','app\Console\Commands\Mws\ScRequestReportList');
        }
        Log::info("filePath:app\Console\Commands\Mws\ScRequestReportList.Request Report List. End Cron.");
        MWSModel::insert_mws_Activity('End Cron.','Request Report List','app\Console\Commands\Mws\ScRequestReportList');
    }
}
