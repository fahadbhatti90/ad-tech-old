<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class TblScfbahealthreport extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tbl_sc_inventory_fba_health_report', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('fkAccountId')->nullable();
            $table->bigInteger('fkBatchId')->nullable();
            $table->integer('fkRequestId')->nullable();
           /* $table->unsignedBigInteger('fkRequestId');
            $table->foreign('fkRequestId')->references('id')->on('tbl_sc_requested_reports');*/
            $table->string('reportId')->nullable();
            $table->string('reportRequestId')->nullable();
            $table->string('reportRequestDate')->nullable();
            $table->string('snapshotDate')->nullable();
            $table->string('sku')->nullable();
            $table->string('fnsku')->nullable();
            $table->string('asin')->nullable();
            $table->text('productName')->nullable();
            $table->string('condition')->nullable();
            $table->string('salesRank')->nullable();
            $table->string('productGroup')->nullable();
            $table->string('totalQuantity')->nullable();
            $table->string('sellableQuantity')->nullable();
            $table->string('unsellableQuantity')->nullable();
            $table->string('invAge0To90Days')->nullable();
            $table->string('invAge91To180Days')->nullable();
            $table->string('invAge181To270Days')->nullable();
            $table->string('invAge271To365Days')->nullable();
            $table->string('invAge365PlusDays')->nullable();
            $table->string('unitsShippedLast24Hrs')->nullable();
            $table->string('unitsShippedLast7Days')->nullable();
            $table->string('unitsShippedLast30Days')->nullable();
            $table->string('unitsShippedLast90Days')->nullable();
            $table->string('unitsShippedLast180Days')->nullable();
            $table->string('unitsShippedLast365Days')->nullable();
            $table->string('weeksOfCoverT7')->nullable();
            $table->string('weeksOfCoverT30')->nullable();
            $table->string('weeksOfCoverT90')->nullable();
            $table->string('weeksOfCoverT180')->nullable();
            $table->string('weeksOfCoverT365')->nullable();
            $table->string('numAfnNewSellers')->nullable();
            $table->string('numAfnUsedSellers')->nullable();
            $table->string('currency')->nullable();
            $table->string('yourPrice')->nullable();
            $table->string('salesPrice')->nullable();
            $table->string('lowestAfnNewPrice')->nullable();
            $table->string('lowestAfnUsedPrice')->nullable();
            $table->string('lowestMfnNewPrice')->nullable();
            $table->string('lowestMfnUsedPrice')->nullable();
            $table->string('qtyToBeChargedlTsf12Mo')->nullable();
            $table->string('qtyInLongTermStorageProgram')->nullable();
            $table->string('qtyWithRemovalsInProgress')->nullable();
            $table->string('projectedlTsf12Mo')->nullable();
            $table->string('perUnitVolume')->nullable();
            $table->string('isHazmat')->nullable();
            $table->string('inBoundQuantity')->nullable();
            $table->string('asinLimit')->nullable();
            $table->string('inboundRecommendQuantity')->nullable();
            $table->string('qtyToBeChargedlTsf6Mo')->nullable();
            $table->string('projectedlTsf6Mo')->nullable();
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
        Schema::dropIfExists('tbl_sc_inventory_fba_health_report');
    }
}
