<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class TblScProductCategoryDetails extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tbl_sc_product_category_details', function (Blueprint $table) {
            $table->bigIncrements('id');
            //$table->integer('fkPdocutTblId')->nullable();
            $table->integer('fkProductTblId')->nullable();
            /*$table->unsignedBigInteger('fkPdocutTblId');
            $table->foreign('fkPdocutTblId')->references('id')->on('tbl_sc_product_ids');*/
            $table->bigInteger('fkAccountId')->nullable();
            $table->bigInteger('fkBatchId')->nullable();
            $table->string('source')->default('SC');
            $table->integer('isActive')->default(1);
            $table->integer('fkSellerConfigId')->nullable();
           /* $table->unsignedBigInteger('fkSellerConfigId');
            $table->foreign('fkSellerConfigId')->references('mws_config_id')->on('tbl_sc_config');*/
            $table->string('asin')->nullable();
            //$table->string('sku')->nullable();
            $table->string('productCategoryId')->nullable();
            $table->text('productCategoryName')->nullable();
            $table->integer('categoryTreeSequence')->nullable();
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
        Schema::dropIfExists('tbl_sc_product_category_details');//
    }
}
