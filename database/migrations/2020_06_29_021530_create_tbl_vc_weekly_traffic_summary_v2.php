<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTblVcWeeklyTrafficSummaryV2 extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (Schema::hasTable('tbl_vc_weekly_traffic_summary')) {
            Schema::table('tbl_vc_weekly_traffic_summary', function (Blueprint $table) {
                if (!Schema::hasColumn('tbl_vc_weekly_traffic_summary', 'asin')) {
                    $table->string('asin',50)->default('NA');
                }if (!Schema::hasColumn('tbl_vc_weekly_traffic_summary', 'product_title')) {
                    $table->text('product_title');
                }if (!Schema::hasColumn('tbl_vc_weekly_traffic_summary', 'subcategory')) {
                    $table->string('subcategory')->default('NA');
                }if (!Schema::hasColumn('tbl_vc_weekly_traffic_summary', 'category')) {
                    $table->string('category')->default('NA');
                }if (!Schema::hasColumn('tbl_vc_weekly_traffic_summary', 'percentage_total_gvs')) {
                    $table->double('percentage_total_gvs', 20, 2)->default(0.00);
                }if (!Schema::hasColumn('tbl_vc_weekly_traffic_summary', 'conversion_percentile')) {
                    $table->double('conversion_percentile', 20, 2)->default(0.00);
                }if (!Schema::hasColumn('tbl_vc_weekly_traffic_summary', 'fast_track_glance_view')) {
                    $table->double('fast_track_glance_view', 20, 2)->default(0.00);
                }
            });
        }
    }
}