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

class ScFbaReturns extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'ScFbaReturns:cron';

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
        Log::info("filePath:app\Console\Commands\Mws\screports\ScFbaReturns.php.Download Sales-ScFbaReturns(_GET_FBA_FULFILLMENT_CUSTOMER_RETURNS_DATA_). Start Cron.");
        MWSModel::insert_mws_Activity('Start Cron.', 'Download Sales-ScFbaReturns(_GET_FBA_FULFILLMENT_CUSTOMER_RETURNS_DATA_)', 'app\Console\Commands\Mws\screports\ScFbaReturns.php');
        $APIParametr = new MWSModel();
        $api_data = $APIParametr->get_merchants();

        if ($api_data) {
            Log::info("filePath:app\Console\Commands\Mws\screports\ScFbaReturns.php.Download Sales-ScFbaReturns(_GET_FBA_FULFILLMENT_CUSTOMER_RETURNS_DATA_). Merchants Found.");
            MWSModel::insert_mws_Activity('Merchants Found.', 'Download Sales-ScFbaReturns(_GET_FBA_FULFILLMENT_CUSTOMER_RETURNS_DATA_)', 'app\Console\Commands\Mws\screports\ScFbaReturns.php');
            foreach ($api_data as $api_parameter) {
                $mws_config_id = trim($api_parameter->mws_config_id);
                Config::set('amazon-mws.store.store1.merchantId', trim($api_parameter->seller_id));
                //Config::set('amazon-mws.store.store1.marketplaceId', trim($api_parameter->marketplace_id));
                Config::set('amazon-mws.store.store1.marketplaceId', 'ATVPDKIKX0DER');
                Config::set('amazon-mws.store.store1.keyId', trim($api_parameter->mws_access_key_id));
                Config::set('amazon-mws.store.store1.secretKey', trim($api_parameter->mws_secret_key));
                Config::set('amazon-mws.store.store1.authToken', trim($api_parameter->mws_authtoken));

                $getMwsDoneReportsArray = MWSModel::get_mws_done_reports('_GET_FBA_FULFILLMENT_CUSTOMER_RETURNS_DATA_', $mws_config_id, 'Sales');
                if ($getMwsDoneReportsArray) {
                    Log::info("filePath:app\Console\Commands\Mws\screports\ScFbaReturns.php.Download Sales-ScFbaReturns(_GET_FBA_FULFILLMENT_CUSTOMER_RETURNS_DATA_). Done Reports Found.");
                    MWSModel::insert_mws_Activity('Done Reports Found.', 'Download Sales-ScFbaReturns(_GET_FBA_FULFILLMENT_CUSTOMER_RETURNS_DATA_)', 'app\Console\Commands\Mws\screports\ScFbaReturns.php');
                    foreach ($getMwsDoneReportsArray as $index) {

                        $account_id=$index->fkAccountId;
                        $sc_batch_id=$index->fkBatchId;
                        $fk_request_id = trim($index->id);
                        $report_id = trim($index->GeneratedReportId);
                        $ReportRequestId = trim($index->ReportRequestId);
                        $reportRequestDate = $index->reportRequestDate;
                        Log::info("filePath:app\Console\Commands\Mws\screports\ScFbaReturns.php.Download Sales-ScFbaReturns(_GET_FBA_FULFILLMENT_CUSTOMER_RETURNS_DATA_). Api Call Starts.(Report Id:'.$report_id.')");
                        MWSModel::insert_mws_Activity('Api Call Starts.', 'Download Sales-ScFbaReturns(_GET_FBA_FULFILLMENT_CUSTOMER_RETURNS_DATA_)(Report Id:'.$report_id.')', 'app\Console\Commands\Mws\screports\ScFbaReturns.php');
                        // $amz = new AmazonReport("store1"); //store name matches the array key in the config file
                        $amz = new AmazonReport("store1");
                        $amz->setReportId($report_id);
                        $amz->fetchReport();
                        $path = '';
                        $report_data = $amz->saveReport($path);
                       /* echo '<pre>';
                        print_r($report_data);
                        exit;*/

                        if (!empty($report_data)) {
                            Log::info("filePath:app\Console\Commands\Mws\screports\ScFbaReturns.php.Download Sales-ScFbaReturns(_GET_FBA_FULFILLMENT_CUSTOMER_RETURNS_DATA_). Data Found In Api.(Report Id:'.$report_id.')");
                            MWSModel::insert_mws_Activity('Data Found In Api.', 'Download Sales-ScFbaReturns(_GET_FBA_FULFILLMENT_CUSTOMER_RETURNS_DATA_)(Report Id:'.$report_id.')', 'app\Console\Commands\Mws\screports\ScFbaReturns.php');
                            $insert_data_array = array();
                            foreach ($report_data as $insert_record) {

                                $data['fkAccountId'] = $account_id;
                                $data['fkBatchId'] = $sc_batch_id;
                                $data['fkRequestId'] = $fk_request_id;
                                $data['reportId'] = $report_id;
                                $data['reportRequestId'] = $ReportRequestId;
                                $data['reportRequestDate'] = $reportRequestDate;
                                $data['returnDate'] = (string)isset($insert_record['return-date']) && trim($insert_record['return-date']) != '' ? $insert_record['return-date'] : 'NA';
                                $data['orderId'] = (string)isset($insert_record['order-id']) && trim($insert_record['order-id']) != '' ? $insert_record['order-id'] : 'NA';
                                $data['sku'] = (string)isset($insert_record['sku']) && trim($insert_record['sku']) != '' ? $insert_record['sku'] : 'NA';
                                $data['asin'] = (string)isset($insert_record['asin']) && trim($insert_record['asin']) != '' ? $insert_record['asin'] : 'NA';
                                $data['fnsku'] = (string)isset($insert_record['fnsku']) && trim($insert_record['fnsku']) != '' ? $insert_record['fnsku'] : 'NA';
                                /* $productName= scPercentageToNull(ScDashToNull(ScRemoveLeftParantesis(ScRemoveRightParantesis(ScRemQuestionMark(ScRemoveDollarSign(scRemoveUnderscoreAndSlash(scRemoveComma(scRemoveSlashnAndr(scConvertToUtf8Strings(strip_tags($insert_record['product-name'])))))))))));*/
                                $productName = scConvertToUtf8Strings($insert_record['product-name']);
                                $data['productName'] = (string)isset($insert_record['product-name']) && trim($insert_record['product-name']) != '' ? $productName : 'NA';
                                $data['quantity'] = (string)isset($insert_record['quantity']) && trim($insert_record['quantity']) != '' ? $insert_record['quantity'] : '0';
                                $data['fulfillmentCenterId'] = (string)isset($insert_record['fulfillment-center-id']) && trim($insert_record['fulfillment-center-id']) != '' ? $insert_record['fulfillment-center-id'] : 'NA';
                                $data['detailedDisposition'] = (string)isset($insert_record['detailed-disposition']) && trim($insert_record['detailed-disposition']) != '' ? $insert_record['detailed-disposition'] : 'NA';
                                $data['reason'] = (string)isset($insert_record['reason']) && trim($insert_record['reason']) != '' ? $insert_record['reason'] : 'NA';
                                $data['status'] = (string)isset($insert_record['status']) && trim($insert_record['status']) != '' ? $insert_record['status'] : 'NA';
                                $data['licensePlateNumber'] = (string)isset($insert_record['license-plate-number']) && trim($insert_record['license-plate-number']) != '' ? $insert_record['license-plate-number'] : 'NA';
                                //$data['customerComments']=(string)isset($insert_record['customer-comments']) && !empty($insert_record['customer-comments']) ? $insert_record['customer-comments'] : 'NA';
                                $customerComments = scConvertToUtf8Strings($insert_record['customer-comments']);
                                $data['customerComments'] = (string)isset($insert_record['customer-comments']) && trim($insert_record['customer-comments']) != '' ? $customerComments : 'NA';
                                $data['createdAt'] = date('Y-m-d H:i:s');
                                //array_push($insert_data_array, $data);
                                $result = MWSModel::insert_mws_ScFbaReturnsReport($data);
                            }
                           /* if (!empty($insert_data_array)) {
                                $result = MWSModel::insert_mws_ScFbaReturnsReport($data);
                            }*/
                            $acknowledgement = MWSModel::update_mws_report_acknowledgement($fk_request_id, 'true');
                            if ($acknowledgement) {
                                Log::info("filePath:app\Console\Commands\Mws\screports\ScFbaReturns.php.Download Sales-ScFbaReturns(_GET_FBA_FULFILLMENT_CUSTOMER_RETURNS_DATA_). Report Acknowledge In Database.(Report Id:'.$report_id.')");
                                MWSModel::insert_mws_Activity('Report Acknowledge In Database.', 'Download Sales-ScFbaReturns(_GET_FBA_FULFILLMENT_CUSTOMER_RETURNS_DATA_)(Report Id:'.$report_id.')', 'app\Console\Commands\Mws\screports\ScFbaReturns.php');
                            } else {
                                Log::info("filePath:app\Console\Commands\Mws\screports\ScFbaReturns.php.Download Sales-ScFbaReturns(_GET_FBA_FULFILLMENT_CUSTOMER_RETURNS_DATA_). Report Not Acknowledge In Database.(Report Id:'.$report_id.')");
                                MWSModel::insert_mws_Activity('Report Not Acknowledge In Database.', 'Download Sales-ScFbaReturns(_GET_FBA_FULFILLMENT_CUSTOMER_RETURNS_DATA_)(Report Id:'.$report_id.')', 'app\Console\Commands\Mws\screports\ScFbaReturns.php');
                            }
                        } else {
                            $acknowledgement = MWSModel::update_mws_report_acknowledgement($fk_request_id, 'no_data');
                            Log::info("filePath:app\Console\Commands\Mws\screports\ScFbaReturns.php.Download Sales-ScFbaReturns(_GET_FBA_FULFILLMENT_CUSTOMER_RETURNS_DATA_). Data Not Found In Api.(Report Id:'.$report_id.')");
                            MWSModel::insert_mws_Activity('Data Not Found In Api.', 'Download Sales-ScFbaReturns(_GET_FBA_FULFILLMENT_CUSTOMER_RETURNS_DATA_)(Report Id:'.$report_id.')', 'app\Console\Commands\Mws\screports\ScFbaReturns.php');
                        }
                    }
                } else {
                    Log::info("filePath:app\Console\Commands\Mws\screports\ScFbaReturns.php.Download Sales-ScFbaReturns(_GET_FBA_FULFILLMENT_CUSTOMER_RETURNS_DATA_). No Done Reports Found.");
                    MWSModel::insert_mws_Activity('No Done Reports Found.', 'Download Sales-ScFbaReturns(_GET_FBA_FULFILLMENT_CUSTOMER_RETURNS_DATA_)', 'app\Console\Commands\Mws\screports\ScFbaReturns.php');
                }
            }
        } else {
            Log::info("filePath:app\Console\Commands\Mws\screports\ScFbaReturns.php.Download Sales-ScFbaReturns(_GET_FBA_FULFILLMENT_CUSTOMER_RETURNS_DATA_). Merchants Not Found.");
            MWSModel::insert_mws_Activity('Merchants Not Found.', 'Download Sales-ScFbaReturns(_GET_FBA_FULFILLMENT_CUSTOMER_RETURNS_DATA_)', 'app\Console\Commands\Mws\screports\ScFbaReturns.php');
        }
        Log::info("filePath:app\Console\Commands\Mws\screports\ScFbaReturns.php.Download Sales-ScFbaReturns(_GET_FBA_FULFILLMENT_CUSTOMER_RETURNS_DATA_). End Cron.");
        MWSModel::insert_mws_Activity('End Cron.', 'Download Sales-ScFbaReturns(_GET_FBA_FULFILLMENT_CUSTOMER_RETURNS_DATA_)', 'app\Console\Commands\Mws\screports\ScFbaReturns.php');
    }
}
