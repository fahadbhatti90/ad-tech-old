<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class TblAmsBiddingRuleCron extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tbl_ams_bidding_rule_cron', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('fkBiddingRuleId');
            $table->string('sponsoredType',50);
            $table->string('lookBackPeriodDays',50);
            $table->string('frequency',50);
            $table->boolean('runStatus')->default(false);
            $table->string('currentRunTime',50);
            $table->string('lastRunTime',50);
            $table->string('nextRunTime',50);
            $table->boolean('isActive');
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
        Schema::drop('tbl_ams_bidding_rule_cron');
    }
}
