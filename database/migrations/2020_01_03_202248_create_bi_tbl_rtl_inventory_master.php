<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateBiTblRtlInventoryMaster extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tbl_rtl_inventory_master', function (Blueprint $table) {
            $table->bigIncrements('rtl_inven_id');
            $table->integer('fk_account_id')->nullable();
            $table->string('asins', 20);
            $table->string('sku', 20)->nullable();
            $table->integer('seller_inv_units')->nullable();
            $table->integer('unseller_inv_units')->nullable();
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
        Schema::dropIfExists('tbl_rtl_inventory_master');
    }
}
