<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class TblAsinSegmentsV3 extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (Schema::hasTable('tbl_asin_segments')) {
            Schema::table('tbl_asin_segments', function (Blueprint $table) {
                if (Schema::hasColumn('tbl_asin_segments', 'isSegment')) {
                    $table->dropColumn('isSegment');
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
