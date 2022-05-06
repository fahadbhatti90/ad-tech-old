<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAmsProfileV1 extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (Schema::hasTable('tbl_ams_profiles')) {
            Schema::table('tbl_ams_profiles', function (Blueprint $table) {
                if (!Schema::hasColumn('tbl_ams_profiles', 'SponsoredBrandCampaignsSixtyDays')) {
                    $table->boolean('SponsoredBrandCampaignsSixtyDays')->default(0);
                }
                if (!Schema::hasColumn('tbl_ams_profiles', 'SponsoredDisplayCampaignsSixtyDays')) {
                    $table->boolean('SponsoredDisplayCampaignsSixtyDays')->default(0);
                }
            });
        }
    }
}
