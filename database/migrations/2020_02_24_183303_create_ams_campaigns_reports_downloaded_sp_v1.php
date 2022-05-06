<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAmsCampaignsReportsDownloadedSpV1 extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('tbl_ams_campaigns_reports_downloaded_sp', function (Blueprint $table) {
            $table->string('attributedUnitsOrdered1dSameSKU',50)->default(0);
            $table->string('attributedUnitsOrdered7dSameSKU',50)->default(0);
            $table->string('attributedUnitsOrdered14dSameSKU',50)->default(0);
            $table->string('attributedUnitsOrdered30dSameSKU',50)->default(0);
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
