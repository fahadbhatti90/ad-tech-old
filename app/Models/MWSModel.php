<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use DB;
use Carbon\Carbon;

class MWSModel extends Model
{
    protected $tb_ams_api = 'tbl_sc_config';
    public $table = "tbl_sc_config";
    protected $primaryKey = 'mws_config_id';

    public function accounts()
    {
        return $this->hasMany('App\Models\AccountModels\AccountModel', 'fkId')->where("fkAccountType", 2);
    } //end function

    /**
     * @param $data
     * @return bool
     */
    public function addRecord($data)
    {

        $data['created_at'] = date('Y-m-d H:i:s');

        DB::beginTransaction();
        try {
            DB::table('tbl_sc_config')->insert($data);
            DB::commit();
            return true;
        } catch (\Exception $e) {
            DB::rollback();
            return false;
        }
    }

    public function update_api_config($data, $config_id)
    {
        $data['created_at'] = date('Y-m-d H:i:s');
        $data['updated_at'] = date('Y-m-d H:i:s');
        DB::beginTransaction();
        try {
            DB::table('tbl_sc_config')
                ->where('mws_config_id', $config_id)
                ->update($data);
            DB::commit();
            return true;
        } catch (\Exception $e) {
            DB::rollback();
            return false;
        }
    }

    public function delete_mws_config($config_id)
    {
        /*try {
            if (!empty($config_id)){
                DB::table('tbl_sc_config')->where('mws_config_id',$config_id)->delete();
                return true;
            }
        } catch (\Exception $e) {
            DB::rollback();
            return false;
        }*/

        $data['updated_at'] = date('Y-m-d H:i:s');
        $data['is_active'] = 0;
        DB::beginTransaction();
        try {
            DB::table('tbl_sc_config')
                ->where('mws_config_id', $config_id)
                ->update($data);
            DB::commit();
            return true;
        } catch (\Exception $e) {
            DB::rollback();
            return false;
        }
    }

    public function get_merchants()
    {
        try {
            $record = DB::table('tbl_sc_config')->get();
            if ($record) {
                return $record;
            }
        } catch (\Exception $e) {
            DB::rollback();
            return false;
        }
    }

    public function get_active_merchants()
    {
        try {
            $record = DB::table('tbl_sc_config')->where('is_active', 1)->get();
            if ($record) {
                return $record;
            }
        } catch (\Exception $e) {
            DB::rollback();
            return false;
        }
    }

    public function getParameter()
    {

        //$record = DB::table('tbl_sc_config')->get()->first();
        $record = DB::table('tbl_sc_config')
            ->where('is_active', 1)
            ->orderBy('mws_config_id', 'desc')
            ->get();
            //->paginate(10);
        if ($record) {
            return $record;
        }
        return false;
    }

    public static function CountSellers($SellerId)
    {
        try {
            $record = DB::table('tbl_sc_config')
                ->where('seller_id', trim($SellerId))
                ->get();
            return $record;
        } catch (\Exception $e) {
            DB::rollback();
            return false;
        }
    }

    public static function CountExistingSellers($SellerId, $api_config_id)
    {
        try {
            $record = DB::table('tbl_sc_config')
                ->where('seller_id', trim($SellerId))
                ->where('mws_config_id', '!=', trim($api_config_id))
                ->get();
            return $record;
        } catch (\Exception $e) {
            DB::rollback();
            return false;
        }
    }

    public function get_active_crons()
    {
        try {
            //DB::enableQueryLog();
            $record = DB::table('tbl_sc_crons')
                ->where('status', 1)
                ->get();
            if ($record) {
                return $record;
            }
        } catch (\Exception $e) {
            DB::rollback();
            return false;
        }
    }

    public function get_crons_to_run()
    {
        try {
            //DB::enableQueryLog();
            $record = DB::table('tbl_sc_crons')
                // ->where('status' ,1)
                ->get();
            if ($record) {
                return $record;
            }
        } catch (\Exception $e) {
            DB::rollback();
            return false;
        }
    }

    public static function updateCronLastRunDate($data, $task_id)
    {
        // echo 'testing';
        // exit;
        // $data['created_at'] = date('Y-m-d H:i:s');
        // $data['updated_at'] = date('Y-m-d H:i:s');
        $data['updatedAt'] = date('Y-m-d H:i:s');
        DB::beginTransaction();
        try {
            DB::table('tbl_sc_crons')
                ->where('task_id', $task_id)
                ->update($data);
            DB::commit();
            return true;
        } catch (\Exception $e) {
            DB::rollback();
            return false;
        }
    }

    public function getMwsCrons()
    {
        $record = DB::table('tbl_sc_crons')->orderBy('task_id', 'desc')->get();
        if ($record) {
            return $record;
        }
        return false;
    }

    public static function checkCronExist($report_type)
    {
        try {
            $record = DB::table('tbl_sc_crons')
                ->where('report_type', $report_type)
                ->get();
            return $record;
        } catch (\Exception $e) {
            DB::rollback();
            return false;
        }
    }

    public static function checkCronTimeOverlap($report_type)
    {
        try {
            $record = DB::table('tbl_sc_crons')
                ->where('report_type', $report_type)
                ->get();
            return $record;
        } catch (\Exception $e) {
            DB::rollback();
            return false;
        }
    }

    public static function updateExistingCron($data, $report_type)
    {
        $data['updatedAt'] = date('Y-m-d H:i:s');
        DB::beginTransaction();
        try {
            DB::table('tbl_sc_crons')
                ->where('report_type', $report_type)
                ->update($data);
            DB::commit();
            return true;
        } catch (\Exception $e) {
            DB::rollback();
            return false;
        }
    }

