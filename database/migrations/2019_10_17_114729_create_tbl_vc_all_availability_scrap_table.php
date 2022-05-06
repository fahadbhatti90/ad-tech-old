<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTblVcAllAvailabilityScrapTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tbl_vc_all_availability_scrap', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->longText('image');
            $table->longText('title');
            $table->string('asin', 100);
            $table->string('upc', 100);
            $table->string('sku', 100);
            $table->string('vendorCode', 100);
            $table->date('lastModifiedDate');
            $table->float('cost', 8, 2);
            $table->string('available', 50);
            $table->string('fkVendorGroupId', 50);
            $table->bigInteger('batchId')->unsigned();
            $table->bigInteger('fkAccountId')->unsigned();
            $table->string('offset', 50);
            $table->dateTime('created_at');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('tbl_vc_all_availability_scrap');
    }
}
