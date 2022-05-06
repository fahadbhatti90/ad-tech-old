<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTblAmsBiddingRulesPortfolioCampaignDataCron extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tbl_ams_bidding_rules_portfolio_campaign_data_cron', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('fkBiddingRuleId');
            $table->string('sponsoredType', 50);
            $table->string('type', 50);
            $table->string('frequency', 50);
            $table->bigInteger('profileId');
            $table->bigInteger('campaignId');
            $table->bigInteger('portfolioId');
            $table->string('reportType', 50);
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
        Schema::dropIfExists('tbl_ams_bidding_rules_portfolio_campaign_data_cron');
    }
}
