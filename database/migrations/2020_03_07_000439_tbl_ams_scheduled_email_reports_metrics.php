<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class TblAmsScheduledEmailReportsMetrics extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tbl_ams_scheduled_email_reports_metrics', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('fkReportScheduleId')->nullable();
            $table->bigInteger('fkReportMetricId')->nullable();
            $table->bigInteger('fkParameterType')->nullable();
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
        Schema::dropIfExists('tbl_ams_scheduled_email_reports_metrics');
    }
}
