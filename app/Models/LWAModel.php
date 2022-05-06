<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class LWAModel extends Model
{
    private static $tbl_ams_valid_token_response = 'tbl_ams_valid_token_response';
    private static $tbl_ams_customer_profile = 'tbl_ams_customer_profile';

    /**
     * This function is used to Token Access Detail
     *
     * @param $DataArray
     * @return bool
     */
    public static function storeTokenDetailData($DataArray)
    {
        DB::beginTransaction();
        try {
            $record = DB::table(LWAModel::$tbl_ams_valid_token_response)->where([
                ['user_id', '=', $DataArray['user_id']],
            ])->get()->first();
            DB::commit();
            if (!$record) {
                DB::table(LWAModel::$tbl_ams_valid_token_response)->insert($DataArray);
                DB::commit();
            } else {
                // if data found
            }
        } catch (\Exception $e) {
            // got error add to log file
            // $e->getMessage()
            DB::rollback();
            // something went wrong
            return false;
        }
    }

    /**
     * This function is used to store Customer Profile Detail
     *
     * @param $DataArray
     * @return bool
     */
    public static function storeProfileDetailData($DataArray)
    {
        DB::beginTransaction();
        try {
            $record = DB::table(LWAModel::$tbl_ams_customer_profile)->where([
                ['user_id', '=', $DataArray['user_id']],
            ])->get()->first();
            DB::commit();
            if (!$record) {
                DB::table(LWAModel::$tbl_ams_customer_profile)->insertGetId($DataArray);
                DB::commit();
                return array('status' => 'true');
            } else {
                // if data found
                return array('status' => 'already');
            }
        } catch (\Exception $e) {
            // got error add to log file
            // $e->getMessage()
            DB::rollback();
            // something went wrong
            return array('status' => 'false');
        }
    }
}
