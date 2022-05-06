<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTblAsinsInstantTempSchedules extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tbl_asins_instant_temp_schedules', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('fkCollectionId')->unsigned();
            $table->boolean('isRunning')->default(false);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('tbl_asins_instant_temp_schedules');
    }
}
