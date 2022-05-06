<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use DB;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Log;

class VCModel extends Model
{
    public $table = "tbl_vc_vendors";
    protected $primaryKey = 'vendor_id';

    public function accounts()
    {
        return $this->hasMany('App\Models\AccountModels\AccountModel', 'fkId')->where("fkAccountType", 3);
    }//end function

    private static $tbl_daily_forecast = 'tbl_stage_vc_daily_forecast';
    private static $tbl_daily_inventory = 'tbl_stage_vc_daily_inventory';
    private static $tbl_vendors = 'tbl_vc_vendors';
    private static $tbl_purchaseorders = 'tbl_stage_vc_purchaseorders';
    private static $tbl_weekly_traffic_summary = 'tbl_stage_vc_weekly_traffic_summary';
    private static $tbl_daily_sales = 'tbl_stage_vc_daily_sales';
    private static $tbl_product_catalog = 'tbl_stage_vc_product_catalog';

    private static $tbl_main_daily_forecast = 'tbl_vc_daily_forecast';
    private static $tbl_main_daily_inventory = 'tbl_vc_daily_inventory';
    private static $tbl_main_purchaseorders = 'tbl_vc_purchaseorders';
    private static $tbl_main_weekly_traffic_summary = 'tbl_vc_weekly_traffic_summary';
    private static $tbl_main_daily_sales = 'tbl_vc_daily_sales';
    private static $tbl_main_product_catalog = 'tbl_vc_product_catalog';

    /**
     * @param $data
     */
    public static function insertDailySales($data)
    {
        return self::insertDataInChunks(self::$tbl_daily_sales, $data);
    }

    /**
     * @param $data
     */
    public static function insertInventoryData($data)
    {
        return self::insertDataInChunks(self::$tbl_daily_inventory, $data);
    }

    /**
     * @param $data
     * @return mixed
     */
    public static function insertVendorData($data)
    {
        return DB::table(self::$tbl_vendors)->insert($data);
    }

    /**
     * @return mixed
     */
    public static function getAllVendors()
    {
        return DB::table(self::$tbl_vendors)->get(['vendor_id', 'vendor_name']);
    }

    /**
     * @param $data
     */
    public static function insertPoData($data)
    {
        return self::insertDataInChunks(self::$tbl_purchaseorders, $data);
    }

    /**
     * @param $data
     * @param $type
     */
    public static function insertTrafficData($data, $type)
    {
        if ($type == 'new') {
            return self::insertDataInChunks(self::$tbl_weekly_traffic_summary, $data);
        }
        return DB::table(self::$tbl_weekly_traffic_summary)->insert($data);
    }

    /**
     * @return mixed
     */
    public static function getDailySalesLastRecord()
    {
        return DB::table(self::$tbl_daily_sales)->select('sale_date')->latest('id')->first();
    }

    /**
     * @return mixed
     */
    public static function getBatchIdIfExist($reportDateCheck, $fkAccuntId)
    {
        return DB::table('tbl_batch_id')
            ->where('reportDate', $reportDateCheck)
            ->where('fkAccountId', $fkAccuntId)
            ->get()->first();
    }

    /**
     * Get Count Daily Saoles
     * @return mixed
     */
    public static function getDailySalesCount()
    {
        return DB::table(self::$tbl_daily_sales)->count();
    }

    /**
     * @param $data
     */
    public static function insertForecastData($data)
    {
        return self::insertDataInChunks(self::$tbl_daily_forecast, $data);
    }

    /**
     * @param $data
     */
    public static function insertproductCatalogData($data)
    {
        return self::insertDataInChunks(self::$tbl_product_catalog, $data);
    }

    /**
     * @param $tableName
     * @param $data
     */
    public static function insertDataInChunks($tableName, $data)
    {
        DB::beginTransaction();
        try {
            // if data found
            foreach (array_chunk($data, 500) as $t) {
                DB::table($tableName)->insert($t);
            }
            DB::commit();
        } catch (\Exception $e) {
            // got error add to log file
            // $e->getMessage()
            DB::rollback();
        }

    }

    /**
     *
     */
    public static function getCategoryNamesFromDailySales()
    {

    }

