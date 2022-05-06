<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTblRtlAmsCampaignList extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable('tbl_rtl_ams_campaign_list')) {
            Schema::create('tbl_rtl_ams_campaign_list', function (Blueprint $table) {
                $table->integer('fkAccountId');
                $table->bigInteger('fkProfileId');
                $table->bigInteger('ProfileId');
                $table->bigInteger('campaignId');
                $table->string('campaignName',191)->charset('utf8mb4')->collation('utf8mb4_unicode_ci');
                $table->string('campaign_type',10)->charset('utf8mb4')->collation('utf8mb4_unicode_ci');
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('tbl_rtl_ams_campaign_list');
    }
}
