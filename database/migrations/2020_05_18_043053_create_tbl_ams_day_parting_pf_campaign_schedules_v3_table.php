<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTblAmsDayPartingPfCampaignSchedulesV3Table extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
            Schema::table('tbl_ams_day_parting_pf_campaign_schedules', function (Blueprint $table) {
                $table->bigInteger('isCronEnd')->after('isCronError')->default(0);
            });
    }
}