    /**
     * @param $data
     */
    public static function insertAsins($data)
    {
        foreach ($data as $row) {
            if (!empty($row['asin']) && $row['asin'] != 'NA') {
                $existData = DB::table('tbl_sc_product_ids')->where([
                    ['fkAccountId', $row['fkAccountId']],
                    ['asin', $row['asin']],
                    ['source', $row['source']]
                ])->get();

                if ($existData->isEmpty()) {
                    try {
                        DB::table('tbl_sc_product_ids')->insert($row);
                        Log::info('Data For Insertion Done');
                    } catch (\Illuminate\Database\QueryException $ex) {
                        Log::error($ex->getMessage());
                    }
                } else {
                    Log::info('No Data For Insertion ');
                }
            }
        }
    }

    /**
     * @return mixed
     */
    public static function getAllCron()
    {
        return DB::table('tbl_vc_cron_list')->where(['isRunned' => 0])->get();
    }

    /**
     * @param $data
     */
    public static function cronInsert($data)
    {

        $existData = DB::table('tbl_vc_cron_list')->where([
            ['moduleName', $data['moduleName']]
        ])->get();

        if ($existData->isEmpty()) {
            try {
                $result = DB::table('tbl_vc_cron_list')->insert($data);
                Log::info('Cron Job Table Insertion' . $data['moduleName']);
            } catch (\Illuminate\Database\QueryException $ex) {
                Log::error($ex->getMessage());
            }
        } else {
            try {
                DB::table('tbl_vc_cron_list')->where([
                    ['moduleName', $data['moduleName']],
                ])->update($data);
                Log::info('Cron Job Table Updated' . $data['moduleName']);
            } catch (\Illuminate\Database\QueryException $ex) {
                Log::error($ex->getMessage());
            }
        }
    }

    /**
     * @return mixed
     */
    public static function getDistinctDailySalesCategory()
    {
        return DB::table(self::$tbl_daily_sales)->select('strCategory')
            ->distinct()
            ->where(['fkCategoryId' => 0])
            ->get();
    }

    /**
     * @param $categoryId
     * @param $categoryName
     * @return mixed
     */
    public static function updateDailySalesCategoryId($categoryId, $categoryName)
    {
        return DB::table(self::$tbl_daily_sales)
            ->where('fkCategoryId', 0)
            ->where('strCategory', $categoryName)
            ->update(['fkCategoryId' => $categoryId]);
    }

    /**
     * @return mixed
     */
    public static function getDistinctDailyInventoryCategory()
    {
        return DB::table(self::$tbl_daily_inventory)->select('strCategory')
            ->distinct()
            ->where(['fkCategoryId' => 0])
            ->get();
    }

    /**
     * @param $categoryId
     * @param $categoryName
     * @return mixed
     */
    public static function updateDailyInventoryCategoryId($categoryId, $categoryName)
    {
        return DB::table(self::$tbl_daily_inventory)
            ->where('fkCategoryId', 0)
            ->where('strCategory', $categoryName)
            ->update(['fkCategoryId' => $categoryId]);
    }

    /**
     * @return mixed
     */
    public static function getDistinctDailyForecastCategory()
    {
        return DB::table(self::$tbl_daily_forecast)->select('strCategory')
            ->distinct()
            ->where(['fkCategoryId' => 0])
            ->get();
    }

    /**
     * @param $categoryId
     * @param $categoryName
     * @return mixed
     */
    public static function updateDailyForecastCategoryId($categoryId, $categoryName)
    {
        return DB::table(self::$tbl_daily_forecast)
            ->where('fkCategoryId', 0)
            ->where('strCategory', $categoryName)
            ->update(['fkCategoryId' => $categoryId]);
    }

    /**
     * @param $categoryString
     * @return mixed
     */
    public static function getCategoryId($categoryString)
    {
        return DB::table('tbl_product_categories')
            ->where('productCategoryName', $categoryString)
            ->orderBy('id', 'desc')
            ->value('id');

    }

