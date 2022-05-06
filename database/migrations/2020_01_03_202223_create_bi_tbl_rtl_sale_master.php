<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateBiTblRtlSaleMaster extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tbl_rtl_sale_master', function (Blueprint $table) {
            $table->bigIncrements('rtl_sal_id');
            $table->integer('fk_account_id')->nullable();
            $table->string('ASIN', 20);
            $table->string('sku', 20)->nullable();
            $table->decimal('shipped_cogs', 19, 4)->nullable();
            $table->decimal('shipped_unit', 19, 4)->nullable();
            $table->decimal('shipped_cogs_last_year', 19, 4)->nullable();
            $table->decimal('shipped_units_last_year', 19, 4)->nullable();
            $table->timestamp('capture_date')->nullable();
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
        Schema::dropIfExists('tbl_rtl_sale_master');
    }
}
