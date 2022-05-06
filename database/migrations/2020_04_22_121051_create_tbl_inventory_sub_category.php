<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTblInventorySubCategory extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::connection(\getDbAndConnectionName("c2"))->create('tbl_inventory_sub_category_override', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('fkSubCategoryId')->unique();
            $table->longText('overrideLabel')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::connection(\getDbAndConnectionName("c2"))->dropIfExists('tbl_inventory_sub_category_override');
    }
}
