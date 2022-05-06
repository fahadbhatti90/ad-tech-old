<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTblBuyboxFailStatuses extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tbl_buybox_fail_statuses', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('failed_data', 500)->default('NA');
            $table->text('failed_reason');
            $table->string('failed_at', 50)->default('0000-00-00');
            $table->unsignedInteger('crawler_id')->unsigned();
            $table->boolean('isNew')->default(true);
            $table->string('created_at', 50)->default('0000-00-00');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('tbl_buybox_fail_statuses');
    }
}
