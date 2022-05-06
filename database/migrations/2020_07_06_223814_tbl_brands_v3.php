<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class TblBrandsV3 extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (Schema::hasTable('tbl_brands')) {
            Schema::table('tbl_brands', function (Blueprint $table) {
                if (!Schema::hasColumn('tbl_brands', 'isParentBrand')) {
                    $table->integer('isParentBrand')->after('password')->default(0);
                }
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
