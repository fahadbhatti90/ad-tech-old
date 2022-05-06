<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class TblAsinSegmentsV4 extends Migration
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
                if (!Schema::hasColumn('tbl_asin_segments', 'occurrenceDate')) {
                    $table->string('occurrenceDate', 50)->after('asin')->default("0000:00:00");
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
