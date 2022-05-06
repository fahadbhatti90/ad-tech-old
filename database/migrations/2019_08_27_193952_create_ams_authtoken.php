<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAmsAuthtoken extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tbl_ams_authtoken', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('client_id');
            $table->longText('access_token');
            $table->longText('refresh_token');
            $table->string('token_type',50);
            $table->string('expires_in',50);
            $table->dateTime('creationDate');
            $table->dateTime('updationDate')->useCurrent();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('tbl_ams_authtoken');
    }
}
