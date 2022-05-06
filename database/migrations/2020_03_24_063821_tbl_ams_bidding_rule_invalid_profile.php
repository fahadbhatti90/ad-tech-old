<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class TblAmsBiddingRuleInvalidProfile extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tbl_ams_bidding_rule_invalid_profile', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('fkId');
            $table->bigInteger('fkBiddingRuleId');
            $table->bigInteger('profileId');
            $table->bigInteger('campaignId');
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
        Schema::drop('tbl_ams_bidding_rule_invalid_profile');
    }
}
