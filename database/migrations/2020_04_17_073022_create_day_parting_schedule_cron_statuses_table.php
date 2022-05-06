<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDayPartingScheduleCronStatusesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tbl_ams_day_parting_schedule_cron_statuses', function (Blueprint $table) {
            $table->bigInteger('fkScheduleId');
            $table->boolean('mon')->default(0)->comment('0:default, 1:Success, 2: Error');
            $table->boolean('tue')->default(0)->comment('0:default, 1:Success, 2: Error');
            $table->boolean('wed')->default(0)->comment('0:default, 1:Success, 2: Error');
            $table->boolean('thu')->default(0)->comment('0:default, 1:Success, 2: Error');
            $table->boolean('fri')->default(0)->comment('0:default, 1:Success, 2: Error');
            $table->boolean('sat')->default(0)->comment('0:default, 1:Success, 2: Error');
            $table->boolean('sun')->default(0)->comment('0:default, 1:Success, 2: Error');
            $table->string('cronMessage', 255)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('tbl_ams_day_parting_schedule_cron_statuses');
    }
}
