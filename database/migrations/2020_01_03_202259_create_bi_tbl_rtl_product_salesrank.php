<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateBiTblRtlProductSalesrank extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tbl_rtl_product_salesrank', function (Blueprint $table) {
            $table->bigIncrements('tbl_sal_id');
            $table->integer('fk_account_id');
            $table->string('ASIN', 20);
            $table->string('category_id', 100)->nullable();
            $table->string('category_name', 100)->nullable();
            $table->string('subcategory_id', 100)->nullable();
            $table->string('subcategory_name', 100)->nullable();
            $table->bigInteger('sales_rank')->nullable();
            $table->string('batchid', 20)->nullable();
            $table->dateTime('LoadDate')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('tbl_rtl_product_salesrank');
    }
}
