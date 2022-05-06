<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTblAmsDayPartingPfCampaignSchedulesV1Table extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (Schema::hasTable('tbl_ams_day_parting_pf_campaign_schedules')) {
            Schema::table('tbl_ams_day_parting_pf_campaign_schedules', function (Blueprint $table) {
                if (!Schema::hasColumn('tbl_ams_day_parting_pf_campaign_schedules', 'fkBrandId')) {
                    $table->bigInteger('fkBrandId');
                }
            });
        }
    }
}
