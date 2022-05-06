<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTblAmsPortfolio extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tbl_ams_portfolios', function (Blueprint $table) {
            $table->bigIncrements('id')->autoIncrement();
            $table->bigInteger('portfolioId');
            $table->bigInteger('fkProfileId');
            $table->bigInteger('profileId');
            $table->string('name', 255);
            $table->double('amount', 20, 2)->default(00.00);
            $table->string('currencyCode', 50)->default('NA');
            $table->string('policy', 100)->default('NA');
            $table->string('inBudget', 50);
            $table->string('state', 50);
            $table->boolean('sandBox');
            $table->boolean('live');
            $table->dateTime('createdAt');
            $table->dateTime('updatedAt');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('tbl_ams_portfolios');
    }
}
