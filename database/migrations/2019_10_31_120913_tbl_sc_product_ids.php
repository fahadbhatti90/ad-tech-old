<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class TblScProductIds extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tbl_sc_product_ids', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('fkAccountId')->nullable();
            $table->bigInteger('fkBatchId')->nullable();
            $table->integer('fkSellerConfigId')->nullable();
            /*$table->unsignedBigInteger('fkSellerConfigId');
            $table->foreign('fkSellerConfigId')->references('mws_config_id')->on('tbl_sc_config');*/
            $table->string('asin')->nullable();
            //$table->string('sku')->nullable();
            $table->string('idType')->nullable();
            $table->string('productDetailsDownloaded')->default(0);
            $table->string('productCategoryDetailsDownloaded')->default(0);
            $table->string('productSalesRankCoppied')->default(0);
            $table->string('source')->nullable();
            $table->timestamp('createdAt')->nullable();
            $table->timestamp('updatedAt')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('tbl_sc_product_ids');
    }
}
