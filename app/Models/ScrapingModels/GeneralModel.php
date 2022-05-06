<?php

namespace App\Models\ScrapingModels;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Eloquent\Model;
use App\Models\ScrapingModels\asinModel;

class GeneralModel extends Model
{
    
    public $col;
    public static function ResetTableStand($tableName,$column){
        if (Schema::hasColumn($tableName, $column))
        {
            GeneralModel::_removeColumn($tableName,$column);
            GeneralModel::_addColumn($tableName,$column);
            GeneralModel::_makeColumnFirst($tableName,$column);
             return true;
        }
        else{
            return false;
        }
       
    }
    public static function RenameColumn($tableName, $fromName,$toName){
        if(Schema::hasColumn($tableName, $fromName)){
            Schema::table($tableName, function($table)use ($fromName,$toName) {
                $table->renameColumn($fromName,$toName);
             });//end closure
             return true;
        }
        return false;
    }
    private static function _removeColumn($t,$c){
        Schema::table($t, function($table)use ($c) {
            $table->dropColumn($c);
         });
    }
    private static function _addColumn($t,$c){
        Schema::table($t, function($table)use ($c) {
            $table->increments($c);
         });
    }
    public static function AddMissingPrimaryKeyColumn($table,$column){
        Schema::table($tableName, function($table)use ($column) {
             $table->increments($column);
         });
    }
    private static function _makeColumnFirst($t,$c){
        DB::select("ALTER TABLE `hellotec`.`$t` 
        CHANGE COLUMN `$c` `$c` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT FIRST;");
    }
}
