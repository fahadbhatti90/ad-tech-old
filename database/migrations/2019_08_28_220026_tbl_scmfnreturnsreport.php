<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class TblScmfnreturnsreport extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tbl_sc_sales_mfn_returns_report', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('fkAccountId')->nullable();
            $table->bigInteger('fkBatchId')->nullable();
            $table->integer('fkRequestId')->nullable();
            /*$table->unsignedBigInteger('fkRequestId');
            $table->foreign('fkRequestId')->references('id')->on('tbl_sc_requested_reports');*/
            $table->string('reportId')->nullable();
            $table->string('reportRequestId')->nullable();
            $table->string('reportRequestDate')->nullable();
            $table->string('orderId')->nullable();
            $table->string('orderDate')->nullable();
            $table->string('returnRequestDate')->nullable();
            $table->string('returnRequestStatus')->nullable();
            $table->string('amazonRmaId')->nullable();
            $table->string('merchantRmaId')->nullable();
            $table->string('labelType')->nullable();
            $table->string('labelCost')->nullable();
            $table->string('currencyCode')->nullable();
            $table->string('returnCarrier')->nullable();
            $table->string('trackingId')->nullable();
            $table->string('labelToBePaidBy')->nullable();
            $table->string('aToZClaim')->nullable();
            $table->string('isPrime')->nullable();
            $table->string('asin')->nullable();
            $table->string('merchantSku')->nullable();
            $table->text('itemName')->nullable();
            $table->string('returnQuantity')->nullable();
            $table->string('shippingPrice')->nullable();
            $table->string('returnReason')->nullable();
            $table->string('inPolicy')->nullable();
            $table->string('returnType')->nullable();
            $table->string('resolution')->nullable();
            $table->string('invoiceNumber')->nullable();
            $table->string('returnDeliveryDate')->nullable();
            $table->string('orderAmount')->nullable();
            $table->string('orderQuantity')->nullable();
            $table->string('safeTActionReason')->nullable();
            $table->string('safeTClaimId')->nullable();
            $table->string('safeTClaimState')->nullable();
            $table->string('safeTClaimCreationTime')->nullable();
            $table->string('safeTClaimReimbursementAmount')->nullable();
            $table->string('refundedAmount')->nullable();
            $table->string('category')->nullable();
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
        Schema::dropIfExists('tbl_sc_sales_mfn_returns_report');
    }
}
