<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class TblAmsAdvertisingScheduleFiles extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tbl_ams_advertising_schedule_files', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('fkScheduleId')->nullable();
            $table->bigInteger('fkParameterTypeId')->nullable();
            $table->string('parameterTypeName')->nullable();
            $table->string('time')->nullable();
            $table->string('date')->nullable();
            $table->string('fileName')->nullable();
            $table->string('filePath')->nullable();
            $table->string('completeFilePath')->nullable();
            $table->string('devServerLink')->nullable();
            $table->string('apiServerLink')->nullable();
            $table->string('isDataFound')->default(0);
            $table->string('isFileDeleted')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('tbl_ams_advertising_schedule_files');
    }
}
