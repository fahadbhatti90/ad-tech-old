<?php

namespace App\Console\Commands\Mws\ScGetProductIds;

use App\Models\MWSModel;
use Illuminate\Console\Command;

class scGetCatalogProductsIds extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'scGetCatalogProductsIds:cron';

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

        /*copy tbl_sc_catalog_cat_active_report starts */

        $catalog_cat_active_report_data = MWSModel::get_asin_catalog_cat_active_report();

        $records_count = count($catalog_cat_active_report_data);
        if ($records_count > 0) {
            foreach ($catalog_cat_active_report_data as $value) {
                $accountId1=$value->fkAccountId;
                $get_asin_duplicate_count1 = MWSModel::get_asin_duplicate_count($accountId1, $value->asin1);
                if ($get_asin_duplicate_count1 == 0) {
                    $catalog_cat_active_report_asins=array();
                   // $account_id1 = $value->fkAccountId;
                    //$get_sc_daily_batch_id1 = MWSModel::get_sc_daily_batch_id($account_id1);
                   // $sc_count_batch_id1 = count($get_sc_daily_batch_id1);
                   // if ($sc_count_batch_id1 > 0) {
                    //$sc_batch_id1 = $get_sc_daily_batch_id1[0]->batchId;
                    $catalog_cat_active_report_asins['fkAccountId'] = $value->fkAccountId;
                    $catalog_cat_active_report_asins['fkBatchId'] = $value->fkBatchId;
                    $catalog_cat_active_report_asins['fkSellerConfigId'] = $value->fk_merchant_id;
                    //$catalog_cat_active_report_asins['fkRequestId'] = $value->fkRequestId;
                    $catalog_cat_active_report_asins['asin'] = $value->asin1;
                    $catalog_cat_active_report_asins['idType'] = 'ASIN';
                    $catalog_cat_active_report_asins['source'] = 'SC';
                    $catalog_cat_active_report_asins['createdAt'] = date('Y-m-d H:i:s');
                    //$catalog_cat_active_report_asins_arr[] = $catalog_cat_active_report_asins;
                    if (isset($catalog_cat_active_report_asins)) {
                        $catalog_cat_active_report_asins_insert = MWSModel::insert_report_asin($catalog_cat_active_report_asins);
                    }
                //}
                }
            }//end foreach

        }//end if

        /*copy tbl_sc_catalog_cat_active_report ends */
        /*copy tbl_sc_catalog_cat_active_report starts */

        $catalog_cat_inactive_report_data = MWSModel::get_asin_catalog_cat_inactive_report();

        $records_count = count($catalog_cat_inactive_report_data);
        if ($records_count > 0) {
            foreach ($catalog_cat_inactive_report_data as $value) {
                $accountId2=$value->fkAccountId;
                $get_asin_duplicate_count2 = MWSModel::get_asin_duplicate_count($accountId2, $value->asin1);
                if ($get_asin_duplicate_count2 == 0) {

                    //$account_id2 = $value->fkAccountId;
                    //$get_sc_daily_batch_id2 = MWSModel::get_sc_daily_batch_id($account_id2);
                    //$sc_count_batch_id2 = count($get_sc_daily_batch_id2);
                  //  if ($sc_count_batch_id2 > 0) {
                        //$sc_batch_id2 = $sc_count_batch_id2[0]->batchId;

                    $catalog_cat_inactive_report_asins=array();
                        $catalog_cat_inactive_report_asins['fkAccountId'] = $value->fkAccountId;
                        $catalog_cat_inactive_report_asins['fkBatchId'] = $value->fkBatchId;
                    $catalog_cat_inactive_report_asins['fkSellerConfigId'] = $value->fk_merchant_id;
                    //$catalog_cat_inactive_report_asins['fkRequestId'] = $value->fkRequestId;
                    $catalog_cat_inactive_report_asins['asin'] = $value->asin1;
                    $catalog_cat_inactive_report_asins['idType'] = 'ASIN';
                    $catalog_cat_inactive_report_asins['source'] = 'SC';
                    $catalog_cat_inactive_report_asins['createdAt'] = date('Y-m-d H:i:s');
                    //$catalog_cat_inactive_report_asins_arr[] = $catalog_cat_inactive_report_asins;
                    if (isset($catalog_cat_inactive_report_asins)) {

                        $catalog_cat_inactive_report_asins_insert = MWSModel::insert_report_asin($catalog_cat_inactive_report_asins);
                    }//end if
               // }
                }//end if
            }//end foreach

        }//end if

        /*copy tbl_sc_catalog_cat_active_report ends */

        /*copy tbl_sc_catalog_fba_health_report starts */

        $catalog_fba_health_report_data = MWSModel::get_asin_catalog_fba_health_report();

        $records_count = count($catalog_fba_health_report_data);
        if ($records_count > 0) {
            foreach ($catalog_fba_health_report_data as $value) {
                $accountId3=$value->fkAccountId;
                $get_asin_duplicate_count3 = MWSModel::get_asin_duplicate_count($accountId3, $value->asin);
                if ($get_asin_duplicate_count3 == 0) {
                    //$account_id = $value->fkAccountId;
                    //$get_sc_daily_batch_id = MWSModel::get_sc_daily_batch_id($account_id);

                    //$sc_count_batch_id = count($get_sc_daily_batch_id);
                    //if ($sc_count_batch_id > 0) {
                        //$sc_batch_id = $get_sc_daily_batch_id[0]->batchId;
                    $catalog_fba_health_report_asins['fkAccountId'] = $value->fkAccountId;
                    $catalog_fba_health_report_asins['fkBatchId'] = $value->fkBatchId;
                    $catalog_fba_health_report_asins['fkSellerConfigId'] = $value->fk_merchant_id;
                    //$catalog_fba_health_report_asins['fkRequestId'] = $value->fkRequestId;
                    $catalog_fba_health_report_asins['asin'] = $value->asin;
                    $catalog_fba_health_report_asins['idType'] = 'ASIN';
                    $catalog_fba_health_report_asins['source'] = 'SC';
                    $catalog_fba_health_report_asins['createdAt'] = date('Y-m-d H:i:s');
                    //$catalog_fba_health_report_asins_arr[] = $catalog_fba_health_report_asins;
                    if (isset($catalog_fba_health_report_asins)) {
                        $catalog_fba_health_report_asins_insert = MWSModel::insert_report_asin($catalog_fba_health_report_asins);
                    }//end if
                //}
                }//end if
            }//end foreach

        }//end if
        /*copy tbl_sc_catalog_fba_health_report ends */

    }
}
