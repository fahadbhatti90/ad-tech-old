<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class TblScCrons extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tbl_sc_crons', function (Blueprint $table) {
            $table->bigIncrements('task_id');
            //$table->string('title')->nullable();
            $table->string('report_type')->nullable();
            $table->string('cronStartTime')->nullable();
            $table->string('isCronRunning')->default(0);
            $table->string('frequency')->nullable();
            $table->string('status')->nullable();
           //$table->string('lastRun')->nullable();
            $table->string('requestReportTime')->nullable();
            $table->date('requestReportLastRun')->nullable();
            $table->timestamp('requestReportCompletedTime')->nullable();
            $table->string('requestReportLISTTime')->nullable();
            $table->date('requestReportListLastRun')->nullable();
            $table->timestamp('requestReportLISTCompletedTime')->nullable();
            //$table->string('reportLISTTime')->nullable();
            //$table->date('reportListLastRun')->nullable();
            $table->string('getReportTime')->nullable();
            $table->date('getReportLastRun')->nullable();
            $table->timestamp('getReportCompletedTime')->nullable();
            /*$table->string('getAsinsFromReportsTime')->nullable();
            $table->date('getAsinsFromReportsLastRun')->nullable();
            $table->timestamp('getAsinsFromReportsCompletedTime')->nullable();

            $table->string('getProductDetailsTime')->nullable();
            $table->date('getProductDetailsLastRun')->nullable();
            $table->timestamp('getProductDetailsCompletedTime')->nullable();

            $table->string('getProductCategoriesDetailsTime')->nullable();
            $table->date('getProductCategoriesDetailsLastRun')->nullable();
            $table->timestamp('getProductCategoriesDetailsCompletedTime')->nullable();*/
            $table->timestamp('createdAt')->nullable();
            $table->timestamp('updatedAt')->nullable();
            //$table->timestamp('reportLISTRunTime')->nullable();

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('tbl_sc_crons');
    }
}
