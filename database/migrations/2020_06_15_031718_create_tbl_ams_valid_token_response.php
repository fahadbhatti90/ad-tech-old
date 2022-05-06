<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTblAmsValidTokenResponse extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tbl_ams_valid_token_response', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('aud',70);
            $table->string('user_id',50);
            $table->string('iss',30);
            $table->integer('exp')->unsigned();
            $table->string('app_id',60);
            $table->integer('iat')->unsigned();
            $table->dateTime('createdAt');
            $table->dateTime('updatedAt');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('tbl_ams_valid_token_response');
    }
}