    public function update_cron_status($data, $task_id)
    {
        // $data['created_at'] = date('Y-m-d H:i:s');
        // $data['updated_at'] = date('Y-m-d H:i:s');
        DB::beginTransaction();
        try {
            DB::table('tbl_sc_crons')
                ->where('task_id', $task_id)
                ->update($data);
            DB::commit();
            return true;
        } catch (\Exception $e) {
            DB::rollback();
            return false;
        }
    }

    public function addCron($data)
    {
        $data['createdAt'] = date('Y-m-d H:i:s');
        //$data['updatedAt'] = date('Y-m-d H:i:s');
        DB::beginTransaction();
        try {
            DB::table('tbl_sc_crons')->insert($data);
            DB::commit();
            return true;
        } catch (\Exception $e) {
            DB::rollback();
            return false;
        }
    }

    public function update_cron($data, $task_id)
    {
        // $data['created_at'] = date('Y-m-d H:i:s');
        // $data['updated_at'] = date('Y-m-d H:i:s');
        DB::beginTransaction();
        try {
            DB::table('tbl_sc_crons')
                ->where('task_id', $task_id)
                ->update($data);
            DB::commit();
            return true;
        } catch (\Exception $e) {
            DB::rollback();
            return false;
        }
    }

    public function delete_mws_cron($task_id)
    {

        try {
            if (!empty($task_id)) {
                DB::table('tbl_sc_crons')->where('task_id', $task_id)->delete();
                return true;
            }
        } catch (\Exception $e) {
            DB::rollback();
            return false;
        }
    }

    public static function insert_mws_report_request($storeArray)
    {

        try {
            $storeArray['Acknowledged'] = 'false';
            $storeArray['created_at'] = date('Y-m-d H:i:s');
            $storeArray['updated_at'] = date('Y-m-d H:i:s');


            DB::table('tbl_sc_requested_reports')->Insert($storeArray);
            return true;
        } catch (\Exception $e) {
            DB::rollback();
            return false;
        }
    }

    public static function get_mws_submitted_request($mws_config_id)
    {
        try {
            $record = DB::table('tbl_sc_requested_reports')->select('ReportRequestId')
                ->where('fk_merchant_id', $mws_config_id)
                ->whereIn('ReportProcessingStatus', ['_SUBMITTED_', '_IN_PROGRESS_'])->get()->toArray();
            return $record;
        } catch (\Exception $e) {
            DB::rollback();
            return false;
        }
    }

    public static function update_mws_report_request_status($storeArray, $ReportRequestId)
    {

        try {
            $storeArray['updated_at'] = date('Y-m-d H:i:s');
            DB::table('tbl_sc_requested_reports')->where('ReportRequestId', $ReportRequestId)->update($storeArray);
            return true;
        } catch (\Exception $e) {
            DB::rollback();
            return false;
        }
    }

    public static function update_mws_report_id($storeArray, $ReportRequestId)
    {

        try {
            $storeArray['updated_at'] = date('Y-m-d H:i:s');
            DB::table('tbl_sc_requested_reports')->where('ReportRequestId', $ReportRequestId)->update($storeArray);
            return true;
        } catch (\Exception $e) {
            DB::rollback();
            return false;
        }
    }

    public static function get_mws_done_request($mws_config_id)
    {
        // DB::enableQueryLog();
        try {
            $record = DB::table('tbl_sc_requested_reports')->select('ReportRequestId')
                ->where('ReportProcessingStatus', '=', '_DONE_')
                ->where('fk_merchant_id', $mws_config_id)
                ->whereNotIn('Acknowledged', ['true'])
                //->Where('Acknowledged','!=', 'true')
                ->orWhereNull('Acknowledged')
                ->get();
            return $record;
        } catch (\Exception $e) {
            DB::rollback();
            return false;
        }
    }

    public static function check_mws_existing_reports($report_types, $mws_config_id)
    {
        try {

            $record = DB::table('tbl_sc_requested_reports')->select('*')
                ->where('fk_merchant_id', $mws_config_id)
                ->where('ReportType', $report_types)
                ->whereNotIn('ReportProcessingStatus', ['_CANCELLED_'])
                ->whereDate('created_at', Carbon::today())
                ->get();

            return $record;
        } catch (\Exception $e) {
            DB::rollback();
            return false;
        }
    }

    public static function get_mws_done_reports($report_type, $mws_config_id, $metrics_type)
    {

        $record = DB::table('tbl_sc_requested_reports')
            ->select('id', 'ReportRequestId', 'fk_merchant_id', 'fkAccountId', 'fkBatchId', 'GeneratedReportId', 'reportRequestDate')
            ->where('ReportProcessingStatus', '=', '_DONE_')
            ->where('report_acknowledgement', '=', 'false')
            ->where('fk_merchant_id', $mws_config_id)
            ->where('metricsType', $metrics_type)
            ->where('ReportType', '=', trim($report_type))
            /*->where(function ($record) {
                $record->where('Acknowledged','false')
                    ->orWhereNull('Acknowledged');
            }
            )*/ ->get()->toArray();

        return $record;
    }

    public static function insert_mws_ScOrdersUpdtReport($storeArray)
    {
        try {
            //echo 'model success';
            // $storeArray['createdAt'] = date('Y-m-d H:i:s');
            //$storeArray['updatedAt'] = date('Y-m-d H:i:s');
            DB::table('tbl_sc_sales_orders_updt_report')->insert($storeArray);
            return true;
        } catch (\Exception $e) {
            DB::rollback();
            return false;
        }
    }

    public static function insert_mws_ScOrdersReport($storeArray)
    {
        try {
            //echo 'model success';
            //$storeArray['createdAt'] = date('Y-m-d H:i:s');
            //$storeArray['updatedAt'] = date('Y-m-d H:i:s');
            DB::table('tbl_sc_sales_orders_report')->insert($storeArray);
            return true;
        } catch (\Exception $e) {
            DB::rollback();
            return false;
        }
    }

