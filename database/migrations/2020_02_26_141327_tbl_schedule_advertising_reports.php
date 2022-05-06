<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class TblScheduleAdvertisingReports extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tbl_ams_schedule_advertising_reports', function (Blueprint $table) {
            
            $table->bigIncrements('id');
            $table->string('reportName')->nullable();
            //$table->string('fkBrandId')->nullable();
            $table->bigInteger('fkAccountId')->nullable();
            $table->bigInteger('fkProfileId')->nullable();
            //$table->string('fkSponsordTypeId')->nullable();
            //$table->string('fkReportTypeId')->nullable();
            $table->string('granularity')->nullable();
            $table->string('fromDate')->nullable();
            $table->string('toDate')->nullable();
            $table->string('addCC')->nullable();
            $table->string('time')->nullable();
           // $table->string('scheduleDate')->nullable();
            $table->integer('status')->default(0);
            $table->string('completedTime')->nullable();
            $table->boolean('mon')->default(0);
            $table->boolean('tue')->default(0);
            $table->boolean('wed')->default(0);
            $table->boolean('thu')->default(0);
            $table->boolean('fri')->default(0);
            $table->boolean('sat')->default(0);
            $table->boolean('sun')->default(0);
            $table->string('createdBy')->nullable();
            $table->timestamps();
            $table->softDeletes();

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('tbl_ams_schedule_advertising_reports');
    }
}
