<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTblVcProductCatalogTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tbl_vc_product_catalog', function (Blueprint $table) {
            $table->bigIncrements('product_catalog_id')->autoIncrement();
            $table->bigInteger('fk_vendor_id')->unsigned();
            $table->bigInteger('batchId')->unsigned();
            $table->bigInteger('fkAccountId')->unsigned();
            //$table->foreign('fk_vendor_id')->references('vendor_id')->on('tbl_vc_vendors');
            $table->string('asin', 100);
            $table->longText('product_title');
            $table->string('parent_asin', 100);
            $table->string('isbn13', 50);
            $table->string('ean', 50);
            $table->string('upc', 50);
            $table->string('release_date', 50);
            $table->string('binding', 100);
            $table->double('list_price', 20,2);
            $table->string('author_artist', 100);
            $table->string('sitbenabled', 100);
            $table->string('apparel_size', 100);
            $table->string('apparel_size_width', 100);
            $table->string('product_group', 100);
            $table->string('replenishment_code', 100);
            $table->string('model_style_number', 100);
            $table->string('colour', 100);
            $table->bigInteger('colour_count');
            $table->string('prep_instructions_required', 100);
            $table->string('prep_instructions_vendor_state', 100);
            $table->string('brand_code', 100);
            $table->string('brand', 100);
            $table->string('manufacturer_code', 100);
            $table->string('parent_manufacturer_code', 100);
            $table->date('capture_date');
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
        Schema::dropIfExists('tbl_vc_product_catalog');
    }
}