    public static function insert_mws_ScCatActiveReport($storeArray)
    {

        try {
            //echo 'model success';
            // $storeArray['createdAt'] = date('Y-m-d H:i:s');
            //$storeArray['updatedAt'] = date('Y-m-d H:i:s');
            DB::table('tbl_sc_inventory_cat_active_report')->Insert($storeArray);
            return true;
        } catch (\Exception $e) {
            DB::rollback();
            return false;
        }
        /* $storeArray['createdAt'] = date('Y-m-d H:i:s');
         $storeArray['updatedAt'] = date('Y-m-d H:i:s');
         DB::table('tbl_sc_inventory_cat_active_report')->Insert($storeArray);*/
    }

    public static function insert_mws_CatalogScCatActiveReport($storeArray)
    {
        try {
            //$storeArray['createdAt'] = date('Y-m-d H:i:s');
            //$storeArray['updatedAt'] = date('Y-m-d H:i:s');
            DB::table('tbl_sc_catalog_cat_active_report')->Insert($storeArray);
            return true;
        } catch (\Exception $e) {
            DB::rollback();
            return false;
        }
        /* $storeArray['createdAt'] = date('Y-m-d H:i:s');
         $storeArray['updatedAt'] = date('Y-m-d H:i:s');
         DB::table('tbl_sc_inventory_cat_active_report')->Insert($storeArray);*/
    }

    public static function insert_mws_ScCatInactiveReport($storeArray)
    {

        try {
            //echo 'model success';
            //$storeArray['createdAt'] = date('Y-m-d H:i:s');
            // $storeArray['updatedAt'] = date('Y-m-d H:i:s');
            DB::table('tbl_sc_catalog_cat_inactive_report')->Insert($storeArray);
            return true;
        } catch (\Exception $e) {
            DB::rollback();
            return false;
        }
    }

    /*Data not found reports starts*/
    public static function insert_mws_ScFbaReceiptReport($storeArray)
    {

        try {
            //echo 'model success';
            //$storeArray['createdAt'] = date('Y-m-d H:i:s');
            //$storeArray['updatedAt'] = date('Y-m-d H:i:s');
            DB::table('tbl_sc_inventory_fba_receipt_report')->Insert($storeArray);
            return true;
        } catch (\Exception $e) {
            DB::rollback();
            return false;
        }
    }

    public static function insert_mws_ScFbaReturnsReport($storeArray)
    {

        try {
            // $storeArray['createdAt'] = date('Y-m-d H:i:s');
            //$storeArray['updatedAt'] = date('Y-m-d H:i:s');
            DB::table('tbl_sc_sales_fba_returns_report')->Insert($storeArray);
            return true;
        } catch (\Exception $e) {
            DB::rollback();
            return false;
        }
    }

    public static function insert_mws_CatalogScFbaHealthReport($storeArray)
    {
        try {
            //$storeArray['createdAt'] = date('Y-m-d H:i:s');
            //$storeArray['updatedAt'] = date('Y-m-d H:i:s');
            DB::table('tbl_sc_catalog_fba_health_report')->Insert($storeArray);
            return true;
        } catch (\Exception $e) {
            DB::rollback();
            return false;
        }
    }

    public static function insert_mws_ScFbaHealthReport($storeArray)
    {
        try {
            //$storeArray['createdAt'] = date('Y-m-d H:i:s');
            // $storeArray['updatedAt'] = date('Y-m-d H:i:s');
            DB::table('tbl_sc_inventory_fba_health_report')->Insert($storeArray);
            return true;
        } catch (\Exception $e) {
            DB::rollback();
            return false;
        }
    }

    public static function insert_mws_ScFbaRestockReport($storeArray)
    {

        try {
            $storeArray['createdAt'] = date('Y-m-d H:i:s');
            //$storeArray['updatedAt'] = date('Y-m-d H:i:s');
            DB::table('tbl_sc_fba_restock_report')->Insert($storeArray);
            return true;
        } catch (\Exception $e) {
            DB::rollback();
            return false;
        }
    }

    public static function insert_mws_ScMfnReturnsReport($storeArray)
    {
        //$storeArray['createdAt'] = date('Y-m-d H:i:s');
        //$storeArray['updatedAt'] = date('Y-m-d H:i:s');
        DB::table('tbl_sc_sales_mfn_returns_report')->Insert($storeArray);
    }

    public static function update_mws_report_acknowledgement($fk_request_id, $acknowledgement_status)
    {
        //$storeArray['updatedAt'] = date('Y-m-d H:i:s');

        try {
            $storeArray['updated_at'] = date('Y-m-d H:i:s');
            $storeArray['report_acknowledgement'] = $acknowledgement_status;
            DB::table('tbl_sc_requested_reports')
                // ->where('ReportId',$report_id )
                ->where('id', $fk_request_id)
                ->update($storeArray);
            return true;
        } catch (\Exception $e) {
            DB::rollback();
            return false;
        }
    }

    public static function insert_mws_Activity($activity, $cron_type, $file_path)
    {
        try {
            $data['activity'] = $activity;
            $data['cron_type'] = $cron_type;
            $data['file_path'] = $file_path;
            $data['activity_time'] = date('Y-m-d H:i:s');
            //  $storeArray['updatedAt'] = date('Y-m-d H:i:s');
            DB::table('tbl_sc_activity_tracker')->Insert($data);
            return true;
        } catch (\Exception $e) {
            DB::rollback();
            return false;
        }
    }

