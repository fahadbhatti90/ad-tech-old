<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class TblScProcessedSalesRank extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //tbl_sc_processed_sales_rank
        Schema::create('tbl_sc_processed_sales_rank', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('fkProductTblId')->nullable();
            $table->bigInteger('fkAccountId')->nullable();
            $table->bigInteger('fkBatchId')->nullable();
            $table->string('source')->default('SC');
            $table->integer('isActive')->default(1);
            $table->integer('fkSellerConfigId')->nullable();
            $table->string('asin')->nullable();
            //$table->string('productCategoryId')->default('NA');
            $table->bigInteger('productCategoryId')->default(0);
            $table->bigInteger('salesRank')->default(0);
            //$table->integer('salesRankCount')->nullable();
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
        Schema::dropIfExists('tbl_sc_processed_sales_rank');
    }
}