    public static function getHistoricalDataFromDB($startDate, $endDate, $reportType)
    {
        $response = array();
        $reportStartDate = date_format(date_create($startDate), "Y-m-d");
        $reportEndDate = date_format(date_create($endDate), "Y-m-d");

        switch ($reportType) {
            case "daily_sales":
                $response = DB::table(self::$tbl_main_daily_sales)
                    ->whereBetween('sale_date', [$reportStartDate, $reportEndDate])->get();
                break;
            case "purchase_order":
                $response = DB::table(self::$tbl_main_purchaseorders)
                    ->whereBetween('capture_date', [$reportStartDate, $reportEndDate])->get();
                break;
            case "daily_inventory":
                $response = DB::table(self::$tbl_main_daily_inventory)
                    ->whereBetween('rec_date', [$reportStartDate, $reportEndDate])->get();
                break;
            case "traffic":
                $response = DB::table(self::$tbl_main_weekly_traffic_summary)
                    ->whereBetween('capture_date', [$reportStartDate, $reportEndDate])->get();
                break;
            case "forecast":
                $response = DB::table(self::$tbl_main_daily_forecast)
                    ->whereBetween('capture_date', [$reportStartDate, $reportEndDate])->get();
                break;
            case "product_catalog":
                $response = DB::table(self::$tbl_main_product_catalog)
                    ->whereBetween('capture_date', [$reportStartDate, $reportEndDate])->get();
                break;
            default:
                Log::info('Report not selected.');
        }
        return $response;
    }

    /**
     * This function is used to delete data
     * @param $data
     * @param $type
     * @return mixed
     */
    public static function deleteDataOfSpecificType($data, $type)
    {
        $return = array();
        switch ($type) {
            case "daily_sales":
                $response = DB::delete('delete from ' . self::$tbl_daily_sales . ' where fk_vendor_id = ? AND sale_date = ?',
                    array(
                        $data['fk_vendor_id'],
                        $data['start_date']
                    )
                );
                $return = array('status' => true, 'count' => $response);
                break;
            case "purchase_order":
                $response = DB::delete('delete from ' . self::$tbl_purchaseorders . ' where fk_vendor_id = ? AND orderon_date = ?',
                    array(
                        $data['fk_vendor_id'],
                        $data['start_date']
                    )
                );
                $return = array('status' => true, 'count' => $response);
                break;
            case "daily_inventory":
                $response = DB::delete('delete from ' . self::$tbl_daily_inventory . ' where fk_vendor_id = ? AND rec_date = ?',
                    array(
                        $data['fk_vendor_id'],
                        $data['start_date']
                    )
                );
                $return = array('status' => true, 'count' => $response);
                break;
            case "traffic":
                $response = DB::delete('delete from ' . self::$tbl_weekly_traffic_summary . ' where fk_vendor_id = ? AND start_date = ? AND end_date = ?',
                    array(
                        $data['fk_vendor_id'],
                        $data['start_date'],
                        $data['end_date']
                    )
                );
                $return = array('status' => true, 'count' => $response);
                break;
            case "forecast":
                $response = DB::delete('delete from ' . self::$tbl_daily_forecast . ' where fk_vendor_id = ? AND capture_date = ?',
                    array(
                        $data['fk_vendor_id'],
                        $data['start_date']
                    )
                );
                $return = array('status' => true, 'count' => $response);
                break;
            case "product_catalog":
                $response = DB::delete('delete from ' . self::$tbl_product_catalog . ' where fk_vendor_id = ? AND capture_date = ?',
                    array(
                        $data['fk_vendor_id'],
                        $data['start_date']
                    )
                );
                $return = array('status' => true, 'count' => $response);
                break;
            default:
                $return = array('status' => false, 'count' => 0);
        }
        return $return;
    }

    /**
     * This function is used to verify data
     * @param $data
     * @param $type
     * @return array
     */
    public static function verifyDataOfSpecificType($data, $type)
    {
        $return = array();
        switch ($type) {
            case "daily_sales":
                $response = \DB::select('CALL spDuplicateVerfDailySalesVC(?)', array($data['fk_vendor_id']));
                $return = array('status' => true, 'type' => $type, 'response' => $response);
                break;
            case "purchase_order":
                $response = \DB::select('CALL spDuplicateVerfDailyPOVC(?)', array($data['fk_vendor_id']));
                $return = array('status' => true, 'type' => $type, 'response' => $response);
                break;
            case "daily_inventory":
                $response = \DB::select('CALL spDuplicateVerfDailyInventoryVC(?)', array($data['fk_vendor_id']));
                $return = array('status' => true, 'type' => $type, 'response' => $response);
                break;
            case "traffic":
                $response = \DB::select('CALL spDuplicateVerfWeeklyTrafficVC(?)', array($data['fk_vendor_id']));
                $return = array('status' => true, 'type' => $type, 'response' => $response);
                break;
            case "forecast":
                $response = \DB::select('CALL spDuplicateVerfDailyForecastVC(?)', array($data['fk_vendor_id']));
                $return = array('status' => true, 'type' => $type, 'response' => $response);
                break;
            case "product_catalog":
                $response = \DB::select('CALL spDuplicateVerfDailyCatalogVC(?)', array($data['fk_vendor_id']));
                $return = array('status' => true, 'type' => $type, 'response' => $response);
                break;
            default:
                $return = array('status' => false, 'type' => '', 'response' => '');
        }
        return $return;
    }

