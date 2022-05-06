<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAmsApiTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tb_ams_api', function (Blueprint $table) {
            $table->bigIncrements('id')->autoIncrement();
            $table->string('grant_type');
            $table->longText('refresh_token');
            $table->string('client_id');
            $table->longText('client_secret');
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
        Schema::dropIfExists('tb_ams_api');
    }
}
