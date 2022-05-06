<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class TblAmsScheduleAdvertisingReportsV5 extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::rename('tbl_ams_schedule_selected_email_sponsord_types', 'tbl_ams_schedule_selected_email_sponsored_types');
        Schema::rename('tbl_ams_sponsord_reports', 'tbl_ams_sponsored_reports');
        Schema::rename('tbl_ams_sponsord_types', 'tbl_ams_sponsored_types');
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
