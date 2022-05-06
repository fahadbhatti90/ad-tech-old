<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class TblMwsConfig extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tbl_sc_config', function (Blueprint $table) {
            $table->bigIncrements('mws_config_id');
            //$table->bigInteger('fkAccountId')->nullable();
            //$table->bigInteger('fkBatchId')->nullable();
            $table->string('merchant_name')->nullable();
            $table->string('seller_id')->nullable();
            $table->string('mws_access_key_id')->nullable();
            $table->string('mws_authtoken')->nullable();
            $table->string('mws_secret_key')->nullable();
           // $table->string('marketplace_id')->nullable();
            //$table->string('is_active')->default(0)->nullable();
            $table->tinyInteger('is_active')->default(1);
            $table->tinyInteger('historical_data_downloaded')->default(0);

            $table->timestamp('created_at')->nullable();
            $table->timestamp('updated_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('tbl_sc_config');
    }
}
