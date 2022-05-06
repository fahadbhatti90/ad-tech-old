<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddFlagTblCampaignList extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (Schema::hasTable('tbl_ams_campaign_list')) {
            Schema::table('tbl_ams_campaign_list', function (Blueprint $table) {
                if (!Schema::hasColumn('tbl_ams_campaign_list', 'isActive')) {
                    $table->boolean('isActive')->default(0);
                }
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
        Schema::table('tbl_ams_campaign_list', function (Blueprint $table) {
            if (Schema::hasColumn('tbl_ams_campaign_list', 'isActive')) {
                $table->dropColumn('isActive');
            }
        });
    }
}
