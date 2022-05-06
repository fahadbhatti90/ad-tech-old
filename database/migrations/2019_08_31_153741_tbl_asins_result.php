<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use phpDocumentor\Reflection\Types\Integer;

class TblAsinsResult extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tbl_asins_result', function (Blueprint $table) {
            $table->increments('id');
            $table->bigInteger('c_id');//fk_c_id
            $table->string('url', 100)->default('NA');
            $table->string('asin', 50)->default('NA');
            $table->smallInteger('asinExists')->default(0);
            $table->date('capturedAt');
            $table->longText('metaKeywords');
            $table->longText('brand');
            $table->string('offerPriceOrignal', 100)->default('NA');
            $table->decimal('offerPrice', 10, 2)->nullable();
            $table->string('listPriceOrignal', 100)->default('NA');
            $table->decimal('listPrice', 10, 2)->nullable();
            $table->integer('offerCount')->unsigned()->default(0);
            $table->string('modelNo', 200)->default('NA');
            $table->longText('breadcrumbs');
            $table->longText('category');
            $table->string('title', 1000)->charset("utf8mb4")->collaction("utf8mb4_unicode_ci")->default('NA');
            $table->longText('images');
            $table->integer('imageCount')->unsigned()->default(0);
            $table->integer('videoCount')->unsigned()->default(0);
            $table->longText('bullets')->charset("utf8mb4")->collaction("utf8mb4_unicode_ci");
            $table->integer('bulletCount')->unsigned()->default(0);
            $table->decimal('avgWordsPerBulitCount', 10, 0)->nullable();
            $table->longText('aplus');
            $table->smallInteger('aplusModule')->default(0);
            $table->decimal('averageReview', 10, 2)->nullable();
            $table->string('totalReviews', 500)->default('NA');
            $table->longText('bestSellerRank');
            $table->longText('bestSellerCategory');
            $table->boolean('isPrime')->default(false);
            $table->boolean('isAvailable')->default(false);
            $table->string('availabilityMessage', 200)->default('NA');
            $table->boolean('isAmazonChoice')->default(false);
            $table->string('amazonChoiceTerm', 200)->default('NA');
            $table->boolean('isPromo')->default(false);
            $table->string('seller', 300)->default('NA');
            $table->string('size', 100)->default('NA');
            $table->longText('color');
            $table->string('weight', 100)->default('NA');
            $table->string('length', 100)->default('NA');
            $table->string('width', 100)->default('NA');
            $table->string('height', 100)->default('NA');
            $table->string('shipWeight', 100)->default('NA');
            $table->string('dateFirstAvailable', 100)->default('NA');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('tbl_asins_result');
    }
}
