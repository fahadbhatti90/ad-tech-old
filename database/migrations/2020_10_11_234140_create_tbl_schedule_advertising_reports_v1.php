<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTblScheduleAdvertisingReportsV1 extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('tbl_ams_schedule_advertising_reports', function (Blueprint $table) {
            if (!Schema::hasColumn('tbl_ams_schedule_advertising_reports', 'allMetricsCheck')) {
                $table->boolean('allMetricsCheck')->after('granularity')->default(0);
            }
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('tbl_schedule_advertising_reports_v1');
    }
}
