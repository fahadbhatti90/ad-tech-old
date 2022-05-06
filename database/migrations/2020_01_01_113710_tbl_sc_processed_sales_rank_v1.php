<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class TblScProcessedSalesRankV1 extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('tbl_sc_processed_sales_rank', function (Blueprint $table) {

            $table->string('productCategoryId')->default(0)->change();
            $table->text('productCategoryName')->after('productCategoryId');
            $table->string('productSubCategoryId')->default(0)->after('productCategoryId');
            $table->text('productSubCategoryName')->after('productCategoryId');
            $table->string('salesRank')->default(0)->change();
        });
    }


    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
