<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAmsProfiles extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tbl_ams_profiles', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('profileId',50);
            $table->string('countryCode',50);
            $table->string('currencyCode',50);
            $table->string('timezone',50);
            $table->string('marketplaceStringId',50);
            $table->string('entityId',50);
            $table->string('type',50);
            $table->string('name',50);
            $table->boolean('adGroupSpSixtyDays');
            $table->boolean('aSINsSixtyDays');
            $table->boolean('campaignSpSixtyDays');
            $table->boolean('keywordSbSixtyDays');
            $table->boolean('keywordSpSixtyDays');
            $table->boolean('productAdsSixtyDays');
            $table->boolean('productTargetingSixtyDays');
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
        Schema::dropIfExists('tbl_ams_profiles');
    }
}
