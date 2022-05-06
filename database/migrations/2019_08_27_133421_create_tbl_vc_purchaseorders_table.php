<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTblVcPurchaseordersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tbl_vc_purchaseorders', function (Blueprint $table) {
            $table->bigIncrements('po_id')->autoIncrement();
            $table->bigInteger('fk_vendor_id')->unsigned();
            $table->bigInteger('batchId')->unsigned();
            $table->bigInteger('fkAccountId')->unsigned();
            //$table->foreign('fk_vendor_id')->references('vendor_id')->on('tbl_vc_vendors');
            $table->string('po', 100);
            $table->string('vendor', 100);
            $table->string('warehouse', 255);
            $table->string('ship_to_location', 100);
            $table->string('model_number', 100);
            $table->string('asin', 50);
            $table->string('product_id_type', 50);
            $table->string('availability', 50);
            $table->string('externalid', 100);
            $table->string('sku', 50);
            $table->longText('title');
            $table->string('ack_code_translation_id', 100);
            $table->string('hand_off_type', 100);
            $table->string('status', 100);
            $table->string('delivery_window_start', 100);
            $table->string('delivery_window_end', 100);
            $table->string('backorder', 100);
            $table->string('expected_delivery_date', 255);
            $table->string('confirmed_delivery_date', 255);
            $table->string('case_size', 100);
            $table->string('submitted_cases', 100);
            $table->string('accepted_cases', 100);
            $table->string('received_cases', 100);
            $table->string('outstanding_cases', 100);
            $table->string('orderon_date', 255);
            $table->double('case_cost', 20, 2);
            $table->double('total_cost', 20, 2);
            $table->date('capture_date');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('tbl_vc_purchaseorders');
    }
}
