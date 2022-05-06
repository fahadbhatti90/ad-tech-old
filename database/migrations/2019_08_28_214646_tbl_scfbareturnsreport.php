<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class TblScfbareturnsreport extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tbl_sc_sales_fba_returns_report', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('fkAccountId')->nullable();
            $table->bigInteger('fkBatchId')->nullable();
            $table->integer('fkRequestId')->nullable();
            /*$table->unsignedBigInteger('fkRequestId');
            $table->foreign('fkRequestId')->references('id')->on('tbl_sc_requested_reports');*/
            $table->string('reportId')->nullable();
            $table->string('reportRequestId')->nullable();
            $table->string('reportRequestDate')->nullable();
            $table->string('returnDate')->nullable();
            $table->string('orderId')->nullable();
            $table->string('sku')->nullable();
            $table->string('asin')->nullable();
            $table->string('fnsku')->nullable();
            $table->text('productName')->nullable();
            $table->string('quantity')->nullable();
            $table->string('fulfillmentCenterId')->nullable();
            $table->string('detailedDisposition')->nullable();
            $table->string('reason')->nullable();
            $table->string('status')->nullable();
            $table->string('licensePlateNumber')->nullable();
            $table->text('customerComments')->nullable();
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
        Schema::dropIfExists('tbl_sc_sales_fba_returns_report');
    }
}
