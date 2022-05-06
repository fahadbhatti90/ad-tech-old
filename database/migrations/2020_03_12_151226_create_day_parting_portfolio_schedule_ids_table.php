<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDayPartingPortfolioScheduleIdsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tbl_ams_day_parting_portfolio_schedule_ids', function (Blueprint $table) {
            $table->bigInteger('fkScheduleId');
            $table->bigInteger('fkPortfolioId');
            $table->time('startTime')->nullable();
            $table->time('endTime')->nullable();
            $table->string('scheduleName', 100);
            $table->string('portfolioName', 255)->nullable();
            $table->boolean('mon')->default(0);
            $table->boolean('tue')->default(0);
            $table->boolean('wed')->default(0);
            $table->boolean('thu')->default(0);
            $table->boolean('fri')->default(0);
            $table->boolean('sat')->default(0);
            $table->boolean('sun')->default(0);
            $table->tinyInteger('userSelection')->default(0)->comment('1.Run today\'s schedule, then pause, 2.Pause campaigns immediately, 3.Campaigns enabled permanently');
            $table->string('enablingPausingTime', 20)->nullable();
            $table->string('enablingPausingStatus', 20)->nullable();
            $table->tinyInteger('isEnablingPausingDone')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('tbl_ams_day_parting_portfolio_schedule_ids');
    }
}
