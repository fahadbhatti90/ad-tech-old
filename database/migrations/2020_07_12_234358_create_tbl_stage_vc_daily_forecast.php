<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTblStageVcDailyForecast extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tbl_stage_vc_daily_forecast', function (Blueprint $table) {
            $table->bigIncrements('daily_forecast_id')->autoIncrement();
            $table->bigInteger('fk_vendor_id')->unsigned();
            $table->bigInteger('batchId')->unsigned();
            $table->bigInteger('fkAccountId')->unsigned();
            $table->string('asin', 50);
            $table->longText('product_title');
            $table->longText('category');
            $table->string('strCategory', 100);
            $table->bigInteger('fkCategoryId')->default(0);
            $table->longText('subcategory');
            $table->double('rep_oos', 20, 2);
            $table->double('rep_oos_percentage_total', 20, 2);
            $table->double('rep_oos_prior_period', 20, 2);
            $table->bigInteger('shipped_units');
            $table->double('shipped_units_prior_period', 20, 2);
            $table->bigInteger('unfilled_customer_ordered_units');
            $table->bigInteger('available_inventory');
            $table->double('available_inventory_prior_period', 20, 2);
            $table->bigInteger('weeks_on_hand');
            $table->bigInteger('open_purchase_order_quantity');
            $table->double('open_purchase_order_quantity_prior_period', 20, 2);
            $table->double('receive_fill_rate', 20, 2);
            $table->double('overall_vendor_lead_time_days', 20, 2);
            $table->string('replenishment_category', 50);
            $table->double('week_1_mean_forecast', 20, 2);
            $table->double('week_2_mean_forecast', 20, 2);
            $table->double('week_3_mean_forecast', 20, 2);
            $table->double('week_4_mean_forecast', 20, 2);
            $table->double('week_5_mean_forecast', 20, 2);
            $table->double('week_6_mean_forecast', 20, 2);
            $table->double('week_7_mean_forecast', 20, 2);
            $table->double('week_8_mean_forecast', 20, 2);
            $table->double('week_9_mean_forecast', 20, 2);
            $table->double('week_10_mean_forecast', 20, 2);
            $table->double('week_11_mean_forecast', 20, 2);
            $table->double('week_12_mean_forecast', 20, 2);
            $table->double('week_13_mean_forecast', 20, 2);
            $table->double('week_14_mean_forecast', 20, 2);
            $table->double('week_15_mean_forecast', 20, 2);
            $table->double('week_16_mean_forecast', 20, 2);
            $table->double('week_17_mean_forecast', 20, 2);
            $table->double('week_18_mean_forecast', 20, 2);
            $table->double('week_19_mean_forecast', 20, 2);
            $table->double('week_20_mean_forecast', 20, 2);
            $table->double('week_21_mean_forecast', 20, 2);
            $table->double('week_22_mean_forecast', 20, 2);
            $table->double('week_23_mean_forecast', 20, 2);
            $table->double('week_24_mean_forecast', 20, 2);
            $table->double('week_25_mean_forecast', 20, 2);
            $table->double('week_26_mean_forecast', 20, 2);
            $table->double('week_1_p70_forecast', 20, 2);
            $table->double('week_2_p70_forecast', 20, 2);
            $table->double('week_3_p70_forecast', 20, 2);
            $table->double('week_4_p70_forecast', 20, 2);
            $table->double('week_5_p70_forecast', 20, 2);
            $table->double('week_6_p70_forecast', 20, 2);
            $table->double('week_7_p70_forecast', 20, 2);
            $table->double('week_8_p70_forecast', 20, 2);
            $table->double('week_9_p70_forecast', 20, 2);
            $table->double('week_10_p70_forecast', 20, 2);
            $table->double('week_11_p70_forecast', 20, 2);
            $table->double('week_12_p70_forecast', 20, 2);
            $table->double('week_13_p70_forecast', 20, 2);
            $table->double('week_14_p70_forecast', 20, 2);
            $table->double('week_15_p70_forecast', 20, 2);
            $table->double('week_16_p70_forecast', 20, 2);
            $table->double('week_17_p70_forecast', 20, 2);
            $table->double('week_18_p70_forecast', 20, 2);
            $table->double('week_19_p70_forecast', 20, 2);
            $table->double('week_20_p70_forecast', 20, 2);
            $table->double('week_21_p70_forecast', 20, 2);
            $table->double('week_22_p70_forecast', 20, 2);
            $table->double('week_23_p70_forecast', 20, 2);
            $table->double('week_24_p70_forecast', 20, 2);
            $table->double('week_25_p70_forecast', 20, 2);
            $table->double('week_26_p70_forecast', 20, 2);
            $table->double('week_1_p80_forecast', 20, 2);
            $table->double('week_2_p80_forecast', 20, 2);
            $table->double('week_3_p80_forecast', 20, 2);
            $table->double('week_4_p80_forecast', 20, 2);
            $table->double('week_5_p80_forecast', 20, 2);
            $table->double('week_6_p80_forecast', 20, 2);
            $table->double('week_7_p80_forecast', 20, 2);
            $table->double('week_8_p80_forecast', 20, 2);
            $table->double('week_9_p80_forecast', 20, 2);
            $table->double('week_10_p80_forecast', 20, 2);
            $table->double('week_11_p80_forecast', 20, 2);
            $table->double('week_12_p80_forecast', 20, 2);
            $table->double('week_13_p80_forecast', 20, 2);
            $table->double('week_14_p80_forecast', 20, 2);
            $table->double('week_15_p80_forecast', 20, 2);
            $table->double('week_16_p80_forecast', 20, 2);
            $table->double('week_17_p80_forecast', 20, 2);
            $table->double('week_18_p80_forecast', 20, 2);
            $table->double('week_19_p80_forecast', 20, 2);
            $table->double('week_20_p80_forecast', 20, 2);
            $table->double('week_21_p80_forecast', 20, 2);
            $table->double('week_22_p80_forecast', 20, 2);
            $table->double('week_23_p80_forecast', 20, 2);
            $table->double('week_24_p80_forecast', 20, 2);
            $table->double('week_25_p80_forecast', 20, 2);
            $table->double('week_26_p80_forecast', 20, 2);
            $table->double('week_1_p90_forecast', 20, 2);
            $table->double('week_2_p90_forecast', 20, 2);
            $table->double('week_3_p90_forecast', 20, 2);
            $table->double('week_4_p90_forecast', 20, 2);
            $table->double('week_5_p90_forecast', 20, 2);
            $table->double('week_6_p90_forecast', 20, 2);
            $table->double('week_7_p90_forecast', 20, 2);
            $table->double('week_8_p90_forecast', 20, 2);
            $table->double('week_9_p90_forecast', 20, 2);
            $table->double('week_10_p90_forecast', 20, 2);
            $table->double('week_11_p90_forecast', 20, 2);
            $table->double('week_12_p90_forecast', 20, 2);
            $table->double('week_13_p90_forecast', 20, 2);
            $table->double('week_14_p90_forecast', 20, 2);
            $table->double('week_15_p90_forecast', 20, 2);
            $table->double('week_16_p90_forecast', 20, 2);
            $table->double('week_17_p90_forecast', 20, 2);
            $table->double('week_18_p90_forecast', 20, 2);
            $table->double('week_19_p90_forecast', 20, 2);
            $table->double('week_20_p90_forecast', 20, 2);
            $table->double('week_21_p90_forecast', 20, 2);
            $table->double('week_22_p90_forecast', 20, 2);
            $table->double('week_23_p90_forecast', 20, 2);
            $table->double('week_24_p90_forecast', 20, 2);
            $table->double('week_25_p90_forecast', 20, 2);
            $table->double('week_26_p90_forecast', 20, 2);
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
        Schema::dropIfExists('tbl_stage_vc_daily_forecast');
    }
}
