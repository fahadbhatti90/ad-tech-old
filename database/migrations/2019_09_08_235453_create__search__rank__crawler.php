<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSearchRankCrawler extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tbl_search_rank_crawler', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('d_id')->unsigned();
            $table->string('c_name', 200)->default('NA');
            $table->integer('c_frequency')->unsigned();
            $table->string('c_lastRun', 100)->default('0000-00-00');
            $table->string('c_nextRun', 100)->default('0000-00-00');
            $table->boolean('isRunning')->default(false);
            $table->string('created_at', 100)->default('0000-00-00');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('tbl_search_rank_crawler');
    }
}
