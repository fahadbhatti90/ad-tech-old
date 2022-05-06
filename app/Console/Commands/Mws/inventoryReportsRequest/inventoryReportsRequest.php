<?php

namespace App\Console\Commands\Mws\inventoryReportsRequest;


use Config;
use Illuminate\Console\Command;
use App\Models\MWSModel;
use Illuminate\Support\Facades\Log;
//use Sonnenglas\AmazonMws\AmazonReportRequest;
use App\Libraries\mws\AmazonReportRequest;

class inventoryReportsRequest extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'inventoryReportsRequest:cron';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

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


        Log::info("filePath:app\Console\Commands\Mws\inventoryReportsRequest.Request Inventory Report. Start Cron.");
        MWSModel::insert_mws_Activity('Inventory Report RequestStart Cron', 'Inventory Report Request', 'app\Console\Commands\Mws\inventoryReportsRequest');
        $APIParametr = new MWSModel();
        $api_data = $APIParametr->get_active_merchants();
        if ($api_data) {
            Log::info("filePath:app\Console\Commands\Mws\inventoryReportsRequest.Request Inventory Report. Merchants Found.");
            MWSModel::insert_mws_Activity('Merchants Found', 'Inventory Report Request', 'app\Console\Commands\Mws\inventoryReportsRequest');
            foreach ($api_data as $api_parameter) {
                $mws_config_id = trim($api_parameter->mws_config_id);
                $check_sc_account_exist = MWSModel::check_sc_account_exist($mws_config_id);
                if ($check_sc_account_exist > 0) {

                    $get_sc_account_id = MWSModel::get_sc_account_id($mws_config_id);
                    if ($get_sc_account_id) {
                        $account_id = $get_sc_account_id[0]->id;
                        $get_sc_daily_batch_id = MWSModel::get_sc_daily_batch_id($account_id);
                        $sc_count_batch_id = count($get_sc_daily_batch_id);

                        if ($sc_count_batch_id > 0) {
                            $sc_batch_id = $get_sc_daily_batch_id[0]->batchId;
                            $historical_data_downloaded = $api_parameter->historical_data_downloaded;
                            Config::set('amazon-mws.store.store1.merchantId', trim($api_parameter->seller_id));
                            //Config::set('amazon-mws.store.store1.marketplaceId', trim($api_parameter->marketplace_id));
                            Config::set('amazon-mws.store.store1.marketplaceId', 'ATVPDKIKX0DER');
                            Config::set('amazon-mws.store.store1.keyId', trim($api_parameter->mws_access_key_id));
                            Config::set('amazon-mws.store.store1.secretKey', trim($api_parameter->mws_secret_key));
                            Config::set('amazon-mws.store.store1.authToken', trim($api_parameter->mws_authtoken));
                            //$store = Config::get('amazon-mws.store');
                            $amz = new AmazonReportRequest("store1"); //store name matches the array key in the config file
                            //$report_types=array('_GET_MERCHANT_LISTINGS_DATA_','_GET_FBA_FULFILLMENT_INVENTORY_HEALTH_DATA_','_GET_FBA_FULFILLMENT_INVENTORY_RECEIPTS_DATA_');
                            $report_types = [
                                '_GET_MERCHANT_LISTINGS_DATA_',
                                '_GET_FBA_FULFILLMENT_INVENTORY_HEALTH_DATA_',
                                '_GET_FBA_FULFILLMENT_INVENTORY_RECEIPTS_DATA_'
                            ];
                            foreach ($report_types as $report_types) {
                                $check_mws_existing_reports = MWSModel::check_mws_existing_reports($report_types, $mws_config_id);
                                $count_mws_existing_reports = count($check_mws_existing_reports);
                                if ($count_mws_existing_reports && $count_mws_existing_reports > 0) {
                                    $duplicate_requerst['fk_merchant_id'] = trim($check_mws_existing_reports[0]->fk_merchant_id);
                                    $duplicate_requerst['fkAccountId'] = trim($check_mws_existing_reports[0]->fkAccountId);
                                    $duplicate_requerst['fkBatchId'] = trim($check_mws_existing_reports[0]->fkBatchId);
                                    $duplicate_requerst['ReportRequestId'] = trim($check_mws_existing_reports[0]->ReportRequestId);
                                    $duplicate_requerst['ReportType'] = trim($check_mws_existing_reports[0]->ReportType);
                                    $duplicate_requerst['metricsType'] = 'Inventory';
                                    $duplicate_requerst['reportRequestDate'] = $check_mws_existing_reports[0]->reportRequestDate;
                                    //$duplicate_requerst['fk_merchant_id']=trim($check_mws_existing_reports[0]->fk_merchant_id);
                                    $duplicate_requerst['StartDate'] = $check_mws_existing_reports[0]->StartDate;
                                    $duplicate_requerst['EndDate'] = $check_mws_existing_reports[0]->EndDate;
                                    $duplicate_requerst['amazonStartDate'] = $check_mws_existing_reports[0]->amazonStartDate;
                                    $duplicate_requerst['amazonEndDate'] = $check_mws_existing_reports[0]->amazonEndDate;
                                    //$duplicate_requerst['Scheduled']=$check_mws_existing_reports[0]->Scheduled;
                                    $duplicate_requerst['SubmittedDate'] = $check_mws_existing_reports[0]->SubmittedDate;
                                    $duplicate_requerst['ReportProcessingStatus'] = '_SUBMITTED_';
                                    $duplicate_requerst['GeneratedReportId'] = $check_mws_existing_reports[0]->GeneratedReportId;
                                    //$duplicate_requerst['StartedProcessingDate']=$check_mws_existing_reports[0]->StartedProcessingDate;
                                    //$duplicate_requerst['CompletedProcessingDate']=$check_mws_existing_reports[0]->CompletedProcessingDate;
                                    //$duplicate_requerst['ReportId']=$check_mws_existing_reports[0]->ReportId;
                                    //$duplicate_requerst['AvailableDate']=$check_mws_existing_reports[0]->AvailableDate;
                                    $duplicate_requerst['Acknowledged'] = 'false';
                                    $duplicate_requerst['report_acknowledgement'] = $check_mws_existing_reports[0]->report_acknowledgement;
                                    $result_insert_duplicate_report = MWSModel::insert_mws_report_request($duplicate_requerst);
                                    if ($result_insert_duplicate_report) {
                                        Log::info("filePath:app\Console\Commands\Mws\inventoryReportsRequest. Request Records Inserted.(Report Type:'.$report_types.')");
                                        MWSModel::insert_mws_Activity('Request Records Inserted.', 'Inventory Report Request(Report Type:' . $report_types . ')', 'app\Console\Commands\Mws\inventoryReportsRequest');
                                    } else {
                                        Log::info("filePath:app\Console\Commands\Mws\inventoryReportsRequest. Request Records Not Inserted.(Report Type:'.$report_types.')");
                                        MWSModel::insert_mws_Activity('Request Records Not Inserted.(Report Type:' . $report_types . ')', 'Inventory Report Request', 'app\Console\Commands\Mws\inventoryReportsRequest');
                                    }
                                } else {
                                    $amz->setReportType(trim($report_types)); //no Amazon-fulfilled orders
                                    // $start_date=date("Y-m-d H:i:s",strtotime("-1 month"));
                                    $getDate = time();
                                    if ($historical_data_downloaded == 0) {
                                        $start_date = date('Y-m-d 00:00:00', strtotime('-31 day', $getDate));
                                    } else {
                                        $start_date = date('Y-m-d 00:00:00', strtotime('-1 day', $getDate));
                                    }
                                    //$start_date=date('Y-m-d 00:00:00', strtotime('-1 day', $getDate));
                                    $end_date = date('Y-m-d 23:59:59', strtotime('-1 day', $getDate));
                                    $reportRequestDate = date('Y-m-d', strtotime('-1 day', $getDate));
                                    $amz->setTimeLimits($start_date, $end_date);

                                    Log::info("filePath:app\Console\Commands\Mws\inventoryReportsRequest.Request Inventory Report. Api Call Starts.(Report Type:'.$report_types.')");
                                    MWSModel::insert_mws_Activity('Api Call Starts', 'Inventory Report Request(Report Type:' . $report_types . ')', 'app\Console\Commands\Mws\inventoryReportsRequest');

                                    $amz->requestReport();
                                    $report_request_data = $amz->getResponse();
                                    if ($report_request_data) {
                                        Log::info("filePath:app\Console\Commands\Mws\inventoryReportsRequest.Request Inventory Report. Data Found In Api.(Report Type:'.$report_types.')");
                                        MWSModel::insert_mws_Activity('Data Found In Api.', 'Inventory Report Request(Report Type:' . $report_types . ')', 'app\Console\Commands\Mws\inventoryReportsRequest');
                                        $report_request_data['fk_merchant_id'] = $mws_config_id;
                                        $report_request_data['fkAccountId'] = $account_id;
                                        $report_request_data['fkBatchId'] = $sc_batch_id;
                                        $report_request_data['metricsType'] = 'Inventory';
                                        $report_request_data['reportRequestDate'] = $reportRequestDate;
                                        $report_request_data['StartDate'] = $start_date;
                                        $report_request_data['EndDate'] = $end_date;
                                        $report_request_data['report_acknowledgement'] = 'false';
                                        $result_insert_report = MWSModel::insert_mws_report_request($report_request_data);
                                        if ($result_insert_report) {
                                            Log::info("filePath:app\Console\Commands\Mws\inventoryReportsRequest.Request Inventory Report. Request Records Inserted.(Report Type:'.$report_types.')");
                                            MWSModel::insert_mws_Activity('Request Records Inserted.', 'Inventory Report Request(Report Type:' . $report_types . ')', 'app\Console\Commands\Mws\inventoryReportsRequest');
                                        } else {
                                            Log::info("filePath:app\Console\Commands\Mws\inventoryReportsRequest.Request Inventory Report. Request Records Not Inserted.(Report Type:'.$report_types.')");
                                            MWSModel::insert_mws_Activity('Request Records Not Inserted.', 'Inventory Report Request(Report Type:' . $report_types . ')', 'app\Console\Commands\Mws\inventoryReportsRequest');
                                        }

                                    } else {
                                        Log::info("filePath:app\Console\Commands\Mws\inventoryReportsRequest.Request Inventory Report. No Data Found In Api.(Report Type:'.$report_types.')");
                                        MWSModel::insert_mws_Activity('No Data Found In Api', 'Inventory Report Request(Report Type:' . $report_types . ')', 'app\Console\Commands\Mws\inventoryReportsRequest');
                                    }
                                }
                            }
                        } else {
                            Log::info("filePath:app\Console\Commands\Mws\catalogReportsRequest. Bathc Id Not found for today.(Config Id:'.$mws_config_id.')");
                            MWSModel::insert_mws_Activity('No Data Found In Api.', 'Bathc Id Not found for today.(Config Id:' . $mws_config_id . ')', 'app\Console\Commands\Mws\catalogReportsRequest');
                        }
                    } else {
                        Log::info("filePath:app\Console\Commands\Mws\catalogReportsRequest. Config not associated with account.(Config Id:'.$mws_config_id.')");
                        MWSModel::insert_mws_Activity('No Data Found In Api.', 'Config not associated with account.(Config Id:' . $mws_config_id . ')', 'app\Console\Commands\Mws\catalogReportsRequest');
                    }
                } else {
                    Log::info("filePath:app\Console\Commands\Mws\catalogReportsRequest. Config not associated with account.(Config Id:'.$mws_config_id.')");
                    MWSModel::insert_mws_Activity('No Data Found In Api.', 'Config not associated with account.(Config Id:' . $mws_config_id . ')', 'app\Console\Commands\Mws\catalogReportsRequest');

                }
            }
        } else {
            Log::info("filePath:app\Console\Commands\Mws\inventoryReportsRequest.Request Inventory Report. Merchants Not Found.");
            MWSModel::insert_mws_Activity('Merchants Not Found.', 'Inventory Report Request', 'app\Console\Commands\Mws\inventoryReportsRequest');
        }
        Log::info("filePath:app\Console\Commands\Mws\inventoryReportsRequest.Request Inventory Report. End Cron.");
        MWSModel::insert_mws_Activity('Inventory Report. End Cron.', 'Inventory Report Request', 'app\Console\Commands\Mws\inventoryReportsRequest');
    }
}
