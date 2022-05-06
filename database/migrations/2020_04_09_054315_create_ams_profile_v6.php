<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAmsProfileV6 extends Migration
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
                if (!Schema::hasColumn('tbl_ams_profiles', 'SponsoredBrandAdgroupSixtyDays')) {
                    $table->boolean('SponsoredBrandAdgroupSixtyDays')->default(0);
                }
                if (!Schema::hasColumn('tbl_ams_profiles', 'SponsoredBrandTargetingSixtyDays')) {
                    $table->boolean('SponsoredBrandTargetingSixtyDays')->default(0);
                }
            });
        }
    }
}
