<?php

namespace App\Console\Commands\Mws\screports;

use App\Libraries\mws\AmazonReport as AmazonReport;
use Config;
use Illuminate\Console\Command;
use App\Models\MWSModel;

//use Sonnenglas\AmazonMws\AmazonReport;
//use app\Libraries\ReportHandler;
use Illuminate\Support\Facades\Log;

class CatalogScCatActivereport extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'CatalogScCatActivereport:cron';

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

        Log::info("filePath:app\Console\Commands\Mws\screports\CatalogScCatActivereport.php.Download Catalog-ScCatActivereport(_GET_MERCHANT_LISTINGS_DATA_). Start Cron.");
        MWSModel::insert_mws_Activity('Start Cron', 'Download Catalog-ScCatActivereport(_GET_MERCHANT_LISTINGS_DATA_)', 'app\Console\Commands\Mws\screports\CatalogScCatActivereport.php');

        $APIParametr = new MWSModel();
        $api_data = $APIParametr->get_merchants();
        if ($api_data) {
            Log::info("filePath:app\Console\Commands\Mws\screports\CatalogScCatActivereport.php.Download Catalog-ScCatActivereport(_GET_MERCHANT_LISTINGS_DATA_). Merchants Found.");
            MWSModel::insert_mws_Activity('Merchants Found.', 'Download Catalog-ScCatActivereport(_GET_MERCHANT_LISTINGS_DATA_)', 'app\Console\Commands\Mws\screports\CatalogScCatActivereport.php');
            foreach ($api_data as $api_parameter) {
                $mws_config_id = trim($api_parameter->mws_config_id);
                Config::set('amazon-mws.store.store1.merchantId', trim($api_parameter->seller_id));
                //Config::set('amazon-mws.store.store1.marketplaceId', trim($api_parameter->marketplace_id));
                Config::set('amazon-mws.store.store1.marketplaceId', 'ATVPDKIKX0DER');
                Config::set('amazon-mws.store.store1.keyId', trim($api_parameter->mws_access_key_id));
                Config::set('amazon-mws.store.store1.secretKey', trim($api_parameter->mws_secret_key));
                Config::set('amazon-mws.store.store1.authToken', trim($api_parameter->mws_authtoken));

                //$store = Config::get('amazon-mws.store');
                $getMwsDoneReportsArray = MWSModel::get_mws_done_reports('_GET_MERCHANT_LISTINGS_DATA_', $mws_config_id, 'Catalog');
                if ($getMwsDoneReportsArray) {
                    Log::info("filePath:app\Console\Commands\Mws\screports\CatalogScCatActivereport.php.Download Catalog-ScCatActivereport(_GET_MERCHANT_LISTINGS_DATA_). Done Reports Found.");
                    MWSModel::insert_mws_Activity('Done Reports Found.', 'Download Catalog-ScCatActivereport(_GET_MERCHANT_LISTINGS_DATA_)', 'app\Console\Commands\Mws\screports\CatalogScCatActivereport.php');

                    foreach ($getMwsDoneReportsArray as $index) {
                        $account_id=$index->fkAccountId;
                        //$sc_batch_id=sc_generate_batch_id($account_id);
                        $sc_batch_id=$index->fkBatchId;
                        $ReportId=$index->GeneratedReportId;
                        $fk_request_id = trim($index->id);
                        $report_id = trim($index->GeneratedReportId);
                        $ReportRequestId = trim($index->ReportRequestId);
                        $reportRequestDate = $index->reportRequestDate;
                        //echo '<br>';

                        //$amz = new AmazonReport("store1"); //store name matches the array key in the config file
                        $amz = new AmazonReport("store1");
                        $amz->setReportId($report_id);
                        Log::info("filePath:app\Console\Commands\Mws\screports\CatalogScCatActivereport.php.Download Catalog-ScCatActivereport(_GET_MERCHANT_LISTINGS_DATA_). Api Call Starts.(Report Id:'.$report_id.')");
                        MWSModel::insert_mws_Activity('Api Call Starts.', 'Download Catalog-ScCatActivereport(_GET_MERCHANT_LISTINGS_DATA_)(Report Id:'.$report_id.')', 'app\Console\Commands\Mws\screports\CatalogScCatActivereport.php');
                        $amz->fetchReport();
                        $path = '';
                        $report_data = $amz->saveReport($path);
                        if (!empty($report_data)) {
                            Log::info("filePath:app\Console\Commands\Mws\screports\CatalogScCatActivereport.php.Download Catalog-ScCatActivereport(_GET_MERCHANT_LISTINGS_DATA_). Data Found In Api.(Report Id:'.$report_id.')");
                            MWSModel::insert_mws_Activity('Data Found In Api.', 'Download Catalog-ScCatActivereport(_GET_MERCHANT_LISTINGS_DATA_)(Report Id:'.$report_id.')', 'app\Console\Commands\Mws\screports\CatalogScCatActivereport.php');

                            $insert_data_array = array();

                            foreach ($report_data as $insert_record) {

                                $data['fkAccountId'] = $account_id;
                                $data['fkBatchId'] = $sc_batch_id;
                                $data['fkRequestId'] = $fk_request_id;
                                $data['reportId'] = $report_id;
                                $data['reportRequestId'] = $ReportRequestId;
                                $data['reportRequestDate'] = $reportRequestDate;

                                /*$item_name= scPercentageToNull(ScDashToNull(ScRemoveLeftParantesis(ScRemoveRightParantesis(ScRemQuestionMark(ScRemoveDollarSign(scRemoveUnderscoreAndSlash(scRemoveComma(scRemoveSlashnAndr(scConvertToUtf8Strings(strip_tags($insert_record['item-name'])))))))))));*/
                                $item_name = scConvertToUtf8Strings($insert_record['item-name']);
                                $data['itemName'] = isset($insert_record['item-name']) && trim($insert_record['item-name']) != '' ? $item_name : 'NA';
                                $item_description = scConvertToUtf8Strings($insert_record['item-description']);
                                $data['itemDescription'] = isset($insert_record['item-description']) && trim($insert_record['item-description']) != '' ? $item_description : 'NA';
                                $data['listingId'] = isset($insert_record['listing-id']) && trim($insert_record['listing-id']) != '' ? $insert_record['listing-id'] : 'NA';
                                $data['sellerSku'] = isset($insert_record['seller-sku']) && trim($insert_record['seller-sku']) != '' ? $insert_record['seller-sku'] : 'NA';
                                $data['price'] = isset($insert_record['price']) && trim($insert_record['price']) != '' ? $insert_record['price'] : '0.00';
                                $data['quantity'] = isset($insert_record['quantity']) && trim($insert_record['quantity']) != '' ? $insert_record['quantity'] : '0';
                                $data['openDate'] = isset($insert_record['open-date']) && trim($insert_record['open-date']) != '' ? $insert_record['open-date'] : '0000-00-00 00:00:00';
                                // Y-m-d H:i:s
                                $data['imageUrl'] = isset($insert_record['image-url']) && trim($insert_record['image-url']) != '' ? $insert_record['image-url'] : 'NA';
                                $data['itemIsMarketplace'] = isset($insert_record['item-is-marketplace']) && trim($insert_record['item-is-marketplace']) != '' ? $insert_record['item-is-marketplace'] : 'NA';
                                $data['productIdType'] = isset($insert_record['product-id-type']) && trim($insert_record['product-id-type']) != '' ? $insert_record['product-id-type'] : 'NA';
                                $data['zshopShippingFee'] = isset($insert_record['zshop-shipping-fee']) && trim($insert_record['zshop-shipping-fee']) != '' ? $insert_record['zshop-shipping-fee'] : '0.00';
                                $data['productIdType'] = isset($insert_record['product-id-type']) && trim($insert_record['product-id-type']) != '' ? $insert_record['product-id-type'] : 'NA';
                                $data['itemNote'] = isset($insert_record['item-note']) && trim($insert_record['item-note']) != '' ? $insert_record['item-note'] : 'NA';
                                $data['itemCondition'] = isset($insert_record['item-condition']) && trim($insert_record['item-condition']) != '' ? $insert_record['item-condition'] : 'NA';
                                $data['zshopCategory1'] = isset($insert_record['zshop-category1']) && trim($insert_record['zshop-category1']) != '' ? $insert_record['zshop-category1'] : 'NA';
                                $data['zshopBrowsePath'] = isset($insert_record['zshop-browse-path']) && trim($insert_record['zshop-browse-path']) != '' ? $insert_record['zshop-browse-path'] : 'NA';
                                $data['zshopStorefrontFeature'] = isset($insert_record['zshop-storefront-feature']) && trim($insert_record['zshop-browse-path']) != '' ? $insert_record['zshop-storefront-feature'] : 'NA';
                                $data['asin1'] = isset($insert_record['asin1']) && trim($insert_record['asin1']) != '' ? $insert_record['asin1'] : 'NA';

                                $data['asin2'] = isset($insert_record['asin2']) && trim($insert_record['asin2']) != '' ? $insert_record['asin2'] : 'NA';
                                $data['asin3'] = isset($insert_record['asin3']) && trim($insert_record['asin3']) != '' ? $insert_record['asin3'] : 'NA';
                                $data['willShipInternationally'] = isset($insert_record['will-ship-internationally']) && trim($insert_record['will-ship-internationally']) != '' ? $insert_record['will-ship-internationally'] : 'NA';
                                $data['expeditedShipping'] = isset($insert_record['expedited-shipping']) && trim($insert_record['expedited-shipping']) != '' ? $insert_record['expedited-shipping'] : 'NA';
                                $data['zshopBoldface'] = isset($insert_record['zshop-boldface']) && trim($insert_record['zshop-boldface']) != '' ? $insert_record['zshop-boldface'] : 'NA';
                                $data['productId'] = isset($insert_record['product-id']) && trim($insert_record['product-id']) != '' ? $insert_record['product-id'] : 'NA';
                                $data['bidForFeaturedPlacement'] = isset($insert_record['bid-for-featured-placement']) && trim($insert_record['bid-for-featured-placement']) != '' ? $insert_record['bid-for-featured-placement'] : 'NA';
                                $data['addDelete'] = isset($insert_record['add-delete']) && trim($insert_record['add-delete']) != '' ? $insert_record['add-delete'] : 'NA';
                                $data['pendingQuantity'] = isset($insert_record['pending-quantity']) && trim($insert_record['pending-quantity']) != '' ? $insert_record['pending-quantity'] : '0';
                                // $data['fulfillmentChannel'] = isset($insert_record['fulfillment-channel']) && trim($insert_record['fulfillment-channel'])!='' ? $insert_record['fulfillment-channel'] : 'NA';
                                if (isset($insert_record['fulfillment-channel']) && trim($insert_record['fulfillment-channel']) != '') {
                                    $fulfillment_channel = get_fullfilment_chanell_value(trim($insert_record['fulfillment-channel']));

                                } else {
                                    $fulfillment_channel = 'NA';
                                }
                                $data['fulfillmentChannel'] = $fulfillment_channel;
                                $data['merchantShippingGroup'] = isset($insert_record['merchant-shipping-group']) && trim($insert_record['merchant-shipping-group']) != '' ? $insert_record['merchant-shipping-group'] : 'NA';
                                $data['createdAt'] = date('Y-m-d H:i:s');
                                // $insert_data_array[] = $data;
                                //array_push($insert_data_array, $data);
                                MWSModel::insert_mws_CatalogScCatActiveReport($data);

                            }//foreach end

                            /*if (!empty($insert_data_array)) {
                                MWSModel::insert_mws_CatalogScCatActiveReport($insert_data_array);
                            }*/

                            Log::info("filePath:app\Console\Commands\Mws\screports\CatalogScCatActivereport.php.Download Catalog-ScCatActivereport(_GET_MERCHANT_LISTINGS_DATA_). Data Insertion Ends.(Report Id:'.$report_id.')");
                            MWSModel::insert_mws_Activity('Data Insertion Ends.', 'Download Catalog-ScCatActivereport(_GET_MERCHANT_LISTINGS_DATA_)(Report Id:'.$report_id.')', 'app\Console\Commands\Mws\screports\CatalogScCatActivereport.php');
                            $acknowledgement = MWSModel::update_mws_report_acknowledgement($fk_request_id, 'true');
                            if ($acknowledgement) {
                                Log::info("filePath:app\Console\Commands\Mws\screports\CatalogScCatActivereport.php.Download Catalog-ScCatActivereport(_GET_MERCHANT_LISTINGS_DATA_)(Report Id:'.$report_id.'). Report Acknowledge In Database.");
                                MWSModel::insert_mws_Activity('Report Acknowledge In Database.', 'Download Catalog-ScCatActivereport(_GET_MERCHANT_LISTINGS_DATA_)(Report Id:'.$report_id.')', 'app\Console\Commands\Mws\screports\CatalogScCatActivereport.php');
                            } else {
                                Log::info("filePath:app\Console\Commands\Mws\screports\CatalogScCatActivereport.php.Download Catalog-ScCatActivereport(_GET_MERCHANT_LISTINGS_DATA_). Report Not Acknowledge In Database.(Report Id:'.$report_id.')");
                                MWSModel::insert_mws_Activity('Report Not Acknowledge In Database.', 'Download Catalog-ScCatActivereport(_GET_MERCHANT_LISTINGS_DATA_)(Report Id:'.$report_id.')', 'app\Console\Commands\Mws\screports\CatalogScCatActivereport.php');
                            }
                        } else {
                            $acknowledgement = MWSModel::update_mws_report_acknowledgement($fk_request_id, 'no_data');
                            Log::info("filePath:app\Console\Commands\Mws\screports\CatalogScCatActivereport.php.Download Catalog-ScCatActivereport(_GET_MERCHANT_LISTINGS_DATA_). No Data Found In Api.(Report Id:'.$report_id.')");
                            MWSModel::insert_mws_Activity('No Data Found In Api.', 'Download Catalog-ScCatActivereport(_GET_MERCHANT_LISTINGS_DATA_)(Report Id:'.$report_id.')', 'app\Console\Commands\Mws\screports\CatalogScCatActivereport.php');
                        }
                    }
                    // exit;
                } else {
                    Log::info("filePath:app\Console\Commands\Mws\screports\CatalogScCatActivereport.php.Download Catalog-ScCatActivereport(_GET_MERCHANT_LISTINGS_DATA_). No Done Reports Found.");
                    MWSModel::insert_mws_Activity('No Done Reports Found.', 'Download Catalog-ScCatActivereport(_GET_MERCHANT_LISTINGS_DATA_)', 'app\Console\Commands\Mws\screports\CatalogScCatActivereport.php');
                }
            }
        } else {
            Log::info("filePath:app\Console\Commands\Mws\screports\CatalogScCatActivereport.php.Download Catalog-ScCatActivereport(_GET_MERCHANT_LISTINGS_DATA_). Merchants Not Found.");
            MWSModel::insert_mws_Activity('Merchants Not Found.', 'Download Catalog-ScCatActivereport(_GET_MERCHANT_LISTINGS_DATA_)', 'app\Console\Commands\Mws\screports\CatalogScCatActivereport.php');
        }
        Log::info("filePath:app\Console\Commands\Mws\screports\CatalogScCatActivereport.php.Download Catalog-ScCatActivereport(_GET_MERCHANT_LISTINGS_DATA_). End Cron.");
        MWSModel::insert_mws_Activity('End Cron.', 'Download Catalog-ScCatActivereport(_GET_MERCHANT_LISTINGS_DATA_)', 'app\Console\Commands\Mws\screports\CatalogScCatActivereport.php');
    }
}
