<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class TblMwsRequestedReports extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tbl_sc_requested_reports', function (Blueprint $table) {
            $table->bigIncrements('id');
            //$table->integer('fkAccountId')->nullable();
            $table->bigInteger('fkAccountId')->nullable();
            $table->bigInteger('fkBatchId')->nullable();
            $table->bigInteger('fk_merchant_id')->nullable();
            /*$table->unsignedBigInteger('fk_merchant_id');
            $table->foreign('fk_merchant_id')->references('mws_config_id')->on('tbl_sc_config');*/
            $table->string('ReportRequestId')->nullable();
            $table->string('ReportType')->nullable();
            $table->string('metricsType')->nullable();
            $table->date('reportRequestDate')->nullable();
            $table->string('StartDate')->nullable();
            $table->string('EndDate')->nullable();
            $table->string('amazonStartDate')->nullable();
            $table->string('amazonEndDate')->nullable();
            //$table->string('Scheduled')->nullable();
            $table->string('SubmittedDate')->nullable();
            $table->string('ReportProcessingStatus')->nullable();
            $table->string('GeneratedReportId')->nullable();
            //$table->string('StartedProcessingDate')->nullable();
            //$table->string('CompletedProcessingDate')->nullable();
            //$table->string('ReportId')->nullable();
            //$table->string('AvailableDate')->nullable();
            $table->string('Acknowledged')->nullable();
            $table->enum('report_acknowledgement', ['false', 'true','no_data'])->default('false');
            $table->timestamp('created_at')->nullable();
            $table->timestamp('updated_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('tbl_sc_requested_reports');
    }
}
