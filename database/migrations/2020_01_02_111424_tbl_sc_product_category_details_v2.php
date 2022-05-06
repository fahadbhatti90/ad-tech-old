<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class TblScProductCategoryDetailsV2 extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('tbl_sc_product_category_details', function (Blueprint $table) {

            //$table->string('productCategoryId')->default(0)->change();
            $table->integer('categoryTreeNumber')->default(1)->after('categoryTreeSequence');
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
