<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AmsAuthtokenV2 extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (Schema::hasTable('tbl_ams_authtoken')) {
            Schema::table('tbl_ams_authtoken', function (Blueprint $table) {
                if (!Schema::hasColumn('tbl_ams_authtoken', 'fkConfigId')) {
                    $table->bigInteger('fkConfigId')->default(1)->after('id');
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
