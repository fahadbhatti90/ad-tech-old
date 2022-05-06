<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTblStageVcDailySales extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tbl_stage_vc_daily_sales', function (Blueprint $table) {
            $table->bigIncrements('id')->autoIncrement();
            $table->bigInteger('fk_vendor_id')->unsigned();
            $table->bigInteger('batchId')->unsigned();
            $table->bigInteger('fkAccountId')->unsigned();
            $table->string('asin', 100);
            $table->longText('product_title');
            $table->string('category', 100);
            $table->string('strCategory', 100);
            $table->bigInteger('fkCategoryId')->default(0);
            $table->string('subcategory', 100);
            $table->double('shipped_cogs', 20, 2);
            $table->double('shipped_cogs_percentage_total', 20, 2);
            $table->double('shipped_cogs_prior_period', 20, 2);
            $table->double('shipped_cogs_last_year', 20, 2);
            $table->bigInteger('shipped_units');
            $table->double('shipped_units_percentage_total', 20, 2);
            $table->double('shipped_units_prior_period', 20, 2);
            $table->double('shipped_units_last_year', 20, 2);
            $table->double('units_percentage_total', 20, 2);
            $table->bigInteger('customer_returns');
            $table->bigInteger('free_replacements');
            $table->double('average_sales_price', 20, 2);
            $table->double('average_sales_price_prior_period', 20, 2);
            $table->date('sale_date');
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
        Schema::dropIfExists('tbl_stage_vc_daily_sales');
    }
}
