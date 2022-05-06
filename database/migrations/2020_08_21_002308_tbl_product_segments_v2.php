<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class TblProductSegmentsV2 extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {

        if (Schema::hasTable('tbl_product_segments')) {
            Schema::table('tbl_product_segments', function (Blueprint $table) {
                if (!Schema::hasColumn('tbl_product_segments', 'fkGroupId')) {
                    $table->bigInteger('fkGroupId')->after('segmentName')->nullable();
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
