<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateBiTblRtlPurchaseorderMaster extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tbl_rtl_purchaseorder_master', function (Blueprint $table) {
            $table->bigIncrements('rtl_po_id');
            $table->integer('fk_account_id')->nullable();
            $table->string('po', 25)->nullable();
            $table->string('hand_off_type', 255)->nullable();
            $table->string('ship_location', 255)->nullable();
            $table->string('model_number', 255)->nullable();
            $table->string('asins', 255);
            $table->string('sku', 255)->nullable();
            $table->string('title', 255)->charset('utf8')->collation('utf8_unicode_ci')->nullable();
            $table->string('po_status', 20)->nullable();
            $table->string('delivery_window_start', 255)->nullable();
            $table->string('delivery_window_end', 255)->nullable();
            $table->string('backorder', 30)->nullable();
            $table->string('expected_ship_date', 255)->nullable();
            $table->string('confirmed_ship_date', 255)->nullable();
            $table->string('case_size', 255)->nullable();
            $table->integer('submitted_cases')->nullable();
            $table->integer('accepted_cases')->nullable();
            $table->integer('received_cases')->nullable();
            $table->integer('outstanding_cases')->nullable();
            $table->string('str_case_cost', 50)->nullable();
            $table->string('str_total_cost', 50)->nullable();
            $table->string('order_date', 255)->nullable();
            $table->integer('case_cost')->nullable();
            $table->integer('total_cost')->nullable();
            $table->integer('accepted_case')->nullable();
            $table->integer('rejected_case')->nullable();
            $table->integer('total_po_cost')->nullable();
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
        Schema::dropIfExists('tbl_rtl_purchaseorder_master');
    }
}
