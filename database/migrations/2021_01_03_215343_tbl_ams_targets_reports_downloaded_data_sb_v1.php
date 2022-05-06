<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class TblAmsTargetsReportsDownloadedDataSbV1 extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //tbl_ams_targets_reports_downloaded_data_sb
        if (Schema::hasTable('tbl_ams_targets_reports_downloaded_data_sb')) {
            Schema::table('tbl_ams_targets_reports_downloaded_data_sb', function (Blueprint $table) {
                if (!Schema::hasColumn('tbl_ams_targets_reports_downloaded_data_sb', 'fkConfigId')) {
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
