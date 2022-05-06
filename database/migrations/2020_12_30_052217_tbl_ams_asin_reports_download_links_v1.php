<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class TblAmsAsinReportsDownloadLinksV1 extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (Schema::hasTable('tbl_ams_asin_reports_download_links')) {
            Schema::table('tbl_ams_asin_reports_download_links', function (Blueprint $table) {
                if (!Schema::hasColumn('tbl_ams_asin_reports_download_links', 'fkConfigId')) {
                    $table->bigInteger('fkConfigId')->default(1)->after('profileID');
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
