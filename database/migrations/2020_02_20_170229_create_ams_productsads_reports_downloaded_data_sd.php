<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAmsProductsadsReportsDownloadedDataSd extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tbl_ams_productsads_reports_downloaded_data_sd', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('fkBatchId');
            $table->bigInteger('fkAccountId');
            $table->integer('fkReportsDownloadLinksId');
            $table->string('fkProfileId', 50);
            $table->string('adGroupName');
            $table->string('adGroupId',50);
            $table->string('asin',50);
            $table->string('sku');
            $table->string('campaignName');
            $table->string('campaignId', 50);
            $table->string('impressions', 50);
            $table->string('clicks', 50);
            $table->string('cost', 50);
            $table->string('currency', 50);
            $table->string('attributedConversions1d', 50);
            $table->string('attributedConversions7d', 50);
            $table->string('attributedConversions14d', 50);
            $table->string('attributedConversions30d', 50);
            $table->string('attributedConversions1dSameSKU', 50);
            $table->string('attributedConversions7dSameSKU', 50);
            $table->string('attributedConversions14dSameSKU', 50);
            $table->string('attributedConversions30dSameSKU', 50);
            $table->string('attributedUnitsOrdered1d', 50);
            $table->string('attributedUnitsOrdered7d', 50);
            $table->string('attributedUnitsOrdered14d', 50);
            $table->string('attributedUnitsOrdered30d', 50);
            $table->string('attributedSales1d', 50);
            $table->string('attributedSales7d', 50);
            $table->string('attributedSales14d', 50);
            $table->string('attributedSales30d', 50);
            $table->string('attributedSales1dSameSKU', 50);
            $table->string('attributedSales7dSameSKU', 50);
            $table->string('attributedSales14dSameSKU', 50);
            $table->string('attributedSales30dSameSKU', 50);
            $table->string('reportDate', 50);
            $table->date('creationDate');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('tbl_ams_productsads_reports_downloaded_data_sd');
    }
}
