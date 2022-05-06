<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ChangeTblClientsToTblBrands extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //Schema::table('tbl_brands', function (Blueprint $table) {
            //
        //});
        Schema::rename('tbl_client', 'tbl_brands');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //Schema::table('tbl_brands', function (Blueprint $table) {
            //
        //});
    }
}
