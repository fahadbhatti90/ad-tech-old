<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class TblAmsReportsMetrics extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tbl_ams_reports_metrics', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('metricName')->nullable();
            $table->string('tblColumnName')->nullable();
            $table->bigInteger('fkParameterType')->nullable();
            $table->string('isActive')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('tbl_ams_reports_metrics');
    }
}
