<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTblBatchId extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tbl_batch_id', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('fkAccountId');
            $table->bigInteger('batchId');
            $table->bigInteger('reportDate');
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
        Schema::dropIfExists('tbl_batch_id');
    }
}
