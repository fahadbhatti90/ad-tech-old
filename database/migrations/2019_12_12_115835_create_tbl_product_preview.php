<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTblProductPreview extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tbl_product_preview', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('fkClientId');
            $table->bigInteger('fkAccountId');
            $table->string('asin', 50);
            $table->bigInteger('fkActionEventId');
            $table->bigInteger('fkProductPreviewTypeId');//user action/ event
            $table->string('occurrenceDate', 50);
            $table->longText("notes");
            $table->string('uniqueColumn', 50)->unique();
            $table->timestamp('createdAt')->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->timestamp('updatedAt')->default(DB::raw('CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP'));
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('tbl_product_preview');
    }
}
