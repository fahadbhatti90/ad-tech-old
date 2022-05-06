<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAmsCrons extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tbl_ams_crons', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('cronType',100);
            $table->string('cronTime',50);
            $table->string('cronStatus',50);
            $table->string('lastRun',50);
            $table->string('modifiedDate',50);
            $table->string('cronRun',50);
            $table->string('nextRunTime',50);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('tbl_ams_crons');
    }
}
