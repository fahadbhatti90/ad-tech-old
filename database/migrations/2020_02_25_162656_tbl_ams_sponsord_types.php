<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class TblAmsSponsordTypes extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tbl_ams_sponsord_types', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('sponsordTypenName')->nullable();
            $table->string('isActive')->default(0);
   
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
         Schema::dropIfExists('tbl_ams_sponsord_types');
    }
}
