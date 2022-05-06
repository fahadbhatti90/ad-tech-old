<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTblVcVendorListScrap extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tbl_vc_vendor_list_scrap', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('businessName', 100);
            $table->string('url');
            $table->string('customerId');
            $table->string('marketscopeId', 50);
            $table->string('vendorGroupId', 50);
            $table->boolean('isScraped');
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
        Schema::dropIfExists('tbl_vc_vendor_list_scrap');
    }
}
