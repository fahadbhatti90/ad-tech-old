<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class TblScSalesRank extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tbl_sc_sales_rank', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('fkProductTblId')->nullable();
            $table->bigInteger('fkAccountId')->nullable();
            $table->bigInteger('fkBatchId')->nullable();
            $table->string('source')->default('SC');
            $table->integer('isActive')->default(1);
            $table->integer('fkSellerConfigId')->nullable();
            $table->string('asin')->nullable();
            $table->string('productCategoryId')->default('NA');
            $table->string('salesRank')->default('NA');
            $table->integer('salesRankCount')->nullable();
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
        Schema::dropIfExists('tbl_sc_sales_rank');
    }
}
