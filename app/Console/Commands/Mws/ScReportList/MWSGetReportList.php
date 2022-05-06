<?php

//namespace App\Console\Commands;
namespace App\Console\Commands\Mws\ScReportList;

use Config;
use Illuminate\Console\Command;
use App\Models\MWSModel;
use Illuminate\Support\Facades\Log;
//use Sonnenglas\AmazonMws\AmazonReportList;
use App\Libraries\mws\AmazonReportList;
class MWSGetReportList extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'mwsreportlist:cron';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'MWS Report List';

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
        Log::info("filePath:app\Console\Commands\Mws\ScReportList.Report List generated ids. Start Cron.");
        MWSModel::insert_mws_Activity('Start Cron','Report List generated ids','app\Console\Commands\Mws\ScReportList');
        $APIParametr = new MWSModel();
        $api_data= $APIParametr->get_merchants();
        if ($api_data) {
            Log::info("filePath:app\Console\Commands\Mws\ScReportList.Report List generated ids. Merchants Found.");
            MWSModel::insert_mws_Activity('Merchants Found.','Report List generated ids','app\Console\Commands\Mws\ScReportList');
            foreach ($api_data as $api_parameter) {
                $mws_config_id=trim($api_parameter->mws_config_id);
                Config::set('amazon-mws.store.store1.merchantId', trim($api_parameter->seller_id));
                //Config::set('amazon-mws.store.store1.marketplaceId', trim($api_parameter->marketplace_id));
                Config::set('amazon-mws.store.store1.marketplaceId','ATVPDKIKX0DER');
                Config::set('amazon-mws.store.store1.keyId', trim($api_parameter->mws_access_key_id));
                Config::set('amazon-mws.store.store1.secretKey', trim($api_parameter->mws_secret_key));
                Config::set('amazon-mws.store.store1.authToken', trim($api_parameter->mws_authtoken));
                $mws_submitted_request = MWSModel::get_mws_done_request($mws_config_id);

                if ($mws_submitted_request) {
                    Log::info("filePath:app\Console\Commands\Mws\ScReportList.Report List generated ids. Get Done Reports.");
                    MWSModel::insert_mws_Activity('Get Done Reports.','Report List generated ids','app\Console\Commands\Mws\ScReportList');
                    $req_id_array = array();
                    $i = 0;
                    foreach ($mws_submitted_request as $key) {
                        $req_id_array[$i] = $key->ReportRequestId;
                        $i++;
                    }

                    $amz = new AmazonReportList("store1"); //store name matches the array key in the config file
                    $amz->setRequestIds($req_id_array);
                    $amz->fetchReportList(); //no Amazon-fulfilled orders
                    Log::info("filePath:app\Console\Commands\Mws\ScReportList.Report List generated ids. Api Call Starts.");
                    MWSModel::insert_mws_Activity('Api Call Starts.','Report List generated ids','app\Console\Commands\Mws\ScReportList');
                    $report_list_data = $amz->getList();
                    if ($report_list_data){
                        Log::info("filePath:app\Console\Commands\Mws\ScReportList.Report List generated ids. Data Found In Api.");
                        MWSModel::insert_mws_Activity('Data Found In Api.','Report List generated ids','app\Console\Commands\Mws\ScReportList');
                    for ($j = 0; $j < sizeof($report_list_data); $j++) {
                        $data = $report_list_data[$j];
                        $MwsReportRequestId = $report_list_data[$j]['ReportRequestId'];
                        $result=MWSModel::update_mws_report_id($data, $MwsReportRequestId);
                        if ($result){
                            Log::info("filePath:app\Console\Commands\Mws\ScReportList.Report List generated ids. Report Ids Updated.");
                            MWSModel::insert_mws_Activity('Report Ids Updated.','Report List generated ids','app\Console\Commands\Mws\ScReportList');
                        }else{
                            Log::info("filePath:app\Console\Commands\Mws\ScReportList.Report List generated ids. Report Ids Not Updated.");
                            MWSModel::insert_mws_Activity('Report Ids Not Updated.','Report List generated ids','app\Console\Commands\Mws\ScReportList');
                        }
                    }
                    }else{
                        Log::info("filePath:app\Console\Commands\Mws\ScReportList.Report List generated ids. Data Not Found In Api.");
                        MWSModel::insert_mws_Activity('Data Not Found In Api.','Report List generated ids','app\Console\Commands\Mws\ScReportList');
                    }


                }else{
                    Log::info("filePath:app\Console\Commands\Mws\ScReportList.Report List generated ids. No Done Reports Found.");
                    MWSModel::insert_mws_Activity('No Done Reports Found.','Report List generated ids','app\Console\Commands\Mws\ScReportList');
                }
            }
        }
        else{
            Log::info("filePath:app\Console\Commands\Mws\ScReportList.Report List generated ids. Merchants Not Found.");
            MWSModel::insert_mws_Activity('Merchants Not Found.','Report List generated ids','app\Console\Commands\Mws\ScReportList');
        }
        Log::info("filePath:app\Console\Commands\Mws\ScReportList.Report List generated ids. End Cron.");
        MWSModel::insert_mws_Activity('End Cron.','Report List generated ids','app\Console\Commands\Mws\ScReportList');
    }
}
