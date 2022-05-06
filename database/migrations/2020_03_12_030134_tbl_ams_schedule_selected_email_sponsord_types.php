<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class TblAmsScheduleSelectedEmailSponsordTypes extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //tbl_ams_schedule_selected_email_sponsord_types
        Schema::create('tbl_ams_schedule_selected_email_sponsord_types', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('fkReportScheduleId')->nullable();
            $table->bigInteger('fkSponsordTypeId')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('tbl_ams_schedule_selected_email_sponsord_types');
    }
}
