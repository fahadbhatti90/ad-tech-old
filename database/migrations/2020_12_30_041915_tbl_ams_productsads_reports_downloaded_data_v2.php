<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class TblAmsProductsadsReportsDownloadedDataV2 extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (Schema::hasTable('tbl_ams_productsads_reports_downloaded_data')) {
            Schema::table('tbl_ams_productsads_reports_downloaded_data', function (Blueprint $table) {
                if (!Schema::hasColumn('tbl_ams_productsads_reports_downloaded_data', 'fkConfigId')) {
                    $table->bigInteger('fkConfigId')->default(1)->after('fkProfileId');
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
