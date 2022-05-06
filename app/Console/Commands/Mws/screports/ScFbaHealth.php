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

class ScFbaHealth extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'ScFbaHealth:cron';

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
        Log::info("filePath:app\Console\Commands\Mws\screports\ScFbaHealth.php.Download Inventory-ScFbaHealth(_GET_FBA_FULFILLMENT_INVENTORY_HEALTH_DATA_). Start Cron.");
        MWSModel::insert_mws_Activity('Start Cron.', 'Download Inventory-ScFbaHealth(_GET_FBA_FULFILLMENT_INVENTORY_HEALTH_DATA_)', 'app\Console\Commands\Mws\screports\ScFbaHealth.php');
        $APIParametr = new MWSModel();
        $api_data = $APIParametr->get_merchants();

        if ($api_data) {
            Log::info("filePath:app\Console\Commands\Mws\screports\ScFbaHealth.php.Download Inventory-ScFbaHealth(_GET_FBA_FULFILLMENT_INVENTORY_HEALTH_DATA_). Merchants Found.");
            MWSModel::insert_mws_Activity('Merchants Found.', 'Download Inventory-ScFbaHealth(_GET_FBA_FULFILLMENT_INVENTORY_HEALTH_DATA_)', 'app\Console\Commands\Mws\screports\ScFbaHealth.php');
            foreach ($api_data as $api_parameter) {
                $mws_config_id = trim($api_parameter->mws_config_id);
                Config::set('amazon-mws.store.store1.merchantId', trim($api_parameter->seller_id));
                //Config::set('amazon-mws.store.store1.marketplaceId', trim($api_parameter->marketplace_id));
                Config::set('amazon-mws.store.store1.marketplaceId', 'ATVPDKIKX0DER');
                Config::set('amazon-mws.store.store1.keyId', trim($api_parameter->mws_access_key_id));
                Config::set('amazon-mws.store.store1.secretKey', trim($api_parameter->mws_secret_key));
                Config::set('amazon-mws.store.store1.authToken', trim($api_parameter->mws_authtoken));

                $getMwsDoneReportsArray = MWSModel::get_mws_done_reports('_GET_FBA_FULFILLMENT_INVENTORY_HEALTH_DATA_', $mws_config_id, 'Inventory');
                if ($getMwsDoneReportsArray) {
                    Log::info("filePath:app\Console\Commands\Mws\screports\ScFbaHealth.php.Download Inventory-ScFbaHealth(_GET_FBA_FULFILLMENT_INVENTORY_HEALTH_DATA_). Done Reports Found.");
                    MWSModel::insert_mws_Activity('Done Reports Found.', 'Download Inventory-ScFbaHealth(_GET_FBA_FULFILLMENT_INVENTORY_HEALTH_DATA_)', 'app\Console\Commands\Mws\screports\ScFbaHealth.php');
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
                        Log::info("filePath:app\Console\Commands\Mws\screports\ScFbaHealth.php.Download Inventory-ScFbaHealth(_GET_FBA_FULFILLMENT_INVENTORY_HEALTH_DATA_). Api Call Starts.(Report Id:'.$report_id.')");
                        MWSModel::insert_mws_Activity('Api Call Starts.', 'Download Inventory-ScFbaHealth(_GET_FBA_FULFILLMENT_INVENTORY_HEALTH_DATA_)(Report Id:'.$report_id.')', 'app\Console\Commands\Mws\screports\ScFbaHealth.php');
                        $path = '';
                        $report_data = $amz->saveReport($path);

                        if (!empty($report_data)) {
                            Log::info("filePath:app\Console\Commands\Mws\screports\ScFbaHealth.php.Download Inventory-ScFbaHealth(_GET_FBA_FULFILLMENT_INVENTORY_HEALTH_DATA_). Data Found In Api.(Report Id:'.$report_id.')");
                            MWSModel::insert_mws_Activity('Data Found In Api.', 'Download Inventory-ScFbaHealth(_GET_FBA_FULFILLMENT_INVENTORY_HEALTH_DATA_)(Report Id:'.$report_id.')', 'app\Console\Commands\Mws\screports\ScFbaHealth.php');
                            $insert_data_array = array();
                            foreach ($report_data as $insert_record) {

                                if (isset($insert_record['asin']) && $insert_record['asin']== 'gl_toy'){
                                    continue;
                                }
                                $data['fkAccountId'] = $account_id;
                                $data['fkBatchId'] = $sc_batch_id;
                                $data['fkRequestId'] = $fk_request_id;
                                $data['reportId'] = $report_id;
                                $data['reportRequestId'] = $ReportRequestId;
                                $data['reportRequestDate'] = $reportRequestDate;
                                $data['snapshotDate'] = (string)(isset($insert_record['snapshot-date']) && trim($insert_record['snapshot-date']) != '') ? $insert_record['snapshot-date'] : '0000-00-00 00:00:00';
                                $data['sku'] = (string)(isset($insert_record['sku']) && trim($insert_record['sku']) != '') ? $insert_record['sku'] : 'NA';
                                $data['fnsku'] = (string)(isset($insert_record['fnsku']) && trim($insert_record['fnsku']) != '') ? $insert_record['fnsku'] : 'NA';
                                $data['asin'] = (string)(isset($insert_record['asin']) && trim($insert_record['asin']) != '') ? $insert_record['asin'] : 'NA';
                                $productName = scConvertToUtf8Strings($insert_record['product-name']);
                                $data['productName'] = (string)(isset($insert_record['product-name']) && trim($insert_record['product-name']) != '') ? $productName : 'NA';
                                $data['condition'] = (string)isset($insert_record['condition']) && trim($insert_record['condition']) != '' ? $insert_record['condition'] : 'NA';
                                //$data['salesRank']= isset($insert_record['sales-rank']) ? $insert_record['sales-rank'] : 'NA';
                                $data['salesRank'] = (string)(isset($insert_record['sales-rank']) && trim($insert_record['sales-rank']) != '') ? $insert_record['sales-rank'] : 'NA';
                                $data['productGroup'] = (string)(isset($insert_record['product-group']) && trim($insert_record['product-group']) != '') ? $insert_record['product-group'] : 'NA';
                                $data['totalQuantity'] = (string)(isset($insert_record['total-quantity']) && trim($insert_record['total-quantity']) != '') ? $insert_record['total-quantity'] : '0';
                                $data['sellableQuantity'] = (string)isset($insert_record['sellable-quantity']) && trim($insert_record['sellable-quantity']) != '' ? $insert_record['sellable-quantity'] : '0';
                                $data['unsellableQuantity'] = (string)(isset($insert_record['unsellable-quantity']) && trim($insert_record['unsellable-quantity']) != '') ? $insert_record['unsellable-quantity'] : '0';
                                $data['invAge0To90Days'] = (string)(isset($insert_record['inv-age-0-to-90-days']) && trim($insert_record['inv-age-0-to-90-days']) != '') ? $insert_record['inv-age-0-to-90-days'] : '0';
                                $data['invAge91To180Days'] = (string)(isset($insert_record['inv-age-91-to-180-days']) && trim($insert_record['inv-age-91-to-180-days']) != '') ? $insert_record['inv-age-91-to-180-days'] : '0';
                                $data['invAge181To270Days'] = (string)(isset($insert_record['inv-age-181-to-270-days']) && trim($insert_record['inv-age-181-to-270-days']) != '') ? $insert_record['inv-age-181-to-270-days'] : '0';
                                $data['invAge271To365Days'] = (string)(isset($insert_record['inv-age-271-to-365-days']) && trim($insert_record['inv-age-271-to-365-days']) != '') ? $insert_record['inv-age-271-to-365-days'] : '0';
                                $data['invAge365PlusDays'] = (string)(isset($insert_record['inv-age-365-plus-days']) && trim($insert_record['inv-age-365-plus-days']) != '') ? $insert_record['inv-age-365-plus-days'] : '0';
                                $data['unitsShippedLast24Hrs'] = (string)(isset($insert_record['units-shipped-last-24-hrs']) && trim($insert_record['units-shipped-last-24-hrs']) != '') ? $insert_record['units-shipped-last-24-hrs'] : '0';
                                $data['unitsShippedLast7Days'] = (string)(isset($insert_record['units-shipped-last-7-days']) && trim($insert_record['units-shipped-last-7-days']) != '') ? $insert_record['units-shipped-last-7-days'] : '0';
                                $data['unitsShippedLast30Days'] = (string)(isset($insert_record['units-shipped-last-30-days']) && trim($insert_record['units-shipped-last-30-days']) != '') ? $insert_record['units-shipped-last-30-days'] : '0';
                                $data['unitsShippedLast90Days'] = (string)isset($insert_record['units-shipped-last-90-days']) && trim($insert_record['units-shipped-last-90-days']) != '' ? $insert_record['units-shipped-last-90-days'] : '0';
                                $data['unitsShippedLast180Days'] = (string)isset($insert_record['units-shipped-last-180-days']) && trim($insert_record['units-shipped-last-180-days']) != '' ? $insert_record['units-shipped-last-180-days'] : '0';
                                $data['unitsShippedLast365Days'] = (string)isset($insert_record['units-shipped-last-365-days']) && trim($insert_record['units-shipped-last-365-days']) != '' ? $insert_record['units-shipped-last-365-days'] : '0';
                                $data['weeksOfCoverT7'] = (string)isset($insert_record['weeks-of-cover-t7']) && trim($insert_record['weeks-of-cover-t7']) != '' ? $insert_record['weeks-of-cover-t7'] : 'NA';
                                $data['weeksOfCoverT30'] = (string)isset($insert_record['weeks-of-cover-t30']) && trim($insert_record['weeks-of-cover-t30']) != '' ? $insert_record['weeks-of-cover-t30'] : 'NA';
                                $data['weeksOfCoverT90'] = (string)isset($insert_record['weeks-of-cover-t90']) && trim($insert_record['weeks-of-cover-t90']) != '' ? $insert_record['weeks-of-cover-t90'] : 'NA';
                                $data['weeksOfCoverT180'] = (string)isset($insert_record['weeks-of-cover-t180']) && trim($insert_record['weeks-of-cover-t180']) != '' ? $insert_record['weeks-of-cover-t180'] : 'NA';
                                $data['weeksOfCoverT365'] = (string)isset($insert_record['weeks-of-cover-t365']) && trim($insert_record['weeks-of-cover-t365']) != '' ? $insert_record['weeks-of-cover-t365'] : 'NA';
                                $data['numAfnNewSellers'] = (string)isset($insert_record['num-afn-new-sellers']) && trim($insert_record['num-afn-new-sellers']) != '' ? $insert_record['num-afn-new-sellers'] : '0';
                                $data['numAfnUsedSellers'] = (string)isset($insert_record['num-afn-used-sellers']) && trim($insert_record['num-afn-used-sellers']) != '' ? $insert_record['num-afn-used-sellers'] : '0';
                                $data['currency'] = (string)isset($insert_record['currency']) && trim($insert_record['currency']) != '' ? $insert_record['currency'] : 'NA';
                                $data['yourPrice'] = (string)isset($insert_record['your-price']) && trim($insert_record['your-price']) != '' ? $insert_record['your-price'] : '0.00';
                                $data['salesPrice'] = (string)isset($insert_record['sales-price']) && trim($insert_record['sales-price']) != '' ? $insert_record['sales-price'] : '0.00';
                                $data['lowestAfnNewPrice'] = (string)isset($insert_record['lowest-afn-new-price']) && trim($insert_record['lowest-afn-new-price']) != '' ? $insert_record['lowest-afn-new-price'] : '0.00';
                                $data['lowestAfnUsedPrice'] = (string)isset($insert_record['lowest-afn-used-price']) && trim($insert_record['lowest-afn-used-price']) != '' ? $insert_record['lowest-afn-used-price'] : '0.00';
                                $data['lowestMfnNewPrice'] = (string)isset($insert_record['lowest-mfn-new-price']) && trim($insert_record['lowest-mfn-new-price']) != '' ? $insert_record['lowest-mfn-new-price'] : '0.00';
                                $data['lowestMfnUsedPrice'] = (string)isset($insert_record['lowest-mfn-used-price']) && trim($insert_record['lowest-mfn-used-price']) != '' ? $insert_record['lowest-mfn-used-price'] : '0.00';
                                $data['qtyToBeChargedlTsf12Mo'] = (string)isset($insert_record['qty-to-be-charged-ltsf-12-mo']) && trim($insert_record['qty-to-be-charged-ltsf-12-mo']) != '' ? $insert_record['qty-to-be-charged-ltsf-12-mo'] : '0';
                                $data['qtyInLongTermStorageProgram'] = (string)isset($insert_record['qty-in-long-term-storage-program']) && trim($insert_record['qty-in-long-term-storage-program']) != '' ? $insert_record['qty-in-long-term-storage-program'] : '0.00';
                                $data['qtyWithRemovalsInProgress'] = (string)isset($insert_record['qty-with-removals-in-progress']) && trim($insert_record['qty-with-removals-in-progress']) != '' ? $insert_record['qty-with-removals-in-progress'] : '0.00';
                                $data['projectedlTsf12Mo'] = (string)isset($insert_record['projected-ltsf-12-mo']) && trim($insert_record['projected-ltsf-12-mo']) != '' ? $insert_record['projected-ltsf-12-mo'] : '0';
                                $data['perUnitVolume'] = (string)isset($insert_record['per-unit-volume']) && trim($insert_record['per-unit-volume']) != '' ? $insert_record['per-unit-volume'] : '0.00';
                                $data['isHazmat'] = (string)isset($insert_record['is-hazmat']) && trim($insert_record['is-hazmat']) != '' ? $insert_record['is-hazmat'] : 'NA';
                                $data['inBoundQuantity'] = (string)isset($insert_record['in-bound-quantity']) && trim($insert_record['in-bound-quantity']) != '' ? $insert_record['in-bound-quantity'] : '0';
                                $data['asinLimit'] = (string)isset($insert_record['asin-limit']) && trim($insert_record['asin-limit']) != '' ? $insert_record['asin-limit'] : 'NA';
                                $data['inboundRecommendQuantity'] = (string)isset($insert_record['inbound-recommend-quantity']) && trim($insert_record['inbound-recommend-quantity']) != '' ? $insert_record['inbound-recommend-quantity'] : '0';
                                $data['qtyToBeChargedlTsf6Mo'] = (string)isset($insert_record['qty-to-be-charged-ltsf-6-mo']) && trim($insert_record['qty-to-be-charged-ltsf-6-mo']) != '' ? $insert_record['qty-to-be-charged-ltsf-6-mo'] : '0';
                                $data['projectedlTsf6Mo'] = (string)isset($insert_record['projected-ltsf-6-mo']) && trim($insert_record['projected-ltsf-6-mo']) != '' ? $insert_record['projected-ltsf-6-mo'] : '0';

                                // $data['ReportRequestId']=$mws_done_reportreq_id;
                                //MWSModel::insert_mws_ScFbaHealthReport($data);
                                $data['createdAt'] = date('Y-m-d H:i:s');
                                //array_push($insert_data_array, $data);
                                MWSModel::insert_mws_ScFbaHealthReport($data);
                            }
                           /* if (!empty($insert_data_array)) {
                                MWSModel::insert_mws_ScFbaHealthReport($insert_data_array);
                            }*/
                            $acknowledgement = MWSModel::update_mws_report_acknowledgement($fk_request_id, 'true');
                            if ($acknowledgement) {
                                Log::info("filePath:app\Console\Commands\Mws\screports\ScFbaHealth.php.Download Inventory-ScFbaHealth(_GET_FBA_FULFILLMENT_INVENTORY_HEALTH_DATA_). Report Acknowledge In Database.(Report Id:'.$report_id.')");
                                MWSModel::insert_mws_Activity('Report Acknowledge In Database.', 'Download Inventory-ScFbaHealth(_GET_FBA_FULFILLMENT_INVENTORY_HEALTH_DATA_)(Report Id:'.$report_id.')', 'app\Console\Commands\Mws\screports\ScFbaHealth.php');
                            } else {
                                Log::info("filePath:app\Console\Commands\Mws\screports\ScFbaHealth.php.Download Inventory-ScFbaHealth(_GET_FBA_FULFILLMENT_INVENTORY_HEALTH_DATA_). Report Not Acknowledge In Database.(Report Id:'.$report_id.')");
                                MWSModel::insert_mws_Activity('Report Not Acknowledge In Database.', 'Download Inventory-ScFbaHealth(_GET_FBA_FULFILLMENT_INVENTORY_HEALTH_DATA_)(Report Id:'.$report_id.')', 'app\Console\Commands\Mws\screports\ScFbaHealth.php');

                            }
                        } else {
                            $acknowledgement = MWSModel::update_mws_report_acknowledgement($fk_request_id, 'no_data');
                            Log::info("filePath:app\Console\Commands\Mws\screports\ScFbaHealth.php.Download Inventory-ScFbaHealth(_GET_FBA_FULFILLMENT_INVENTORY_HEALTH_DATA_). No Data Found In Api.(Report Id:'.$report_id.')");
                            MWSModel::insert_mws_Activity('No Data Found In Api.', 'Download Inventory-ScFbaHealth(_GET_FBA_FULFILLMENT_INVENTORY_HEALTH_DATA_)(Report Id:'.$report_id.')', 'app\Console\Commands\Mws\screports\ScFbaHealth.php');
                        }
                    }
                } else {
                    Log::info("filePath:app\Console\Commands\Mws\screports\ScFbaHealth.php.Download Inventory-ScFbaHealth(_GET_FBA_FULFILLMENT_INVENTORY_HEALTH_DATA_). No Done Reports Found.");
                    MWSModel::insert_mws_Activity('No Done Reports Found.', 'Download Inventory-ScFbaHealth(_GET_FBA_FULFILLMENT_INVENTORY_HEALTH_DATA_)', 'app\Console\Commands\Mws\screports\ScFbaHealth.php');
                }
            }
        } else {
            Log::info("filePath:app\Console\Commands\Mws\screports\ScFbaHealth.php.Download Inventory-ScFbaHealth(_GET_FBA_FULFILLMENT_INVENTORY_HEALTH_DATA_). Merchants Not Found.");
            MWSModel::insert_mws_Activity('Merchants Not Found.', 'Download Inventory-ScFbaHealth(_GET_FBA_FULFILLMENT_INVENTORY_HEALTH_DATA_)', 'app\Console\Commands\Mws\screports\ScFbaHealth.php');
        }
        Log::info("filePath:app\Console\Commands\Mws\screports\ScFbaHealth.php.Download Inventory-ScFbaHealth(_GET_FBA_FULFILLMENT_INVENTORY_HEALTH_DATA_). End Cron.");
        MWSModel::insert_mws_Activity('End Cron.', 'Download Inventory-ScFbaHealth(_GET_FBA_FULFILLMENT_INVENTORY_HEALTH_DATA_)', 'app\Console\Commands\Mws\screports\ScFbaHealth.php');
    }
}
