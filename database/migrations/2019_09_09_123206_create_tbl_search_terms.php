<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTblSearchTerms extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tbl_search_terms', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('crawler_id')->unsigned();
            $table->longText('st_term');
            $table->longText('st_alias');
            $table->longText('st_url');
            $table->string('created_at', 100)->default('0000-00-00');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('tbl_search_terms');
    }
}
