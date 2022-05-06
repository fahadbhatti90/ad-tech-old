<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTblCampaignList extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tbl_ams_campaign_list', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('fkProfileId');
            $table->bigInteger('profileId');
            $table->bigInteger('portfolioId');
            $table->bigInteger('campaignId');
            $table->string('type',50);
            $table->string('campaignType',100);
            $table->mediumText('name');
            $table->string('targetingType',50);
            $table->string('premiumBidAdjustment',20);
            $table->decimal('dailyBudget', 8, 2);
            $table->decimal('budget', 8, 2);
            $table->string('endDate',20);
            $table->string('bidOptimization',10);
            $table->string('budgetType',20);
            $table->string('startDate',10);
            $table->string('state',20);
            $table->string('servingStatus',20);
            $table->string('pageType',50);
            $table->mediumText('url');
            $table->string('brandName',100);
            $table->string('brandLogoAssetID',100);
            $table->mediumText('headline');
            $table->string('shouldOptimizeAsins',20);
            $table->mediumText('brandLogoUrl');
            $table->mediumText('asins');
            $table->string('strategy',100);
            $table->string('predicate',100);
            $table->integer('percentage');
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
        Schema::dropIfExists('tbl_ams_campaign_list');
    }
}
