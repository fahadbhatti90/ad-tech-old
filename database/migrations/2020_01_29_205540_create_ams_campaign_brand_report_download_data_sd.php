<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAmsCampaignBrandReportDownloadDataSD extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tbl_ams_campaigns_reports_downloaded_sd', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('fkBatchId');
            $table->bigInteger('fkAccountId');
            $table->integer('fkReportsDownloadLinksId');
            $table->string('fkProfileId', 50);
            $table->string('campaignName');
            $table->string('campaignId', 50);
            $table->string('campaignStatus', 50);
            $table->string('impressions', 50);
            $table->string('clicks', 50);
            $table->string('cost', 50);
            $table->string('currency', 50);
            $table->string('attributedDPV14d', 50);
            $table->string('attributedUnitsSold14d', 50);
            $table->string('attributedSales14d', 50);
            $table->string('reportDate', 50);
            $table->date('creationDate');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('tbl_ams_campaigns_reports_downloaded_sd');
    }
}
