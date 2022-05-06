<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatebiddingrulecrondataV1 extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (Schema::hasTable('tbl_ams_bidding_rules_portfolio_campaign_data_cron')) {
            Schema::table('tbl_ams_bidding_rules_portfolio_campaign_data_cron', function (Blueprint $table) {
                if (!Schema::hasColumn('tbl_ams_bidding_rules_portfolio_campaign_data_cron', 'isDone')) {
                    $table->boolean('isDone')->default(0);
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
        Schema::table('tbl_ams_bidding_rules_portfolio_campaign_data_cron', function (Blueprint $table) {
            if (Schema::hasColumn('tbl_ams_bidding_rules_portfolio_campaign_data_cron', 'isDone')) {
                $table->dropColumn('isDone');
            }
        });
    }
}
