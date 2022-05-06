<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePortfoliosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tbl_ams_day_parting_portfolios', function (Blueprint $table) {
            $table->bigIncrements('id')->autoIncrement();
            $table->bigInteger('portfolioId');
            $table->string('name', 255);
            $table->double('amount', 20, 2);
            $table->string('currencyCode', 50);
            $table->string('policy', 100);
            $table->string('inBudget', 50);
            $table->bigInteger('fkProfileId');
            $table->bigInteger('profileId');
            $table->string('state', 50);
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
        Schema::dropIfExists('tbl_ams_day_parting_portfolios');
    }
}
