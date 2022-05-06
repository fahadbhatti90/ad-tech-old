<?php

namespace App\Console\Commands\Mws\ScGetProductIds;

use App\Models\MWSModel;
use Illuminate\Console\Command;

class scGetSalesProductsIds extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'scGetSalesProductsIds:cron';

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
        /*copy tbl_sc_sales_fba_returns_report starts */
        //tbl_sc_sales_fba_returns_report
        $sales_fba_returns_report_data = MWSModel::get_asin_sales_fba_returns_report();
        $records_count = count($sales_fba_returns_report_data);
        if ($records_count > 0) {
            foreach ($sales_fba_returns_report_data as $value) {
                $accountId1=$value->fkAccountId;
                $get_asin_duplicate_count1 = MWSModel::get_asin_duplicate_count($accountId1, $value->asin);
                if ($get_asin_duplicate_count1 == 0) {
                    $sales_fba_returns_report_asins['fkAccountId'] = $value->fkAccountId;
                    $sales_fba_returns_report_asins['fkBatchId'] = $value->fkBatchId;
                    $sales_fba_returns_report_asins['fkSellerConfigId'] = $value->fk_merchant_id;
                    //$sales_fba_returns_report_asins['fkRequestId'] = $value->fkRequestId;
                    $sales_fba_returns_report_asins['asin'] = $value->asin;
                    $sales_fba_returns_report_asins['idType'] = 'ASIN';
                    $sales_fba_returns_report_asins['source'] = 'SC';
                    $sales_fba_returns_report_asins['createdAt'] = date('Y-m-d H:i:s');
                    //$sales_fba_returns_report_arr[] = $sales_fba_returns_report_asins;
                    if (isset($sales_fba_returns_report_asins)) {
                        $sales_fba_returns_report_insert = MWSModel::insert_report_asin($sales_fba_returns_report_asins);
                    }
                }
            }

        }
        /*copy tbl_sc_sales_fba_returns_report ends */
        //exit;
        /*copy tbl_sc_sales_orders_report starts */
        //tbl_sc_sales_orders_report
        $sc_sales_orders_report_data = MWSModel::get_asin_sc_sales_orders_report();
        $records_count = count($sc_sales_orders_report_data);
        if ($records_count > 0) {
            foreach ($sc_sales_orders_report_data as $value) {
                $accountId2=$value->fkAccountId;
                $get_asin_duplicate_count2 = MWSModel::get_asin_duplicate_count($accountId2, $value->asin);
                if ($get_asin_duplicate_count2 == 0) {
                    $sales_orders_report_asins['fkAccountId'] = $value->fkAccountId;
                    $sales_orders_report_asins['fkBatchId'] = $value->fkBatchId;
                    $sales_orders_report_asins['fkSellerConfigId'] = $value->fk_merchant_id;
                    //$sales_orders_report_asins['fkRequestId'] = $value->fkRequestId;
                    $sales_orders_report_asins['asin'] = $value->asin;
                    $sales_orders_report_asins['idType'] = 'ASIN';
                    $sales_orders_report_asins['source'] = 'SC';
                    $sales_orders_report_asins['createdAt'] = date('Y-m-d H:i:s');
                    //$sales_orders_report_arr[] = $sales_orders_report_asins;
                    if (isset($sales_orders_report_asins)) {
                        $sales_orders_report_insert = MWSModel::insert_report_asin($sales_orders_report_asins);
                    }
                }
            }

        }
        /*copy tbl_sc_sales_orders_report ends */

        /*copy tbl_sc_sales_orders_report starts */
        //tbl_sc_sales_orders_updt_report
        $sc_sales_orders_updt_report_data = MWSModel::get_asin_sc_sales_orders_updt_report();
        $records_count = count($sc_sales_orders_updt_report_data);
        if ($records_count > 0) {
            foreach ($sc_sales_orders_updt_report_data as $value) {
                $accountId3=$value->fkAccountId;
                $get_asin_duplicate_count3 = MWSModel::get_asin_duplicate_count($accountId3, $value->asin);
                if ($get_asin_duplicate_count3 == 0) {
                    $sc_sales_orders_updt_report_asins['fkAccountId'] = $value->fkAccountId;
                    $sc_sales_orders_updt_report_asins['fkBatchId'] = $value->fkBatchId;
                    $sc_sales_orders_updt_report_asins['fkSellerConfigId'] = $value->fk_merchant_id;
                    //$sc_sales_orders_updt_report_asins['fkRequestId'] = $value->fkRequestId;
                    $sc_sales_orders_updt_report_asins['asin'] = $value->asin;
                    $sc_sales_orders_updt_report_asins['idType'] = 'ASIN';
                    $sc_sales_orders_updt_report_asins['source'] = 'SC';
                    $sc_sales_orders_updt_report_asins['createdAt'] = date('Y-m-d H:i:s');
                    //$sc_sales_orders_updt_report_arr[] = $sc_sales_orders_updt_report_asins;
                    if (isset($sc_sales_orders_updt_report_asins)) {
                        $sales_orders_updt_report_insert = MWSModel::insert_report_asin($sc_sales_orders_updt_report_asins);
                    }
                }
            }

        }
        /*copy tbl_sc_sales_orders_updt_report ends */

        /*copy tbl_sc_sales_mfn_returns_report starts */
        //tbl_sc_sales_mfn_returns_report
        $sc_sales_mfn_returns_report_data = MWSModel::get_asin_sc_sales_mfn_returns_report();
        $records_count = count($sc_sales_mfn_returns_report_data);
        if ($records_count > 0) {
            foreach ($sc_sales_mfn_returns_report_data as $value) {
                $accountId4=$value->fkAccountId;
                $get_asin_duplicate_count4 = MWSModel::get_asin_duplicate_count($accountId4, $value->asin);
                if ($get_asin_duplicate_count4 == 0) {
                    $sales_mfn_returns_report_asins['fkAccountId'] = $value->fkAccountId;
                    $sales_mfn_returns_report_asins['fkBatchId'] = $value->fkBatchId;
                    $sales_mfn_returns_report_asins['fkSellerConfigId'] = $value->fk_merchant_id;
                    //$sales_mfn_returns_report_asins['fkRequestId'] = $value->fkRequestId;
                    $sales_mfn_returns_report_asins['asin'] = $value->asin;
                    $sales_mfn_returns_report_asins['idType'] = 'ASIN';
                    $sales_mfn_returns_report_asins['source'] = 'SC';
                    $sales_mfn_returns_report_asins['createdAt'] = date('Y-m-d H:i:s');
                    //$sc_sales_mfn_returns_report_arr[] = $sales_mfn_returns_report_asins;
                    if (isset($sales_mfn_returns_report_asins)) {
                        $sc_sales_mfn_returns_report_insert = MWSModel::insert_report_asin($sales_mfn_returns_report_asins);
                    }
                }
            }

        }
        /*copy tbl_sc_sales_mfn_returns_report ends */

    }
}
