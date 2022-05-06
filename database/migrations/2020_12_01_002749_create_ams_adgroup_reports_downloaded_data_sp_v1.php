<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAmsAdgroupReportsDownloadedDataSpV1 extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (Schema::hasTable('tbl_ams_adgroup_reports_downloaded_data_sp')) {
            Schema::table('tbl_ams_adgroup_reports_downloaded_data_sp', function (Blueprint $table) {
                if (Schema::hasColumn('tbl_ams_adgroup_reports_downloaded_data_sp', 'adGroupName')) {
                    $table->string('adGroupName', 191)->change();
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

    }
}
