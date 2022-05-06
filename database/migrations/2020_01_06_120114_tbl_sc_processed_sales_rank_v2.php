<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class TblScProcessedSalesRankV2 extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('tbl_sc_processed_sales_rank', function (Blueprint $table) {

            //$table->string('productCategoryId')->default(0)->change();
            //$table->timestamp('updatedAt')->default('0000-00-00 00:00:00');
            //$table->timestamp('created_at')->useCurrent();
            $table->timestamp('updatedAt')->default(DB::raw('CURRENT_TIMESTAMP'));

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
