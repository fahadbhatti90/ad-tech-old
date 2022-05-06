<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class TblScfbareceiptreport extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tbl_sc_inventory_fba_receipt_report', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('fkAccountId')->nullable();
            $table->bigInteger('fkBatchId')->nullable();
            //$table->string('fk_request_id')->nullable();
            $table->integer('fkRequestId');
           /* $table->unsignedBigInteger('fkRequestId');
            $table->foreign('fkRequestId')->references('id')->on('tbl_sc_requested_reports');*/
            $table->string('reportId')->nullable();
            $table->string('reportRequestId')->nullable();
            $table->string('reportRequestDate')->nullable();
            $table->string('receivedDate')->nullable();
            //$table->string('merchantOrderId')->nullable();
            $table->string('fnsku')->nullable();
            $table->string('sku')->nullable();
            $table->text('productName')->nullable();
            $table->string('quantity')->nullable();
            $table->string('fbaShipmentId')->nullable();
            $table->string('fulfillmentCenterId')->nullable();
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
        Schema::dropIfExists('tbl_sc_inventory_fba_receipt_report');
    }
}