    public static function insert_failed_job($data)
    {
        try {

            $data['createdAt'] = date('Y-m-d H:i:s');
            //  $storeArray['updatedAt'] = date('Y-m-d H:i:s');
            DB::table('tbl_sc_failed_reports_request')->Insert($data);
            return true;
        } catch (\Exception $e) {
            DB::rollback();
            return false;
        }
    }

    public static function get_report_excel($start_date, $end_date, $table_name)
    {

        /*$start = Carbon::parse($request->start)->startOfDay();  //2016-09-29 00:00:00.000000
        $end = Carbon::parse($request->end)->endOfDay(); //2016-09-29 23:59:59.000000
        $clicks->dateBetween($start, $end);*/
        try {
            $record = DB::table($table_name)
                ->whereBetween('createdAt', [$start_date, $end_date])->get();
            //>whereBetween('createdAt', ['2019-09-19', '2019-09-21'])
            //->whereDate('created_at', Carbon::today())
            //->get();
            if ($record) {
                return $record;
            }
        } catch (\Exception $e) {
            DB::rollback();
            return false;
        }
    }
    /*$users = User::select("users.*")

    ->whereBetween('created', ['2018-02-01', '2018-02-10'])

    ->get();*/


    //dd($users);
    public static function get_userdata()
    {
        try {
            $record = DB::table('tbl_sc_config')->where('is_active', 1)->get();
            if ($record) {
                return $record;
            }
        } catch (\Exception $e) {
            DB::rollback();
            return false;
        }
    }

    public static function get_user2data()
    {
        try {
            $record = DB::table('tbl_sc_config')->where('is_active', 1)->get();
            if ($record) {
                return $record;
            }
        } catch (\Exception $e) {
            DB::rollback();
            return false;
        }
    }

    public static function get_sc_product_ids()
    {
        DB::beginTransaction();
        try {
            $record = DB::table('tbl_sc_product_ids')
                ->select('id', 'asin','idType', 'fkAccountId', 'fkBatchId', 'source', 'fkSellerConfigId')
                ->take(10000)
                ->where('productDetailsDownloaded', 0)
                ->where('productDetailsInQueue', 0)
                ->get();
            DB::commit();
                return $record;
        } catch (\Exception $e) {
            DB::rollback();
            return false;
        }
    }

    public static function getSellerDetailsById($sellerConfigId)
    {
        DB::beginTransaction();
        try {
            $record = DB::table('tbl_sc_config')
                ->select('mws_config_id','seller_id','mws_access_key_id','mws_secret_key','mws_authtoken')
                ->take(1)
                ->where('mws_config_id', $sellerConfigId)
                ->get();
            DB::commit();
                return $record;
        } catch (\Exception $e) {
            DB::rollback();
            return false;
        }
    }

    public static function get_sc_product_ids_for_categories()
    {
        DB::beginTransaction();
        try {
            $record = DB::table('tbl_sc_product_ids')
                ->take(500)
                ->where('productCategoryDetailsDownloaded', 0)
                ->where('productCategoryDetailsInQueue', 0)
                ->get();
            DB::commit();
                return $record;
        } catch (\Exception $e) {
            DB::rollback();
            return false;
        }
    }

    public static function insert_product_details($storeArray)
    {
        try {
            //echo 'model success';

            // $storeArray['updatedAt'] = date('Y-m-d H:i:s');
            DB::table('tbl_sc_product_details')->Insert($storeArray);
            return true;
        } catch (\Exception $e) {
            DB::rollback();
            return false;
        }
    }

    public static function insert_product_sales_rank($storeArray)
    {
        DB::beginTransaction();
        try {
            DB::table('tbl_sc_sales_rank')->Insert($storeArray);
            DB::commit();
            return true;
        } catch (\Exception $e) {
            DB::rollback();
            return false;
        }
    }
    public static function update_product_download_status($storeArray, $product_tbl_id)
    {
        DB::beginTransaction();
        try {
            $storeArray['updatedAt'] = date('Y-m-d H:i:s');
            DB::table('tbl_sc_product_ids')->where('id', $product_tbl_id)->update($storeArray);
            DB::commit();
            return true;
        } catch (\Exception $e) {
            DB::rollback();
            return false;
        }
    }

    //Update product id table to put ids in queue
    public static function updateCategoriesInQueue()
    {
        DB::beginTransaction();
        try {
            $storeArray = array();
            $storeArray['updatedAt'] = date('Y-m-d H:i:s');
            $storeArray['productCategoryDetailsInQueue'] = 1;
            DB::table('tbl_sc_product_ids')->where('productCategoryDetailsDownloaded', 0)->where('productCategoryDetailsInQueue', 0)->limit(500)->update($storeArray);
            DB::commit();
            return true;
        } catch (\Exception $e) {
            DB::rollback();
            return false;
        }
    }

    public static function updateProductDetailsInQueue()
    {
        DB::beginTransaction();
        try {
            $storeArray = array();
            $storeArray['updatedAt'] = date('Y-m-d H:i:s');
            $storeArray['productDetailsInQueue'] = 1;
            DB::table('tbl_sc_product_ids')->where('productDetailsDownloaded', 0)->where('productDetailsInQueue', 0)->limit(10000)->update($storeArray);
            DB::commit();
            return true;
        } catch (\Exception $e) {
            DB::rollback();
            return false;
        }
    }

    public static function insert_product_category_details($storeArray)
    {
        DB::beginTransaction();
        try {
            DB::table('tbl_sc_product_category_details')->Insert($storeArray);
            DB::commit();
            return true;
        } catch (\Exception $e) {
            DB::rollback();
            return false;
        }
    }

    public static function insert_product_categories($storeArray)
    {
        try {
            //echo 'model success';

            // $storeArray['updatedAt'] = date('Y-m-d H:i:s');
            DB::table('tbl_product_categories')->Insert($storeArray);
            return true;
        } catch (\Exception $e) {
            echo $e;
            DB::rollback();
            return false;
        }
    }

