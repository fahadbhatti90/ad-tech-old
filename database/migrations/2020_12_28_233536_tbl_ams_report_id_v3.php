<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class TblAmsReportIdV3 extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (Schema::hasTable('tbl_ams_report_id')) {
            Schema::table('tbl_ams_report_id', function (Blueprint $table) {
                if (!Schema::hasColumn('tbl_ams_report_id', 'fkConfigId')) {
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
