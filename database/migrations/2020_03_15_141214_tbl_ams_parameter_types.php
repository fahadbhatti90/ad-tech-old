<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class TblAmsParameterTypes extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tbl_Ams_Parameter_Types', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('parameterName')->nullable();
            $table->string('isSd')->nullable()->default(0);
            $table->string('isSp')->nullable()->default(0);
            $table->string('isSb')->nullable()->default(0);
            $table->string('isActive')->default(0);

            
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
