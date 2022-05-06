<?php

namespace App\Console\Commands\Mws\ScGetProductIds;

use App\Models\MWSModel;
use Illuminate\Console\Command;

class scGetInventoryProductsIds extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'scGetInventoryProductsIds:cron';

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
        /*copy tbl_sc_inventory_cat_active_report starts */

        $inventory_cat_active_report_data = MWSModel::get_asin_inventory_cat_active_report();
        $records_count = count($inventory_cat_active_report_data);
        if ($records_count > 0) {
            foreach ($inventory_cat_active_report_data as $value) {
                $accountId1=$value->fkAccountId;
                $get_asin_duplicate_count1 = MWSModel::get_asin_duplicate_count($accountId1, $value->asin1);
                if ($get_asin_duplicate_count1 == 0) {
                    $inventory_cat_active_report_asins['fkAccountId'] = $value->fkAccountId;
                    $inventory_cat_active_report_asins['fkBatchId'] = $value->fkBatchId;
                    $inventory_cat_active_report_asins['fkSellerConfigId'] = $value->fk_merchant_id;
                    //$inventory_cat_active_report_asins['fkRequestId'] = $value->fkRequestId;
                    $inventory_cat_active_report_asins['asin'] = $value->asin1;
                    $inventory_cat_active_report_asins['idType'] = 'ASIN';
                    $inventory_cat_active_report_asins['source'] = 'SC';
                    $inventory_cat_active_report_asins['createdAt'] = date('Y-m-d H:i:s');
                    //$inventory_cat_active_report_asins_arr[] = $inventory_cat_active_report_asins;
                    if (isset($inventory_cat_active_report_asins)) {
                        $inventory_cat_active_report_asins_insert = MWSModel::insert_report_asin($inventory_cat_active_report_asins);
                    }//end if
                }//end if
            }//end foreach

        }//end if
        /*copy tbl_sc_inventory_cat_active_report ends */

        /*copy tbl_sc_inventory_cat_active_report starts */
        //tbl_sc_inventory_fba_health_report
        $inventory_fba_health_report_data = MWSModel::get_asin_inventory_fba_health_report();
        $records_count = count($inventory_fba_health_report_data);
        if ($records_count > 0) {
            foreach ($inventory_fba_health_report_data as $value) {
                $accountId2=$value->fkAccountId;
                $get_asin_duplicate_count2 = MWSModel::get_asin_duplicate_count($accountId2, $value->asin);
                if ($get_asin_duplicate_count2 == 0) {
                    $inventory_fba_health_report_asins['fkAccountId'] = $value->fkAccountId;
                    $inventory_fba_health_report_asins['fkBatchId'] = $value->fkBatchId;
                    $inventory_fba_health_report_asins['fkSellerConfigId'] = $value->fk_merchant_id;
                    //$inventory_fba_health_report_asins['fkRequestId'] = $value->fkRequestId;
                    $inventory_fba_health_report_asins['asin'] = $value->asin;
                    $inventory_fba_health_report_asins['idType'] = 'ASIN';
                    $inventory_fba_health_report_asins['source'] = 'SC';
                    $inventory_fba_health_report_asins['createdAt'] = date('Y-m-d H:i:s');
                    // $inventory_fba_health_report_arr[] = $inventory_fba_health_report_asins;
                    if (isset($inventory_fba_health_report_asins)) {
                        $inventory_fba_health_report_asins_insert = MWSModel::insert_report_asin($inventory_fba_health_report_asins);
                    }//end if
                }//end if
            }//end each

        }//end if


        /*copy tbl_sc_inventory_fba_health_report ends */

        /*copy tbl_sc_inventory_fba_receipt_report starts */
        //tbl_sc_inventory_fba_receipt_report
        /* $inventory_fba_receipt_report_data = MWSModel::get_asin_inventory_fba_receipt_report();
         $records_count=count($inventory_fba_receipt_report_data);
         if ($records_count > 0) {
             foreach ($inventory_fba_receipt_report_data as $value) {
                 $inventory_fba_receipt_report_asins['fkAccountId'] = $value->fkAccountId;
                 $inventory_fba_receipt_report_asins['fkBatchId'] = $value->fkBatchId;
                 $inventory_fba_receipt_report_asins['fkSellerConfigId'] = $value->fk_merchant_id;
                 //$inventory_fba_receipt_report_asins['fkRequestId'] = $value->fkRequestId;
                 $inventory_fba_receipt_report_asins['sku'] = $value->sku;
                 $inventory_fba_receipt_report_asins['idType'] = 'SellerSKU';
                 $inventory_fba_receipt_report_asins['source'] = 'SC';
                 $inventory_fba_receipt_report_asins['createdAt'] = date('Y-m-d H:i:s');
                 $inventory_fba_receipt_report_arr[] = $inventory_fba_receipt_report_asins;
             }
             $inventory_fba_receipt_report_asins_insert = MWSModel::insert_report_asin($inventory_fba_receipt_report_arr);
         }*/


        /*copy tbl_sc_inventory_fba_health_report ends */
    }
}
