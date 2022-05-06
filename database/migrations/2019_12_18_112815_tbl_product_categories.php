<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class TblProductCategories extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tbl_product_categories', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('fkProductTblId')->nullable();
            $table->bigInteger('fkAccountId')->nullable();
            $table->bigInteger('fkBatchId')->nullable();
            $table->string('source')->default('SC');
            $table->integer('isActive')->default(1);
            $table->integer('fkSellerConfigId')->nullable();
            /* $table->unsignedBigInteger('fkSellerConfigId');
             $table->foreign('fkSellerConfigId')->references('mws_config_id')->on('tbl_sc_config');*/
            $table->string('asin')->nullable();
            $table->string('productCategoryId')->default(0);
            $table->text('productCategoryName')->nullable();
            $table->integer('categoryTreeSequence')->nullable();
            $table->integer('categoryTreeNumber')->nullable();
            //$table->integer('categoryTreeSequence')->nullable();
            $table->timestamp('createdAt')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('tbl_product_categories');
    }
}