    public static function update_product_category_download_status($storeArray, $product_tbl_id)
    {

        try {
            $storeArray['updatedAt'] = date('Y-m-d H:i:s');
            DB::table('productCategoryDetailsDownloaded')->where('id', $product_tbl_id)->update($storeArray);
            return true;
        } catch (\Exception $e) {
            DB::rollback();
            return false;
        }
    }

    public static function get_asin_duplicate_count($AccountId1, $asin)
    {
        try {
            //DB::enableQueryLog();
            $record = DB::table('tbl_sc_product_ids')
                ->where('fkAccountId', $AccountId1)
                ->where('asin', $asin)
                ->where('source', 'SC')
                ->count();
            //->get();
            if ($record) {
                return $record;
            }
        } catch (\Exception $e) {
            DB::rollback();
            return false;
        }
    }

    public static function get_categories_duplicate_count($productCategoryId, $singleCatAsin, $singleCatCategoryTreeSequence, $singleCatCategoryTreeNumber, $fkAccountId)
    {
        try {
            //DB::enableQueryLog();
            $record = DB::table('tbl_product_categories')
                ->where('productCategoryId', $productCategoryId)
                ->where('asin', $singleCatAsin)
                ->where('categoryTreeSequence', $singleCatCategoryTreeSequence)
                ->where('categoryTreeNumber', $singleCatCategoryTreeNumber)
                ->where('fkAccountId', $fkAccountId)
                ->count();
            //->get();
            if ($record) {
                return $record;
            }
        } catch (\Exception $e) {
            DB::rollback();
            return false;
        }
    }

    public static function getScCategoriesDuplicateCount($fkAccountId, $asinValue, $source)
    {
        try {
            //DB::enableQueryLog();
            $record = DB::table('tbl_sc_product_category_details')
                ->where('productCategoryId', $productCatId)
                ->where('fkAccountId', $productCatFkAccountId)
                ->where('asin', $productCatAsin)
                ->where('categoryTreeSequence', $productCatTreeSequence)
                ->where('isActive', 1)
                ->count();
            //->get();
            if ($record) {
                return $record;
            }
        } catch (\Exception $e) {
            DB::rollback();
            return false;
        }
    }

    public static function getScProductDetailsDuplicateCount($fkAccountId, $asinValue, $source)
    {
        DB::beginTransaction();
        try {
            $record = DB::table('tbl_sc_product_details')
                ->where('fkAccountId', $fkAccountId)
                ->where('asin', $asinValue)
                ->where('source', $source)
                ->where('isActive', 1)
                ->count('id');
            DB::commit();
                return $record;
        } catch (\Exception $e) {
            DB::rollback();
            return false;
        }
    }

    public static function get_asin_catalog_cat_active_report()
    {
        try {
            $record = DB::table('tbl_sc_catalog_cat_active_report')
                ->select('tbl_sc_catalog_cat_active_report.asin1', 'tbl_sc_requested_reports.fk_merchant_id', 'tbl_sc_catalog_cat_active_report.fkAccountId', 'tbl_sc_catalog_cat_active_report.fkBatchId')
                ->join('tbl_sc_requested_reports', 'tbl_sc_catalog_cat_active_report.fkRequestId', '=', 'tbl_sc_requested_reports.id')
                ->whereDate('createdAt', Carbon::today())
                ->distinct()
                //->get(['asin1'])
                ->get()
                ->toArray();
            return $record;
        } catch (\Exception $e) {
            DB::rollback();
            return false;
        }
    }

    public static function get_asin_catalog_cat_inactive_report()
    {
        try {
            // ,'fkAccountId','fkBatchId'
            $record = DB::table('tbl_sc_catalog_cat_inactive_report')
                // ->select('asin1', 'fk_merchant_id','fkAccountId','fkBatchId')
                ->select('tbl_sc_catalog_cat_inactive_report.asin1', 'tbl_sc_requested_reports.fk_merchant_id', 'tbl_sc_catalog_cat_inactive_report.fkAccountId', 'tbl_sc_catalog_cat_inactive_report.fkBatchId')
                ->join('tbl_sc_requested_reports', 'tbl_sc_catalog_cat_inactive_report.fkRequestId', '=', 'tbl_sc_requested_reports.id')
                ->whereDate('createdAt', Carbon::today())
                ->distinct()
                ->get()->toArray();
            return $record;
        } catch (\Exception $e) {
            DB::rollback();
            return false;
        }
    }

    public static function get_asin_catalog_fba_health_report()
    {
        try {
            $record = DB::table('tbl_sc_catalog_fba_health_report')
                //->select('asin', 'fk_merchant_id')
                ->select('tbl_sc_catalog_fba_health_report.asin', 'tbl_sc_requested_reports.fk_merchant_id', 'tbl_sc_catalog_fba_health_report.fkAccountId', 'tbl_sc_catalog_fba_health_report.fkBatchId')
                ->join('tbl_sc_requested_reports', 'tbl_sc_catalog_fba_health_report.fkRequestId', '=', 'tbl_sc_requested_reports.id')
                ->whereDate('createdAt', Carbon::today())
                ->distinct()
                ->get()->toArray();
            return $record;
        } catch (\Exception $e) {
            DB::rollback();
            return false;
        }
    }

