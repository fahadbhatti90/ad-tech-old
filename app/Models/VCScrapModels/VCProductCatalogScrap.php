<?php

namespace App\Models\VCScrapModels;

use Illuminate\Database\Eloquent\Model;
use DB;
use Illuminate\Support\Facades\Log;

class VCProductCatalogScrap extends Model
{

    /**
     * This Function is used to store distinct Records of Product Catalog
     * @param $insertionArrayData
     */
    public static function insertLookupScrapCatalog($insertionArrayData)
    {
        foreach ($insertionArrayData as $row) {
            $existData = DB::table('tbl_vc_look_up_availability_scrap')->where([
                ['asin', $row['asin']],
                ['vendorCode', $row['vendorCode']],
                ['lastModifiedDate', $row['lastModifiedDate']],
            ])->get();

            if ($existData->isEmpty()) {
                try {
                    DB::table('tbl_vc_look_up_availability_scrap')->insert($row);

                } catch (\Illuminate\Database\QueryException $ex) {
                    Log::error($ex->getMessage());
                }
            } else {
                $row['updated_at'] = date('Y-m-d h:i:s');
                try {
                    DB::table('tbl_vc_look_up_availability_scrap')->where([
                        ['asin', $row['asin']],
                        ['vendorCode', $row['vendorCode']],
                        ['lastModifiedDate', $row['lastModifiedDate']],
                    ])->update($row);
                    Log::info('Update Record Look up availability Scrap = ');
                } catch (\Illuminate\Database\QueryException $ex) {
                    Log::error($ex->getMessage());
                }
            }
        }
    }

    /**
     * This Function is used to store daily Record of Product Catalog
     * @param $insertionArray
     * @return mixed
     */
    public static function insertScrapCatalog($insertionArray)
    {
        return DB::table('tbl_vc_all_availability_scrap')->insert($insertionArray);
    }

    /**
     * This function is used to store all Vendor List
     * @param $insertionArray
     * @return mixed
     */
    public static function insertScrapVendorsList($insertionArray)
    {
        foreach ($insertionArray as $row) {
            $existData = DB::table('tbl_vc_vendor_list_scrap')->where([
                ['businessName', $row['businessName']],
                ['vendorGroupId', $row['vendorGroupId']],
                ['marketscopeId', $row['marketscopeId']]
            ])->get();

            if ($existData->isEmpty()) {
                try {
                    DB::table('tbl_vc_vendor_list_scrap')->insert($row);

                } catch (\Illuminate\Database\QueryException $ex) {
                    Log::error($ex->getMessage());
                }
            }

        }
    }

    /**
     * This function is used to get list of all vendors which record is not scraped yet for today
     * @return mixed
     */
    public static function getAllScrapVendorsList()
    {
        return DB::table('tbl_vc_vendor_list_scrap')
                ->select('id', 'url', 'vendorGroupId')
                ->where('isScraped', 0)
                ->get();
    }

    /**
     * This function is used to insert all scrap data into tmp table
     * @param $insertionArray
     * @return mixed
     */
    public static function insertTmpScrapCatalog($insertionArray)
    {
        return DB::table('tbl_vc_tmp_all_availability_scrap')->insert($insertionArray);
    }


    public static function getLastOffsetToContinue()
    {
        return DB::table('tbl_vc_tmp_all_availability_scrap')->select('offset')->latest('id')->first();
    }

    /**
     * This function is used to update those vendors whom record is fully scraped
     * @param $vendorId
     * @param $updateData
     */
    public static function updateScrapVendorList( $vendorId, $updateData)
    {
        DB::table('tbl_vc_vendor_list_scrap')->where('id', $vendorId)->update($updateData);

    }

    /**
     * This funciton is used to delete record of the vendor which record is not completly scraped
     * @param $vendorId
     * @param $updateData
     */
    public static function deleteRecordsOfSpecificVendor( $vendorGroupId)
    {
        DB::table('tbl_vc_tmp_all_availability_scrap')->where('fkVendorGroupId', $vendorGroupId)->delete();

    }


}