    /**
     * This function is used to verify data
     * @param $data
     * @param $type
     * @return array
     */
    public static function moveDataOfSpecificType($data, $type)
    {
        $return = array();
        switch ($type) {
            case "daily_sales":
                // changes by Umer  http://jira.codeinformatics.com/browse/HTK-1205
                $asinProductIds = DB::table(self::$tbl_daily_sales)
                    ->select('fkAccountId','batchId','asin','fk_vendor_id')
                    ->where('fk_vendor_id', $data['fk_vendor_id'])
                    ->get();

                if (count($asinProductIds) > 0 ){
                    $storeAsinsScProduct = storeAsinHelper($asinProductIds);
                    VCModel::insertAsins($storeAsinsScProduct);
                }
                $response = \DB::select('CALL spMoveDailySalesVC(?)', array($data['fk_vendor_id']));
                $return = array('status' => true, 'count' => $response);

                break;
            case "purchase_order":
                // changes by Umer  http://jira.codeinformatics.com/browse/HTK-1205
                $asinProductIds = DB::table(self::$tbl_purchaseorders)
                    ->select('fkAccountId','batchId','asin','fk_vendor_id')
                    ->where('fk_vendor_id', $data['fk_vendor_id'])
                    ->get();

                if (count($asinProductIds) > 0 ){
                    $storeAsinsScProduct = storeAsinHelper($asinProductIds);
                    VCModel::insertAsins($storeAsinsScProduct);
                }

                $response = \DB::select('CALL spMoveDailyPurchaseOrderVC(?)', array($data['fk_vendor_id']));
                $return = array('status' => true, 'count' => $response);
                break;
            case "daily_inventory":
                // changes by Umer  http://jira.codeinformatics.com/browse/HTK-1205
                $asinProductIds = DB::table(self::$tbl_daily_inventory)
                    ->select('fkAccountId','batchId','asin','fk_vendor_id')
                    ->where('fk_vendor_id', $data['fk_vendor_id'])
                    ->get();

                if (count($asinProductIds) > 0 ){
                    $storeAsinsScProduct = storeAsinHelper($asinProductIds);
                    VCModel::insertAsins($storeAsinsScProduct);
                }
                $response = \DB::select('CALL spMoveDailyInventoryVC(?)', array($data['fk_vendor_id']));
                $return = array('status' => true, 'count' => $response);
                break;
            case "traffic":
                $response = \DB::select('CALL spMoveWeeklyTrafficVC(?)', array($data['fk_vendor_id']));
                $return = array('status' => true, 'count' => $response);
                break;
            case "forecast":
                // changes by Umer  http://jira.codeinformatics.com/browse/HTK-1205
                $asinProductIds = DB::table(self::$tbl_daily_forecast)
                    ->select('fkAccountId','batchId','asin','fk_vendor_id')
                    ->where('fk_vendor_id', $data['fk_vendor_id'])
                    ->get();

                if (count($asinProductIds) > 0 ){
                    $storeAsinsScProduct = storeAsinHelper($asinProductIds);
                    VCModel::insertAsins($storeAsinsScProduct);
                }
                $response = \DB::select('CALL spMoveDailyForecastVC(?)', array($data['fk_vendor_id']));
                $return = array('status' => true, 'count' => $response);
                break;
            case "product_catalog":
                // changes by Umer  http://jira.codeinformatics.com/browse/HTK-1205
                $asinProductIds = DB::table(self::$tbl_product_catalog)
                    ->select('fkAccountId','batchId','asin','fk_vendor_id')
                    ->where('fk_vendor_id', $data['fk_vendor_id'])
                    ->get();

                if (count($asinProductIds) > 0 ){
                    $storeAsinsScProduct = storeAsinHelper($asinProductIds);
                    VCModel::insertAsins($storeAsinsScProduct);
                }
                $response = \DB::select('CALL spMoveDailyCatalogVC(?)', array($data['fk_vendor_id']));
                $return = array('status' => true, 'count' => $response);
                break;
            default:
                $return = array('status' => false, 'count' => 0);
        }
        return $return;
    }
}