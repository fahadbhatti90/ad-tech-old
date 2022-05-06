<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class TblAsinSegmentsV2 extends Migration
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
                if (Schema::hasColumn('tbl_asin_segments', 'fkTagId')) {
                    $table->dropColumn('fkTagId');
                }
                if (!Schema::hasColumn('tbl_asin_segments', 'fkGroupId')) {
                    $table->bigInteger('fkGroupId')->after('fkSegmentId')->nullable();
                }
                if (!Schema::hasColumn('tbl_asin_segments', 'uniqueColumn')) {
                    $table->string('uniqueColumn', 50)->after('asin')->unique();
                }
                if (!Schema::hasColumn('tbl_asin_segments', 'isSegment')) {
                    $table->integer('isSegment')->after('asin')->default(0);
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
