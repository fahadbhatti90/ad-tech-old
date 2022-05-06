<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class TblSccatinactivereport extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tbl_sc_catalog_cat_inactive_report', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('fkAccountId')->nullable();
            $table->bigInteger('fkBatchId')->nullable();
            $table->integer('fkRequestId')->nullable();
            /*$table->unsignedBigInteger('fkRequestId');
            $table->foreign('fkRequestId')->references('id')->on('tbl_sc_requested_reports');*/
            $table->string('reportId')->nullable();
            $table->string('reportRequestId')->nullable();
            $table->string('reportRequestDate')->nullable();
            $table->text('itemName')->nullable();
            $table->text('itemDescription')->nullable();
            $table->string('listingId')->nullable();
            $table->string('sellerSku')->nullable();
            $table->string('price')->nullable();
            $table->string('quantity')->nullable();
            $table->string('openDate')->nullable();
            $table->string('imageUrl')->nullable();
            $table->string('itemIsMarketplace')->nullable();
            $table->string('productIdType')->nullable();
            $table->string('zshopShippingFee')->nullable();
            $table->string('itemNote')->nullable();
            $table->string('itemCondition')->nullable();
            $table->string('zshopCategory1')->nullable();
            $table->string('zshopBrowsePath')->nullable();
            $table->string('zshopStorefrontFeature')->nullable();
            $table->string('asin1')->nullable();
            $table->string('asin2')->nullable();
            $table->string('asin3')->nullable();
            $table->string('willShipInternationally')->nullable();
            $table->string('expeditedShipping')->nullable();
            $table->string('zshopBoldface')->nullable();
            $table->string('productId')->nullable();
            $table->string('bidForFeaturedPlacement')->nullable();
            $table->string('addDelete')->nullable();
            $table->string('pendingQuantity')->nullable();
            $table->string('fulfillmentChannel')->nullable();
            $table->string('merchantShippingGroup')->nullable();
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
        Schema::dropIfExists('tbl_sc_catalog_cat_inactive_report');
    }
}
