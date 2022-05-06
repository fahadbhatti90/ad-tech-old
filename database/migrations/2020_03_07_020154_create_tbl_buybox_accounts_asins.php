<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTblBuyboxAccountsAsins extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tbl_buybox_accounts_asins', function (Blueprint $table) {
            $table->bigInteger('fkAccountId')->unassigned();
            $table->bigInteger('fkAsinId')->unassigned();
            $table->string('uniqueColumn', 100)->unique();
            $table->string('createdaAt', 50);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('tbl_buybox_accounts_asins');
    }
}
