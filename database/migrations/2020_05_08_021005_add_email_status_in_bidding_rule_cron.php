<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddEmailStatusInBiddingRuleCron extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (Schema::hasTable('tbl_ams_bidding_rule_cron')) {
            Schema::table('tbl_ams_bidding_rule_cron', function (Blueprint $table) {
                if (!Schema::hasColumn('tbl_ams_bidding_rule_cron', 'emailSent')) {
                    $table->boolean('emailSent')->default(false);
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
        Schema::table('tbl_ams_bidding_rule_cron', function (Blueprint $table) {
            if (Schema::hasColumn('tbl_ams_bidding_rule_cron', 'emailSent')) {
                $table->dropColumn('emailSent');
            }
        });
    }
}
