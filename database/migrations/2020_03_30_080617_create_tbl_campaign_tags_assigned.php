<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTblCampaignTagsAssigned extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::connection(\getDbAndConnectionName("c2"))->create('tbl_campaign_tags_assigned', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('campaignId');
            $table->unsignedBigInteger('fkAccountId');
            $table->unsignedBigInteger('fkTagId');
            $table->string("tag", 20);
            $table->smallInteger('type')->unassigned();
            $table->string('uniqueColumn', 100)->unique();
            $table->timestamp('createdAt');
            $table->timestamp('updatedAt');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::connection(\getDbAndConnectionName("c2"))->dropIfExists('tbl_campaign_tags_assigned');
    }
}