    public static function get_asin_inventory_cat_active_report()
    {
        try {
            $record = DB::table('tbl_sc_inventory_cat_active_report')
                //->select('asin1', 'fk_merchant_id')
                ->select('tbl_sc_inventory_cat_active_report.asin1', 'tbl_sc_requested_reports.fk_merchant_id', 'tbl_sc_inventory_cat_active_report.fkAccountId', 'tbl_sc_inventory_cat_active_report.fkBatchId')
                ->join('tbl_sc_requested_reports', 'tbl_sc_inventory_cat_active_report.fkRequestId', '=', 'tbl_sc_requested_reports.id')
                ->whereDate('createdAt', Carbon::today())
                ->distinct()
                ->get()->toArray();
            return $record;
        } catch (\Exception $e) {
            DB::rollback();
            return false;
        }
    }

    public static function get_asin_inventory_fba_health_report()
    {
        try {
            $record = DB::table('tbl_sc_inventory_fba_health_report')
                //->select('asin', 'fk_merchant_id')
                ->select('tbl_sc_inventory_fba_health_report.asin', 'tbl_sc_requested_reports.fk_merchant_id', 'tbl_sc_inventory_fba_health_report.fkAccountId', 'tbl_sc_inventory_fba_health_report.fkBatchId')
                ->join('tbl_sc_requested_reports', 'tbl_sc_inventory_fba_health_report.fkRequestId', '=', 'tbl_sc_requested_reports.id')
                ->whereDate('createdAt', Carbon::today())
                ->distinct()
                ->get()->toArray();
            return $record;
        } catch (\Exception $e) {
            DB::rollback();
            return false;
        }
    }

    public static function get_asin_inventory_fba_receipt_report()
    {
        try {
            $record = DB::table('tbl_sc_inventory_fba_receipt_report')
                //->select('sku', 'fk_merchant_id')
                ->join('tbl_sc_requested_reports', 'tbl_sc_inventory_fba_receipt_report.fkRequestId', '=', 'tbl_sc_requested_reports.id')
                ->whereDate('createdAt', Carbon::today())
                ->distinct()
                ->get()->toArray();
            return $record;
        } catch (\Exception $e) {
            DB::rollback();
            return false;
        }
    }

    public static function get_asin_sales_fba_returns_report()
    {
        try {
            $record = DB::table('tbl_sc_sales_fba_returns_report')
                //->select('asin', 'fk_merchant_id')
                ->select('tbl_sc_sales_fba_returns_report.asin', 'tbl_sc_requested_reports.fk_merchant_id', 'tbl_sc_sales_fba_returns_report.fkAccountId', 'tbl_sc_sales_fba_returns_report.fkBatchId')
                ->join('tbl_sc_requested_reports', 'tbl_sc_sales_fba_returns_report.fkRequestId', '=', 'tbl_sc_requested_reports.id')
                ->whereDate('createdAt', Carbon::today())
                ->distinct()
                ->get()->toArray();
            return $record;
        } catch (\Exception $e) {
            DB::rollback();
            return false;
        }
    }

    public static function get_asin_sc_sales_orders_report()
    {
        try {
            $record = DB::table('tbl_sc_sales_orders_report')
                //->select('asin', 'fk_merchant_id')
                ->select('tbl_sc_sales_orders_report.asin', 'tbl_sc_requested_reports.fk_merchant_id', 'tbl_sc_sales_orders_report.fkAccountId', 'tbl_sc_sales_orders_report.fkBatchId')
                ->join('tbl_sc_requested_reports', 'tbl_sc_sales_orders_report.fkRequestId', '=', 'tbl_sc_requested_reports.id')
                ->whereDate('createdAt', Carbon::today())
                ->distinct()
                ->get()->toArray();
            return $record;
        } catch (\Exception $e) {
            DB::rollback();
            return false;
        }
    }

    public static function get_asin_sc_sales_orders_updt_report()
    {
        try {
            $record = DB::table('tbl_sc_sales_orders_updt_report')
                //->select('asin', 'fk_merchant_id')
                ->select('tbl_sc_sales_orders_updt_report.asin', 'tbl_sc_requested_reports.fk_merchant_id', 'tbl_sc_sales_orders_updt_report.fkAccountId', 'tbl_sc_sales_orders_updt_report.fkBatchId')
                ->join('tbl_sc_requested_reports', 'tbl_sc_sales_orders_updt_report.fkRequestId', '=', 'tbl_sc_requested_reports.id')
                ->whereDate('createdAt', Carbon::today())
                ->distinct()
                ->get()->toArray();
            return $record;
        } catch (\Exception $e) {
            DB::rollback();
            return false;
        }
    }

    public static function get_asin_sc_sales_mfn_returns_report()
    {
        try {
            $record = DB::table('tbl_sc_sales_mfn_returns_report')
                //->select('asin', 'fk_merchant_id')
                ->select('tbl_sc_sales_mfn_returns_report.asin', 'tbl_sc_requested_reports.fk_merchant_id', 'tbl_sc_sales_mfn_returns_report.fkAccountId', 'tbl_sc_sales_mfn_returns_report.fkBatchId')
                ->join('tbl_sc_requested_reports', 'tbl_sc_sales_mfn_returns_report.fkRequestId', '=', 'tbl_sc_requested_reports.id')
                ->whereDate('createdAt', Carbon::today())
                ->distinct()
                ->get()->toArray();
            return $record;
        } catch (\Exception $e) {
            DB::rollback();
            return false;
        }
    }

    public static function insert_report_asin($storeArray)
    {
        try {
            DB::table('tbl_sc_product_ids')->Insert($storeArray);
            return true;
        } catch (\Exception $e) {
            DB::rollback();
            return false;
        }
    }

    /**
     * @return bool
     */
    public static function update_customer_historical_data_status($mws_config_id)
    {
        try {
            $storeArray['historical_data_downloaded'] = 1;
            $storeArray['updated_at'] = date('Y-m-d H:i:s');
            DB::table('tbl_sc_config')->where('is_active', '1')->where('historical_data_downloaded', '0')->where('mws_config_id', $mws_config_id)->update($storeArray);
            return true;
        } catch (\Exception $e) {
            echo $e;
            DB::rollback();
            return false;
        }
    }

