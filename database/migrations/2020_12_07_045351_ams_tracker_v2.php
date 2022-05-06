<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AmsTrackerV2 extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (Schema::hasTable('tbl_ams_tracker')) {
            Schema::table('tbl_ams_tracker', function (Blueprint $table) {
                if (Schema::hasColumn('tbl_ams_tracker', 'reportName')) {
                    $table->mediumText('reportName')->change();
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
