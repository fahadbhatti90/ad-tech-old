<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAmsCampaignBrandReportDownloadDataSB extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tbl_ams_campaigns_reports_downloaded_sb', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('fkBatchId');
            $table->bigInteger('fkAccountId');
            $table->integer('fkReportsDownloadLinksId');
            $table->string('fkProfileId', 50);
            $table->string('campaignName');
            $table->string('campaignId',50);
            $table->string('campaignStatus',50);
            $table->string('campaignBudget',50);
            $table->string('campaignBudgetType',50);
            $table->string('impressions',50);
            $table->string('clicks',50);
            $table->string('cost',50);
            $table->string('attributedDetailPageViewsClicks14d',50);
            $table->string('attributedSales14d',50);
            $table->string('attributedSales14dSameSKU',50);
            $table->string('attributedConversions14d',50);
            $table->string('attributedConversions14dSameSKU',50);
            $table->string('attributedOrdersNewToBrand14d',50);
            $table->string('attributedOrdersNewToBrandPercentage14d',50);
            $table->string('attributedOrderRateNewToBrand14d',50);
            $table->string('attributedSalesNewToBrand14d',50);
            $table->string('attributedSalesNewToBrandPercentage14d',50);
            $table->string('attributedUnitsOrderedNewToBrand14d',50);
            $table->string('attributedUnitsOrderedNewToBrandPercentage14d',50);
            $table->string('unitsSold14d',50);
            $table->string('dpv14d',50);
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
        Schema::dropIfExists('tbl_ams_campaigns_reports_downloaded_sb');
    }
}
