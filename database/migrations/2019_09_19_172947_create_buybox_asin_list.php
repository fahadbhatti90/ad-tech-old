<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateBuyboxAsinList extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tbl_buybox_asin_list', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('cNameBuybox', 100);
            $table->string('frequency',10);
            $table->string('duration',10);
            $table->string('asinCode',15);
            $table->integer('scrapStatus');
            $table->dateTime('createdAt');
            $table->dateTime('updatedAt');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('tbl_buybox_asin_list');
    }
}
