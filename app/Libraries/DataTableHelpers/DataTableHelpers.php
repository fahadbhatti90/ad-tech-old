<?php

namespace App\Libraries\DataTableHelpers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Rap2hpoutre\FastExcel\FastExcel;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\File;
use App\Models\Inventory\InventoryModel;
use App\Models\Inventory\InventoryBrandsModel;
use App\Models\Inventory\InventoryProductModel;
use App\Models\Inventory\InventoryCategoryModel;
use App\Models\Inventory\InventorySubCategoryModel;

class DataTableHelpers extends Controller
{
    public function __construct()
    {

    }//end constructor
    private static function getTableInformation($query){
        $data = [];
        $tableName = $query->getModel()->table;
     
        $connectionObject =  $query->getModel()->getConnection();
        $config = $query->getModel()->getConnection()->getConfig();

        $OnlyTableName = $tableName;
        if(str_contains($tableName, '.')){
            $OnlyTableName = substr($tableName, \strpos($tableName,".")+1);
        }
        $data["fullTableName"] = $tableName;
        $data["table"] = $OnlyTableName;
        $data["database"] = $config["database"];
        $data["queryColumns"] = is_string($query->getQuery()->columns[0]) ?  $query->getQuery()->columns : explode(",",preg_replace('/\s+/', '', $query->getQuery()->columns[0]->getValue()));
        $data["columns"] = $connectionObject->getSchemaBuilder()->getColumnListing($OnlyTableName);
        $data["modal"] = $connectionObject;
        $data["connection"] = $config["name"];
        return $data;
    }
    private static function getCountForGroupBy($model, $tableInfo, $groupByColName = null, $groupByWhere=""){
        $queryColumns = $tableInfo["queryColumns"];
        $groupByCN = $groupByColName == null ? $queryColumns[0] : $groupByColName;
        $tableName = $tableInfo["table"];
        $result = $model->select(\DB::raw("SELECT count(*) as totalRec FROM (SELECT count(*) FROM $tableName group by $groupByCN) as aggregrated"));
        return count($result) > 0 ? $result[0]->totalRec : 0;
       
    }
    /**
     * Static Function Will Return Paginated Data for react data table according to proper require Options
     *
     * @param [type] $query // with out calling $query->get() function
     * @param [type] $options // Contains Options For Pagination like pageNumber, rowPerPage etc.
     * @param [type] $columnsToSearch
     * @param [type] $defaultSortingColumn
     * @param string $defaultSortingDireaction
     * @return ["data","total"]
     */
    public static function GetPaginatedData($query, $options, $columnsToSearchAndSort, $columnsToSearch = null, $defaultSortingColumn = null, $defaultSortingDireaction = "ASC") {
        $countQuery = $query->toSql();
        $options = json_decode($options);
        $search = ($options->search);
        if(isset($columnsToSearchAndSort["sortColumn"])){
            $sortColumn = $columnsToSearchAndSort["sortColumn"];
            $query->orWhere($sortColumn[0], "<>" , "-1");
        }
        if($search->isSearching){
            $searchStrings = self::getSearchString($columnsToSearch, $search->query);
            $query = $query->whereRaw("(".$searchStrings.")");
        }
        if(str_contains($countQuery, 'group by')) {
            $tableInformation = self::getTableInformation($query);
            $modal = $tableInformation["modal"];
            $columns = $tableInformation["columns"];
            
            $totalRecords = self::getCountForGroupBy($modal,$tableInformation);
        }
        else{
            $totalRecords = $query->count();
        }
        $totalRecords = $query->count();
        $pageNumber = $options->pageNumber;
        $perPage = $options->perPage;
        
        $realTootalNumberOfPages = floor($totalRecords/$perPage);

        $sort = ($options->sort);
        if($sort->isSorting){
            $orderByString = "$sort->column1";
            $query = $query->orderBy(\DB::raw("$orderByString"), $sort->direction);
        }
        else{
            if($defaultSortingColumn)
            $query = $query->orderBy("$defaultSortingColumn", "$defaultSortingDireaction");
        }
        
        $offset = (($pageNumber-1) * $perPage);
        $offset = ($pageNumber > $realTootalNumberOfPages) ? $realTootalNumberOfPages * $perPage : $offset;
        
        $query = $query->offset($offset)
        ->limit($perPage);
        return [
            "data"=> $query->get(),
            "total"=> $totalRecords,
        ];
    }
    public static function GetProductTablePaginatedData($query, $options, $columnsToSearch = null, $defaultSortingColumn = null, $defaultSortingDireaction = "ASC", $groupByColName = null) {
        $countQuery = $query["count"];
        $query = $query["data"];
        $options = json_decode($options);
        $search = ($options->search);
        if($search->isSearching){
            $searchStrings = self::getSearchString($columnsToSearch, $search->query);
            $query = $query->whereRaw("(".$searchStrings.")");
            $countQuery = $countQuery->whereRaw("(".$searchStrings.")");
        }
        $totalRecords = $countQuery->first();
        if($totalRecords){
            $totalRecords = $totalRecords->TotalRecords;
        }
        else{
            $totalRecords = 0;
        }
        $pageNumber = $options->pageNumber;
        $perPage = $options->perPage;
        
        $realTootalNumberOfPages = floor($totalRecords/$perPage);

        $sort = ($options->sort);
        if($sort->isSorting){
            $orderByString = "$sort->column1";
            $query = $query->orderBy(\DB::raw("$orderByString"), $sort->direction);
        }
        else{
            if($defaultSortingColumn)
            $query = $query->orderBy("$defaultSortingColumn", "$defaultSortingDireaction");
        }
        
        $offset = (($pageNumber-1) * $perPage);
        $offset = ($pageNumber > $realTootalNumberOfPages) ? $realTootalNumberOfPages * $perPage : $offset;
        
        $query = $query->offset($offset)
        ->limit($perPage);
        return [
            "data"=> $query->get(),
            "total"=> $totalRecords,
        ];
    }
    public static function GetManualEventPaginatedData($query, $options, $columnsToSearch = null, $defaultSortingColumn = null, $defaultSortingDireaction = "ASC", $groupByColName = null) {
        $options = json_decode($options);
        $search = ($options->search);
        if($search->isSearching){
            $searchStrings = self::getSearchString($columnsToSearch, $search->query);
            $query = $query->whereRaw("(".$searchStrings.")");
        }
        $totalRecords = $query->count();
        $pageNumber = $options->pageNumber;
        $perPage = $options->perPage;
        
        $realTootalNumberOfPages = floor($totalRecords/$perPage);

        $sort = ($options->sort);
        if($sort->isSorting){
            $orderByString = "$sort->column1";
            $query = $query->orderBy(\DB::raw("$orderByString"), $sort->direction);
        }
        else{
            if($defaultSortingColumn)
            $query = $query->orderBy(\DB::raw("$defaultSortingColumn"), "$defaultSortingDireaction");
        }
        
        $offset = (($pageNumber-1) * $perPage);
        $offset = ($pageNumber > $realTootalNumberOfPages) ? $realTootalNumberOfPages * $perPage : $offset;
        
        $query = $query->offset($offset)
        ->limit($perPage);
        return [
            "data"=> $query->get(),
            "total"=> $totalRecords,
        ];
    }

    private static function getSearchString($columnsToSearch, $searchQuery) {
        $searchString = "";
        $searchQuery = \strtolower($searchQuery);
        $columnsLength = count($columnsToSearch)-1;
        foreach ($columnsToSearch as $key => $column) {
            if($columnsLength == $key)
            $searchString .= "LOWER($column) like '%$searchQuery%' ";
            else
            $searchString .= "LOWER($column) like '%$searchQuery%' OR ";
        }
        return $searchString;
    }
}//end class

