<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAmsProfileV2 extends Migration
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
                if (!Schema::hasColumn('tbl_ams_profiles', 'SponsoredDisplayProductAdsSixtyDays')) {
                    $table->boolean('SponsoredDisplayProductAdsSixtyDays')->default(0);
                }
                if (!Schema::hasColumn('tbl_ams_profiles', 'SponsoredDisplayAdgroupSixtyDays')) {
                    $table->boolean('SponsoredDisplayAdgroupSixtyDays')->default(0);
                }
            });
        }
    }
}
