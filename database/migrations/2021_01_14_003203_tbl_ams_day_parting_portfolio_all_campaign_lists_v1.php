<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class TblAmsDayPartingPortfolioAllCampaignListsV1 extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (Schema::hasTable('tbl_ams_day_parting_portfolio_all_campaign_lists')) {
            Schema::table('tbl_ams_day_parting_portfolio_all_campaign_lists', function (Blueprint $table) {
                if (!Schema::hasColumn('tbl_ams_day_parting_portfolio_all_campaign_lists', 'fkConfigId')) {
                    $table->bigInteger('fkConfigId')->default(1)->after('fkProfileId');
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
        //
    }
}
