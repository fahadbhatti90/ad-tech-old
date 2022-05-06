<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTblStageVcWeeklyTrafficSummary extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tbl_stage_vc_weekly_traffic_summary', function (Blueprint $table) {
            $table->bigIncrements('traffic_summary_id')->autoIncrement();
            $table->bigInteger('fk_vendor_id')->unsigned();
            $table->bigInteger('batchId')->unsigned();
            $table->bigInteger('fkAccountId')->unsigned();
            $table->string('asin',50);
            $table->text('product_title');
            $table->string('category');
            $table->string('subcategory');
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
            $table->double('fast_track_glance_view', 20, 2);
            $table->double('conversion_percentile', 20, 2);
            $table->double('percentage_total_gvs', 20, 2);
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
        Schema::dropIfExists('tbl_stage_vc_weekly_traffic_summary');
    }
}
