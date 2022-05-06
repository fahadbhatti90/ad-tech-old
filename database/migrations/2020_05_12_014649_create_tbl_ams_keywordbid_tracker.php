<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTblAmsKeywordbidTracker extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tbl_ams_keywordbid_tracker', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('profileId');
            $table->unsignedBigInteger('campaignId');
            $table->boolean('status');
            $table->string('dated',50);
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
        Schema::dropIfExists('tbl_ams_keywordbid_tracker');
    }
}
