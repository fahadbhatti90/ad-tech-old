<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AmsBiddingUpdateTracker extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tbl_ams_bidding_tracker', function (Blueprint $table) {
            $table->increments('id');
            $table->string('profileId',50);
            $table->string('adGroupId',50);
            $table->string('campaignId',50);
            $table->string('state');
            $table->string('reportType',10);
            $table->string('oldBid');
            $table->string('bid');
            $table->string('keywordId',50);
            $table->string('code');
            $table->dateTime('creationDate');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('tbl_ams_bidding_tracker');
    }
}
