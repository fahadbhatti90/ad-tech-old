<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAmsAsinReportsDownloadedSp extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tbl_ams_asin_reports_downloaded_sp', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('fkBatchId');
            $table->bigInteger('fkAccountId');
            $table->integer('fkReportsDownloadLinksId');
            $table->string('fkProfileId', 50);
            $table->string('campaignName');
            $table->string('campaignId',50);
            $table->string('adGroupName');
            $table->string('adGroupId',50);
            $table->string('keywordId',50);
            $table->string('keywordText');
            $table->string('asin',50);
            $table->string('otherAsin',50);
            $table->string('currency',50);
            $table->string('matchType',50);
            $table->string('attributedUnitsOrdered1dOtherSKU',50);
            $table->string('attributedUnitsOrdered7dOtherSKU',50);
            $table->string('attributedUnitsOrdered14dOtherSKU',50);
            $table->string('attributedUnitsOrdered30dOtherSKU',50);
            $table->string('attributedSales1dOtherSKU',50);
            $table->string('attributedSales7dOtherSKU',50);
            $table->string('attributedSales14dOtherSKU',50);
            $table->string('attributedSales30dOtherSKU',50);
            $table->string('sku');
            $table->string('reportDate',50);
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
        Schema::dropIfExists('tbl_ams_asin_reports_downloaded_sp');
    }
}
