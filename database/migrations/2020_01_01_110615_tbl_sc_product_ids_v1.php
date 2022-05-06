<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class TblScProductIdsV1 extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('tbl_sc_product_ids', function (Blueprint $table) {
            $table->string('productDetailsInQueue')->default(0)->after('productDetailsDownloaded');
            $table->string('productCategoryDetailsInQueue')->default(0)->after('productCategoryDetailsDownloaded');
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
