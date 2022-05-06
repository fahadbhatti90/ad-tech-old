<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddColumnToTblAsinsResult extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('tbl_asins_result', function (Blueprint $table) {
            $table->unsignedBigInteger('fkAsinId')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('tbl_asins_result', function (Blueprint $table) {
            $table->dropColumn('fkAsinId');
        });
    }
}
