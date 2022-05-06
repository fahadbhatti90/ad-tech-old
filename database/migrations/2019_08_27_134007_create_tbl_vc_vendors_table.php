<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTblVcVendorsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tbl_vc_vendors', function (Blueprint $table) {
            $table->bigIncrements('vendor_id', '11')->autoIncrement();
            $table->string('vendor_name', 255);
            $table->string('domain', 255);
            $table->string('tier', 255);
            $table->tinyInteger('vendor_status')->comment('0 = inActive, 1 = Active')->unsigned();
            $table->date('created_date');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('tbl_vc_vendors');
    }
}
