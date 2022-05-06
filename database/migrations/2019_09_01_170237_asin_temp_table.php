<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AsinTempTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tbl_asins', function (Blueprint $table) {
            $table->increments('asin_id');
            $table->integer('c_id')->unsigned();
            $table->string('asin_code', 100)->default('NA');
            $table->string('created_on', 50);
            $table->string('asin_status',300)->default('i');
            $table->string('allocatedThread',300)->default('NA');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('tbl_asins');
    }
}
