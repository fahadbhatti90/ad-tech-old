<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDayPartingScheduleCronStatusesV2Table extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('tbl_ams_day_parting_schedule_cron_statuses', function($table) {
            $table->dropColumn('mon')->default(0)->comment('0:default, 1:Success, 2: Error');
            $table->dropColumn('tue')->default(0)->comment('0:default, 1:Success, 2: Error');
            $table->dropColumn('wed')->default(0)->comment('0:default, 1:Success, 2: Error');
            $table->dropColumn('thu')->default(0)->comment('0:default, 1:Success, 2: Error');
            $table->dropColumn('fri')->default(0)->comment('0:default, 1:Success, 2: Error');
            $table->dropColumn('sat')->default(0)->comment('0:default, 1:Success, 2: Error');
            $table->dropColumn('sun')->default(0)->comment('0:default, 1:Success, 2: Error');
            $table->string('scheduleDate', 20)->nullable();

        });

        Schema::table('tbl_ams_day_parting_schedule_cron_statuses', function (Blueprint $table) {
            $table->boolean('scheduleStatus')->default(0)->comment('0:default, 1:Success, 2: Error')->after('scheduleDate');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('day_parting_schedule_cron_statuses_v2');
    }
}
