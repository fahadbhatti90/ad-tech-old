<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateBiTblRtlAccountClient extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tbl_rtl_account_client', function (Blueprint $table) {
            $table->bigIncrements('rtl_acc_cl_id');
            $table->integer('agency_id');
            $table->string('Agency_Name', 200);
            $table->unsignedBigInteger('client_id');
            $table->string('client_name', 200);
            $table->string('client_email', 191);
            $table->string('client_account_password', 191);
            $table->timestamp('client_creation');
            $table->timestamp('client_updation');
            $table->unsignedBigInteger('account_id');
            $table->bigInteger('marketplaceid');
            $table->integer('account_type');
            $table->string('account_type_name', 191);
            $table->bigInteger('fkId');
            $table->string('accountName', 191);
            $table->timestamp('account_creation')->nullable();
            $table->timestamp('account_updation')->nullable();
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
        Schema::dropIfExists('tbl_rtl_account_client');
    }
}