    public static function count_active_crons()
    {
        /*echo 'test';
        exit;*/
        try {
            //DB::enableQueryLog();
            $record = DB::table('tbl_sc_crons')
                ->where('status', 1)->count();
            //->get();
            if ($record) {
                return $record;
            }
        } catch (\Exception $e) {
            DB::rollback();
            return false;
        }
    }

    public static function count_crons_run_today()
    {

        try {
            //DB::enableQueryLog();
            $record = DB::table('tbl_sc_crons')
                ->where('isCronRunning', 0)
                ->whereDate('getReportLastRun', Carbon::today())->count();
            //->get();
            if ($record) {
                return $record;
            }
        } catch (\Exception $e) {
            DB::rollback();
            return false;
        }
    }

    public static function count_total_crons()
    {
        /*echo 'test';
        exit;*/
        try {
            //DB::enableQueryLog();
            $record = DB::table('tbl_sc_crons')
                ->count();
            //->get();
            if ($record) {
                return $record;
            }
        } catch (\Exception $e) {
            DB::rollback();
            return false;
        }
    }

    public static function check_sc_account_exist($mws_config_id)
    {
        try {
            $record = DB::table('tbl_account')
                ->where('fkId', $mws_config_id)
                ->where('fkAccountType', 2)
                ->count();
            if ($record) {
                return $record;
            }
        } catch (\Exception $e) {
            DB::rollback();
            return false;
        }
    }

    public static function get_sc_account_id($mws_config_id)
    {
        try {

            $record = DB::table('tbl_account')
                ->where('fkId', $mws_config_id)
                ->where('fkAccountType', 2)
                ->take(1)
                ->get();
            if ($record) {
                return $record;
            }
        } catch (\Exception $e) {
            DB::rollback();
            return false;
        }
    }

    public static function get_sc_daily_batch_id($sc_account_id)
    {
        DB::beginTransaction();
        try {
            $report_date = date('Ymd', strtotime('-1 day', time()));
            $record = DB::table('tbl_batch_id')
                ->where('fkAccountId', $sc_account_id)
                ->where('reportDate', $report_date)
                ->take(1)
                ->get();
            DB::commit();
            return $record;

        } catch (\Exception $e) {
            DB::rollback();
            return false;
        }
    }

    public static function getScAsinToCopy()
    {
        DB::beginTransaction();
        try {
            $record = DB::table('tbl_sc_product_ids')
                ->select('id', 'asin', 'fkAccountId', 'fkBatchId', 'source', 'fkSellerConfigId')
                ->where('productDetailsDownloaded', '>', 0)
                ->where('productCategoryDetailsDownloaded', '>', 0)
                ->where('productSalesRankCoppied', 0)
                ->take(4000)
                ->get();
            DB::commit();
            if ($record) {
                return $record;
            }
        } catch (\Exception $e) {
            DB::rollback();
            return false;
        }
    }

    /**
     * @param $productTblAsin
     * @param $accountId
     * @param $categoryTreeNo
     * @uses in App\Console\Commands\copyProductSalesRank
     */
    public static function getScAsinCategoryId($productTblAsin, $accountId, $categoryTreeNo)
    {
        DB::beginTransaction();
        try {
            $record = DB::table('tbl_sc_product_category_details')
                ->select('id', 'asin', 'productCategoryId', 'productCategoryName', 'categoryTreeSequence')
                ->where('asin', $productTblAsin)
                ->where('fkAccountId', $accountId)
                ->where('isActive', 1)
                ->where('categoryTreeNumber', $categoryTreeNo)
                ->where(function ($query) use ($productTblAsin, $categoryTreeNo) {
                    $query->
                    where(
                        'categoryTreeSequence',
                        '=',
                        Db::raw("(select max(categoryTreeSequence) from tbl_sc_product_category_details where asin = '" . $productTblAsin . "' and categoryTreeNumber='" . $categoryTreeNo . "' and isActive='1'
                         )")
                    )
                        ->orWhere(
                            'categoryTreeSequence',
                            '=',
                            Db::raw("(select min(categoryTreeSequence) from tbl_sc_product_category_details where asin = '" . $productTblAsin . "' and categoryTreeNumber='" . $categoryTreeNo . "' and isActive='1')")
                        );
                })
                ->take(2)
                ->get();
            DB::commit();
                return $record;
        } catch (\Exception $e) {
            DB::rollback();
            return false;
        }
    }

    public static function updateSalesRankCopystatus($storeArray, $product_tbl_id)
    {
        DB::beginTransaction();
        try {
            $storeArray['updatedAt'] = date('Y-m-d H:i:s');
            DB::table('tbl_sc_product_ids')->where('id', $product_tbl_id)->update($storeArray);
            DB::commit();
            return true;
        } catch (\Exception $e) {
            DB::rollback();
            return false;
        }
    }

    public static function getScSalesRankDataToCopy($productCategoryId, $productTblAsin, $accountId)
    {
        DB::beginTransaction();
        try {
            $record = DB::table('tbl_sc_sales_rank')
                ->select('salesRank')
                ->where('asin', $productTblAsin)
                ->where('productCategoryId', $productCategoryId)
                ->where('fkAccountId', $accountId)
                ->where('isActive',1)
                ->take(1)
                ->get();
            DB::commit();
            return $record;
        } catch (\Exception $e) {
            DB::rollback();
            return false;
        }
    }

    public static function scInsertSalesRankData($storeArray)
    {
        DB::beginTransaction();
        try {
            DB::table('tbl_sc_processed_sales_rank')->insert($storeArray);
            DB::commit();
            return true;
        } catch (\Exception $e) {
            DB::rollback();
            return false;
        }
    }

