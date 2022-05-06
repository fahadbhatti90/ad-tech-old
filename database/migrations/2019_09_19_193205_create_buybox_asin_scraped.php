<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateBuyboxAsinScraped extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tbl_buybox_asin_scraped', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('fkCollection',100);
            $table->boolean('isNew')->default(true);
            $table->text('brand');
            $table->string('soldBy');
            $table->integer('soldByAlert');
            $table->double('price', 10, 2);
            $table->string('priceOrignal',100);
            $table->string('primeDesc');
            $table->integer('prime');
            $table->string('stock');
            $table->integer('stockAlert');
            $table->string('url');
            $table->string('asinCode',15);
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
        Schema::dropIfExists('tbl_buybox_asin_scraped');
    }
}
