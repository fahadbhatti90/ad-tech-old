<?php

//namespace App\Console\Commands;
namespace App\Console\Commands\Mws\screports;

use App\Libraries\mws\AmazonReport as AmazonReport;
use Config;
use Illuminate\Console\Command;
use App\Models\MWSModel;

//use Sonnenglas\AmazonMws\AmazonReport;
//use app\Libraries\ReportHandler;
use Illuminate\Support\Facades\Log;

class ScFbaReceipt extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'ScFbaReceipt:cron';

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
        Log::info("filePath:app\Console\Commands\Mws\screports\ScFbaReceipt.php.Download Inventory-ScFbaReceipt(_GET_FBA_FULFILLMENT_INVENTORY_RECEIPTS_DATA_). Start Cron.");
        MWSModel::insert_mws_Activity('Start Cron.', 'Download Inventory-ScFbaReceipt(_GET_FBA_FULFILLMENT_INVENTORY_RECEIPTS_DATA_)', 'app\Console\Commands\Mws\screports\ScFbaReceipt.php');
        $APIParametr = new MWSModel();
        $api_data = $APIParametr->get_merchants();
        /* echo '<pre>';
         print_r($api_data);*/
        if ($api_data) {
            Log::info("filePath:app\Console\Commands\Mws\screports\ScFbaReceipt.php.Download Inventory-ScFbaReceipt(_GET_FBA_FULFILLMENT_INVENTORY_RECEIPTS_DATA_). Merchants Found.");
            MWSModel::insert_mws_Activity('Merchants Found.', 'Download Inventory-ScFbaReceipt(_GET_FBA_FULFILLMENT_INVENTORY_RECEIPTS_DATA_)', 'app\Console\Commands\Mws\screports\ScFbaReceipt.php');
            foreach ($api_data as $api_parameter) {
                $mws_config_id = trim($api_parameter->mws_config_id);
                Config::set('amazon-mws.store.store1.merchantId', trim($api_parameter->seller_id));
                //Config::set('amazon-mws.store.store1.marketplaceId', trim($api_parameter->marketplace_id));
                Config::set('amazon-mws.store.store1.marketplaceId', 'ATVPDKIKX0DER');
                Config::set('amazon-mws.store.store1.keyId', trim($api_parameter->mws_access_key_id));
                Config::set('amazon-mws.store.store1.secretKey', trim($api_parameter->mws_secret_key));
                Config::set('amazon-mws.store.store1.authToken', trim($api_parameter->mws_authtoken));

                $getMwsDoneReportsArray = MWSModel::get_mws_done_reports('_GET_FBA_FULFILLMENT_INVENTORY_RECEIPTS_DATA_', $mws_config_id, 'Inventory');
                if ($getMwsDoneReportsArray) {
                    Log::info("filePath:app\Console\Commands\Mws\screports\ScFbaReceipt.php.Download Inventory-ScFbaReceipt(_GET_FBA_FULFILLMENT_INVENTORY_RECEIPTS_DATA_). Done Reports Found.");
                    MWSModel::insert_mws_Activity('Done Reports Found.', 'Download Inventory-ScFbaReceipt(_GET_FBA_FULFILLMENT_INVENTORY_RECEIPTS_DATA_)', 'app\Console\Commands\Mws\screports\ScFbaReceipt.php');
                    foreach ($getMwsDoneReportsArray as $index) {

                        $account_id=$index->fkAccountId;
                        $sc_batch_id=$index->fkBatchId;
                        $fk_request_id = trim($index->id);
                        $report_id = trim($index->GeneratedReportId);
                        $ReportRequestId = trim($index->ReportRequestId);
                        $reportRequestDate = $index->reportRequestDate;
                        //echo '<br>';

                        //$amz = new AmazonReport("store1"); //store name matches the array key in the config file
                        $amz = new AmazonReport("store1");
                        $amz->setReportId($report_id);
                        $amz->fetchReport();
                        Log::info("filePath:app\Console\Commands\Mws\screports\ScFbaReceipt.php.Download Inventory-ScFbaReceipt(_GET_FBA_FULFILLMENT_INVENTORY_RECEIPTS_DATA_). Api Call Starts.(Report Id:'.$report_id.')");
                        MWSModel::insert_mws_Activity('Api Call Starts.', 'Download Inventory-ScFbaReceipt(_GET_FBA_FULFILLMENT_INVENTORY_RECEIPTS_DATA_)(Report Id:'.$report_id.')', 'app\Console\Commands\Mws\screports\ScFbaReceipt.php');
                        $path = '';
                        $report_data = $amz->saveReport($path);
                        if (!empty($report_data)) {
                            Log::info("filePath:app\Console\Commands\Mws\screports\ScFbaReceipt.php.Download Inventory-ScFbaReceipt(_GET_FBA_FULFILLMENT_INVENTORY_RECEIPTS_DATA_). Data Found In Api.(Report Id:'.$report_id.')");
                            MWSModel::insert_mws_Activity('Data Found In Api.', 'Download Inventory-ScFbaReceipt(_GET_FBA_FULFILLMENT_INVENTORY_RECEIPTS_DATA_)(Report Id:'.$report_id.')', 'app\Console\Commands\Mws\screports\ScFbaReceipt.php');
                            $insert_data_array = array();
                            foreach ($report_data as $insert_record) {
                                // echo '<pre>';
                                //print_r($insert_record);
                                //exit;
                                $data['fkAccountId'] = $account_id;
                                $data['fkBatchId'] = $sc_batch_id;
                                $data['fkRequestId'] = $fk_request_id;
                                $data['reportId'] = $report_id;
                                $data['reportRequestId'] = $ReportRequestId;
                                $data['reportRequestDate'] = $reportRequestDate;
                                $data['receivedDate'] = (string)isset($insert_record['received-date']) && trim($insert_record['received-date']) != '' ? $insert_record['received-date'] : '0000-00-00 00:00:00';
                                //$data['merchantOrderId']=addslashes($insert_record['merchantOrderId']);
                                $data['fnsku'] = (string)isset($insert_record['fnsku']) && trim($insert_record['fnsku']) != '' ? $insert_record['fnsku'] : 'NA';
                                $data['sku'] = (string)isset($insert_record['sku']) && trim($insert_record['sku']) != '' ? $insert_record['sku'] : 'NA';
                                /* $productName= scPercentageToNull(ScDashToNull(ScRemoveLeftParantesis(ScRemoveRightParantesis(ScRemQuestionMark(ScRemoveDollarSign(scRemoveUnderscoreAndSlash(scRemoveComma(scRemoveSlashnAndr(scLimitStringLength(scConvertToUtf8Strings($insert_record['product-name'])))))))))));
                                 $data['productName'] = (string)isset($insert_record['product-name']) && !empty($insert_record['product-name']) ? $productName : 'NA';*/
                                $productName = scConvertToUtf8Strings($insert_record['product-name']);
                                $data['productName'] = (string)isset($insert_record['product-name']) && trim($insert_record['product-name']) != '' ? $productName : 'NA';
                                $data['quantity'] = (string)isset($insert_record['quantity']) && trim($insert_record['quantity']) != '' ? $insert_record['quantity'] : '0';
                                $data['fbaShipmentId'] = (string)isset($insert_record['fba-shipment-id']) && trim($insert_record['fba-shipment-id']) != '' ? $insert_record['fba-shipment-id'] : 'NA';
                                $data['fulfillmentCenterId'] = (string)isset($insert_record['fulfillment-center-id']) && trim($insert_record['fulfillment-center-id']) != '' ? $insert_record['fulfillment-center-id'] : 'NA';

                                //MWSModel::insert_mws_ScFbaReceiptReport($data);
                                // MWSModel::insert_mws_ScFbaRestockReport($data);
                                $data['createdAt'] = date('Y-m-d H:i:s');
                                //array_push($insert_data_array, $data);
                                MWSModel::insert_mws_ScFbaReceiptReport($data);
                            }
                            /*if (!empty($insert_data_array)) {
                                MWSModel::insert_mws_ScFbaReceiptReport($insert_data_array);
                            }*/
                            $acknowledgement = MWSModel::update_mws_report_acknowledgement($fk_request_id, 'true');
                            if ($acknowledgement) {
                                Log::info("filePath:app\Console\Commands\Mws\screports\ScFbaReceipt.php.Download Inventory-ScFbaReceipt(_GET_FBA_FULFILLMENT_INVENTORY_RECEIPTS_DATA_). Report Acknowledge In Database.(Report Id:'.$report_id.')");
                                MWSModel::insert_mws_Activity('Report Acknowledge In Database', 'Download Inventory-ScFbaReceipt(_GET_FBA_FULFILLMENT_INVENTORY_RECEIPTS_DATA_)(Report Id:'.$report_id.')', 'app\Console\Commands\Mws\screports\ScFbaReceipt.php');

                            } else {
                                Log::info("filePath:app\Console\Commands\Mws\screports\ScFbaReceipt.php.Download Inventory-ScFbaReceipt(_GET_FBA_FULFILLMENT_INVENTORY_RECEIPTS_DATA_). Report Not Acknowledge In Database.");
                                MWSModel::insert_mws_Activity('Report Not Acknowledge In Database.', 'Download Inventory-ScFbaReceipt(_GET_FBA_FULFILLMENT_INVENTORY_RECEIPTS_DATA_)', 'app\Console\Commands\Mws\screports\ScFbaReceipt.php');

                            }
                        } else {
                            $acknowledgement = MWSModel::update_mws_report_acknowledgement($fk_request_id, 'no_data');
                            Log::info("filePath:app\Console\Commands\Mws\screports\ScFbaReceipt.php.Download Inventory-ScFbaReceipt(_GET_FBA_FULFILLMENT_INVENTORY_RECEIPTS_DATA_). No Data Found In Api.(Report Id:'.$report_id.')");
                            MWSModel::insert_mws_Activity('No Data Found In Api.', 'Download Inventory-ScFbaReceipt(_GET_FBA_FULFILLMENT_INVENTORY_RECEIPTS_DATA_)(Report Id:'.$report_id.')', 'app\Console\Commands\Mws\screports\ScFbaReceipt.php');
                        }
                    }
                } else {
                    Log::info("filePath:app\Console\Commands\Mws\screports\ScFbaReceipt.php.Download Inventory-ScFbaReceipt(_GET_FBA_FULFILLMENT_INVENTORY_RECEIPTS_DATA_). Done Reports Not Found.");
                    MWSModel::insert_mws_Activity('Done Reports Not Found.', 'Download Inventory-ScFbaReceipt(_GET_FBA_FULFILLMENT_INVENTORY_RECEIPTS_DATA_)', 'app\Console\Commands\Mws\screports\ScFbaReceipt.php');
                }
            }
        } else {
            Log::info("filePath:app\Console\Commands\Mws\screports\ScFbaReceipt.php.Download Inventory-ScFbaReceipt(_GET_FBA_FULFILLMENT_INVENTORY_RECEIPTS_DATA_). Merchants Not Found.");
            MWSModel::insert_mws_Activity('Merchants Not Found.', 'Download Inventory-ScFbaReceipt(_GET_FBA_FULFILLMENT_INVENTORY_RECEIPTS_DATA_)', 'app\Console\Commands\Mws\screports\ScFbaReceipt.php');
        }
        Log::info("filePath:app\Console\Commands\Mws\screports\ScFbaReceipt.php.Download Inventory-ScFbaReceipt(_GET_FBA_FULFILLMENT_INVENTORY_RECEIPTS_DATA_). End Cron.'");
        MWSModel::insert_mws_Activity('End Cron.\'', 'Download Inventory-ScFbaReceipt(_GET_FBA_FULFILLMENT_INVENTORY_RECEIPTS_DATA_)', 'app\Console\Commands\Mws\screports\ScFbaReceipt.php');
    }
}