    public static function scCheckSalesRankExist($productTblAsin, $accountId)
    {
        try {
            DB::beginTransaction();
            $record = DB::table('tbl_sc_processed_sales_rank')
                ->where('asin', $productTblAsin)
                ->where('fkAccountId', $accountId)
                ->where('isActive', 1)
                ->count('id');
            DB::commit();
                return $record;
        } catch (\Exception $e) {
            DB::rollback();
            return false;
        }
    }

    /**
     * @return bool
     * @uses in App\Console\Commands\scUpdateProductStatus
     */
    public static function resetAllProductDetailsDownloadedStatus()
    {
        $data = array();
        $data['productDetailsDownloaded'] = 0;
        //$data['productDetailsInQueue'] = 0;
        $data['updatedAt'] = date('Y-m-d H:i:s');
        DB::beginTransaction();
        try {
            DB::table('tbl_sc_product_ids')
                ->where('productDetailsDownloaded', '>', 0)
                //->where('productDetailsInQueue', '>', 0)
                ->update($data);
            DB::commit();
            return true;
        } catch (\Exception $e) {
            DB::rollback();
            return false;
        }
    }
    /**
     * @return bool
     * @uses in App\Console\Commands\scUpdateProductStatus
     */
    public static function resetAllProductDetailsInQueueStatus()
    {
        $data = array();
        //$data['productDetailsDownloaded'] = 0;
        $data['productDetailsInQueue'] = 0;
        $data['updatedAt'] = date('Y-m-d H:i:s');
        DB::beginTransaction();
        try {
            DB::table('tbl_sc_product_ids')
                //->where('productDetailsDownloaded', '>', 0)
                ->where('productDetailsInQueue', '>', 0)
                ->update($data);
            DB::commit();
            return true;
        } catch (\Exception $e) {
            DB::rollback();
            return false;
        }
    }

    /**
     * @return bool
     * @uses in App\Console\Commands\scUpdateProductStatus
     */
    public static function resetAllProductSalesRankCoppiedStatus()
    {
        $data = array();
        $data['productSalesRankCoppied'] = 0;
        $data['updatedAt'] = date('Y-m-d H:i:s');
        DB::beginTransaction();
        try {
            DB::table('tbl_sc_product_ids')
                ->where('productSalesRankCoppied', '>', 0)
                ->update($data);
            DB::commit();
            return true;
        } catch (\Exception $e) {
            DB::rollback();
            return false;
        }
    }

    /**
     * @return bool
     * @uses in App\Console\Commands\scUpdateProductStatus
     */
    public static function resetAllProductDetailsIsActiveStatus()
    {
        $data = array();
        //$data['productDetailsDownloaded'] = 0;
        $data['isActive'] = 0;
        $data['updatedAt'] = date('Y-m-d H:i:s');
        DB::beginTransaction();
        try {
            DB::table('tbl_sc_product_details')
                ->where('isActive', 1)
                ->update($data);
            DB::commit();
            return true;
        } catch (\Exception $e) {
            DB::rollback();
            return false;
        }
    }

    /**
     * @return bool
     * @uses in App\Console\Commands\scUpdateProductStatus
     */
    public static function resetAllProductSalesRankIsActiveStatus()
    {
        $data = array();
        //$data['productDetailsDownloaded'] = 0;
        $data['isActive'] = 0;
        $data['updatedAt'] = date('Y-m-d H:i:s');
        DB::beginTransaction();
        try {
            DB::table('tbl_sc_sales_rank')
                ->where('isActive', 1)
                ->update($data);
            DB::commit();
            return true;
        } catch (\Exception $e) {
            DB::rollback();
            return false;
        }
    }

    /**
     * @return bool
     * @uses in App\Console\Commands\scUpdateProductStatus
     */
    public static function resetAllProductProcessedSalesRankIsActiveStatus()
    {
        $data = array();
        $data['isActive'] = 0;
        $data['updatedAt'] = date('Y-m-d H:i:s');
        DB::beginTransaction();
        try {
            DB::table('tbl_sc_processed_sales_rank')
                ->where('isActive', 1)
                ->update($data);
            DB::commit();
            return true;
        } catch (\Exception $e) {
            DB::rollback();
            return false;
        }
    }

    /**
     * @param $productTblAsin
     * @param $accountId
     * @uses in App\Console\Commands\copyProductSalesRank
     */
    public static function getScTreeNo($productTblAsin, $accountId)
    {
        DB::beginTransaction();
        try {
            $record = DB::table('tbl_sc_product_category_details')
                ->where('asin', $productTblAsin)
                ->where('fkAccountId', $accountId)
                ->where('isActive', 1)
                ->max('categoryTreeNumber');
            DB::commit();
                return $record;
        } catch (\Exception $e) {
            DB::rollback();
            return false;
        }
    }

    /**
     * @param $productTblAsin
     * @param $accountId
     * @uses in App\Console\Commands\copyProductSalesRank
     */
    public static function getVcTreeNo($productTblAsin, $accountId)
    {
        DB::beginTransaction();
        try {
            $record = DB::table('tbl_sc_product_category_details')
                ->where('asin', $productTblAsin)
                ->where('fkAccountId', $accountId)
                ->where('isActive', 1)
                ->min('categoryTreeNumber');
            DB::commit();
                return $record;
        } catch (\Exception $e) {
            DB::rollback();
            return false;
        }
    }
    /**
     * @return bool
     */
    public static function get_merchants_historical_data()
    {
        try {
            $record = DB::table('tbl_sc_config')->where('historical_data_downloaded', 0)->get();
            if ($record) {
                return $record;
            }
        } catch (\Exception $e) {
            DB::rollback();
            return false;
        }
    }

}

