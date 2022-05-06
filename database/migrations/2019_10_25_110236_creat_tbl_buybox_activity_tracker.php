<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatTblBuyboxActivityTracker extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tbl_buybox_activity_tracker', function (Blueprint $table) {
            $table->increments('id');
            $table->longText('activity')->nullable();
            $table->string('activity_type', 255)->nullable()->default('NA');
            $table->string('cron_type', 255)->nullable()->default('NA');
            $table->string('file_path', 255)->nullable()->default('NA');
            $table->dateTime('activity_time');
       
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('tbl_buybox_activity_tracker');
    }
}
