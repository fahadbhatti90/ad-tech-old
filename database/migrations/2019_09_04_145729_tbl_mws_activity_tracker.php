<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class TblMwsActivityTracker extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tbl_sc_activity_tracker', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('activity')->nullable();
            $table->string('cron_type')->nullable();
            $table->string('file_path')->nullable();
            $table->timestamp('activity_time')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('tbl_sc_activity_tracker');
    }
}
