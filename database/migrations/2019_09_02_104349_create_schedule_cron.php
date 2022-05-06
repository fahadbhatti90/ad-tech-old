<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateScheduleCron extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tbl_schedule_cron', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('c_id')->unsigned();
            $table->string('cronStatus', 50);
            $table->string('lastRun', 50)->default("0");
            $table->string('cronDuration', 50)->default("0");
            
            $table->boolean('isRunning')->default(false);
            $table->string('created_at', 50)->default("0");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('tbl_schedule_cron');
    }
}
