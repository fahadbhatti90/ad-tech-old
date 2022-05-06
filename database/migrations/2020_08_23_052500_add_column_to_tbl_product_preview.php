<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddColumnToTblProductPreview extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        
        Schema::rename("tbl_product_preview", "tbl_event_logs");
        Schema::table('tbl_event_logs', function (Blueprint $table) {
            $table->dropColumn(['fkClientId', 'fkProductPreviewTypeId']);
            $table->renameColumn('fkActionEventId', 'fkEventId');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::rename("tbl_event_logs", "tbl_product_preview");
    }
}
