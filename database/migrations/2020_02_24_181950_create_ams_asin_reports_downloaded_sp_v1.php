<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAmsAsinReportsDownloadedSpV1 extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('tbl_ams_asin_reports_downloaded_sp', function (Blueprint $table) {
            $table->string('attributedUnitsOrdered1d',50)->default(0);
            $table->string('attributedUnitsOrdered7d',50)->default(0);
            $table->string('attributedUnitsOrdered14d',50)->default(0);
            $table->string('attributedUnitsOrdered30d',50)->default(0);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
    }
}
