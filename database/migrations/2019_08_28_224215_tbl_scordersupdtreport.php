<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class TblScordersupdtreport extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tbl_sc_sales_orders_updt_report', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('fkAccountId')->nullable();
            $table->bigInteger('fkBatchId')->nullable();
            $table->integer('fkRequestId')->nullable();
            /*$table->unsignedBigInteger('fkRequestId');
            $table->foreign('fkRequestId')->references('id')->on('tbl_sc_requested_reports');*/
            $table->string('reportId')->nullable();
            $table->string('reportRequestId')->nullable();
            $table->string('reportRequestDate')->nullable();
            $table->string('amazonOrderId')->nullable();
            $table->string('merchantOrderId')->nullable();
            $table->string('purchaseDate')->nullable();
            $table->string('lastUpdatedDate')->nullable();
            $table->string('orderStatus')->nullable();
            $table->string('fulfillmentChannel')->nullable();
            $table->string('salesChannel')->nullable();
            $table->string('orderChannel')->nullable();
            $table->string('url')->nullable();
            $table->string('shipServiceLevel')->nullable();
            $table->text('productName')->nullable();
            $table->string('sku')->nullable();
            $table->string('asin')->nullable();
            $table->string('itemStatus')->nullable();
            $table->string('quantity')->nullable();
            $table->string('currency')->nullable();
            $table->string('itemPrice')->nullable();
            $table->string('itemTax')->nullable();
            $table->string('shippingPrice')->nullable();
            $table->string('shippingTax')->nullable();
            $table->string('giftWrapPrice')->nullable();
            $table->string('giftWrapTax')->nullable();
            $table->string('itemPromotionDiscount')->nullable();
            $table->string('shipPromotionDiscount')->nullable();
            $table->string('shipCity')->nullable();
            $table->string('shipState')->nullable();
            $table->string('shipPostalCode')->nullable();
            $table->string('shipCountry')->nullable();
            $table->string('promotionIds')->nullable();
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
        Schema::dropIfExists('tbl_sc_sales_orders_updt_report');
    }
}
