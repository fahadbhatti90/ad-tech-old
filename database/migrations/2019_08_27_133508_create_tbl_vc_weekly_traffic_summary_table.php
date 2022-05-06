<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTblVcWeeklyTrafficSummaryTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tbl_vc_weekly_traffic_summary', function (Blueprint $table) {
            $table->bigIncrements('traffic_summary_id')->autoIncrement();
            $table->bigInteger('fk_vendor_id')->unsigned();
            $table->bigInteger('batchId')->unsigned();
            $table->bigInteger('fkAccountId')->unsigned();
            //$table->foreign('fk_vendor_id')->references('vendor_id')->on('tbl_vc_vendors');
//            $table->string('str_change_glance_view_reported', 255);
//            $table->string('str_change_glance_view_prior_period', 255);
//            $table->string('str_change_glance_view_last_year', 255);
//            $table->string('str_change_conversion_reported', 255);
//            $table->string('str_change_conversion_prior_period', 255);
//            $table->string('str_change_conversion_last_year', 255);
//            $table->string('str_change_unique_visitors_reported', 255);
//            $table->string('str_change_unique_visitors_prior_period', 255);
//            $table->string('str_change_unique_visitors_last_year', 255);
//            $table->string('str_fast_track_glance_view_reported', 255);
//            $table->string('str_fast_track_glance_view_last_year', 255);
//            $table->string('str_fast_track_glance_view_prior_period', 255);
            $table->double('change_glance_view_reported', 20, 2);
            $table->double('change_glance_view_prior_period', 20, 2);
            $table->double('change_glance_view_last_year', 20, 2);
            $table->double('change_conversion_reported', 20, 2);
            $table->double('change_conversion_prior_period', 20, 2);
            $table->double('change_conversion_last_year', 20, 2);
            $table->double('change_unique_visitors_reported', 20, 2);
            $table->double('change_unique_visitors_prior_period', 20, 2);
            $table->double('change_unique_visitors_last_year', 20, 2);
            $table->double('fast_track_glance_view_reported', 20, 2);
            $table->double('fast_track_glance_view_prior_period', 20, 2);
            $table->double('fast_track_glance_view_last_year', 20, 2);
            $table->date('start_date');
            $table->date('end_date');
            $table->date('capture_date');
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
        Schema::dropIfExists('tbl_vc_weekly_traffic_summary');
    }
}
