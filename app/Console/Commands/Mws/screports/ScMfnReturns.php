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

class ScMfnReturns extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'ScMfnReturns:cron';

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
        Log::info("filePath:app\Console\Commands\Mws\screports\ScMfnReturns.php.Download Sales-ScMfnReturns(_GET_FLAT_FILE_RETURNS_DATA_BY_RETURN_DATE_). Start Cron.");
        MWSModel::insert_mws_Activity('Start Cron.', 'Download Sales-ScMfnReturns(_GET_FLAT_FILE_RETURNS_DATA_BY_RETURN_DATE_)', 'app\Console\Commands\Mws\screports\ScMfnReturns.php');
        $APIParametr = new MWSModel();
        $api_data = $APIParametr->get_merchants();
        if ($api_data) {
            Log::info("filePath:app\Console\Commands\Mws\screports\ScMfnReturns.php.Download Sales-ScMfnReturns(_GET_FLAT_FILE_RETURNS_DATA_BY_RETURN_DATE_). Merchants Found.");
            MWSModel::insert_mws_Activity('Merchants Found.', 'Download Sales-ScMfnReturns(_GET_FLAT_FILE_RETURNS_DATA_BY_RETURN_DATE_)', 'app\Console\Commands\Mws\screports\ScMfnReturns.php');
            foreach ($api_data as $api_parameter) {
                $mws_config_id = trim($api_parameter->mws_config_id);
                Config::set('amazon-mws.store.store1.merchantId', trim($api_parameter->seller_id));
                //Config::set('amazon-mws.store.store1.marketplaceId', trim($api_parameter->marketplace_id));
                Config::set('amazon-mws.store.store1.marketplaceId', 'ATVPDKIKX0DER');
                Config::set('amazon-mws.store.store1.keyId', trim($api_parameter->mws_access_key_id));
                Config::set('amazon-mws.store.store1.secretKey', trim($api_parameter->mws_secret_key));
                Config::set('amazon-mws.store.store1.authToken', trim($api_parameter->mws_authtoken));

                $getMwsDoneReportsArray = MWSModel::get_mws_done_reports('_GET_FLAT_FILE_RETURNS_DATA_BY_RETURN_DATE_', $mws_config_id, 'Sales');
                if ($getMwsDoneReportsArray) {
                    Log::info("filePath:app\Console\Commands\Mws\screports\ScMfnReturns.php.Download Sales-ScMfnReturns(_GET_FLAT_FILE_RETURNS_DATA_BY_RETURN_DATE_). Merchants Found.");
                    MWSModel::insert_mws_Activity('Merchants Found.', 'Download Sales-ScMfnReturns(_GET_FLAT_FILE_RETURNS_DATA_BY_RETURN_DATE_)', 'app\Console\Commands\Mws\screports\ScMfnReturns.php');
                    foreach ($getMwsDoneReportsArray as $index) {

                        $account_id=$index->fkAccountId;
                        $sc_batch_id=$index->fkBatchId;
                        $fk_request_id = trim($index->id);
                        $report_id = trim($index->GeneratedReportId);
                        $ReportRequestId = trim($index->ReportRequestId);
                        $reportRequestDate = $index->reportRequestDate;
                        Log::info("filePath:app\Console\Commands\Mws\screports\ScMfnReturns.php.Download Sales-ScMfnReturns(_GET_FLAT_FILE_RETURNS_DATA_BY_RETURN_DATE_). Api Call Starts.");
                        MWSModel::insert_mws_Activity('Api Call Starts.', 'Download Sales-ScMfnReturns(_GET_FLAT_FILE_RETURNS_DATA_BY_RETURN_DATE_)', 'app\Console\Commands\Mws\screports\ScMfnReturns.php');
                        //$amz = new AmazonReport("store1"); //store name matches the array key in the config file
                        $amz = new AmazonReport("store1");
                        $amz->setReportId($report_id);
                        $amz->fetchReport();
                        $path = '';
                        $report_data = $amz->saveReport($path);

                        if (!empty($report_data)) {
                            Log::info("filePath:app\Console\Commands\Mws\screports\ScMfnReturns.php.Download Sales-ScMfnReturns(_GET_FLAT_FILE_RETURNS_DATA_BY_RETURN_DATE_). Data Found In Api.");
                            MWSModel::insert_mws_Activity('Data Found In Api.', 'Download Sales-ScMfnReturns(_GET_FLAT_FILE_RETURNS_DATA_BY_RETURN_DATE_)', 'app\Console\Commands\Mws\screports\ScMfnReturns.php');
                            $insert_data_array = array();
                            foreach ($report_data as $insert_record) {

                                $data['fkAccountId'] = $account_id;
                                $data['fkBatchId'] = $sc_batch_id;
                                $data['fkRequestId'] = $fk_request_id;
                                $data['reportId'] = $report_id;
                                $data['reportRequestId'] = $ReportRequestId;
                                $data['reportRequestDate'] = $reportRequestDate;
                                $data['orderId'] = isset($insert_record['OrderID']) && trim($insert_record['OrderID']) != '' ? $insert_record['OrderID'] : 'NA';
                                $data['orderDate'] = isset($insert_record['Orderdate']) && trim($insert_record['Orderdate']) != '' ? $insert_record['Orderdate'] : '0000-00-00 00:00:00';
                                $data['returnRequestDate'] = isset($insert_record['Returnrequestdate']) && trim($insert_record['Returnrequestdate']) != '' ? $insert_record['Returnrequestdate'] : '0000-00-00 00:00:00';
                                $data['returnRequestStatus'] =  isset($insert_record['Returnrequeststatus']) && trim($insert_record['Returnrequeststatus']) != '' ? $insert_record['Returnrequeststatus'] : 'NA';
                                $data['amazonRmaId'] = isset($insert_record['AmazonRMAID']) && trim($insert_record['AmazonRMAID']) != '' ? $insert_record['AmazonRMAID'] : 'NA';
                                $data['merchantRmaId'] = isset($insert_record['MerchantRMAID']) && trim($insert_record['MerchantRMAID']) != '' ? $insert_record['MerchantRMAID'] : 'NA';
                                $data['labelType'] = isset($insert_record['Labeltype']) && trim($insert_record['Labeltype']) != '' ? $insert_record['Labeltype'] : 'NA';
                                $data['labelCost'] = isset($insert_record['Labelcost']) && trim($insert_record['Labelcost']) != '' ? $insert_record['Labelcost'] : '0.00';
                                $data['currencyCode'] = isset($insert_record['Currencycode']) && trim($insert_record['Currencycode']) != '' ? $insert_record['Currencycode'] : 'NA';
                                $data['returnCarrier'] = isset($insert_record['Returncarrier']) && trim($insert_record['Returncarrier']) != '' ? $insert_record['Returncarrier'] : 'NA';
                                $data['trackingId'] = isset($insert_record['TrackingID']) && trim($insert_record['TrackingID']) != '' ? $insert_record['TrackingID'] : 'NA';
                                $data['labelToBePaidBy'] = isset($insert_record['Labeltobepaidby']) && trim($insert_record['Labeltobepaidby']) != '' ? $insert_record['Labeltobepaidby']: 'NA';
                                $data['aToZClaim'] = isset($insert_record['A-to-zclaim']) && trim($insert_record['A-to-zclaim']) != '' ? $insert_record['A-to-zclaim']: 'NA';
                                $data['isPrime'] = isset($insert_record['Isprime']) && trim($insert_record['Isprime']) != '' ? $insert_record['Isprime']: 'NA';
                                $data['asin'] = isset($insert_record['ASIN']) && trim($insert_record['ASIN']) != '' ? $insert_record['ASIN']: 'NA';
                                $data['merchantSku'] = isset($insert_record['MerchantSKU']) && trim($insert_record['MerchantSKU']) != '' ? $insert_record['MerchantSKU']: 'NA';
                                $item_name = isset($insert_record['ItemName']) && trim($insert_record['ItemName']) != '' ? scConvertToUtf8Strings($insert_record['ItemName']): 'NA';
                                $data['itemName'] =isset($item_name) && trim($item_name) != '' ? $item_name: 'NA';
                                $data['returnQuantity'] = isset($insert_record['Returnquantity']) && trim($insert_record['Returnquantity']) != '' ? $insert_record['Returnquantity']: '0';
                                //shippingPrice  (remove column in migration)
                                $data['shippingPrice'] ='NA';
                                $data['returnReason'] = isset($insert_record['Returnreason']) && trim($insert_record['Returnreason']) != '' ? $insert_record['Returnreason']: 'NA';
                                $data['inPolicy'] = isset($insert_record['Inpolicy']) && trim($insert_record['Inpolicy']) != '' ? $insert_record['Inpolicy']: 'NA';
                                $data['returnType'] = isset($insert_record['Returntype']) && trim($insert_record['Returntype']) != '' ? $insert_record['Returntype']: 'NA';
                                $data['resolution'] = isset($insert_record['Resolution']) && trim($insert_record['Resolution']) != '' ? $insert_record['Resolution']: 'NA';
                                $data['invoiceNumber'] = isset($insert_record['Invoicenumber']) && trim($insert_record['Invoicenumber']) != '' ? $insert_record['Invoicenumber']: 'NA';
                                $data['returnDeliveryDate'] = isset($insert_record['Returndeliverydate']) && trim($insert_record['Returndeliverydate']) != '' ? $insert_record['Returndeliverydate']: '0000-00-00 00:00:00';
                                $data['orderAmount'] = isset($insert_record['OrderAmount']) && trim($insert_record['OrderAmount']) != '' ? $insert_record['OrderAmount']: '0.00';
                                $data['orderQuantity'] = isset($insert_record['Orderquantity']) && trim($insert_record['Orderquantity']) != '' ? $insert_record['Orderquantity']: '0';
                                $data['safeTActionReason'] = isset($insert_record['SafeTactionreason']) && trim($insert_record['SafeTactionreason']) != '' ? $insert_record['SafeTactionreason']: 'NA';
                                $data['safeTClaimId'] = isset($insert_record['SafeTclaimid']) && trim($insert_record['SafeTclaimid']) != '' ? $insert_record['SafeTclaimid']: 'NA';
                                $data['safeTClaimState'] = isset($insert_record['SafeTclaimstate']) && trim($insert_record['SafeTclaimstate']) != '' ? $insert_record['SafeTclaimstate']: 'NA';
                                $data['safeTClaimCreationTime'] = isset($insert_record['SafeTclaimcreationtime']) && trim($insert_record['SafeTclaimcreationtime']) != '' ? $insert_record['SafeTclaimcreationtime']: '0000-00-00 00:00:00';
                                $data['safeTClaimReimbursementAmount'] = isset($insert_record['SafeTclaimreimbursementamount']) && trim($insert_record['SafeTclaimreimbursementamount']) != '' ? $insert_record['SafeTclaimreimbursementamount']: '0.00';
                                $data['refundedAmount'] = isset($insert_record['RefundedAmount']) && trim($insert_record['RefundedAmount']) != '' ? $insert_record['RefundedAmount']: '0.00';
                                $data['createdAt'] = date('Y-m-d H:i:s');
                                //array_push($insert_data_array, $data);
                                //MWSModel::insert_mws_ScMfnReturnsReport($data);
                                MWSModel::insert_mws_ScMfnReturnsReport($data);
                            }
                            /*if (!empty($insert_data_array)) {
                                MWSModel::insert_mws_ScMfnReturnsReport($data);
                            }*/
                            $acknowledgement = MWSModel::update_mws_report_acknowledgement($fk_request_id, 'true');
                            if ($acknowledgement) {
                                Log::info("filePath:app\Console\Commands\Mws\screports\ScMfnReturns.php.Download Sales-ScMfnReturns(_GET_FLAT_FILE_RETURNS_DATA_BY_RETURN_DATE_). Report Acknowledge In Database.");
                                MWSModel::insert_mws_Activity('Report Acknowledge In Database.', 'Download Sales-ScMfnReturns(_GET_FLAT_FILE_RETURNS_DATA_BY_RETURN_DATE_)', 'app\Console\Commands\Mws\screports\ScMfnReturns.php');

                            } else {
                                Log::info("filePath:app\Console\Commands\Mws\screports\ScMfnReturns.php.Download Sales-ScMfnReturns(_GET_FLAT_FILE_RETURNS_DATA_BY_RETURN_DATE_). Report Not Acknowledge In Database.");
                                MWSModel::insert_mws_Activity('Report Not Acknowledge In Database.', 'Download Sales-ScMfnReturns(_GET_FLAT_FILE_RETURNS_DATA_BY_RETURN_DATE_)', 'app\Console\Commands\Mws\screports\ScMfnReturns.php');

                            }
                        } else {
                            $acknowledgement = MWSModel::update_mws_report_acknowledgement($fk_request_id, 'no_data');
                            Log::info("filePath:app\Console\Commands\Mws\screports\ScMfnReturns.php.Download Sales-ScMfnReturns(_GET_FLAT_FILE_RETURNS_DATA_BY_RETURN_DATE_). Data Not Found In Api.");
                            MWSModel::insert_mws_Activity('Data Not Found In Api.', 'Download Sales-ScMfnReturns(_GET_FLAT_FILE_RETURNS_DATA_BY_RETURN_DATE_)', 'app\Console\Commands\Mws\screports\ScMfnReturns.php');
                        }
                    }
                } else {
                    Log::info("filePath:app\Console\Commands\Mws\screports\ScMfnReturns.php.Download Sales-ScMfnReturns(_GET_FLAT_FILE_RETURNS_DATA_BY_RETURN_DATE_). Merchants Not Found.");
                    MWSModel::insert_mws_Activity('Merchants Not Found.', 'Download Sales-ScMfnReturns(_GET_FLAT_FILE_RETURNS_DATA_BY_RETURN_DATE_)', 'app\Console\Commands\Mws\screports\ScMfnReturns.php');
                }

            }
        } else {
            Log::info("filePath:app\Console\Commands\Mws\screports\ScMfnReturns.php.Download Sales-ScMfnReturns(_GET_FLAT_FILE_RETURNS_DATA_BY_RETURN_DATE_). Merchants Not Found.");
            MWSModel::insert_mws_Activity('Merchants Not Found.', 'Download Sales-ScMfnReturns(_GET_FLAT_FILE_RETURNS_DATA_BY_RETURN_DATE_)', 'app\Console\Commands\Mws\screports\ScMfnReturns.php');
        }
        Log::info("filePath:app\Console\Commands\Mws\screports\ScMfnReturns.php.Download Sales-ScMfnReturns(_GET_FLAT_FILE_RETURNS_DATA_BY_RETURN_DATE_). End Cron.");
        MWSModel::insert_mws_Activity('End Cron.', 'Download Sales-ScMfnReturns(_GET_FLAT_FILE_RETURNS_DATA_BY_RETURN_DATE_)', 'app\Console\Commands\Mws\screports\ScMfnReturns.php');
    }
}
