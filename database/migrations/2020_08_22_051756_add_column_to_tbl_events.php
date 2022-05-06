<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddColumnToTblEvents extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('tbl_events', function (Blueprint $table) {
            $table->string('eventColor',50)->after('isEventAuto');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('tbl_events', function (Blueprint $table) {
            if (Schema::hasColumn('tbl_events', 'eventColor')) {
                $table->dropColumn('eventColor');
            }
        });
    }
}
