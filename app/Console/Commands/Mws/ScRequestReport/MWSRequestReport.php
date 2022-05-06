<?php

//namespace App\Console\Commands;
namespace App\Console\Commands\Mws\ScRequestReport;


use Config;
use Illuminate\Console\Command;
use App\Models\MWSModel;
use Sonnenglas\AmazonMws\AmazonReportRequest;

class MWSRequestReport extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'mwsrequestreport:cron';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Request for MWS Report';

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
        $api_data= $APIParametr->get_merchants();
        if ($api_data){
            foreach ($api_data as $api_parameter)
            {
                $mws_config_id=trim($api_parameter->mws_config_id);
                Config::set('amazon-mws.store.store1.merchantId', trim($api_parameter->seller_id));
                //Config::set('amazon-mws.store.store1.marketplaceId', trim($api_parameter->marketplace_id));
                Config::set('amazon-mws.store.store1.marketplaceId','ATVPDKIKX0DER');
                Config::set('amazon-mws.store.store1.keyId', trim($api_parameter->mws_access_key_id));
                Config::set('amazon-mws.store.store1.secretKey', trim($api_parameter->mws_secret_key));
                Config::set('amazon-mws.store.store1.authToken', trim($api_parameter->mws_authtoken));
                //$store = Config::get('amazon-mws.store');
                $amz = new AmazonReportRequest("store1"); //store name matches the array key in the config file
                $report_types=array('_GET_MERCHANT_LISTINGS_DATA_','_GET_MERCHANT_LISTINGS_INACTIVE_DATA_','_GET_FBA_FULFILLMENT_CUSTOMER_RETURNS_DATA_','_GET_FLAT_FILE_RETURNS_DATA_BY_RETURN_DATE_','_GET_FLAT_FILE_ALL_ORDERS_DATA_BY_LAST_UPDATE_','_GET_FLAT_FILE_ALL_ORDERS_DATA_BY_ORDER_DATE_','_GET_FBA_FULFILLMENT_INVENTORY_HEALTH_DATA_','_GET_FBA_FULFILLMENT_INVENTORY_RECEIPTS_DATA_','_GET_RESTOCK_INVENTORY_RECOMMENDATIONS_REPORT_');
                foreach ($report_types as $report_types){

                    $amz->setReportType(trim($report_types)); //no Amazon-fulfilled orders
                    $start_date=date("Y-m-d H:i:s",strtotime("-1 month"));
                    $amz->setTimeLimits($start_date);
                    $amz->requestReport();
                    $report_request_data=$amz->getResponse();
                    if ($report_request_data){
                        $report_request_data['fk_merchant_id']=$mws_config_id;
                        $result_insert_report=MWSModel::insert_mws_report_request($report_request_data);
                        echo 'inserted';
                        echo '<br>';
                        //exit;
                    }
                }
            }
        }else{
            echo 'Api parameters not found';
        }
    }
}
