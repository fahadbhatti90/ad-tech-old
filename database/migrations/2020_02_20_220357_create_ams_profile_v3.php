<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAmsProfileV3 extends Migration
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
                if (!Schema::hasColumn('tbl_ams_profiles', 'isActive')) {
                    $table->boolean('isActive')->default(1);
                }
            });
        }
    }
}
