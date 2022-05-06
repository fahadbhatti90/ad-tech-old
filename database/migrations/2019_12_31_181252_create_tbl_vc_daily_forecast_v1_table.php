<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTblVcDailyForecastV1Table extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('tbl_vc_daily_forecast', function (Blueprint $table) {
            if(!Schema::hasColumn('tbl_vc_daily_forecast', 'strCategory')){
                $table->string('strCategory', 100)->after('category');
            }
            if(!Schema::hasColumn('tbl_vc_daily_forecast', 'fkCategoryId')){
                $table->bigInteger('fkCategoryId')->default(0)->after('strCategory');
            }
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('tbl_vc_daily_forecast_v1');
    }
}
