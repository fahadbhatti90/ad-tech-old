<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAmsKeywordReportsDownloadedDataSbV1 extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('tbl_ams_keyword_reports_downloaded_data_sb', function (Blueprint $table) {
            $table->string('keywordBid',50)->default(0);
            $table->string('keywordStatus',50)->default('NA');
            $table->string('targetId',50)->default(0);
            $table->string('targetingExpression',50)->default('NA');
            $table->string('targetingText',50)->default('NA');
            $table->string('targetingType',50)->default('NA');
            $table->string('unitsSold14d',50)->default(0);
            $table->string('dpv14d',50)->default(0);
            $table->string('attributedDetailPageViewsClicks14d',50)->default(0);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
    }
}
