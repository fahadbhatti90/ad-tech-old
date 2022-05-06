<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class TblAmsScheduleAdvertisingReportsV2 extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('tbl_ams_schedule_advertising_reports', function (Blueprint $table) {
            $table->integer('timeFrame')->after('addCC')->nullable();
            $table->string('timeFrameType')->after('addCC')->nullable();
             $table->dropColumn('fromDate');
             $table->dropColumn('toDate');
        });
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
