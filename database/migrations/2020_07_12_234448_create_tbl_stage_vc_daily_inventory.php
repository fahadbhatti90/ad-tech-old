<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTblStageVcDailyInventory extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tbl_stage_vc_daily_inventory', function (Blueprint $table) {
            $table->bigIncrements('daily_inventory_id')->autoIncrement();
            $table->bigInteger('fk_vendor_id')->unsigned();
            $table->bigInteger('batchId')->unsigned();
            $table->bigInteger('fkAccountId')->unsigned();
            $table->string('asin', 100);
            $table->longText('product_title');
            $table->string('category', 100);
            $table->string('strCategory', 100);
            $table->bigInteger('fkCategoryId')->default(0);
            $table->string('subcategory', 100);
            $table->double('net_recieved', 20, 2);
            $table->bigInteger('net_revieved_units');
            $table->double('sell_through_rate', 20, 2);
            $table->bigInteger('open_purchase_order_quantity');
            $table->double('sellable_on_hand_inventory', 20, 2);
            $table->double('sellable_on_hand_inventory_trailing_30_day_average', 20,2);
            $table->bigInteger('sellable_on_hand_units');
            $table->double('unsellable_on_hand_inventory', 20, 2);
            $table->double('unsellable_on_hand_inventory_trailing_30_day_average', 20,2);
            $table->bigInteger('unsellable_on_hand_units');
            $table->double('aged_90_days_sellable_inventory', 20, 2);
            $table->double('aged_90+_days_sellable_inventory_trailing_30_day_average', 20,2);
            $table->bigInteger('aged_90_days_sellable_units');
            $table->double('unhealthy_inventory', 20, 2);
            $table->double('unhealthy_inventory_trailing_30day_average', 20,2);
            $table->bigInteger('unhealthy_units');
            $table->string('replenishment_category', 50);
            $table->date('rec_date');
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
        Schema::dropIfExists('tbl_stage_vc_daily_inventory');
    }
}
