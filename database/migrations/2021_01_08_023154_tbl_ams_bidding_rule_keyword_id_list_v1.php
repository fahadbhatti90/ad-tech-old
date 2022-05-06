<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class TblAmsBiddingRuleKeywordIdListV1 extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (Schema::hasTable('tbl_ams_bidding_rule_keywordId_list')) {
            Schema::table('tbl_ams_bidding_rule_keywordId_list', function (Blueprint $table) {
                if (!Schema::hasColumn('tbl_ams_bidding_rule_keywordId_list', 'fkConfigId')) {
                    $table->bigInteger('fkConfigId')->default(1)->after('fkBiddingRuleId');
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
