<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTblBuyBoxTempUrls extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tbl_buybox_temp_urls', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('fk_bbc_id');
            $table->bigInteger('fk_bb_asin_list_id');
            $table->string('frequency',10);
            $table->string('asinCode',15);
            $table->string('scrapStatus');
            $table->string('allocatedThread');
            $table->dateTime('createdAt');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('tbl_buybox_temp_urls');
    }
}
