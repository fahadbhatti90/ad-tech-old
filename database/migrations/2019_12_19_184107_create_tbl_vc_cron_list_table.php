<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTblVcCronListTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tbl_vc_cron_list', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('moduleName', 100);
            $table->bigInteger('isDoneModuleData');
            $table->bigInteger('isRunned');
            $table->bigInteger('isFailed');
            $table->bigInteger('isSuccess');
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
        Schema::dropIfExists('tbl_vc_cron_list');
    }
}
