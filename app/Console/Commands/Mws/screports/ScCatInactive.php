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

class ScCatInactive extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'ScCatInactivereport:cron';

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
        Log::info("filePath:app\Console\Commands\Mws\screports\ScCatInactivereport.php.Download Catalog-ScCatInactivereport(_GET_MERCHANT_LISTINGS_INACTIVE_DATA_). Start Cron.");
        MWSModel::insert_mws_Activity('Start Cron.', 'Download Catalog-ScCatInactivereport(_GET_MERCHANT_LISTINGS_INACTIVE_DATA_)', 'app\Console\Commands\Mws\screports\ScCatInactivereport.php');
        $APIParametr = new MWSModel();
        $api_data = $APIParametr->get_merchants();
        if ($api_data) {
            Log::info("filePath:app\Console\Commands\Mws\screports\ScCatInactivereport.php.Download Catalog-ScCatInactivereport(_GET_MERCHANT_LISTINGS_INACTIVE_DATA_). Merchants Found.");
            MWSModel::insert_mws_Activity('Merchants Found.', 'Download Catalog-ScCatInactivereport(_GET_MERCHANT_LISTINGS_INACTIVE_DATA_)', 'app\Console\Commands\Mws\screports\ScCatInactivereport.php');
            foreach ($api_data as $api_parameter) {
                $mws_config_id = trim($api_parameter->mws_config_id);
                Config::set('amazon-mws.store.store1.merchantId', trim($api_parameter->seller_id));
                //Config::set('amazon-mws.store.store1.marketplaceId', trim($api_parameter->marketplace_id));
                Config::set('amazon-mws.store.store1.marketplaceId', 'ATVPDKIKX0DER');
                Config::set('amazon-mws.store.store1.keyId', trim($api_parameter->mws_access_key_id));
                Config::set('amazon-mws.store.store1.secretKey', trim($api_parameter->mws_secret_key));
                Config::set('amazon-mws.store.store1.authToken', trim($api_parameter->mws_authtoken));

                //$store = Config::get('amazon-mws.store');

                $getMwsDoneReportsArray = MWSModel::get_mws_done_reports('_GET_MERCHANT_LISTINGS_INACTIVE_DATA_', $mws_config_id, 'Catalog');
                if ($getMwsDoneReportsArray) {
                    Log::info("filePath:app\Console\Commands\Mws\screports\ScCatInactivereport.php.Download Catalog-ScCatInactivereport(_GET_MERCHANT_LISTINGS_INACTIVE_DATA_). Done Reports Found.");
                    MWSModel::insert_mws_Activity('Done Reports Found.', 'Download Catalog-ScCatInactivereport(_GET_MERCHANT_LISTINGS_INACTIVE_DATA_)', 'app\Console\Commands\Mws\screports\ScCatInactivereport.php');
                    foreach ($getMwsDoneReportsArray as $index) {
                        $account_id=$index->fkAccountId;
                        $sc_batch_id=$index->fkBatchId;
                        $fk_request_id = trim($index->id);
                        $report_id = trim($index->GeneratedReportId);
                        $ReportRequestId = trim($index->ReportRequestId);
                        $reportRequestDate = $index->reportRequestDate;
                        $amz = new AmazonReport("store1");
                        $amz->setReportId($report_id);
                        $amz->fetchReport();
                        Log::info("filePath:app\Console\Commands\Mws\screports\ScCatInactivereport.php.Download Catalog-ScCatInactivereport(_GET_MERCHANT_LISTINGS_INACTIVE_DATA_). Api Call Starts.(Report Id:'.$report_id.')");
                        MWSModel::insert_mws_Activity('Api Call Starts.', 'Download Catalog-ScCatInactivereport(_GET_MERCHANT_LISTINGS_INACTIVE_DATA_)(Report Id:'.$report_id.')', 'app\Console\Commands\Mws\screports\ScCatInactivereport.php');
                        $path = '';
                        $report_data = $amz->saveReport($path);
                        if (!empty($report_data)) {
                            Log::info("filePath:app\Console\Commands\Mws\screports\ScCatInactivereport.php.Download Catalog-ScCatInactivereport(_GET_MERCHANT_LISTINGS_INACTIVE_DATA_). Data Found In Api.(Report Id:'.$report_id.')");
                            MWSModel::insert_mws_Activity('Data Found In Api.', 'Download Catalog-ScCatInactivereport(_GET_MERCHANT_LISTINGS_INACTIVE_DATA_)(Report Id:'.$report_id.')', 'app\Console\Commands\Mws\screports\ScCatInactivereport.php');
                            $insert_data_array = array();
                            foreach ($report_data as $insert_record) {
                                $data['fkAccountId'] = $account_id;
                                $data['fkBatchId'] = $sc_batch_id;
                                $data['fkRequestId'] = $fk_request_id;
                                $data['reportId'] = $report_id;
                                $data['reportRequestId'] = $ReportRequestId;
                                $data['reportRequestDate'] = $reportRequestDate;
                                // $data['ReportRequestId']=$mws_done_reportreq_id;
                                /* $item_name= scPercentageToNull(ScDashToNull(ScRemoveLeftParantesis(ScRemoveRightParantesis(ScRemQuestionMark(ScRemoveDollarSign(scRemoveUnderscoreAndSlash(scRemoveComma(scRemoveSlashnAndr(scConvertToUtf8Strings(strip_tags($insert_record['item-name'])))))))))));*/
                                $item_name = scConvertToUtf8Strings($insert_record['item-name']);
                                $data['itemName'] = (string)isset($insert_record['item-name']) && trim($insert_record['item-name']) != '' ? $item_name : 'NA';
                                $item_description = scConvertToUtf8Strings($insert_record['item-description']);
                                $data['itemDescription'] = (string)isset($insert_record['item-description']) && trim($insert_record['item-description']) != '' ? $item_description : 'NA';
                                $data['listingId'] = (string)isset($insert_record['listing-id']) && trim($insert_record['listing-id']) != '' ? $insert_record['listing-id'] : 'NA';
                                $data['sellerSku'] = (string)isset($insert_record['seller-sku']) && trim($insert_record['seller-sku']) != '' ? $insert_record['seller-sku'] : 'NA';
                                $data['price'] = (string)isset($insert_record['price']) && trim($insert_record['price']) != '' ? $insert_record['price'] : '0.00';
                                $data['quantity'] = (string)isset($insert_record['quantity']) && trim($insert_record['quantity']) != '' ? $insert_record['quantity'] : '0';
                                $data['openDate'] = (string)isset($insert_record['open-date']) && trim($insert_record['open-date']) != '' ? $insert_record['open-date'] : '0000-00-00 00:00:00';
                                // Y-m-d H:i:s
                                $data['imageUrl'] = (string)isset($insert_record['image-url']) && trim($insert_record['image-url']) != '' ? $insert_record['image-url'] : 'NA';
                                $data['itemIsMarketplace'] = (string)isset($insert_record['item-is-marketplace']) && trim($insert_record['item-is-marketplace']) != '' ? $insert_record['item-is-marketplace'] : 'NA';
                                $data['productIdType'] = (string)isset($insert_record['product-id-type']) && trim($insert_record['product-id-type']) != '' ? $insert_record['product-id-type'] : 'NA';
                                $data['zshopShippingFee'] = (string)isset($insert_record['zshop-shipping-fee']) && trim($insert_record['zshop-shipping-fee']) != '' ? $insert_record['zshop-shipping-fee'] : '0.00';
                                $data['productIdType'] = (string)isset($insert_record['product-id-type']) && trim($insert_record['product-id-type']) != '' ? $insert_record['product-id-type'] : 'NA';
                                $data['itemNote'] = (string)isset($insert_record['item-note']) && trim($insert_record['item-note']) != '' ? $insert_record['item-note'] : 'NA';
                                $data['itemCondition'] = (string)isset($insert_record['item-condition']) && trim($insert_record['item-condition']) != '' ? $insert_record['item-condition'] : 'NA';
                                $data['zshopCategory1'] = (string)isset($insert_record['zshop-category1']) && trim($insert_record['zshop-category1']) != '' ? $insert_record['zshop-category1'] : 'NA';
                                $data['zshopBrowsePath'] = (string)isset($insert_record['zshop-browse-path']) && trim($insert_record['zshop-browse-path']) != '' ? $insert_record['zshop-browse-path'] : 'NA';
                                $data['zshopStorefrontFeature'] = (string)isset($insert_record['zshop-storefront-feature']) && trim($insert_record['zshop-browse-path']) != '' ? $insert_record['zshop-storefront-feature'] : 'NA';
                                $data['asin1'] = (string)isset($insert_record['asin1']) && trim($insert_record['asin1']) != '' ? $insert_record['asin1'] : 'NA';

                                $data['asin2'] = (string)isset($insert_record['asin2']) && trim($insert_record['asin2']) != '' ? $insert_record['asin2'] : 'NA';
                                $data['asin3'] = (string)isset($insert_record['asin3']) && trim($insert_record['asin3']) != '' ? $insert_record['asin3'] : 'NA';
                                $data['willShipInternationally'] = (string)isset($insert_record['will-ship-internationally']) && trim($insert_record['will-ship-internationally']) != '' ? $insert_record['will-ship-internationally'] : 'NA';
                                $data['expeditedShipping'] = (string)isset($insert_record['expedited-shipping']) && trim($insert_record['expedited-shipping']) != '' ? $insert_record['expedited-shipping'] : 'NA';
                                $data['zshopBoldface'] = (string)isset($insert_record['zshop-boldface']) && trim($insert_record['zshop-boldface']) != '' ? $insert_record['zshop-boldface'] : 'NA';
                                $data['productId'] = (string)isset($insert_record['product-id']) && trim($insert_record['product-id']) != '' ? $insert_record['product-id'] : 'NA';
                                $data['bidForFeaturedPlacement'] = (string)isset($insert_record['bid-for-featured-placement']) && trim($insert_record['bid-for-featured-placement']) != '' ? $insert_record['bid-for-featured-placement'] : 'NA';
                                $data['addDelete'] = (string)isset($insert_record['add-delete']) && trim($insert_record['add-delete']) != '' ? $insert_record['add-delete'] : 'NA';
                                $data['pendingQuantity'] = (string)isset($insert_record['pending-quantity']) && trim($insert_record['pending-quantity']) != '' ? $insert_record['pending-quantity'] : '0';
                                if (isset($insert_record['fulfillment-channel']) && trim($insert_record['fulfillment-channel']) != '') {
                                    $fulfillment_channel = get_fullfilment_chanell_value(trim($insert_record['fulfillment-channel']));
                                } else {
                                    $fulfillment_channel = 'NA';
                                }
                                $data['fulfillmentChannel'] = $fulfillment_channel;
                                //$data['fulfillmentChannel'] = (string)isset($insert_record['fulfillment-channel']) && trim($insert_record['fulfillment-channel'])!='' ? $insert_record['fulfillment-channel'] : 'NA';
                                $data['merchantShippingGroup'] = (string)isset($insert_record['merchant-shipping-group']) && trim($insert_record['merchant-shipping-group']) != '' ? $insert_record['merchant-shipping-group'] : 'NA';

                                //MWSModel::insert_mws_ScCatInactiveReport($data);
                                //tbl_ScCatInactiveReport
                                $data['createdAt'] = date('Y-m-d H:i:s');
                                array_push($insert_data_array, $data);
                                MWSModel::insert_mws_ScCatInactiveReport($data);
                            }
                           /* if (!empty($insert_data_array)) {
                                MWSModel::insert_mws_ScCatInactiveReport($insert_data_array);
                            }*/

                            $acknowledgement = MWSModel::update_mws_report_acknowledgement($fk_request_id, 'true');
                            if ($acknowledgement) {
                                Log::info("filePath:app\Console\Commands\Mws\screports\ScCatInactivereport.php.Download Catalog-ScCatInactivereport(_GET_MERCHANT_LISTINGS_INACTIVE_DATA_). Report Acknowledge In Database.(Report Id:'.$report_id.')");
                                MWSModel::insert_mws_Activity('Data Found In Api.', 'Download Catalog-ScCatInactivereport(_GET_MERCHANT_LISTINGS_INACTIVE_DATA_)(Report Id:'.$report_id.')', 'app\Console\Commands\Mws\screports\ScCatInactivereport.php');
                            } else {
                                Log::info("filePath:app\Console\Commands\Mws\screports\ScCatInactivereport.php.Download Catalog-ScCatInactivereport(_GET_MERCHANT_LISTINGS_INACTIVE_DATA_). Report Not Acknowledge In Database.(Report Id:'.$report_id.')");
                                MWSModel::insert_mws_Activity('Report Not Acknowledge In Database.', 'Download Catalog-ScCatInactivereport(_GET_MERCHANT_LISTINGS_INACTIVE_DATA_)(Report Id:'.$report_id.')', 'app\Console\Commands\Mws\screports\ScCatInactivereport.php');
                            }
                        } else {
                            $acknowledgement = MWSModel::update_mws_report_acknowledgement($fk_request_id, 'no_data');
                            Log::info("filePath:app\Console\Commands\Mws\screports\ScCatInactivereport.php.Download Catalog-ScCatInactivereport(_GET_MERCHANT_LISTINGS_INACTIVE_DATA_). No Data Found In Api.(Report Id:'.$report_id.')");
                            MWSModel::insert_mws_Activity('No Data Found In Api.', 'Download Catalog-ScCatInactivereport(_GET_MERCHANT_LISTINGS_INACTIVE_DATA_)(Report Id:'.$report_id.')', 'app\Console\Commands\Mws\screports\ScCatInactivereport.php');
                        }
                    }
                } else {
                    Log::info("filePath:app\Console\Commands\Mws\screports\ScCatInactivereport.php.Download Catalog-ScCatInactivereport(_GET_MERCHANT_LISTINGS_INACTIVE_DATA_). No Done Reports Found.");
                    MWSModel::insert_mws_Activity('No Done Reports Found.', 'Download Catalog-ScCatInactivereport(_GET_MERCHANT_LISTINGS_INACTIVE_DATA_)', 'app\Console\Commands\Mws\screports\ScCatInactivereport.php');
                }

            }
        } else {
            Log::info("filePath:app\Console\Commands\Mws\screports\ScCatInactivereport.php.Download Catalog-ScCatInactivereport(_GET_MERCHANT_LISTINGS_INACTIVE_DATA_). Merchants Not Found.");
            MWSModel::insert_mws_Activity('Merchants Not Found.', 'Download Catalog-ScCatInactivereport(_GET_MERCHANT_LISTINGS_INACTIVE_DATA_)', 'app\Console\Commands\Mws\screports\ScCatInactivereport.php');
        }
        Log::info("filePath:app\Console\Commands\Mws\screports\ScCatInactivereport.php.Download Catalog-ScCatInactivereport(_GET_MERCHANT_LISTINGS_INACTIVE_DATA_). End Cron.");
        MWSModel::insert_mws_Activity('End Cron.', 'Download Catalog-ScCatInactivereport(_GET_MERCHANT_LISTINGS_INACTIVE_DATA_)', 'app\Console\Commands\Mws\screports\ScCatInactivereport.php');
    }
}
