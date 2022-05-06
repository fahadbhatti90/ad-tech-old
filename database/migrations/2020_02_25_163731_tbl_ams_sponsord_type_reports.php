<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class TblAmsSponsordTypeReports extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tbl_ams_sponsord_reports', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('reportName')->nullable();
            $table->string('fkSponsordTypeId')->nullable();
            $table->string('fkParameterType')->nullable();
            $table->string('isActive')->default(0);

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('tbl_ams_sponsord_reports');
    }
}
