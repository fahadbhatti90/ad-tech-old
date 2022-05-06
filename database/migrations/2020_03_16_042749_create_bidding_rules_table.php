<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateBiddingRulesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tbl_ams_bidding_rules', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('fkUserId');
            $table->bigInteger('fKPreSetRule');
            $table->string('ruleName',100);
            $table->string('sponsoredType',50);
            $table->string('type',50);
            $table->string('lookBackPeriod',50);
            $table->string('lookBackPeriodDays',50);
            $table->text('pfCampaigns');
            $table->text('profileId');
            $table->string('frequency',50);
            $table->text('metric');
            $table->text('condition');
            $table->string('integerValues',100);
            $table->text('thenClause');
            $table->string('bidBy',100);
            $table->string('andOr',100);
            $table->text('ccEmails');
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
        Schema::dropIfExists('tbl_ams_bidding_rules');
    }
}
