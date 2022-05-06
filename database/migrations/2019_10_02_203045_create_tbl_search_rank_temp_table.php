c<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTblSearchRankTempTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tbl_search_rank_temp_urls', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('department_id')->unsigned();
            $table->integer('searchTerm_id')->unsigned();
            $table->string('searchRankUrl', 500);
            $table->smallInteger('pageNo')->unsigned();
            $table->string('urlStatus', 50);
            $table->string('allocatedThread', 50)->default("NA");
            $table->string('created_at', 50)->default('0000-00-00');
            
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('tbl_search_rank_temp_urls');
    }
}
