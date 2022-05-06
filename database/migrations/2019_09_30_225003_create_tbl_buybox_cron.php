<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTblBuyboxCron extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tbl_buybox_cron', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('email', 100);
            $table->string('cNameBuybox', 100);
            $table->string('frequency', 100);
            $table->integer('currentFrequency')->unsigned();
            $table->string('duration', 100);
            $table->string('nextRun', 100);
            $table->string('nextRunTime', 100);
            $table->integer('hoursToAdd')->unsigned();
            $table->integer('cronStatus');
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
        Schema::dropIfExists('tbl_buybox_cron');
    }
}
