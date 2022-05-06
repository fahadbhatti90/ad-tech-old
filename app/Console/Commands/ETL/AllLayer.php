<?php

namespace App\Console\Commands\ETL;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class AllLayer extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'alllayer:etl';

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
        Log::info('Start ETL here');
        $DB1 = 'mysql'; // layer 0 database
        $DB2 = 'mysqlDb2'; // layer 1 BI database
        $ETL_date = date('Ymd', strtotime('-2 day', time()));
        // Weekly status
        $checkWeeklyStatus = array(0);
        $checkWeeklyStatus[0] = new \stdClass();
        $checkWeeklyStatus[0]->count = 0; // default value 0 means weekly table empty
        // Monthly status
        $checkMonthlyStatus = array(0);
        $checkMonthlyStatus[0] = new \stdClass();
        $checkMonthlyStatus[0]->count = 0; // default value 0 means monthly table empty
        Log::info('ETL date:' . $ETL_date);
        \DB::connection($DB1)->statement('CALL tbl_rtl_truncates()');
        \DB::connection($DB1)->statement('CALL tbl_rtl_account_client()');
        \DB::connection($DB1)->statement('CALL tbl_rtl_sale_master(?)', array($ETL_date));
        \DB::connection($DB1)->statement('CALL tbl_rtl_product_salesrank(?)', array($ETL_date));
        \DB::connection($DB1)->statement('CALL tbl_rtl_product_catalog_sc(?)', array($ETL_date));
        \DB::connection($DB1)->statement('CALL tbl_rtl_product_catalog_sc_lookup(?)', array($ETL_date));

        \DB::connection($DB2)->statement('CALL truncate_stage_tables()');
        \DB::connection($DB2)->statement('CALL stage_account_client()');
        \DB::connection($DB2)->statement('CALL stage_sales_master()');
        \DB::connection($DB2)->statement('CALL stage_product_catalog()');

        \DB::connection($DB2)->statement('CALL dim_client()');
        \DB::connection($DB2)->statement('CALL expire_existing_account()');
        \DB::connection($DB2)->statement('CALL new_row_for_changing_account()');
        \DB::connection($DB2)->statement('CALL add_new_account_dimension()');
        \DB::connection($DB2)->statement('CALL update_dim_product_master()');
        \DB::connection($DB2)->statement('CALL add_new_record_dim_product_master()');

        \DB::connection($DB2)->statement('CALL expire_existing_account_product_category()');
        \DB::connection($DB2)->statement('CALL up_new_row_for_changing_product_category()');
        \DB::connection($DB2)->statement('CALL add_new_record_dim_product_category_master()');

        \DB::connection($DB2)->statement('CALL fact_sale_master(?)', array($ETL_date));

        \DB::connection($DB2)->statement('CALL all_asin_details()');
//        \DB::connection($DB2)->statement('CALL tbl_cat_vew_daily(?)', array($ETL_date));
//        \DB::connection($DB2)->statement('CALL tbl_cat_vew_weekly()');
//        \DB::connection($DB2)->statement('CALL tbl_cat_vew_monthly()');
//        \DB::connection($DB2)->statement('CALL tbl_cat_subcat_vew_daily(?)', array($ETL_date));
//        \DB::connection($DB2)->statement('CALL tbl_cat_subcat_vew_weekly()');
//        \DB::connection($DB2)->statement('CALL tbl_cat_subcat_vew_monthly()');
        \DB::connection($DB2)->statement('CALL tbl_cat_subcat_asin_vew_daily(?)', array($ETL_date));
        \DB::connection($DB2)->statement('CALL tbl_cat_subcat_asin_vew_weekly()');
        \DB::connection($DB2)->statement('CALL tbl_cat_subcat_asin_vew_monthly()');
        // check weekly stored procedure is execute or not
        $checkWeeklyStatus = \DB::connection($DB2)->select('SELECT COUNT(1) as count WHERE EXISTS (SELECT * FROM `tbl_cat_subcat_asin_vew_weekly`)');
        Log::info('Check Weekly Status:' . $checkWeeklyStatus[0]->count);
        if ($checkWeeklyStatus[0]->count == 0) { //if this query return you 0 then execute weekly stored procedure again
            \DB::connection($DB2)->statement('CALL tbl_cat_subcat_asin_vew_weekly()');
            $checkWeeklyStatus = \DB::connection($DB2)->select('SELECT COUNT(1) as count WHERE EXISTS (SELECT * FROM `tbl_cat_subcat_asin_vew_weekly`)');
            Log::info('Weekly Status if condition');
            Log::info('Check Weekly Status:' . $checkWeeklyStatus[0]->count);
        }
        // check monthly stored procedure is execute or not
        $checkMonthlyStatus = \DB::connection($DB2)->select('SELECT COUNT(1) as count WHERE EXISTS (SELECT * FROM `tbl_cat_subcat_asin_vew_monthly`)');
        Log::info('Check Monthly Status:' . $checkMonthlyStatus[0]->count);
        if ($checkMonthlyStatus[0]->count == 0) {//if below query return you 0 then execute monthly stored procedure again
            \DB::connection($DB2)->statement('CALL tbl_cat_subcat_asin_vew_monthly()');
            $checkMonthlyStatus = \DB::connection($DB2)->select('SELECT COUNT(1) as count WHERE EXISTS (SELECT * FROM `tbl_cat_subcat_asin_vew_monthly`)');
            Log::info('Monthly Status if condition');
            Log::info('Check Monthly Status:' . $checkMonthlyStatus[0]->count);
        }
        Log::info('End ETL here');
    }
}
