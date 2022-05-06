<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class Collection extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tbl_asin_collection', function (Blueprint $table) {
            $table->increments('id');
            $table->string('c_name', 100);
            $table->boolean('c_type')->default(true);
            $table->dateTime('created_at');
            $table->boolean('isNew')->default(true);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('tbl_asin_collection');
    }
}
