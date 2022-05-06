<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class TblScProductDetails extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {


        Schema::create('tbl_sc_product_details', function (Blueprint $table) {
            $table->bigIncrements('id');
            //$table->integer('fkPdocutTblId')->nullable();
            $table->integer('fkProductTblId')->nullable();
            /*$table->unsignedBigInteger('fkPdocutTblId');
            $table->foreign('fkPdocutTblId')->references('id')->on('tbl_sc_product_ids');*/
            $table->bigInteger('fkAccountId')->nullable();
            $table->bigInteger('fkBatchId')->nullable();
            $table->string('source')->default('SC');
            $table->integer('isActive')->default(1);
            $table->integer('fkSellerConfigId')->nullable();
            /*$table->unsignedBigInteger('fkSellerConfigId');
            $table->foreign('fkSellerConfigId')->references('mws_config_id')->on('tbl_sc_config');*/
            $table->string('marketplaceId')->default('NA');
            $table->string('asin')->default('NA');
            $table->string('binding')->default('NA');
            $table->string('brand')->default('NA');
            $table->string('color')->default('NA');
            $table->string('department')->default('NA');
            $table->decimal('itemHeight', 8, 2)->default(0.00);
            $table->decimal('itemLength', 8, 2)->default(0.00);
            $table->decimal('itemWidth', 8, 2)->default(0.00);
            $table->decimal('itemWeight', 8, 2)->default(0.00);
            $table->string('itemLabel')->default('NA');
            //$table->string('itemAmount')->nullable();
            $table->decimal('itemAmount', 8, 2)->default(0.00);
            $table->string('currencyCode')->default('NA');
            $table->string('manufacturer')->default('NA');
            $table->string('materialType')->default('NA');
            $table->string('model')->default('NA');
            $table->string('numberOfItems')->default(0);
            $table->decimal('packageHeight', 8, 2)->default(0.00);
            $table->decimal('packageLength', 8, 2)->default(0.00);
            $table->decimal('packageWidth', 8, 2)->default(0.00);
            $table->decimal('packageWeight', 8, 2)->default(0.00);
            $table->string('packageQuantity')->default(0);
            $table->string('partNumber')->default('NA');
            $table->string('productGroup')->default('NA');
            $table->string('productTypeName')->default('NA');
            $table->string('publisher')->default('NA');
            $table->string('releaseDate')->default('0000-00-00');
            $table->string('size')->default('NA');
            $table->string('smallImageURL')->default('NA');
            $table->decimal('smallImageHeight', 8, 2)->default(0.00);
            $table->decimal('smallImageWidth', 8, 2)->default(0.00);
            $table->string('studio')->default('NA');
            $table->text('title')->nullable();
            $table->text('warranty')->nullable();
            $table->string('parentAsinMarketplaceId')->default('NA');
            $table->string('parentAsin')->default('NA');
            $table->string('isAdultProduct')->default('NA');
            $table->string('isAutographed')->default('NA');
            $table->string('isMemorabilia')->default('NA');
            $table->string('platform')->default('NA');
            $table->string('publicationDate')->default('0000-00-00');
            $table->decimal('manufacturerMaximumAge', 8, 2)->default(0.00);
            $table->decimal('manufacturerMinimumAge', 8, 2)->default(0.00);

           // $table->string('salesRankProductCategoryId')->default('NA');
            //$table->string('salesRank')->default('NA');
            $table->timestamp('createdAt')->nullable();
            $table->timestamp('updatedAt')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('tbl_sc_product_details');
    }
}
