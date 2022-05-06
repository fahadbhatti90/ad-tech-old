<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTblRtlAmsAsinList extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable('tbl_rtl_ams_asin_list')) {
            Schema::create('tbl_rtl_ams_asin_list', function (Blueprint $table) {
                $table->bigInteger('fkProfileId')->nullable();
                $table->bigInteger('campaignId')->nullable();
                $table->string('asin',40)->nullable();
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('tbl_rtl_ams_asin_list');
    }
}
