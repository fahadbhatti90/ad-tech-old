<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTblAmsKeywordbid extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tbl_ams_keywordbid', function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->unsignedBigInteger('fkAccountId');
                $table->string('adtype',30);
                $table->string('type',10);
                $table->unsignedBigInteger('profileId');
                $table->unsignedBigInteger('keywordId');
                $table->unsignedBigInteger('adGroupId');
                $table->unsignedBigInteger('campaignId');
                $table->string('keywordText');
                $table->string('matchType');
                $table->string('state');
                $table->decimal('bid', 8, 2);
                $table->string('servingStatus');
                $table->string('creationDate');
                $table->string('lastUpdatedDate');
                $table->string('reportDate',50);
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
        Schema::dropIfExists('tbl_ams_keywordbid');
    }
}
