<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class TblScmfnreturnsreportV2 extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (Schema::hasColumn('tbl_sc_sales_mfn_returns_report', 'category'))
        {
            Schema::table('tbl_sc_sales_mfn_returns_report', function (Blueprint $table)
            {
                $table->dropColumn('category');
            });
        }
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
