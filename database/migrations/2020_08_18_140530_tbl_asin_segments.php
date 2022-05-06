<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class TblAsinSegments extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tbl_asin_segments', function (Blueprint $table) {
        $table->bigIncrements('id');
        $table->bigInteger('fkAccountId');
        $table->bigInteger('fkSegmentId');
        $table->bigInteger('fkTagId');
        $table->string('asin', 50);
        $table->timestamp('createdAt')->default(DB::raw('CURRENT_TIMESTAMP'));
        $table->timestamp('updatedAt')->default(DB::raw('CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP'));
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('tbl_asin_segments');
    }
}
