<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAmsCampaignBrandReportDownloadLinkSB extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tbl_ams_campaigns_reports_download_links_sb', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('fkBatchId');
            $table->bigInteger('fkAccountId');
            $table->string('profileID', 50);
            $table->string('reportId');
            $table->string('status', 50);
            $table->string('statusDetails', 100);
            $table->string('location');
            $table->string('fileSize', 50);
            $table->string('reportDate', 20);
            $table->date('creationDate');
            $table->integer('isDone');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('tbl_ams_campaigns_reports_download_links_sb');
    }
}
