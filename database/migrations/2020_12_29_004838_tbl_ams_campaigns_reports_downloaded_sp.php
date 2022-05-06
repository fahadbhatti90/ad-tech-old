<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class TblAmsCampaignsReportsDownloadedSp extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (Schema::hasTable('tbl_ams_campaigns_reports_downloaded_sp')) {
            Schema::table('tbl_ams_campaigns_reports_downloaded_sp', function (Blueprint $table) {
                if (!Schema::hasColumn('tbl_ams_campaigns_reports_downloaded_sp', 'fkConfigId')) {
                    $table->bigInteger('fkConfigId')->default(1)->after('fkAccountId');
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
