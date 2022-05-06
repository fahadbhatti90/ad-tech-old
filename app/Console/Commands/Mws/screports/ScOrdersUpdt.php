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

class ScOrdersUpdt extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'ScOrdersUpdt:cron';

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
        scSetMemoryLimitAndExeTime();
        Log::info("filePath:app\Console\Commands\Mws\screports\ScOrdersUpdt.php.Download Sales-ScOrdersUpdt(_GET_FLAT_FILE_ALL_ORDERS_DATA_BY_LAST_UPDATE_). Start Cron.");
        MWSModel::insert_mws_Activity('Start Cron.', 'Download Sales-ScOrdersUpdt(_GET_FLAT_FILE_ALL_ORDERS_DATA_BY_LAST_UPDATE_)', 'app\Console\Commands\Mws\screports\ScOrdersUpdt.php');
        $APIParametr = new MWSModel();
        $api_data = $APIParametr->get_merchants();
        if ($api_data) {
            Log::info("filePath:app\Console\Commands\Mws\screports\ScOrdersUpdt.php.Download Sales-ScOrdersUpdt(_GET_FLAT_FILE_ALL_ORDERS_DATA_BY_LAST_UPDATE_). Merchants Found.");
            MWSModel::insert_mws_Activity('Merchants Found.', 'Download Sales-ScOrdersUpdt(_GET_FLAT_FILE_ALL_ORDERS_DATA_BY_LAST_UPDATE_)', 'app\Console\Commands\Mws\screports\ScOrdersUpdt.php');
            foreach ($api_data as $api_parameter) {
                $mws_config_id = trim($api_parameter->mws_config_id);
                Config::set('amazon-mws.store.store1.merchantId', trim($api_parameter->seller_id));
                //Config::set('amazon-mws.store.store1.marketplaceId', trim($api_parameter->marketplace_id));
                Config::set('amazon-mws.store.store1.marketplaceId', 'ATVPDKIKX0DER');
                Config::set('amazon-mws.store.store1.keyId', trim($api_parameter->mws_access_key_id));
                Config::set('amazon-mws.store.store1.secretKey', trim($api_parameter->mws_secret_key));
                Config::set('amazon-mws.store.store1.authToken', trim($api_parameter->mws_authtoken));

                //$store = Config::get('amazon-mws.store');

                $getMwsDoneReportsArray = MWSModel::get_mws_done_reports('_GET_FLAT_FILE_ALL_ORDERS_DATA_BY_LAST_UPDATE_', $mws_config_id, 'Sales');
                if ($getMwsDoneReportsArray) {
                    Log::info("filePath:app\Console\Commands\Mws\screports\ScOrdersUpdt.php.Download Sales-ScOrdersUpdt(_GET_FLAT_FILE_ALL_ORDERS_DATA_BY_LAST_UPDATE_). Done Reports Found.");
                    MWSModel::insert_mws_Activity('Done Reports Found.', 'Download Sales-ScOrdersUpdt(_GET_FLAT_FILE_ALL_ORDERS_DATA_BY_LAST_UPDATE_)', 'app\Console\Commands\Mws\screports\ScOrdersUpdt.php');
                    foreach ($getMwsDoneReportsArray as $index) {

                        $account_id=$index->fkAccountId;
                        $sc_batch_id=$index->fkBatchId;
                        $fk_request_id = trim($index->id);
                        $report_id = trim($index->GeneratedReportId);
                        $ReportRequestId = trim($index->ReportRequestId);
                        $reportRequestDate = $index->reportRequestDate;
                        //$amz = new AmazonReport("store1"); //store name matches the array key in the config file
                        $amz = new AmazonReport("store1");
                        $amz->setReportId($report_id);
                        $amz->fetchReport();
                        Log::info("filePath:app\Console\Commands\Mws\screports\ScOrdersUpdt.php.Download Sales-ScOrdersUpdt(_GET_FLAT_FILE_ALL_ORDERS_DATA_BY_LAST_UPDATE_). Api Call Starts.(Report Id:'.$report_id.')");
                        MWSModel::insert_mws_Activity('Api Call Starts.', 'Download Sales-ScOrdersUpdt(_GET_FLAT_FILE_ALL_ORDERS_DATA_BY_LAST_UPDATE_)(Report Id:'.$report_id.')', 'app\Console\Commands\Mws\screports\ScOrdersUpdt.php');
                        $path = '';
                        $report_data = $amz->saveReport($path);
                        if (!empty($report_data)) {
                            Log::info("filePath:app\Console\Commands\Mws\screports\ScOrdersUpdt.php.Download Sales-ScOrdersUpdt(_GET_FLAT_FILE_ALL_ORDERS_DATA_BY_LAST_UPDATE_). Data Found In Api.(Report Id:'.$report_id.')");
                            MWSModel::insert_mws_Activity('Data Found In Api.', 'Download Sales-ScOrdersUpdt(_GET_FLAT_FILE_ALL_ORDERS_DATA_BY_LAST_UPDATE_)(Report Id:'.$report_id.')', 'app\Console\Commands\Mws\screports\ScOrdersUpdt.php');
                            $insert_data_array = array();
                            foreach ($report_data as $insert_record) {

                                $data['fkAccountId'] = $account_id;
                                $data['fkBatchId'] = $sc_batch_id;
                                $data['fkRequestId'] = $fk_request_id;
                                $data['reportId'] = $report_id;
                                $data['reportRequestId'] = $ReportRequestId;
                                $data['reportRequestDate'] = $reportRequestDate;
                                $data['amazonOrderId'] = (string)isset($insert_record['amazon-order-id']) && trim($insert_record['amazon-order-id']) != '' ? $insert_record['amazon-order-id'] : 'NA';
                                $data['merchantOrderId'] = (string)isset($insert_record['merchant-order-id']) && trim($insert_record['merchant-order-id']) != '' ? $insert_record['merchant-order-id'] : 'NA';
                                $data['purchaseDate'] = (string)isset($insert_record['purchase-date']) && trim($insert_record['purchase-date']) != '' ? $insert_record['purchase-date'] : '0000-00-00 00:00:00';
                                $data['lastUpdatedDate'] = (string)isset($insert_record['last-updated-date']) && trim($insert_record['last-updated-date']) != '' ? $insert_record['last-updated-date'] : '0000-00-00 00:00:00';
                                $data['orderStatus'] = (string)isset($insert_record['order-status']) && trim($insert_record['order-status']) != '' ? $insert_record['order-status'] : 'NA';
                                if (isset($insert_record['fulfillment-channel']) && trim($insert_record['fulfillment-channel']) != '') {
                                    $fulfillment_channel = get_fullfilment_chanell_value(trim($insert_record['fulfillment-channel']));

                                } else {
                                    $fulfillment_channel = 'NA';
                                }
                                $data['fulfillmentChannel'] = $fulfillment_channel;
                                //$data['fulfillmentChannel']=(string)isset($insert_record['fulfillment-channel']) && trim($insert_record['fulfillment-channel'])!='' ? $insert_record['fulfillment-channel'] : 'NA';
                                $data['salesChannel'] = (string)isset($insert_record['sales-channel']) && trim($insert_record['sales-channel']) != '' ? $insert_record['sales-channel'] : 'NA';
                                $data['orderChannel'] = (string)isset($insert_record['order-channel']) && trim($insert_record['order-channel']) != '' ? $insert_record['order-channel'] : 'NA';
                                $data['url'] = (string)isset($insert_record['url']) && trim($insert_record['url']) != '' ? $insert_record['url'] : 'NA';
                                $data['shipServiceLevel'] = (string)isset($insert_record['ship-service-level']) && trim($insert_record['ship-service-level']) != '' ? $insert_record['ship-service-level'] : 'NA';
                                //$data['productName']=(string)isset($insert_record['product-name']) && !empty($insert_record['product-name']) ? $insert_record['product-name'] : 'NA';
                                /*$productName= scPercentageToNull(ScDashToNull(ScRemoveLeftParantesis(ScRemoveRightParantesis(ScRemQuestionMark(ScRemoveDollarSign(scRemoveUnderscoreAndSlash(scRemoveComma(scRemoveSlashnAndr(scConvertToUtf8Strings(strip_tags($insert_record['product-name'])))))))))));*/
                                $productName = scConvertToUtf8Strings($insert_record['product-name']);
                                $data['productName'] = (string)isset($insert_record['product-name']) && trim($insert_record['product-name']) != '' ? $productName : 'NA';
                                $data['sku'] = (string)isset($insert_record['sku']) && trim($insert_record['sku']) != '' ? $insert_record['sku'] : 'NA';
                                $data['asin'] = (string)isset($insert_record['asin']) && trim($insert_record['asin']) != '' ? $insert_record['asin'] : 'NA';
                                $data['itemStatus'] = (string)isset($insert_record['item-status']) && trim($insert_record['item-status']) != '' ? $insert_record['item-status'] : 'NA';
                                $data['quantity'] = (string)isset($insert_record['quantity']) && trim($insert_record['quantity']) != '' ? $insert_record['quantity'] : '0';
                                $data['currency'] = (string)isset($insert_record['currency']) && trim($insert_record['currency']) != '' ? $insert_record['currency'] : 'NA';
                                $data['itemPrice'] = (string)isset($insert_record['item-price']) && trim($insert_record['item-price']) != '' ? $insert_record['item-price'] : '0.00';
                                $data['itemTax'] = (string)isset($insert_record['item-tax']) && trim($insert_record['item-tax']) != '' ? $insert_record['item-tax'] : '0.00';
                                $data['shippingPrice'] = (string)isset($insert_record['shipping-price']) && trim($insert_record['shipping-price']) != '' ? $insert_record['shipping-price'] : '0.00';
                                $data['shippingTax'] = (string)isset($insert_record['shipping-tax']) && trim($insert_record['shipping-tax']) != '' ? $insert_record['shipping-tax'] : '0.00';
                                $data['giftWrapPrice'] = (string)isset($insert_record['gift-wrap-price']) && trim($insert_record['gift-wrap-price']) != '' ? $insert_record['gift-wrap-price'] : '0.00';
                                $data['giftWrapTax'] = (string)isset($insert_record['gift-wrap-tax']) && trim($insert_record['gift-wrap-tax']) != '' ? $insert_record['gift-wrap-tax'] : '0.00';
                                $data['itemPromotionDiscount'] = (string)isset($insert_record['item-promotion-discount']) && trim($insert_record['item-promotion-discount']) != '' ? $insert_record['item-promotion-discount'] : '0.00';
                                $data['shipPromotionDiscount'] = (string)isset($insert_record['ship-promotion-discount']) && trim($insert_record['ship-promotion-discount']) != '' ? $insert_record['ship-promotion-discount'] : '0.00';
                                $data['shipCity'] = (string)isset($insert_record['ship-city']) && trim($insert_record['ship-city']) != '' ? $insert_record['ship-city'] : 'NA';
                                $data['shipState'] = (string)isset($insert_record['ship-state']) && trim($insert_record['ship-state']) != '' ? $insert_record['ship-state'] : 'NA';
                                $data['shipPostalCode'] = (string)isset($insert_record['ship-postal-code']) && trim($insert_record['ship-postal-code']) != '' ? $insert_record['ship-postal-code'] : 'NA';
                                $data['shipCountry'] = (string)isset($insert_record['ship-country']) && trim($insert_record['ship-country']) != '' ? $insert_record['ship-country'] : 'NA';
                                if (isset($insert_record['promotion-ids '])) {
                                    $data['promotionIds'] = (string)isset($insert_record['promotion-ids ']) && trim($insert_record['promotion-ids ']) != '' ? $insert_record['promotion-ids '] : 'NA';
                                }
                                if (isset($insert_record['promotion-ids'])) {
                                    $data['promotionIds'] = (string)isset($insert_record['promotion-ids']) && trim($insert_record['promotion-ids']) != '' ? $insert_record['promotion-ids'] : 'NA';
                                }
                                $data['createdAt'] = date('Y-m-d H:i:s');
                                //array_push($insert_data_array, $data);
                                MWSModel::insert_mws_ScOrdersUpdtReport($data);
                            }
                           /* if (!empty($insert_data_array)) {
                                MWSModel::insert_mws_ScOrdersUpdtReport($insert_data_array);
                            }*/
                            $acknowledgement = MWSModel::update_mws_report_acknowledgement($fk_request_id, 'true');
                            if ($acknowledgement) {
                                Log::info("filePath:app\Console\Commands\Mws\screports\ScOrdersUpdt.php.Download Sales-ScOrdersUpdt(_GET_FLAT_FILE_ALL_ORDERS_DATA_BY_LAST_UPDATE_). Report Acknowledge In Database.(Report Id:'.$report_id.')");
                                MWSModel::insert_mws_Activity('Report Acknowledge In Database.', 'Download Sales-ScOrdersUpdt(_GET_FLAT_FILE_ALL_ORDERS_DATA_BY_LAST_UPDATE_)(Report Id:'.$report_id.')', 'app\Console\Commands\Mws\screports\ScOrdersUpdt.php');

                            } else {
                                Log::info("filePath:app\Console\Commands\Mws\screports\ScOrdersUpdt.php.Download Sales-ScOrdersUpdt(_GET_FLAT_FILE_ALL_ORDERS_DATA_BY_LAST_UPDATE_). Report Not Acknowledge In Database.(Report Id:'.$report_id.')");
                                MWSModel::insert_mws_Activity('Report Not Acknowledge In Database.', 'Download Sales-ScOrdersUpdt(_GET_FLAT_FILE_ALL_ORDERS_DATA_BY_LAST_UPDATE_)(Report Id:'.$report_id.')', 'app\Console\Commands\Mws\screports\ScOrdersUpdt.php');
                            }
                        } else {
                            $acknowledgement = MWSModel::update_mws_report_acknowledgement($fk_request_id, 'no_data');
                            Log::info("filePath:app\Console\Commands\Mws\screports\ScOrdersUpdt.php.Download Sales-ScOrdersUpdt(_GET_FLAT_FILE_ALL_ORDERS_DATA_BY_LAST_UPDATE_). Data Not Found In Api.(Report Id:'.$report_id.')");
                            MWSModel::insert_mws_Activity('Data Not Found In Api.', 'Download Sales-ScOrdersUpdt(_GET_FLAT_FILE_ALL_ORDERS_DATA_BY_LAST_UPDATE_)(Report Id:'.$report_id.')', 'app\Console\Commands\Mws\screports\ScOrdersUpdt.php');
                        }
                    }
                } else {
                    Log::info("filePath:app\Console\Commands\Mws\screports\ScOrdersUpdt.php.Download Sales-ScOrdersUpdt(_GET_FLAT_FILE_ALL_ORDERS_DATA_BY_LAST_UPDATE_). Done Reports Not Found.");
                    MWSModel::insert_mws_Activity('Done Reports Not Found.', 'Download Sales-ScOrdersUpdt(_GET_FLAT_FILE_ALL_ORDERS_DATA_BY_LAST_UPDATE_)', 'app\Console\Commands\Mws\screports\ScOrdersUpdt.php');
                }

            }
        } else {
            Log::info("filePath:app\Console\Commands\Mws\screports\ScOrdersUpdt.php.Download Sales-ScOrdersUpdt(_GET_FLAT_FILE_ALL_ORDERS_DATA_BY_LAST_UPDATE_). Merchants Not Found.");
            MWSModel::insert_mws_Activity('Merchants Not Found.', 'Download Sales-ScOrdersUpdt(_GET_FLAT_FILE_ALL_ORDERS_DATA_BY_LAST_UPDATE_)', 'app\Console\Commands\Mws\screports\ScOrdersUpdt.php');
        }
        Log::info("filePath:app\Console\Commands\Mws\screports\ScOrdersUpdt.php.Download Sales-ScOrdersUpdt(_GET_FLAT_FILE_ALL_ORDERS_DATA_BY_LAST_UPDATE_). End Cron.");
        MWSModel::insert_mws_Activity('End Cron', 'Download Sales-ScOrdersUpdt(_GET_FLAT_FILE_ALL_ORDERS_DATA_BY_LAST_UPDATE_)', 'app\Console\Commands\Mws\screports\ScOrdersUpdt.php');
    }
}
