<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class Chnageinbiddingrule extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (Schema::hasTable('tbl_ams_bidding_rules')) {
            Schema::table('tbl_ams_bidding_rules', function (Blueprint $table) {
                if (!Schema::hasColumn('tbl_ams_bidding_rules', 'fkBrandId')) {
                    $table->bigInteger('fkBrandId');
                }
            });
        }
    }
}
