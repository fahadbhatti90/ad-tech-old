<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateViewProductAsinSegments extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('view_product_asin_segments', function (Blueprint $table) {
            $table->string('segmentASIN', 50)->primary();
            $table->text('segmentId');
            $table->string('segmentName', 300);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('view_product_asin_segments');
    }
}
