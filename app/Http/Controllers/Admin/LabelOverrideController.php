<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\File;
use Rap2hpoutre\FastExcel\FastExcel;
use Illuminate\Support\Facades\Artisan;
use App\Models\Inventory\InventoryModel;
use App\Models\Inventory\InventoryBrandsModel;
use App\Models\Inventory\InventoryProductModel;
use App\Models\Inventory\InventoryCategoryModel;
use App\Models\Inventory\InventorySubCategoryModel;
use App\Libraries\DataTableHelpers\DataTableHelpers;

class LabelOverrideController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth.admin');
    }//end constructor
    public function attributes(){
        $data['pageTitle'] = 'Label Override';
        $data['pageHeading'] = 'Label Override';
        $data['inventoryHasData'] = InventoryProductModel::exists();
        return view("admin.LabelOverride")->with($data);
    }//end function

    public function getAttributesData(Request $request)
    {
        Artisan::call('cache:clear');
        
        $options = $request->options;
        $cName = $request->columnName;
        
        $columnsToSearch = InventoryModel::getColumnsToSearch($cName);

        if($cName != "all"){
            $inventory = InventoryModel::getInentory($cName);
            $paginatedData = DataTableHelpers::GetPaginatedData($inventory["query"], $options, $inventory, $inventory["columns"]);
            $paginatedData["status"] = true;
            return $paginatedData;
        }
        else{
            $inventory = InventoryModel::getFullInventory();

            $paginatedData = DataTableHelpers::GetPaginatedData($inventory["query"], $options, $inventory, $columnsToSearch, "fk_account_id", "DESC");
            $paginatedData["status"] = true;
            return $paginatedData;
        } 
    } //end function  
    /***********************************************************************************************************/
    /*******************************Bulk Insertion via File Upload**********************************************/
    /***********************************************************************************************************/
    public function AliasBulkInsertion(Request $request){
        $respon["status"] = false;
        if($request->hasFile('attributeFile'))
        {
            $result = $this->handleTempFileUploadForBulkInsertion($request);
            if(!$result["status"]) return $result;
            $inputFileName = $result["fileName"];
            $collection = (new FastExcel)->import($inputFileName);
            
            $result = $this->validateFileData($collection,$inputFileName);
            if(!$result["status"]) return $result;

            $result = $this->initBulkUploadFileData($collection);
            
            $this->removeTempUploadedFile($inputFileName);

            if(!$result["status"]) return $result;
            $data = $result["tempData"];
            $this->bulkInsertBrands($data["brands"]);
            $this->bulkInsertProducts($data["products"]);
            $this->bulkInsertCategory($data["catgories"]);
            $this->bulkInsertSubCategory($data["subCatgories"]);
            $respon["status"] = true;
            $respon["message"] = "Aliases Added Successfully";
            $respon["tableData"] = InventoryModel::getInentory();
            return ($respon);//return's the success status
        }
        else
        {
            $respon["message"] = "Fail To Upload File, File Not Found Try Again";
            return ($respon);
        }
    }//end function

    private function handleTempFileUploadForBulkInsertion($request){
        $respon = [];
        $respon["status"] = true;
        $file = $request->file('attributeFile');
        $fileExt = $file->getClientOriginalExtension();
        if ($fileExt != 'xls' && $fileExt != 'xlsx' && $fileExt != 'csv') {
                $respon["status"] = false;
                $respon["message"] = "Please Select A Valid File Type";
                return ($respon);
        }

        $fullFileName = $file->getClientOriginalName();//getting Full File Name
        $fileNameOnly = pathinfo($fullFileName,PATHINFO_FILENAME);//getting File Name With out extension
        $newFileName = $fileNameOnly .'_'.time().'.'.$fileExt;//Foramting New Name with Time stamp for avoiding any duplicated names in databese
        $path = public_path('LabelOverride/Uploads/');
        $inputFileName =  $path . $newFileName ;
        // if(!File::isDirectory($path)){
        //     File::makeDirectory($path, 0777, true, true);
        // }
        $file->move( $path, $newFileName );
        $respon["fileName"] = $inputFileName;
        return $respon;
    }//end function
    private function validateFileData($collection, $inputFileName){
        $respon["status"] = true;
        if(count($collection) <= 0){

            $this->removeTempUploadedFile($inputFileName);
            $respon["status"] = false;
            $respon["message"] = "No record found in uploaded file";
            return $respon;
        }//end if
        // $respon["status"] = false;
        // $respon["message"] = $collection[0];
        // return $respon;
        // if ((!isset(($collection[0]['Asin'])) || empty(trim($collection[0]['Asin'])))) {

        //     $this->removeTempUploadedFile($inputFileName);
        //     $respon["status"] = false;
        //     $respon["message"] = "Please Select A Valid File ";
        //     return ($respon);
        // }
        return ($respon);
    }//end function
    private function checkIsDataValideForAnyAttributeType($collection){
        if (
            (
                (isset(($collection[0]['Asin'])) && !empty(trim($collection[0]['Asin'])) && strlen($collection[0]['Asin']) == 10) ||
                (isset(($collection[0]['Category Id'])) && !empty(trim($collection[0]['Category Id']))) ||
                (isset(($collection[0]['Sub-Category Id'])) && !empty(trim($collection[0]['Sub-Category Id']))) ||
                (isset(($collection[0]['Brand Id'])) && !empty(trim($collection[0]['Brand Id'])))
            ) && 
                isset(($collection[0]['Override Value'])) 
        ) {
            return true;
        }
        return false;
    }
    private function initBulkUploadFileData($collection){
        $tempData = [];
        $tempData["products"] = $tempData["catgories"] = $tempData["subCatgories"] = $tempData["brands"] = $tempData["asins"] = [];
        $dataFound = false;
        $message = null;
        if($this->checkIsDataValideForAnyAttributeType($collection))
        foreach ($collection as $row) {
                if(isset($row['Asin'])){
                    if(strlen($row['Override Value']) > 100){
                        $dataFound = false;
                        $message = "Alias cannot be greator than 100 characters";
                        break;
                    }
                    if(strlen($row['Override Value'])>0){
                        $tempData["products"][] = [
                            "asin" => $row['Asin'],
                            "overrideLabel" =>$row['Override Value'],
                        ];
                        $dataFound = true;
                    }
                }
                if(isset($row['Category Id'])){
                    if(strlen($row['Override Value']) > 100){
                        $dataFound = false;
                        $message = "Alias cannot be greator than 100 characters";
                        break;
                    }
                    if(strlen($row['Override Value'])>0){
                        $tempData["catgories"][] = [
                            "fkCategoryId" =>$row['Category Id'],
                            "overrideLabel" =>$row['Override Value'],
                        ];
                        $dataFound = true;
                    }
                }
                if(isset($row['Sub-Category Id'])){
                    if(strlen($row['Override Value']) > 100){
                        $dataFound = false;
                        $message = "Alias cannot be greator than 100 characters";
                        break;
                    }
                    if(strlen($row['Override Value'])>0){
                        $tempData["subCatgories"][] = [
                            "fkSubCategoryId" =>$row['Sub-Category Id'],
                            "overrideLabel" =>$row['Override Value'],
                        ];
                        $dataFound = true;
                    }
                }
                if(isset($row['Brand Id'])){
                    if(strlen($row['Override Value']) > 100){
                        $dataFound = false;
                        $message = "Alias cannot be greator than 100 characters";
                        break;
                    }
                    if(strlen($row['Override Value'])>0){
                        $tempData["brands"][] = [
                            "fkAccountId" =>$row['Brand Id'],
                            "overrideLabel" =>$row['Override Value'],
                        ];
                        $dataFound = true;
                    }
                }
        }//end foreach
        return [
            "status"=>$dataFound,
            "tempData"=>$tempData,
            "message"=>$message != null ? $message : "No record found in uploaded file Or file format is wrong",
        ];
    }//end function
    private function bulkInsertCategory($categories){
        $categoriesChunks = array_chunk($categories,1000);
        foreach ($categoriesChunks as $categoriesChunkkey => $categoriesChunk) { 
            $query = InventoryCategoryModel::insertOrUpdate($categoriesChunk,InventoryCategoryModel::getTableName());
        }
    }//end function
    private function bulkInsertSubCategory($subCategories){
        $subCategoriesChunks = array_chunk($subCategories,1000);
        foreach ($subCategoriesChunks as $subCategoriesChunkkey => $subCategoriesChunk) { 
            InventorySubCategoryModel::insertOrUpdate($subCategoriesChunk,InventorySubCategoryModel::getTableName());
        }
    }//end function
    private function bulkInsertProducts($products){
        $productsChunks = array_chunk($products,1000);
        foreach ($productsChunks as $productsChunkkey => $productsChunk) { 
            InventoryProductModel::insertOrUpdate($productsChunk,InventoryProductModel::getTableName());
        }
    }//end function
    private function bulkInsertBrands($brands){
        $brandsChunks = array_chunk($brands,1000);
        foreach ($brandsChunks as $brandsChunkkey => $brandsChunk) { 
            InventoryBrandsModel::insertOrUpdate($brandsChunk,InventoryBrandsModel::getTableName());
        }
    }//end function
    private function removeTempUploadedFile($fileName){
        if (File::exists($fileName)) {
            File::delete($fileName);
        }//end if
    }//end funciton
    /***********************************************************************************************************/
    /*******************************Alias Bulk Insertion via File Upload****************************************/
    /***********************************************************************************************************/



    /***********************************************************************************************************/
    /*******************************Menual Alias Insertion******************************************************/
    /***********************************************************************************************************/
    public function addLabel(Request $request){
        switch ($request->type) {
            case 1:
                $affectedRows = $this->AddBrandLabel($request);
                break;
            case 2:
                $affectedRows = $this->AddProductTitleLabel($request);
                break;
            case 3:
                $affectedRows = $this->AddCategoryLabel($request);
                break;
            case 4:
                $affectedRows = $this->AddSubCategoryLabel($request);
                break;
            
            default:
                return [
                    "status"=>false,
                    "message"=>"Not a valid attribute type"

                ];
                break;
        }
        return [
            "status"=>$affectedRows > 0 ? true : false,
            "message"=>$affectedRows > 0 ? "" : "Fail To Add Alias",
            "tableData"=>InventoryModel::getInentory(),
        ];
    }
    private function AddBrandLabel($request){
        $accountId = $request->fkId;
        $overrideLabel = $request->overrideLabel;
        return InventoryBrandsModel::where("fkAccountId","$accountId")->update([
            'overrideLabel'=>$overrideLabel
        ]);
    }//end function
    private function AddProductTitleLabel($request){
        $asin = $request->fkId;
        $overrideLabel = $request->overrideLabel;
        return InventoryProductModel::where("asin","$asin")->update([
            'overrideLabel'=>$overrideLabel
        ]);
    }//end function
    private function AddCategoryLabel($request){
        $fkCategoryId = $request->fkId;
        $overrideLabel = $request->overrideLabel;
        return InventoryCategoryModel::where("fkCategoryId","$fkCategoryId")->update([
            'overrideLabel'=>$overrideLabel
        ]);
    }//end function
    private function AddSubCategoryLabel($request){
        $fkSubCategoryId = $request->fkId;
        $overrideLabel = $request->overrideLabel;
        return InventorySubCategoryModel::where("fkSubCategoryId","$fkSubCategoryId")->update([
            'overrideLabel'=>$overrideLabel
        ]);
        
    }//end function
    /***********************************************************************************************************/
    /*******************************Menual Alias Insertion******************************************************/
    /***********************************************************************************************************/

    /***********************************************************************************************************/
    /*******************************Download Attribute Data******************************************************/
    /***********************************************************************************************************/
    public function downloadAttribute(Request $request){
        ini_set('memory_limit', '2048M');
        ini_set('max_execution_time', 0);
        $attributeName = null;
        $primaryKey = null;
        switch ($request->type) {
            case 1:
                $inventory = $this->getInventoryOnlyWithASIN($request);
                $attributeName = "Products";
                $primaryKey = "Asin";
                break;
            case 2:
                $inventory = $this->getInventoryOnlyWithSubCategory($request);
                $attributeName = "SubCategories";
                $primaryKey = "Sub-Category Id";
                break;
            case 3:
                $inventory = $this->getInventoryOnlyWithCategory($request);
                $attributeName = "Categories";
                $primaryKey = "Category Id";
                break;
            case 4:
                $inventory = $this->getInventoryOnlyWithBrand($request);
                $attributeName = "Brands";
                $primaryKey = "Brand Id";
                break;
            
            default:
                return abort(404);
                break;
        }
        return (new FastExcel(($inventory)))->download("$attributeName.xlsx", function ($result) use ($primaryKey) {
            return  [
                "$primaryKey" =>$result->attributeId,
                "Original Value" =>$result->orignalName,
                "Override Value" =>$result->overrideLabel == NULL ? "":$result->overrideLabel
            ];
        });
    }
    private function getInventoryOnlyWithASIN(){
        ini_set('memory_limit', '2048M');
        ini_set('max_execution_time', 0);
        $inventroyProductsTN = InventoryProductModel::getTableName();
        $inventroyTN = InventoryModel::getTableName();
        $inventory = InventoryModel::selectRaw(
            "
                $inventroyTN.product_title orignalName,
                $inventroyProductsTN.asin attributeId,
                $inventroyProductsTN.overrideLabel
            "
        )
        ->leftJoin("$inventroyProductsTN", "$inventroyTN.ASIN", '=', "$inventroyProductsTN.asin")
        ->groupBy($inventroyTN."."."ASIN")
        ->where(\DB::raw($inventroyTN.".ASIN"),"<>","N/A")
        ->get();
        
        return $inventory;
    }//end function
    private function getInventoryOnlyWithSubCategory(){
        ini_set('memory_limit', '2048M');
        ini_set('max_execution_time', 0);
        $inventroySubCategoryTN = InventorySubCategoryModel::$tableName;
        $inventroyTN = InventoryModel::$tableName;
        $inventory = InventoryModel::selectRaw(
            "
                $inventroyTN.subcategory_name orignalName,
                $inventroySubCategoryTN.fkSubCategoryId attributeId,
                $inventroySubCategoryTN.overrideLabel
            "
        )
        ->leftJoin("$inventroySubCategoryTN", "$inventroyTN.subcategory_id", '=', "$inventroySubCategoryTN.fkSubCategoryId")
        ->where("subcategory_id",">",0)
        ->groupBy($inventroyTN."."."subcategory_id")
        ->where(\DB::raw($inventroyTN.".subcategory_id"),">",0)
        ->get();
        return $inventory;
    }//end function
    private function getInventoryOnlyWithCategory(){
        ini_set('memory_limit', '2048M');
        ini_set('max_execution_time', 0);
        $inventroyCategoryTN = InventoryCategoryModel::$tableName;
        $inventroyTN = InventoryModel::$tableName;
        $inventory = InventoryModel::selectRaw(
            "
                $inventroyTN.category_name orignalName,
                $inventroyCategoryTN.fkCategoryId attributeId,
                $inventroyCategoryTN.overrideLabel 
            "
        )
        ->leftJoin("$inventroyCategoryTN", "$inventroyTN.category_id", '=', "$inventroyCategoryTN.fkCategoryId")
        ->where("category_id",">",0)
        ->groupBy($inventroyTN."."."category_id")
        ->where(\DB::raw($inventroyTN.".category_id"),">",0)
        ->get();
        return $inventory;
    }//end function
    private function getInventoryOnlyWithBrand(){
        ini_set('memory_limit', '2048M');
        ini_set('max_execution_time', 0);
        $inventroyBrandTN = InventoryBrandsModel::$tableName;
        $inventroyTN = InventoryModel::$tableName;
        $inventory = InventoryModel::selectRaw(
            "
                $inventroyTN.accountName orignalName,
                $inventroyBrandTN.fkAccountId attributeId,
                $inventroyBrandTN.overrideLabel overrideLabel
            "
        )
        ->leftJoin("$inventroyBrandTN", "$inventroyTN.fk_account_id", '=', "$inventroyBrandTN.fkAccountId")
        ->groupBy($inventroyTN."."."fk_account_id")
        ->whereNotNull(\DB::raw($inventroyTN.".fk_account_id"))
        ->get();
        return $inventory;
    }//end function
    /***********************************************************************************************************/
    /*******************************Download Attribute Data******************************************************/
    /***********************************************************************************************************/
}//end class

