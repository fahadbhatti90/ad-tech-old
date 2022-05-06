<?php

//namespace App\Console\Commands;
namespace App\Console\Commands\Mws\screports;

use App\Libraries\mws\AmazonReport as AmazonReport;
use Config;
use Illuminate\Console\Command;
use App\Models\MWSModel;

//use Sonnenglas\AmazonMws\AmazonReport;
//use app\Libraries\ReportHandler;

class ScFbaRestock extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'ScFbaRestock:cron';

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
        $APIParametr = new MWSModel();
        $api_data = $APIParametr->get_merchants();

        if ($api_data) {
            foreach ($api_data as $api_parameter) {
                $mws_config_id = trim($api_parameter->mws_config_id);
                Config::set('amazon-mws.store.store1.merchantId', trim($api_parameter->seller_id));
                //Config::set('amazon-mws.store.store1.marketplaceId', trim($api_parameter->marketplace_id));
                Config::set('amazon-mws.store.store1.marketplaceId', 'ATVPDKIKX0DER');
                Config::set('amazon-mws.store.store1.keyId', trim($api_parameter->mws_access_key_id));
                Config::set('amazon-mws.store.store1.secretKey', trim($api_parameter->mws_secret_key));
                Config::set('amazon-mws.store.store1.authToken', trim($api_parameter->mws_authtoken));

                $getMwsDoneReportsArray = MWSModel::get_mws_done_reports('_GET_RESTOCK_INVENTORY_RECOMMENDATIONS_REPORT_', $mws_config_id, 'Inventory');
                /*echo '<pre>';
                print_r($getMwsDoneReportsArray);
                exit;*/
                foreach ($getMwsDoneReportsArray as $index) {

                    $fk_request_id = trim($index->id);

                    $report_id = trim($index->GeneratedReportId);

                    $ReportRequestId = trim($index->ReportRequestId);
                    $reportRequestDate = $index->reportRequestDate;
                    //echo '<br>';

                    // $amz = new AmazonReport("store1"); //store name matches the array key in the config file
                    $amz = new AmazonReport("store1");
                    $amz->setReportId($report_id);
                    $amz->fetchReport();
                    $path = '';
                    $report_data = $amz->saveReport($path);
                    if (!empty($report_data)) {
                        foreach ($report_data as $insert_record) {

                            $data['fkRequestId'] = $fk_request_id;
                            $data['reportId'] = $report_id;
                            $data['reportRequestId'] = $ReportRequestId;
                            $data['reportRequestDate'] = $reportRequestDate;
                            $data['country'] = $insert_record['Country'];

                            //$data['productDescription']=$insert_record['Product Description'];
                            $data['FNSKU'] = $insert_record['FNSKU'];
                            //$data['merchant']=$insert_record['Merchant'];
                            $data['sku'] = $insert_record['SKU'];
                            $data['asin'] = $insert_record['ASIN'];
                            $data['condition'] = $insert_record['Condition'];
                            //$data['snapshotDate']= '';Supplier
                            $data['supplier'] = $insert_record['Supplier'];
                            $data['partNo'] = $insert_record['Supplierpartno.'];
                            $data['currencyCode'] = $insert_record['CurrencyCode'];
                            $data['price'] = $insert_record['Price'];
                            //$data['SalesLast30Days']= $insert_record['Saleslast30days(sales)'];
                            $data['unitsSoldLast30Days'] = $insert_record['Saleslast30days(units)'];
                            //$data['totalUnits']=$insert_record['Total Units'];
                            $data['totalInventory'] = $insert_record['TotalInventory'];
                            $data['inboundInventory'] = $insert_record['InboundInventory'];

                            $data['availableInventory'] = $insert_record['AvailableInventory'];
                            //$data['InboundAvailable']=$insert_record['Available Inventory'];
                            $data['fcTransfer'] = $insert_record['Reserved-FCtransfer'];
                            $data['fcProcessing'] = $insert_record['Reserved-FCprocessing'];
                            $data['customerOrder'] = $insert_record['Reserved-CustomerOrder'];
                            $data['unfulfillable'] = $insert_record['Unfulfillable'];
                            $data['fulfilledBy'] = $insert_record['Fulfilledby'];
                            $data['daysOfSupply'] = $insert_record['DaysofSupply'];
                            $data['instockAlert'] = $insert_record['InstockAlert'];
                            $data['recommendedOrderQty'] = $insert_record['RecommendedOrderQuantity'];
                            $data['recommendedOrderDate'] = $insert_record['RecommendedOrderDate'];
                            //$data['eligibleForStorageFee']=$insert_record['EligibleforStorageFeeDiscountCurrentMonth'];
                            $data['currentMonthVeryHighInventoryThreshold'] = $insert_record['CurrentMonth-VeryHighInventoryThreshold'];
                            $data['eligibleForStorageFeeDiscountNextMonth'] = $insert_record['EligibleforStorageFeeDiscountNextMonth'];
                            //$data['nextMonthVeryLowInventoryThreshold']=$insert_record['NextMonth-VeryLowInventoryThreshold'];
                            $data['nextMonthVeryHighInventoryThreshold'] = $insert_record['NextMonth-VeryHighInventoryThreshold'];
                            $result = MWSModel::insert_mws_ScFbaRestockReport($data);
                            if ($result) {
                                echo 'success';
                            } else {
                                echo 'fail';
                            }
                        }
                        //$acknowledgement=MWSModel::update_mws_report_acknowledgement($fk_request_id,'true');
                    } else {
                        //$acknowledgement=MWSModel::update_mws_report_acknowledgement($fk_request_id,'no_data');
                    }
                }
            }
        }

    }
}
