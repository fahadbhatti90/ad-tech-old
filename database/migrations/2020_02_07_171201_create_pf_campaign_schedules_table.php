<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePfCampaignSchedulesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tbl_ams_day_parting_pf_campaign_schedules', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('fkManagerId')->unsigned();
            $table->string('managerEmail', 100);
            $table->string('scheduleName', 100);
            $table->string('portfolioCampaignType', 30);
            $table->boolean('mon')->default(0);
            $table->boolean('tue')->default(0);
            $table->boolean('wed')->default(0);
            $table->boolean('thu')->default(0);
            $table->boolean('fri')->default(0);
            $table->boolean('sat')->default(0);
            $table->boolean('sun')->default(0);
            $table->time('startTime');
            $table->time('endTime');
            $table->boolean('emailReceiptStart', 10)->default(0);
            $table->boolean('emailReceiptEnd', 10)->default(0);
            $table->boolean('isCronRunning')->default(0);
            $table->boolean('isCronSuccess')->default(0);
            $table->boolean('isCronError')->default(0);
            $table->boolean('isScheduleExpired')->default(0);
            $table->string('ccEmails', 255);
            $table->boolean('isActive');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('tbl_ams_day_parting_pf_campaign_schedules');
    }
}
