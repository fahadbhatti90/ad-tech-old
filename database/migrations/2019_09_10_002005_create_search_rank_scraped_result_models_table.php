<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSearchRankScrapedResultModelsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tbl_search_rank_scraped_result', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('st_id')->unsigned();
            $table->longText('title')->charset("utf8mb4")->collaction("utf8mb4_unicode_ci");
            $table->string('proxyIp', 100)->default('NA');
            $table->string('brand', 300)->default('NA');
            $table->string('asin', 50)->default('NA');
            $table->integer('rank')->unsigned()->default(0);
            $table->integer('pageNo')->unsigned();
            
            $table->string('offerPriceOrignal', 100)->default('NA');
            $table->decimal('offerPrice', 10, 2)->nullable();
            
            $table->string('listPriceOrignal', 100)->default('NA');
            $table->decimal('listPrice', 10, 2)->nullable();

            $table->integer('offerCount')->unsigned()->default(0);
            $table->boolean('isSponsered')->default(false);
            $table->boolean('isPromo')->default(false);
            $table->boolean('isBestSeller')->default(false);
            $table->boolean('isAmazonChoice')->default(false);
            $table->boolean('isPrime')->default(false);
            $table->string('reviewCount', 100)->default('NA');
            $table->string('reviewScore', 100)->default('NA');
            $table->string('created_at', 100)->default('0000-00-00');  
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('tbl_search_rank_scraped_result');
    }
}
