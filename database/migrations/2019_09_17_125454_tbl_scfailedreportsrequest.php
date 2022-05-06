<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class TblScfailedreportsrequest extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tbl_sc_failed_reports_request', function (Blueprint $table) {

            $table->bigIncrements('id');
            $table->string('errorCode')->nullable();
            $table->string('httpStatusCode')->nullable();
            //$table->string('error')->nullable();
            $table->string('description')->nullable();
            $table->string('apiType')->nullable();
            $table->string('sellerId')->nullable();
            $table->string('reportType')->nullable();
            $table->string('reportRequestId')->nullable();
            $table->string('reportId')->nullable();
            $table->string('startDate')->nullable();
            $table->string('endDate')->nullable();
            $table->timestamp('createdAt')->nullable();
            //$table->timestamp('updatedAt')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('tbl_sc_failed_reports_request');
    }
}
