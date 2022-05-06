<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTblAmsDayPartingDailyCampaigns extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tbl_ams_day_parting_daily_campaigns', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name', 255);
            $table->double('budget', 20, 2);
            $table->string('bidOptimization', 20);
            $table->string('targetingType', 20);
            $table->string('premiumBidAdjustment', 20);
            $table->string('strategy', 30);
            $table->string('predicate', 30);
            $table->bigInteger('percentage');
            $table->bigInteger('fkProfileId');
            $table->bigInteger('portfolioId')->default(0);
            $table->bigInteger('profileId');
            $table->string('campaignId', 50);
            $table->string('budgetType', 50);
            $table->string('startDate', 50);
            $table->string('state', 50);
            $table->string('servingStatus', 50);
            $table->string('brandName', 255);
            $table->string('brandLogoAssetID', 255);
            $table->longText('headline');
            $table->string('shouldOptimizeAsins', 20);
            $table->string('asins', 50);
            $table->longText('brandLogoUrl');
            $table->string('pageType', 50);
            $table->string('reportType', 20);
            $table->longText('url');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('tbl_ams_day_parting_daily_campaigns');
    }
}
