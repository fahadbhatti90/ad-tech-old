<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateBiddingRulePreset extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tbl_ams_bidding_rule_preset', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('presetName',50);
            $table->text('metric');
            $table->text('condition');
            $table->string('integerValues',100);
            $table->text('thenClause');
            $table->string('bidBy',100);
            $table->string('andOr',100);
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
        Schema::dropIfExists('tbl_ams_bidding_rule_preset');
    }
}
