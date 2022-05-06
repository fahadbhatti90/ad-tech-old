<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateBiTblRtlProductCatalog extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tbl_rtl_product_catalog', function (Blueprint $table) {
            $table->bigIncrements('tbl_pc_id');
            $table->integer('fk_account_id');
            $table->string('last_refresh', 20);
            $table->string('ASIN', 20);
            $table->string('sku', 20)->nullable();
            $table->string('model', 50)->nullable();
            $table->text('fulfillment_channel')->nullable();
            $table->tinyInteger('sc_status')->nullable();
            $table->tinyInteger('vc_status')->nullable();
            $table->string('upc', 20)->nullable();
            $table->string('release_date', 20)->nullable();
            $table->string('color', 255)->charset('utf8mb4')->collation('utf8mb4_unicode_ci')->nullable();
            $table->string('size', 50)->nullable();
            $table->decimal('price', 9, 4)->nullable();
            $table->string('product_width', 50)->nullable();
            $table->string('product_length', 50)->nullable();
            $table->string('product_height', 50)->nullable();
            $table->string('product_ship_weight', 50)->nullable();
            $table->string('brand', 100)->nullable();
            $table->string('manufacturer', 150)->nullable();
            $table->string('binding', 150)->nullable();
            $table->string('product_group', 150)->nullable();
            $table->string('product_type', 50)->nullable();
            $table->string('product_title', 255)->nullable();
            $table->string('parent_asins', 20)->nullable();
            $table->bigInteger('category_id')->nullable();
            $table->string('category_name', 100)->nullable();
            $table->bigInteger('subcategory_id')->nullable();
            $table->string('subcategory_name', 100)->nullable();
            $table->bigInteger('salesrank')->nullable();
            $table->timestamp('capture_date')->nullable();
            $table->string('batchid', 20)->nullable();
            $table->dateTime('LoadDate')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('tbl_rtl_product_catalog');
    }
}
