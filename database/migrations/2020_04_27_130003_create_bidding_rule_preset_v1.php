<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateBiddingRulePresetV1 extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //$table->string('cronLastRunTime')->after('andOr')->nullable();
        /*$table->string('frequency',50);
        $table->string('lookBackPeriod',50);
        $table->string('lookBackPeriodDays',50);*/
        if (Schema::hasTable('tbl_ams_bidding_rule_preset')) {
            Schema::table('tbl_ams_bidding_rule_preset', function (Blueprint $table) {
                if (!Schema::hasColumn('tbl_ams_bidding_rule_preset', 'lookBackPeriodDays')) {
                    $table->string('lookBackPeriodDays',50)->after('andOr');
                }
                if (!Schema::hasColumn('tbl_ams_bidding_rule_preset', 'lookBackPeriod')) {
                    $table->string('lookBackPeriod',50)->after('andOr');
                }
                if (!Schema::hasColumn('tbl_ams_bidding_rule_preset', 'frequency')) {
                    $table->string('frequency',50)->after('andOr');
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
        //Schema::dropIfExists('bidding_rule_preset_v1');
    }
}
