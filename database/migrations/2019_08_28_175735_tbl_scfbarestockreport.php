<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class TblScfbarestockreport extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tbl_sc_fba_restock_report', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('fkAccountId')->nullable();
            $table->bigInteger('fkBatchId')->nullable();
            $table->integer('fkRequestId')->nullable();
            /*$table->unsignedBigInteger('fkRequestId');
            $table->foreign('fkRequestId')->references('id')->on('tbl_sc_requested_reports');*/
            $table->string('reportId')->nullable();
            $table->string('reportRequestId')->nullable();
            $table->string('reportRequestDate')->nullable();
            $table->string('country')->nullable();
            $table->string('snapshotDate')->nullable();
            $table->string('productName')->nullable();
            $table->string('FNSKU')->nullable();
            $table->string('merchant')->nullable();
            $table->string('sku')->nullable();
            $table->string('asin')->nullable();
            $table->string('condition')->nullable();
            $table->string('supplier')->nullable();
            $table->string('partNo')->nullable();
            $table->string('currencyCode')->nullable();
            $table->string('price')->nullable();
            $table->string('unitsSoldLast30Days')->nullable();
            $table->string('totalInventory')->nullable();
            $table->string('inboundInventory')->nullable();
            $table->string('availableInventory')->nullable();
            $table->string('recommendedOrderQuantity')->nullable();
            $table->string('fcTransfer')->nullable();
            $table->string('fcProcessing')->nullable();
            $table->string('itemPromotionDiscount')->nullable();
            $table->string('customerOrder')->nullable();
            $table->string('shipCity')->nullable();
            $table->string('unfulfillable')->nullable();
            $table->string('fulfilledBy')->nullable();
            $table->string('daysOfSupply')->nullable();
            $table->string('instockAlert')->nullable();
            $table->string('recommendedOrderQty')->nullable();
            $table->string('recommendedOrderDate')->nullable();
            $table->string('eligibleForStorageFeeDiscountCurrentMonth')->nullable();

            $table->string('currentMonthVeryLowInventoryThreshold')->nullable();
            $table->string('currentMonthStorageDiscountMinimumInventoryThreshold')->nullable();
            $table->string('currentMonthStorageDiscountMaximumInventoryThreshold')->nullable();
            $table->string('currentMonthVeryHighInventoryThreshold')->nullable();
            $table->string('eligibleForStorageFeeDiscountNextMonth')->nullable();
            $table->string('nextMonthStorageDiscountMinimumInventoryThreshold')->nullable();
            $table->string('nextMonthStorageDiscountMaximumInventoryThreshold')->nullable();
            $table->string('nextMonthVeryHighInventoryThreshold')->nullable();
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
        Schema::dropIfExists('tbl_sc_fba_restock_report');
    }
}
