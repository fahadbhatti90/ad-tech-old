<?php

namespace App\Http\Controllers;

use App\Models\AccountModels\AccountModel;
use Illuminate\Http\Request;
// include for forms
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Validator;
// Import excel Library
use \PhpOffice\PhpSpreadsheet\Spreadsheet;
use \PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Illuminate\Support\Facades\URL;
// Import Fast Excel
use Rap2hpoutre\FastExcel\FastExcel;

use App\Models\VCModel;
use App\Models\BatchIdModels;


class VCController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');

    }

    /**
     * Display a listing of the resource.
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function dashboard()
    {
        $data['pageTitle'] = 'VC Dashboard';
        //$data['pageHeading'] = 'Vendor Central Dashboard';
        $data['dailySalesCount'] = VCModel::getDailySalesCount();
        $data['dailySalesCountLastRecord'] = VCModel::getDailySalesLastRecord();
        return view('subpages.vc.dashboard')->with($data);
    }

    /**
     * Show Daily Sales Form
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function dailySalesView()
    {

        $data['vendors'] = VCModel::getAllVendors();
        $data['pageTitle'] = 'Daily Sales';
        //$data['pageHeading'] = 'Daily Sales';
        return view('subpages.vc.sales.dailysales')->with($data);
    }

    /**
     * Store Records of Daily Sales in database
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function dailySalesStoreRecords(Request $request)
    {
        // Set Memory Limit And Execution Time
        setMemoryLimitAndExeTime();
        $validator = Validator::make($request->all(), ['vendor' => 'required', 'daily_sales' => 'required', 'daily_sales_date' => 'required']);
        $responseData = array();
        $errorMessage = array();
        $successMessage = array();
        // If validation Passes e.g no errors
        if ($validator->passes()) {
            // get Extension
            $fileExtension = ($request->hasFile('daily_sales') ? $request->file('daily_sales')->getClientOriginalExtension() : '');
            // Validate upload file
            if ($this->validateExcelFile($fileExtension) != false) {
                $fkVendorId = $request->input('vendor');
                $isAccountAssociated = AccountModel::where('fkId', $fkVendorId)
                    ->where('fkAccountType', 3)
                    ->first();
                $date = $request->input('daily_sales_date');
                if (!is_null($isAccountAssociated)) {
                    //  Check if not generate batchId for specified date then generate it

                    $batchAccountId = $this->getBatchIdAccountId($date, $isAccountAssociated->id);
                    $batchId = $batchAccountId['batchId'];
                    $accountId = $batchAccountId['accountId'];

                    // get sales data After Read File
                    $salesData = getDataFromExcelFile($request->file('daily_sales'), 'dailysales');
                    // check if sales Data not empty
                    if (!empty($salesData)) {
                        if (isset($salesData[0]['asin']) && isset($salesData[0]['product_title']) && isset($salesData[0]['shipped_cogs']) && isset($salesData[0]['shipped_units']) && isset($salesData[0]['customer_returns']) && isset($salesData[0]['free_replacements'])) {
                            $storeDailySalesData = []; // define array for Store Data into DB
                            $storeAsinsScProduct = []; // define array for Store Data into SC Product DB
                            $dbData = [];
                            $asinsToStore = [];
                            $cronJobList = [];
                            // make array to insert data
                            foreach ($salesData as $data) {
                                $dbData = $this->dailySalesData($data);
                                $dbData['fk_vendor_id'] = $fkVendorId;
                                $dbData['batchId'] = $batchId;
                                $dbData['fkAccountId'] = $accountId;
                                $dbData['sale_date'] = dateConversion($date);
                                $dbData['created_at'] = date('Y-m-d h:i:s');
                                $dbData['updated_at'] = date('Y-m-d h:i:s');
                                array_push($storeDailySalesData, $dbData);
                                // Making array for insertion of ASIN's in sc_product_ids
                                //array_push($storeAsinsScProduct, $asinsToStore);
                                // making array for insertion in daily sales
                                /*
                                commented by Umer http://jira.codeinformatics.com/browse/HTK-1205
                                $asinsToStore = $this->storeAsin($dbData);
                                */
                            } // End for each Loop

                            // Insertion Daily Sales
                            VCModel::insertDailySales($storeDailySalesData);
                            // Set Cron Data
                            $cronJobList = $this->makeCronJobList('daily_sales');
                            VCModel::cronInsert($cronJobList);
                            // Insertion Daily Sales
                            //VCModel::insertAsins($storeAsinsScProduct);
                            unset($salesData);
                            unset($storeDailySalesData);
                            unset($dbData);
                            $request->session()->put('fk_vendor_id', $fkVendorId);
                            $responseData = array('success' => 'You have successfully uploaded Report!', 'ajax_status' => true);
                        } else {
                            $errorMessage = array('Uploaded file is not valid kindly upload daily sales file!');
                            $responseData = array('error' => $errorMessage, 'ajax_status' => false);
                        } // End condition of if else
                    } else {
                        $errorMessage = array('Uploaded file is empty kindly upload updated file!');
                        $responseData = array('error' => $errorMessage, 'ajax_status' => false);
                    } // End condition of if else
                } else {
                    $errorMessage = array('This Account is not associated kindly associate it with any Brand!');
                    $responseData = array('error' => $errorMessage, 'ajax_status' => false);
                }

            } else {
                $errorMessage = array('Upload File Extension should csv, xls, xlsx');
                $responseData = array('error' => $errorMessage, 'ajax_status' => false);
            }
        } else {
            $responseData = array('error' => $validator->errors()->all(), 'ajax_status' => false);
        } // End condition of if else of checking validations
        return response()->json($responseData);
    }

    /**
     * @param $date
     * @param $fkVendorId
     * @return array
     */
    private function getBatchIdAccountId($date, $fkAccountId)
    {
        $getBatchAccountId = [];
        $reportDate = date('Ymd', strtotime($date));
        $batchIdResult = VCModel::getBatchIdIfExist($reportDate, $fkAccountId);

        if (!is_null($batchIdResult)) {
            $getBatchAccountId['accountId'] = $batchIdResult->fkAccountId;
            $getBatchAccountId['batchId'] = $batchIdResult->batchId;
        } else {
            $singleArray = [];
            $singleArray['fkAccountId'] = $getBatchAccountId['accountId'] = $fkAccountId;
            $singleArray['batchID'] = $getBatchAccountId['batchId'] = $reportDate . $fkAccountId;
            $singleArray['reportDate'] = $reportDate;
            $singleArray["created_at"] = date('Y-m-d H:i:s');
            $singleArray["updated_at"] = date('Y-m-d H:i:s');
            \DB::table('tbl_batch_id')->insert($singleArray);
        }

        return $getBatchAccountId;
    }


    /**
     * @param $moduleName
     * @return mixed
     */
    private function makeCronJobList($moduleName)
    {
        $cronJobList['moduleName'] = $moduleName;
        $cronJobList['isDoneModuleData'] = 0;
        $cronJobList['isRunned'] = 0;
        $cronJobList['isFailed'] = 0;
        $cronJobList['isSuccess'] = 0;
        $cronJobList['createdAt'] = date('Y-m-d H:i:s');
        $cronJobList['updatedAt'] = date('Y-m-d H:i:s');

        return $cronJobList;
    }

    /**
     *  Purchase Order View Page
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function purchaseOrderView()
    {
        $data['vendors'] = VCModel::getAllVendors();
        $data['pageTitle'] = 'Purchase Order';
        //$data['pageHeading'] = 'Purchase Order';
        return view('subpages.vc.po.purchaseorder')->with($data);
    }

    /**
     * This function is used to store data of purchase orders different files
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function purchaseOrderStoreRecords(Request $request)
    {
        // Set Memory Limit And Execution Time
        setMemoryLimitAndExeTime();
        $responseErrorArray = array();
        $responseSuccessArray = array();
        $errorMessage = array();
        $successMessage = array();
        $responseData = array();
        // check open files
        if ($request->hasFile('open_agg_file')) {
            if (!$request->hasFile('open_nonagg_file')) {
                $responseErrorArray = array('error' => array('Open NON Agg File required'), 'ajax_status' => false);
                return response()->json($responseErrorArray);
            }
        } else if ($request->hasFile('open_nonagg_file')) {
            if (!$request->hasFile('open_agg_file')) {
                $responseErrorArray = array('error' => array('Open Agg File required'), 'ajax_status' => false);
                return response()->json($responseErrorArray);
            }
        }
        // check close files
        if ($request->hasFile('close_agg_file')) {
            if (!$request->hasFile('close_nonagg_file')) {
                $responseErrorArray = array('error' => array('Close NON Agg File required'), 'ajax_status' => false);
                return response()->json($responseErrorArray);
            }
        } else if ($request->hasFile('close_nonagg_file')) {
            if (!$request->hasFile('close_agg_file')) {
                $responseErrorArray = array('error' => array('Close Agg File required'), 'ajax_status' => false);
                return response()->json($responseErrorArray);
            }
        }
        if (!$request->hasFile('close_agg_file') && !$request->hasFile('close_nonagg_file') && !$request->hasFile('open_agg_file') && !$request->hasFile('open_nonagg_file')) {
            $responseErrorArray = array('error' => array('Kindly Upload the required Files'), 'ajax_status' => false);
            return response()->json($responseErrorArray);
        }
        // If validation Passes e.g no errors
        // get Extension of upload files
        $fileExtensionOpenAgg = ($request->hasFile('open_agg_file') ? $request->file('open_agg_file')->getClientOriginalExtension() : '');
        $fileExtensionOpenNonAgg = ($request->hasFile('open_nonagg_file') ? $request->file('open_nonagg_file')->getClientOriginalExtension() : '');
        $fileExtensionCloseAgg = ($request->hasFile('close_agg_file') ? $request->file('close_agg_file')->getClientOriginalExtension() : '');
        $fileExtensionNonCloseAgg = ($request->hasFile('close_nonagg_file') ? $request->file('close_nonagg_file')->getClientOriginalExtension() : '');
        $storeDataArray = [];
        $closeStoreDataArray = [];
        $storeAsinsScProduct = [];
        $asinsToStore = [];
        $fkVendorId = $request->input('vendor');
        $date = date('01-m-Y');
        $isAccountAssociated = AccountModel::where('fkId', $fkVendorId)
            ->where('fkAccountType', 3)
            ->first();
        if (!is_null($isAccountAssociated)) {
            /*****************************************
             *   Open AGG and NON-AGG FILE DATA     *
             *****************************************/
            // Check file validation Open Agg and Open Non Agg
            if ($this->validateExcelFile($fileExtensionOpenAgg) != false && $this->validateExcelFile($fileExtensionOpenNonAgg) != false) {
                // Agg File Open
                $fileUploadedOpenAggPath = uploadCsvFile($request->file('open_agg_file'), 'purchaseorder');
                // check if file uploaded successfully
                if ($fileUploadedOpenAggPath != FALSE) {
                    // Read open AGG file
                    $getDataFromOpenAggFile = readExcelPoFile($fileUploadedOpenAggPath);
                    // if not empty open AGG file
                    if (!empty($getDataFromOpenAggFile)) {
                        // validate the columns e.g make sure it is open Agg file
                        if (isset($getDataFromOpenAggFile[0]['poid']) || isset($getDataFromOpenAggFile[0]['po']) && isset($getDataFromOpenAggFile[0]['orderdate']) || isset($getDataFromOpenAggFile[0]['ordered_on'])) {
                            $singleEntry['openAgg'] = array();
                            // making array for data collection
                            foreach ($getDataFromOpenAggFile as $index) {
                                $openAggData['po'] = isset($index['poid']) ? $index['poid'] : $index['po'];
                                if (isset($index['orderdate']) && !empty($index['orderdate'])) {
                                    $openAggData['orderon_date'] = $index['orderdate'];
                                } elseif (isset($index['ordered_on']) && !empty($index['ordered_on'])) {
                                    $openAggData['orderon_date'] = $index['ordered_on'];
                                }
                                array_push($singleEntry['openAgg'], $openAggData);
                            } // End Foreach Loop
                            $storeDataArray = $singleEntry;
                        } else {
                            array_push($errorMessage, 'Uploaded file is not valid kindly upload open AGG  file!');
                            $responseErrorArray = array('error' => $errorMessage, 'ajax_status' => false);
                        } // End validate file
                    } else {
                        array_push($errorMessage, 'Uploaded file is empty kindly upload open AGG  updated file!');
                        $responseErrorArray = array('error' => $errorMessage, 'ajax_status' => false);
                    }
                } else {
                    array_push($errorMessage, 'File open AGG not uploaded successfully!');
                    $responseErrorArray = array('error' => $errorMessage, 'ajax_status' => false);
                } // End condition ---> check if file uploaded successfully


                // Open Non Agg File OPEN
                $fileUploadedOpenNonAggPath = uploadCsvFile($request->file('open_nonagg_file'), 'purchaseorder');
                // check if file uploaded successfully
                if ($fileUploadedOpenNonAggPath != FALSE) {
                    // Read open Non AGG file

                    $getDataFromOpenNonAggFile = readExcelPoFile($fileUploadedOpenNonAggPath);
                    // if not empty open Non AGG file
                    if (!empty($getDataFromOpenNonAggFile)) {
                        // validate the columns e.g make sure it is open non Agg file
                        if (
                            isset($getDataFromOpenNonAggFile[0]['poid']) || isset($getDataFromOpenNonAggFile[0]['po'])
                            && isset($getDataFromOpenNonAggFile[0]['vendor']) && isset($getDataFromOpenNonAggFile[0]['asin'])
                            && isset($getDataFromOpenNonAggFile[0]['title'])
                            && isset($getDataFromOpenNonAggFile[0]['totalcost']) || isset($getDataFromOpenNonAggFile[0]['total_cost'])
                        ) {
                            $singleEntry['openNonAgg'] = array();
                            // making array for data collection
                            foreach ($getDataFromOpenNonAggFile as $index) {

                                $data = $this->NonAggFileData($index);
                                $data['fk_vendor_id'] = $fkVendorId;
                                // check if open non agg file column po is equal to open agg po column.
                            if (isset($storeDataArray['openAgg'])){
                                foreach ($storeDataArray['openAgg'] as $openAgg) {
                                    if ($openAgg['po'] == $data['po']) {
                                        $orderOpenAggDate = (isset($openAgg['orderon_date']) && !empty($openAgg['orderon_date']) ? dateConversion($openAgg['orderon_date']) : 'NA');
                                    }
                                }
                            }
                                $data['orderon_date'] = (isset($orderOpenAggDate) && !empty($orderOpenAggDate)) ? $orderOpenAggDate : 'NA';
                                $batchAccountId = $this->getBatchIdAccountId($data['orderon_date'], $isAccountAssociated->id);
                                $batchId = $batchAccountId['batchId'];
                                $accountId = $batchAccountId['accountId'];
                                $data['fkAccountId'] = $accountId;
                                $data['batchId'] = $batchId;
                                $data['capture_date'] = dateConversion(date('Y-m-d'));
                                $data['created_at'] = date('Y-m-d h:i:s');
                                $data['updated_at'] = date('Y-m-d h:i:s');
                                //$asinsToStore = $this->storeAsin($data);
                                // Making array for insetion of ASIN's in sc_product_ids
                                //array_push($storeAsinsScProduct, $asinsToStore);


                                array_push($singleEntry['openNonAgg'], $data);

                            } // End foreach Loop
                            $storeDataArray = $singleEntry;

                        } else {
                            array_push($errorMessage, 'Uploaded file is not valid kindly upload open Non AGG  file!');
                            $responseErrorArray = array('error' => $errorMessage, 'ajax_status' => false);
                        } // End Condition -->  validate the columns
                    } else {
                        array_push($errorMessage, 'Uploaded file is empty kindly upload open Non AGG  updated file!');
                        $responseErrorArray = array('error' => $errorMessage, 'ajax_status' => false);
                    } // End condition -->  if not empty open Non AGG file
                } else {
                    array_push($errorMessage, 'File open Non AGG not uploaded successfully!');
                    $responseErrorArray = array('error' => $errorMessage, 'ajax_status' => false);
                } // End condition --> check if file uploaded successfully

                // check if Array Is not empty then insert data into DB OPEN
                if (!empty($storeDataArray['openNonAgg'])) {
                    $request->session()->put('fk_vendor_id', $fkVendorId);
                    VCModel::insertPoData($storeDataArray['openNonAgg']);
                    // ASIN's Insetion
                    //VCModel::insertAsins($storeAsinsScProduct);
                    array_push($successMessage, 'Open AGG and Non-AGG file uploaded successfully!');
                    $responseSuccessArray = array('success' => $successMessage, 'ajax_status' => true);
                    unset($storeDataArray);
                    unset($data);
                    unset($getDataFromOpenNonAggFile);
                }
            }

            /*****************************************
             *   Close AGG and NON-AGG FILE DATA     *
             *****************************************/
            // Check file validation Close Agg and Close Non Agg
            if ($this->validateExcelFile($fileExtensionCloseAgg) != false && $this->validateExcelFile($fileExtensionNonCloseAgg) != false) {
                // Close Agg File
                $fileUploadedCloseAggPath = uploadCsvFile($request->file('close_agg_file'), 'purchaseorder');

                // check if file uploaded successfully
                if ($fileUploadedCloseAggPath != FALSE) {
                    // Read close Agg File
                    $getDataFromCloseAggFile = readExcelPoFile($fileUploadedCloseAggPath);
                    // if not empty close AGG file
                    if (!empty($getDataFromCloseAggFile)) {
                        // validate the columns e.g make sure it is close Agg file
                        if (isset($getDataFromCloseAggFile[0]['poid']) || isset($getDataFromCloseAggFile[0]['po']) && isset($getDataFromCloseAggFile[0]['orderdate']) || isset($getDataFromCloseAggFile[0]['ordered_on'])) {
                            $singleEntry['closeAgg'] = array();
                            // making array for data collection
                            foreach ($getDataFromCloseAggFile as $index) {
                                $closeAggData['po'] = isset($index['poid']) ? $index['poid'] : $index['po'];
                                if (isset($index['orderdate']) && !empty($index['orderdate'])) {
                                    $closeAggData['orderon_date'] = $index['orderdate'];
                                } elseif (isset($index['ordered_on']) && !empty($index['ordered_on'])) {
                                    $closeAggData['orderon_date'] = $index['ordered_on'];
                                }
                                array_push($singleEntry['closeAgg'], $closeAggData);
                            } // End foreach loop
                            $closeStoreDataArray = $singleEntry;
                        } else {
                            array_push($errorMessage, 'Uploaded file is not valid kindly upload close AGG  file!');
                            $responseErrorArray = array('error' => $errorMessage, 'ajax_status' => false);
                        } // End validate file
                    } else {
                        array_push($errorMessage, 'Uploaded file is empty kindly upload close AGG  updated file!');
                        $responseErrorArray = array('error' => $errorMessage, 'ajax_status' => false);
                    } // End condtion -->  if not empty close AGG file
                } else {
                    array_push($errorMessage, 'File close AGG not uploaded successfully!');
                    $responseErrorArray = array('error' => $errorMessage, 'ajax_status' => false);
                }
                // Non Agg File Close
                $fileUploadedCloseNonAggPath = uploadCsvFile($request->file('close_nonagg_file'), 'purchaseorder');
                if ($fileUploadedCloseNonAggPath != FALSE) {
                    // Read close Non AGG file
                    $getDataFromCloseNonAggFile = readExcelPoFile($fileUploadedCloseNonAggPath);

                    // check if file uploaded successfully
                    if (!empty($getDataFromCloseNonAggFile)) {
                        // validate the columns e.g make sure it is close non Agg file
                        if (
                            isset($getDataFromCloseNonAggFile[0]['poid']) || isset($getDataFromCloseNonAggFile[0]['po'])
                            && isset($getDataFromCloseNonAggFile[0]['vendor']) && isset($getDataFromCloseNonAggFile[0]['asin'])
                            && isset($getDataFromCloseNonAggFile[0]['title'])
                            && isset($getDataFromCloseNonAggFile[0]['totalcost']) || isset($getDataFromCloseNonAggFile[0]['total_cost'])
                        ) {
                            $singleEntry['closeNonAgg'] = array();
                            // making array for data collection
                            foreach ($getDataFromCloseNonAggFile as $index) {
                                $data = $this->NonAggFileData($index);
                                $data['fk_vendor_id'] = $fkVendorId;
                                if (isset($closeStoreDataArray['closeAgg'])){
                                    foreach ($closeStoreDataArray['closeAgg'] as $closeAgg) {
                                        if ($closeAgg['po'] == $data['po']) {
                                            $orderCloseAggDate = (isset($closeAgg['orderon_date']) && !empty($closeAgg['orderon_date']) ? dateConversion($closeAgg['orderon_date']) : 'NA');
                                        }
                                    }
                            }
                                $data['orderon_date'] = (isset($orderCloseAggDate) && !empty($orderCloseAggDate)) ? $orderCloseAggDate : 'NA';
                                $batchAccountId = $this->getBatchIdAccountId($data['orderon_date'], $isAccountAssociated->id);
                                $batchId = $batchAccountId['batchId'];
                                $accountId = $batchAccountId['accountId'];
                                $data['batchId'] = $batchId;
                                $data['fkAccountId'] = $accountId;
                                $data['capture_date'] = dateConversion(date('Y-m-d'));
                                //$asinsToStore = $this->storeAsin($data);
                                $data['created_at'] = date('Y-m-d h:i:s');
                                $data['updated_at'] = date('Y-m-d h:i:s');
                                // Making array for insetion of ASIN's in sc_product_ids
                                //array_push($storeAsinsScProduct, $asinsToStore);

                                array_push($singleEntry['closeNonAgg'], $data);
                            } // End foreach loop
                            $closeStoreDataArray = $singleEntry;
                        } else {
                            array_push($errorMessage, 'Uploaded file is not valid kindly upload close Non AGG  file!');
                            $responseErrorArray = array('error' => $errorMessage, 'ajax_status' => false);
                        } // End Validation --> validate  columns
                    } else {
                        array_push($errorMessage, 'Uploaded file is empty kindly upload close Non AGG  updated file!');
                        $responseErrorArray = array('error' => $errorMessage, 'ajax_status' => false);
                    } // End condtion -->  if not empty close non AGG file

                } else {
                    array_push($errorMessage, 'File close non AGG not upload successfully!');
                    $responseErrorArray = array('error' => $errorMessage, 'ajax_status' => false);
                }
                // Close Agg Function DATA INSERT
                if (!empty($closeStoreDataArray['closeNonAgg'])) {

                    // Insetion of PO Data
                    VCModel::insertPoData($closeStoreDataArray['closeNonAgg']);
                    // Insertion Of ASIN's
                    //VCModel::insertAsins($storeAsinsScProduct);
                    array_push($successMessage, 'Close AGG and Non-AGG file uploaded successfully!');
                    $responseSuccessArray = array('success' => $successMessage, 'ajax_status' => true);
                    unset($closeStoreDataArray);
                    unset($data);
                    unset($singleEntry);
                    unset($getDataFromCloseAggFile);
                }
            }
        } else {
            array_push($errorMessage, 'This Account is not associated kindly associate it with any Brand!');
            $responseErrorArray = array('error' => $errorMessage, 'ajax_status' => false);
        }
        $responseData = $responseErrorArray + $responseSuccessArray;
        return response()->json($responseData);
    }

    /**
     * Show Daily Inventory Form
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function dailyInventoryView()
    {
        $data['vendors'] = VCModel::getAllVendors();
        $data['pageTitle'] = 'Daily Inventory';
        //$data['pageHeading'] = 'Daily Inventory';
        return view('subpages.vc.inventory.dailyinventory')->with($data);
    }

    /**
     * Store Records of Daily Inventory in database
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function dailyInventoryStoreRecords(Request $request)
    {
        // Set Memory Limit And Execution Time
        setMemoryLimitAndExeTime();
        $validator = Validator::make($request->all(), ['vendor' => 'required', 'daily_inventory' => 'required', 'daily_inventory_date' => 'required']);
        $responseData = array();
        $errorMessage = array();
        $successMessage = array();
        // check validations of form
        if ($validator->passes()) {
            // get upload file extension
            $fileExtension = ($request->hasFile('daily_inventory') ? $request->file('daily_inventory')->getClientOriginalExtension() : '');
            // check file validation extension
            if ($this->validateExcelFile($fileExtension) != false) {
                $fkVendorId = $request->input('vendor');
                $date = $request->input('daily_inventory_date');
                $isAccountAssociated = AccountModel::where('fkId', $fkVendorId)
                    ->where('fkAccountType', 3)
                    ->first();
                if (!is_null($isAccountAssociated)) {
                    // Check if not generate batchId for specified date then generate it
                    $batchAccountId = $this->getBatchIdAccountId($date, $isAccountAssociated->id);
                    $batchId = $batchAccountId['batchId'];
                    $accountId = $batchAccountId['accountId'];
//                    $currentVcModel = $currentVcModel->first();
//                    $batchId = $currentVcModel->accounts[0]->relationBatchId[0]->batchId;
//                    $accountId = $currentVcModel->accounts[0]->relationBatchId[0]->fkAccountId;
                    // Read inventory uploaded file
                    $dailyInventoryData = getDataFromExcelFile($request->file('daily_inventory'), 'inventory');
                    // if not empty uplaoded file
                    if (!empty($dailyInventoryData)) {
                        // check validation inventory file columns
                        if (isset($dailyInventoryData[0]['asin']) && isset($dailyInventoryData[0]['product_title']) && isset($dailyInventoryData[0]['sellable_on_hand_units']) && isset($dailyInventoryData[0]['sellable_on_hand_inventory']) && isset($dailyInventoryData[0]['net_received']) && isset($dailyInventoryData[0]['net_received_units'])) {
                            $storeData = []; // define array for Store Data into DB
                            $storeAsinsScProduct = [];
                            $dbData = [];
                            $asinsToStore = [];
                            foreach ($dailyInventoryData as $key2 => $data) {
                                $dbData = $this->dailyInventoryData($data);
                                $dbData['fk_vendor_id'] = $fkVendorId;
                                $dbData['batchId'] = $batchId;
                                $dbData['fkAccountId'] = $accountId;
                                $dbData['rec_date'] = dateConversion($date);
                                $dbData['created_at'] = date('Y-m-d h:i:s');
                                $dbData['updated_at'] = date('Y-m-d h:i:s');
                                //$asinsToStore = $this->storeAsin($dbData);
                                // Making array for insetion of ASIN's in sc_product_ids
                                //array_push($storeAsinsScProduct, $asinsToStore);

                                array_push($storeData, $dbData);
                            } // End foreach Loop


                            // Insertion Database
                            VCModel::insertInventoryData($storeData);
                            // out of stock event tracking for VC inventory.
                            Artisan::call('eventTrackingCron:OOS vc ' . dateConversion($date));
                            // Set Cron Data
                            $cronJobList = $this->makeCronJobList('daily_inventory');
                            VCModel::cronInsert($cronJobList);
                            //VCModel::insertAsins($storeAsinsScProduct);
                            unset($dailyInventoryData);
                            unset($storeData);
                            unset($dbData);
                            // Storing Vendor Id in sessions
                            $request->session()->put('fk_vendor_id', $fkVendorId);
                            $responseData = array('success' => 'You have successfully uploaded Report!', 'ajax_status' => true);
                        } else {
                            $errorMessage = array('Uploaded file is not valid kindly upload daily inventory file!');
                            $responseData = array('error' => $errorMessage, 'ajax_status' => false);
                        } // End condition --> check validation inventory file columns

                    } else {
                        $errorMessage = array('Upload file is empty kindly upload updated file!');
                        $responseData = array('error' => $errorMessage, 'ajax_status' => false);
                    } // End Condition --> if not empty uplaoded file
                } else {
                    $errorMessage = array('This Account is not associated kindly associate it with any Brand!');
                    $responseData = array('error' => $errorMessage, 'ajax_status' => false);
                } // End Condition --> checking batch ID
            } else {
                $errorMessage = array('Upload File Extension should csv, xls, xlsx');
                $responseData = array('error' => $errorMessage, 'ajax_status' => false);
            } // End Condition --> check file validation extension

        } else {
            $responseData = array('error' => $validator->errors()->all(), 'ajax_status' => false);
        }
        return response()->json($responseData);

    }

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function trafficView()
    {
        $data['vendors'] = VCModel::getAllVendors();
        $data['pageTitle'] = 'Traffic';
        //$data['pageHeading'] = 'Traffic';
        return view('subpages.vc.traffic.traffic')->with($data);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function trafficStoreRecords(Request $request)
    {
        // Set Memory Limit And Execution Time
        setMemoryLimitAndExeTime();
        $validator = Validator::make($request->all(), ['vendor' => 'required', 'traffic_upload_file' => 'required', 'daterange' => 'required'
        ]);
        $responseData = array();
        $errorMessage = array();
        $successMessage = array();
        if ($validator->passes()) {
            $fileExtension = ($request->hasFile('traffic_upload_file') ? $request->file('traffic_upload_file')->getClientOriginalExtension() : '');
            if ($this->validateExcelFile($fileExtension) != false) {
                $fkVendorId = $request->input('vendor');
                $separetDateRange = explode(' - ', $request->daterange);
                $trafficStartDate = $separetDateRange[0];
                $trafficEndDate = $separetDateRange[1];
                $isAccountAssociated = AccountModel::where('fkId', $fkVendorId)
                    ->where('fkAccountType', 3)
                    ->first();
                if (!is_null($isAccountAssociated)) {
                    // Check if not generate batchId for specified date then generate it
                    $batchAccountId = $this->getBatchIdAccountId($trafficEndDate, $isAccountAssociated->id);
                    $batchId = $batchAccountId['batchId'];
                    $accountId = $batchAccountId['accountId'];
                    // get Data From uploaded Traffic Excel File
                    $trafficData = getDataFromExcelFile($request->file('traffic_upload_file'), 'traffic');

                    if (!empty($trafficData)) {
                        // check validation traffic columns
                        if (isset($trafficData[0]['kpis'])
                            && isset($trafficData[0]['reported'])
                            && isset($trafficData[0]['prior_period'])
                            && isset($trafficData[0]['last_year'])) {
                            // get extracted data from excel file
                            $trafficDataArrayStore = $this->trafficData($trafficData);
                            $trafficDataArrayStore['asin'] = 'NA';
                            $trafficDataArrayStore['product_title'] = 'NA';
                            $trafficDataArrayStore['subcategory'] = 'NA';
                            $trafficDataArrayStore['category'] = 'NA';
                            $trafficDataArrayStore['percentage_total_gvs'] = 0.00;
                            $trafficDataArrayStore['conversion_percentile'] = 0.00;
                            $trafficDataArrayStore['fast_track_glance_view'] = 0.00;
                            $trafficDataArrayStore['fk_vendor_id'] = $fkVendorId;
                            $trafficDataArrayStore['batchId'] = $batchId;
                            $trafficDataArrayStore['fkAccountId'] = $accountId;
                            $trafficDataArrayStore['start_date'] = dateConversion($trafficStartDate);
                            $trafficDataArrayStore['end_date'] = dateConversion($trafficEndDate);
                            $trafficDataArrayStore['capture_date'] = date('Y-m-d');
                            $trafficDataArrayStore['created_at'] = date('Y-m-d h:i:s');
                            $trafficDataArrayStore['updated_at'] = date('Y-m-d h:i:s');
                            // Insetion Database
                            VCModel::insertTrafficData($trafficDataArrayStore, 'old');
                            unset($trafficData);
                            unset($trafficDataArrayStore);
                            // Storing Vendor Id in sessions
                            $request->session()->put('fk_vendor_id', $fkVendorId);
                            $responseData = array('success' => 'You have successfully uploaded Report!', 'ajax_status' => true);
                        } else if (isset($trafficData[0]['asin']) && isset($trafficData[0]['product_title'])&& isset($trafficData[0]['category'])
                            && isset($trafficData[0]['subcategory']) && isset($trafficData[0]['unique_visitors_prior_period'])) { // latest file column handling
                            // get extracted data from excel file
                            $Store = $this->trafficDataUpdated($trafficData, $fkVendorId, $batchId, $accountId, $trafficStartDate, $trafficEndDate);
                            // Insertion Database
                            VCModel::insertTrafficData($Store, 'new');
                            unset($trafficData);
                            unset($Store);
                            // Storing Vendor Id in sessions
                            $request->session()->put('fk_vendor_id', $fkVendorId);
                            $responseData = array('success' => 'You have successfully uploaded Report!', 'ajax_status' => true);
                        } else {
                            $errorMessage = array('Uploaded file is not valid kindly upload traffic file!');
                            $responseData = array('error' => $errorMessage, 'ajax_status' => false);
                        } // End Condition -->  check validation traffic columns
                    } else {
                        $errorMessage = array('Upload file is empty kindly upload updated file!');
                        $responseData = array('error' => $errorMessage, 'ajax_status' => false);
                    }
                } else {
                    $errorMessage = array('This Account is not associated kindly associate it with any Brand!');
                    $responseData = array('error' => $errorMessage, 'ajax_status' => false);
                }

            } else {
                $errorMessage = array('Upload File Extension should csv, xls, xlsx');
                $responseData = array('error' => $errorMessage, 'ajax_status' => false);
            }
        } else {
            $responseData = array('error' => $validator->errors()->all(), 'ajax_status' => false);
        } // End Condition validation passes
        return response()->json($responseData);
    }

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function forecastView()
    {
        $data['vendors'] = VCModel::getAllVendors();
        $data['pageTitle'] = 'Forecast';
        //$data['pageHeading'] = 'Forecast';
        return view('subpages.vc.forecast.forecast')->with($data);
    }

    /**
     * This function is used to upload excel file and read data from it and store data into database.
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Illuminate\Validation\ValidationException
     */
    public function forecastStoreRecords(Request $request)
    {
        // Set Memory Limit And Execution Time
        setMemoryLimitAndExeTime();
        $validator = Validator::make($request->all(), ['vendor' => 'required', 'forecast_upload_file' => 'required', 'forecast_date' => 'required']);
        $responseData = array();
        $errorMessage = array();
        $successMessage = array();
        if ($validator->passes()) {
            $fileExtension = ($request->hasFile('forecast_upload_file') ? $request->file('forecast_upload_file')->getClientOriginalExtension() : '');
            // validate excel file
            if ($this->validateExcelFile($fileExtension) != false) {
                $forecastStoreDataArray = array();
                $fkVendorId = $request->input('vendor');
                $date = $request->input('forecast_date');
                $isAccountAssociated = AccountModel::where('fkId', $fkVendorId)
                    ->where('fkAccountType', 3)
                    ->first();
                if (!is_null($isAccountAssociated)) {
                    // Check if not generate batchId for specified date then generate it
                    $batchAccountId = $this->getBatchIdAccountId($date, $isAccountAssociated->id);
                    $batchId = $batchAccountId['batchId'];
                    $accountId = $batchAccountId['accountId'];
                    $date = $request->input('forecast_date');
                    $forecastData = getDataFromExcelFile($request->file('forecast_upload_file'), 'forecast');
                    // check if not empty uploaded file
                    if (!empty($forecastData)) {

                        // validate forecaset file columns
                        if (isset($forecastData[0]['asin']) && isset($forecastData[0]['product_title']) && isset($forecastData[0]['open_purchase_order_quantity']) && isset($forecastData[0]['week_1_p70_forecast']) && isset($forecastData[0]['week_2_p70_forecast']) && isset($forecastData[0]['week_3_p70_forecast']) && isset($forecastData[0]['week_3_p70_forecast']) && isset($forecastData[0]['week_1_p90_forecast']) && isset($forecastData[0]['week_2_p90_forecast']) && isset($forecastData[0]['week_3_p90_forecast']) && isset($forecastData[0]['week_4_p90_forecast'])) {
                            $dbData = [];
                            $storeAsinsScProduct = [];
                            $asinsToStore = [];
                            foreach ($forecastData as $data) {
                                // getting data of forecast file into array
                                $dbData = $this->forecastData($data);
                                $dbData['fk_vendor_id'] = $fkVendorId;
                                $dbData['batchId'] = $batchId;
                                $dbData['fkAccountId'] = $accountId;
                                $dbData['capture_date'] = dateConversion($date);
                                $dbData['created_at'] = date('Y-m-d h:i:s');
                                $dbData['updated_at'] = date('Y-m-d h:i:s');
                                //$asinsToStore = $this->storeAsin($dbData);
                                // Making array for insetion of ASIN's in sc_product_ids
                                //array_push($storeAsinsScProduct, $asinsToStore);
                                // Making array To store data in forecast Table
                                array_push($forecastStoreDataArray, $dbData);
                            }
                            VCModel::insertForecastData($forecastStoreDataArray);
                            // Set Cron Data
                            $cronJobList = $this->makeCronJobList('daily_forecast');
                            VCModel::cronInsert($cronJobList);
                            //VCModel::insertAsins($storeAsinsScProduct);
                            unset($dbData);
                            unset($forecastStoreDataArray);
                            // Storing Vendor Id in sessions
                            $request->session()->put('fk_vendor_id', $fkVendorId);
                            $responseData = array('success' => 'You have successfully uploaded Report!', 'ajax_status' => true);
                        } else {
                            $errorMessage = array('Uploaded file is not valid kindly upload forecast file!');
                            $responseData = array('error' => $errorMessage, 'ajax_status' => false);
                        }
                    } else {
                        $errorMessage = array('Upload file is empty kindly upload updated file!');
                        $responseData = array('error' => $errorMessage, 'ajax_status' => false);
                    }
                } else {
                    $errorMessage = array('This Account is not associated kindly associate it with any Brand!');
                    $responseData = array('error' => $errorMessage, 'ajax_status' => false);
                }

            } else {
                $errorMessage = array('Upload File Extension should csv, xls, xlsx');
                $responseData = array('error' => $errorMessage, 'ajax_status' => false);
            } // End validation of excel file
        } else {
            $responseData = array('error' => $validator->errors()->all(), 'ajax_status' => false);
        }
        return response()->json($responseData);
    }

    /**
     * @return mixed
     */
    public function getAllVendors()
    {
        return VCModel::getAllVendors();
    }

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function productCatalogView()
    {
        $data['vendors'] = VCModel::getAllVendors();
        $data['pageTitle'] = 'Product Catalog';
        //$data['pageHeading'] = 'Product Catalog';
        return view('subpages.vc.productcatalog.productcatalog')->with($data);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function productCatalogStoreRecords(Request $request)
    {

        // Set Memory Limit And Execution Time
        setMemoryLimitAndExeTime();
        $validator = Validator::make($request->all(), ['vendor' => 'required', 'catalog_upload_file' => 'required', 'catalog_date' => 'required']);
        $responseData = [];
        $errorMessage = [];

        if ($validator->passes()) {
            $fileExtension = ($request->hasFile('catalog_upload_file') ? $request->file('catalog_upload_file')->getClientOriginalExtension() : '');
            if ($this->validateExcelFile($fileExtension) != false) {
                $pCatalogStoreDataArray = [];
                $fkVendorId = $request->input('vendor');
                $date = $request->input('catalog_date');
                $isAccountAssociated = AccountModel::where('fkId', $fkVendorId)
                    ->where('fkAccountType', 3)
                    ->first();
                if (!is_null($isAccountAssociated)) {
                    // Check if not generate batchId for specified date then generate it
                    $batchAccountId = $this->getBatchIdAccountId($date, $isAccountAssociated->id);
                    $batchId = $batchAccountId['batchId'];
                    $accountId = $batchAccountId['accountId'];
                    $productCatalogData = getDataFromExcelFile($request->file('catalog_upload_file'), 'productcatalog');

                    if (!empty($productCatalogData)) {
                        if (isset($productCatalogData[0]['asin']) && isset($productCatalogData[0]['product_title']) && isset($productCatalogData[0]['parent_asin']) && isset($productCatalogData[0]['upc']) && isset($productCatalogData[0]['list_price'])) {
                            $dbData = [];
                            $storeAsinsScProduct = [];
                            $asinsToStore = [];
                            foreach ($productCatalogData as $data) {
                                // getting data of product Catalog file
                                $dbData = $this->productCatalogData($data);
                                $dbData['fk_vendor_id'] = $fkVendorId;
                                $dbData['batchId'] = $batchId;
                                $dbData['fkAccountId'] = $accountId;
                                $dbData['capture_date'] = dateConversion($date);
                                $dbData['created_at'] = date('Y-m-d h:i:s');
                                $dbData['updated_at'] = date('Y-m-d h:i:s');
                                //$asinsToStore = $this->storeAsin($dbData);
                                // Making array for insetion of ASIN's in sc_product_ids
                                //array_push($storeAsinsScProduct, $asinsToStore);


                                array_push($pCatalogStoreDataArray, $dbData);
                            } // End foreach Loop


                            VCModel::insertproductCatalogData($pCatalogStoreDataArray);
                            //VCModel::insertAsins($storeAsinsScProduct);
                            unset($dbData);
                            unset($productCatalogData);
                            // Storing Vendor Id in sessions
                            $request->session()->put('fk_vendor_id', $fkVendorId);
                            $responseData = array('success' => 'You have successfully uploaded Report!', 'ajax_status' => true);

                        } else {
                            $errorMessage = array('Uploaded file is not valid kindly upload catalog file!');
                            $responseData = array('error' => $errorMessage, 'ajax_status' => false);
                        }
                    } else {
                        $errorMessage = array('Upload file is empty kindly upload updated file!');
                        $responseData = array('error' => $errorMessage, 'ajax_status' => false);
                    }
                } else {
                    $errorMessage = array('This Account is not associated kindly associate it with any Brand!');
                    $responseData = array('error' => $errorMessage, 'ajax_status' => false);
                }

            } else {
                $errorMessage = array('Upload File Extension should csv, xls, xlsx');
                $responseData = array('error' => $errorMessage, 'ajax_status' => false);
            }
        } else {
            $responseData = array('error' => $validator->errors()->all(), 'ajax_status' => false);
        }
        return response()->json($responseData);
    }

    /**
     * Used to show vendor add form
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function vendorsView()
    {
        $data['pageTitle'] = 'Vendor Add';
        //$data['pageHeading'] = 'Add Vendor';
        return view('subpages.vc.vendor.vendoradd')->with($data);
    }

    /**
     * This function is used to add vendors
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     * @throws \Illuminate\Validation\ValidationException
     */
    public function vendorsAdd(Request $request)
    {

        $messages = [
            'vendor_name.unique' => 'This vendor is already exist.'
        ];
        $validator = Validator::make($request->all(), [
            'vendor_name' => 'required|unique:tbl_vc_vendors|max:40', 'domain' => 'required|max:10', 'tier' => 'required|max:10'
        ], $messages);
        $responseData = array();
        if ($validator->passes()) {
            $dbData = array();
            $dbData['vendor_name'] = $request->input('vendor_name');
            $dbData['domain'] = $request->input('domain');
            $dbData['tier'] = $request->input('tier');
            $dbData['created_date'] = date('Y-m-d h:i:s');
            $dbData['vendor_status'] = 1;
            VCModel::insertVendorData($dbData);
            unset($dbData);
            $responseData = array('success' => 'Vendor has been added successfully!', 'ajax_status' => true);
        } else {
            $responseData = array('error' => $validator->errors()->all(), 'ajax_status' => false);
        }
        return response()->json($responseData);
    }

    /**
     * This function is used to show history form
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function showHistoryForm()
    {
        $data['pageTitle'] = 'Export Data';
        //$data['pageHeading'] = 'Export Data';
        return view("subpages.vc.historicaldata.historicaldata")->with($data);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function historicalDataDownload(Request $request)
    {
        setMemoryLimitAndExeTime();
        $separetDateRange = explode(' - ', $request->daterange);
        $startDate = $separetDateRange[0];
        $endDate = $separetDateRange[1];
        $reportType = $request->historicalDataReportType;
        $response["title"] = ucfirst(str_replace('_', ' ', $reportType));
        $getDataFromDB = VCModel::getHistoricalDataFromDB($startDate, $endDate, $reportType);
        $response["status"] = true;
        if (count($getDataFromDB) > 0) {
            $response["url"] = url('vc-download/' . $reportType . '/' . $startDate . '/' . $endDate);
            $response["message"] = "Please click down there to download file.";
            return $response;
        }
        $response["status"] = false;
        $response["message"] = "No Data Found against This Date";
        return response()->json($response);

    }

    /**
     * @param $reportType
     * @param $startDate
     * @param $endDate
     * @return string|\Symfony\Component\HttpFoundation\StreamedResponse
     * @throws \Box\Spout\Common\Exception\IOException
     * @throws \Box\Spout\Common\Exception\InvalidArgumentException
     * @throws \Box\Spout\Common\Exception\UnsupportedTypeException
     * @throws \Box\Spout\Writer\Exception\WriterNotOpenedException
     */
    public function historicalDataDownloadCSV($reportType, $startDate, $endDate)
    {
        setMemoryLimitAndExeTime();
        $getDataFromDB = VCModel::getHistoricalDataFromDB($startDate, $endDate, $reportType);
        return (new FastExcel($getDataFromDB))->download($reportType . '-' . $startDate . '-' . $endDate . '.csv');
    }

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function deleteView()
    {
        $data['vendors'] = VCModel::getAllVendors();
        $data['pageTitle'] = 'Delete Record';
        return view('subpages.vc.verify.verify')->with($data);
    }

    /**
     * This function is used to delete the stored data from database
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function deleteStoreRecords(Request $request)
    {
        // Set Memory Limit And Execution Time
        setMemoryLimitAndExeTime();
        $validator = Validator::make($request->all(),
            [
                'vendor' => 'required',
                'type' => 'required',
                'daterange' => 'required'
            ]
        );
        $responseData = array();
        $errorMessage = array();
        $successMessage = array();
        if ($validator->passes()) {
            $fkVendorId = $request->input('vendor');
            $type = $request->input('type');
            $separetDateRange = explode(' - ', $request->daterange);
            $startDate = $separetDateRange[0];
            $endDate = isset($separetDateRange[1]) ? $separetDateRange[1] : 0;
            // check validation traffic columns
            $dataArray = array(
                'fk_vendor_id' => $fkVendorId,
                'start_date' => dateConversion($startDate),
                'end_date' => dateConversion($endDate)
            );

            $deleteRecordResponse = VCModel::deleteDataOfSpecificType($dataArray, $type);
            if ($deleteRecordResponse['status'] == true) {
                if ($deleteRecordResponse['count'] == 0) {
                    $responseData = array(
                        'error' => 'No data found',
                        'ajax_status' => false
                    );
                } else {
                    $request->session()->put('fk_vendor_id', $fkVendorId);
                    $responseData = array(
                        'success' => 'You have successfully Delete Record and Count : ' . $deleteRecordResponse['count'],
                        'ajax_status' => true
                    );
                }
            } else {
                $responseData = array(
                    'success' => 'Record Not delete of selected report type',
                    'ajax_status' => true
                );
            }// End Condition -->  check type of delete record
        } else {
            $responseData = array('error' => $validator->errors()->all(), 'ajax_status' => false);
        } // End Condition validation passes
        return response()->json($responseData);
    }

    /**
     * This function is used to verify the stored data from databases
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function verifyStoredRecords(Request $request)
    {
        // Set Memory Limit And Execution Time
        setMemoryLimitAndExeTime();
        $validator = Validator::make($request->all(),
            [
                'vendor' => 'required',
                'type' => 'required',
            ]
        );
        $responseData = array();
        $errorMessage = array();
        $successMessage = array();
        if ($validator->passes()) {
            $fkVendorId = $request->input('vendor');
            $type = $request->input('type');
            // check validation traffic columns
            $dataArray = array(
                'fk_vendor_id' => $fkVendorId,
            );
            $verifyRecordResponse = VCModel::verifyDataOfSpecificType($dataArray, $type);
            if ($verifyRecordResponse['status'] == true && !empty($verifyRecordResponse['response'])) {
                $request->session()->put('fk_vendor_id', $fkVendorId);
                $responseData = array(
                    'success' => 'Data found.',
                    'type' => $type,
                    'data_array' => $verifyRecordResponse['response'],
                    'ajax_status' => true
                );
            } else {
                $responseData = array(
                    'error' => 'No Data found.',
                    'type' => '',
                    'data_array' => '',
                    'ajax_status' => false
                );
            }// End Condition -->  check type of delete record
        } else {
            $responseData = array('error' => $validator->errors()->all(), 'ajax_status' => false);
        } // End Condition validation passes
        return response()->json($responseData);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function moveStoredRecords(Request $request)
    {
        // Set Memory Limit And Execution Time
        setMemoryLimitAndExeTime();
        $validator = Validator::make($request->all(),
            [
                'vendor' => 'required',
                'type' => 'required',
            ]
        );
        $responseData = array();
        $errorMessage = array();
        $successMessage = array();
        if ($validator->passes()) {
            $fkVendorId = $request->input('vendor');
            $type = $request->input('type');
            // check validation traffic columns
            $dataArray = array(
                'fk_vendor_id' => $fkVendorId
            );
            $verifyRecordResponse = VCModel::moveDataOfSpecificType($dataArray, $type);

            if ($verifyRecordResponse['status'] == true) {
                $count = (isset($verifyRecordResponse['count'][0]->asin_count)) ? $verifyRecordResponse['count'][0]->asin_count : 0;
                if ($count == 0) {
                    $responseData = array(
                        'error' => 'No data found',
                        'ajax_status' => false
                    );
                } else {
                    $request->session()->put('fk_vendor_id', $fkVendorId);
                    $responseData = array(
                        'success' => 'Successfully Record Move into Main Table and count is : ' . $count,
                        'ajax_status' => true
                    );
                }
            } else {
                $responseData = array(
                    'error' => 'Record Not exist of selected report type',
                    'ajax_status' => false
                );
            }// End Condition -->  check type of delete record
        } else {
            $responseData = array('error' => $validator->errors()->all(), 'ajax_status' => false);
        } // End Condition validation passes
        return response()->json($responseData);
    }

    function storeAsin($data)
    {
        $storeAsins['fkAccountId'] = $data['fkAccountId'];
        $storeAsins['fkBatchId'] = $data['batchId'];
        $storeAsins['fkSellerConfigId'] = $data['fk_vendor_id'];
        $storeAsins['asin'] = (isset($data['asin']) && !empty($data['asin']) ? $data['asin'] : 'NA');
        $storeAsins['idType'] = 'ASIN';
        $storeAsins['productDetailsDownloaded'] = 0;
        $storeAsins['productCategoryDetailsDownloaded'] = 0;
        $storeAsins['source'] = 'VC';
        $storeAsins['createdAt'] = date('Y-m-d h:i:s');
        $storeAsins['updatedAt'] = date('Y-m-d h:i:s');

        return $storeAsins;
    }

    /**
     *  This function is used to gather Daily Sales Data
     * @param $data
     * @return array
     */
    private function dailySalesData($data)
    {

        $dbData = array();
        $dbData['asin'] = (isset($data['asin']) && !empty($data['asin']) ? $data['asin'] : 'NA');
        $dbData['product_title'] = (isset($data['product_title']) && !empty($data['product_title']) ? $data['product_title'] : 'NA');
        $dbData['category'] = (isset($data['category']) && !empty($data['category']) ? $data['category'] : 'NA');
        $dbData['strCategory'] = (isset($data['category']) && !empty($data['category']) ? getOnlyStringValCatetgory($data['category']) : 'NA');
        $dbData['fkCategoryId'] = 0;
        $dbData['subcategory'] = (isset($data['subcategory']) && !empty($data['subcategory']) ? $data['subcategory'] : 'NA');
        $dbData['shipped_cogs'] = (isset($data['shipped_cogs']) && !empty($data['shipped_cogs']) && strpos($data['shipped_cogs'], '—') === FALSE ? removeDollarCommaSpace($data['shipped_cogs']) : 0);
        $dbData['shipped_cogs_percentage_total'] = (isset($data['shipped_cogs_percentage_total']) && !empty($data['shipped_cogs_percentage_total']) ? checkPercentageValue($data['shipped_cogs_percentage_total']) : 0);
        $dbData['shipped_cogs_prior_period'] = (isset($data['shipped_cogs_prior_period']) && !empty($data['shipped_cogs_prior_period']) ? checkPercentageValue($data['shipped_cogs_prior_period']) : 0);
        $dbData['shipped_cogs_last_year'] = (isset($data['shipped_cogs_last_year']) && !empty($data['shipped_cogs_last_year']) ?
            checkPercentageValue($data['shipped_cogs_last_year']) : 0);
        $dbData['shipped_units'] = (isset($data['shipped_units']) && !empty($data['shipped_units']) ? removeDollarCommaSpace($data['shipped_units']) : 0);
        $dbData['shipped_units_percentage_total'] = (isset($data['shipped_units_percentage_total']) && !empty($data['shipped_units_percentage_total']) ? checkPercentageValue($data['shipped_units_percentage_total']) : 0);
        $dbData['shipped_units_prior_period'] = (isset($data['shipped_units_prior_period']) && !empty($data['shipped_units_prior_period']) ? checkPercentageValue($data['shipped_units_prior_period']) : 0);
        $dbData['shipped_units_last_year'] = (isset($data['shipped_units_last_year']) && !empty($data['shipped_units_last_year']) ? checkPercentageValue($data['shipped_units_last_year']) : 0);
        $dbData['units_percentage_total'] = (isset($data['units_percentage_total']) && !empty($data['units_percentage_total']) ? checkPercentageValue($data['units_percentage_total']) : 0);
        $dbData['customer_returns'] = (isset($data['customer_returns']) && !empty($data['customer_returns']) && strpos($data['customer_returns'], '—') === FALSE ? removeDollarCommaSpace($data['customer_returns']) : 0);
        $dbData['free_replacements'] = (isset($data['free_replacements']) && !empty($data['free_replacements']) && strpos($data['free_replacements'], '—') === FALSE ? removeDollarCommaSpace($data['free_replacements']) : 0);
        $dbData['average_sales_price'] = (isset($data['average_sales_price']) && !empty($data['average_sales_price']) && strpos($data['average_sales_price'], '—') === FALSE ? removeDollarCommaSpace($data['average_sales_price']) : 0);
        $dbData['average_sales_price_prior_period'] = (isset($data['average_sales_price_prior_period']) && !empty($data['average_sales_price_prior_period']) ? checkPercentageValue($data['average_sales_price_prior_period']) : 0);

        return $dbData;
    }

    private function dailyInventoryData($data)
    {
        $dbData['asin'] = (isset($data['asin']) && !empty($data['asin']) ? $data['asin'] : 'NA');
        $dbData['product_title'] = (isset($data['product_title']) && !empty($data['product_title']) ? $data['product_title'] : 'NA');
        $dbData['category'] = (isset($data['category']) && !empty($data['category']) ? $data['category'] : 'NA');
        $dbData['strCategory'] = (isset($data['category']) && !empty($data['category']) ? getOnlyStringValCatetgory($data['category']) : 'NA');
        $dbData['fkCategoryId'] = 0;
        $dbData['subcategory'] = (isset($data['subcategory']) && !empty($data['subcategory']) ? $data['subcategory'] : 'NA');
        $dbData['net_recieved'] = (isset($data['net_received']) && !empty($data['net_received']) && strpos($data['net_received'], '—') === FALSE ? removeDollarCommaSpace($data['net_received']) : 0);
        $dbData['net_revieved_units'] = (isset($data['net_received_units']) && !empty($data['net_received_units']) && strpos($data['net_received_units'], '—') === FALSE ? removeDollarCommaSpace($data['net_received_units']) : 0);
        $dbData['sell_through_rate'] = (isset($data['sellthrough_rate']) && !empty($data['sellthrough_rate']) && strpos($data['sellthrough_rate'], '—') === FALSE ? checkPercentageValue($data['sellthrough_rate']) : 0);
        $dbData['open_purchase_order_quantity'] = (isset($data['open_purchase_order_quantity']) && !empty($data['open_purchase_order_quantity']) && strpos($data['open_purchase_order_quantity'], '—') === FALSE ? removeDollarCommaSpace($data['open_purchase_order_quantity']) : 0);
        $dbData['sellable_on_hand_inventory'] = (isset($data['sellable_on_hand_inventory']) && !empty($data['sellable_on_hand_inventory']) && strpos($data['sellable_on_hand_inventory'], '—') === FALSE ? removeDollarCommaSpace($data['sellable_on_hand_inventory']) : 0);

        if (isset($data['sellable_on_hand_inventory_trailing_30day_average']) && !empty($data['sellable_on_hand_inventory_trailing_30day_average']) && strpos($data['sellable_on_hand_inventory_trailing_30day_average'], '—') === FALSE) {
            $dbData['sellable_on_hand_inventory_trailing_30_day_average'] = removeDollarCommaSpace($data['sellable_on_hand_inventory_trailing_30day_average']);
        } elseif (isset($data['sellable_onhand_inventory_trailing_30day_average']) && !empty($data['sellable_onhand_inventory_trailing_30day_average']) && strpos($data['sellable_onhand_inventory_trailing_30day_average'], '—') === FALSE) {
            $dbData['sellable_on_hand_inventory_trailing_30_day_average'] = removeDollarCommaSpace($data['sellable_onhand_inventory_trailing_30day_average']);
        } else {
            $dbData['sellable_on_hand_inventory_trailing_30_day_average'] = 0;
        }

        $dbData['sellable_on_hand_units'] = (isset($data['sellable_on_hand_units']) && !empty($data['sellable_on_hand_units']) && strpos($data['sellable_on_hand_units'], '—') === FALSE ? removeDollarCommaSpace($data['sellable_on_hand_units']) : 0);


        if (isset($data['unsellable_on_hand_inventory']) && !empty($data['unsellable_on_hand_inventory']) && strpos($data['unsellable_on_hand_inventory'], '—') === FALSE) {
            $dbData['unsellable_on_hand_inventory'] = removeDollarCommaSpace($data['unsellable_on_hand_inventory']);

        } elseif (isset($data['unsellable_onhand_inventory']) && !empty($data['unsellable_onhand_inventory']) && strpos($data['unsellable_onhand_inventory'], '—') === FALSE) {
            $dbData['unsellable_on_hand_inventory'] = removeDollarCommaSpace($data['unsellable_onhand_inventory']);

        } else {
            $dbData['unsellable_on_hand_inventory'] = 0;

        }

        if (isset($data['unsellable_on_hand_inventory_trailing_30day_average']) && !empty($data['unsellable_on_hand_inventory_trailing_30day_average']) && strpos($data['unsellable_on_hand_inventory_trailing_30day_average'], '—') === FALSE) {
            $dbData['unsellable_on_hand_inventory_trailing_30_day_average'] = removeDollarCommaSpace($data['unsellable_on_hand_inventory_trailing_30day_average']);

        } elseif (isset($data['unsellable_onhand_inventory_trailing_30day_average']) && !empty($data['unsellable_onhand_inventory_trailing_30day_average']) && strpos($data['unsellable_onhand_inventory_trailing_30day_average'], '—') === FALSE) {
            $dbData['unsellable_on_hand_inventory_trailing_30_day_average'] = removeDollarCommaSpace($data['unsellable_onhand_inventory_trailing_30day_average']);

        } else {
            $dbData['unsellable_on_hand_inventory_trailing_30_day_average'] = 0;

        }
        if (isset($data['unsellable_on_hand_units']) && !empty($data['unsellable_on_hand_units']) && strpos($data['unsellable_on_hand_units'], '—') === FALSE) {
            $dbData['unsellable_on_hand_units'] = removeDollarCommaSpace($data['unsellable_on_hand_units']);

        } elseif (isset($data['unsellable_onhand_units']) && !empty($data['unsellable_onhand_units']) && strpos($data['unsellable_onhand_units'], '—') === FALSE) {
            $dbData['unsellable_on_hand_units'] = removeDollarCommaSpace($data['unsellable_onhand_units']);

        } else {
            $dbData['unsellable_on_hand_units'] = 0;

        }
        $dbData['aged_90_days_sellable_inventory'] = (isset($data['aged_90+_days_sellable_inventory']) && !empty($data['aged_90+_days_sellable_inventory']) && strpos($data['aged_90+_days_sellable_inventory'], '—') === FALSE ? removeDollarCommaSpace($data['aged_90+_days_sellable_inventory']) : 0);
        $dbData['aged_90+_days_sellable_inventory_trailing_30_day_average'] = (isset($data['aged_90+_days_sellable_inventory_trailing_30day_average']) && !empty($data['aged_90+_days_sellable_inventory_trailing_30day_average']) && strpos($data['aged_90+_days_sellable_inventory_trailing_30day_average'], '—') === FALSE ? removeDollarCommaSpace($data['aged_90+_days_sellable_inventory_trailing_30day_average']) : 0);
        $dbData['aged_90_days_sellable_units'] = (isset($data['aged_90+_days_sellable_units']) && !empty($data['aged_90+_days_sellable_units']) && strpos($data['aged_90+_days_sellable_units'], '—') === FALSE ? removeDollarCommaSpace($data['aged_90+_days_sellable_units']) : 0);


        /* New ADded => 25/Nov/2019*/
        $dbData['unhealthy_inventory'] = (isset($data['unhealthy_inventory']) && !empty($data['unhealthy_inventory']) && strpos($data['unhealthy_inventory'], '—') === FALSE ? removeDollarCommaSpace($data['unhealthy_inventory']) : 0);
        $dbData['unhealthy_inventory_trailing_30day_average'] = (isset($data['unhealthy_inventory_trailing_30day_average']) && !empty($data['unhealthy_inventory_trailing_30day_average']) && strpos($data['unhealthy_inventory_trailing_30day_average'], '—') === FALSE ? removeDollarCommaSpace($data['unhealthy_inventory_trailing_30day_average']) : 0);
        $dbData['unhealthy_units'] = (isset($data['unhealthy_units']) && !empty($data['unhealthy_units']) && strpos($data['unhealthy_units'], '—') === FALSE ? removeDollarCommaSpace($data['unhealthy_units']) : 0);
        $dbData['replenishment_category'] = (isset($data['replenishment_category']) && !empty($data['replenishment_category']) ? $data['replenishment_category'] : 'NA');

        return $dbData;
    }

    private function trafficData($trafficData)
    {
        $trafficDataArrayOne = array();
        $storeDataArrayTwo = array();
        $trafficDataArrayThree = array();
        $trafficDataArrayFour = array();
        $storeDataArrayOne = array();
        $storeDataArrayTwo = array();
        $storeDataArrayThree = array();
        $storeDataArrayFour = array();

        $trafficDataArrayOne = $trafficData[0];
        if (!empty($trafficDataArrayOne)) {
            $ar1_data['change_glance_view_reported'] = (isset($trafficDataArrayOne['reported']) && !empty($trafficDataArrayOne['reported']) && strpos($trafficDataArrayOne['reported'], '—') === FALSE ? checkPercentageValue($trafficDataArrayOne['reported']) : 0);
            $ar1_data['change_glance_view_prior_period'] = (isset($trafficDataArrayOne['prior_period']) && !empty($trafficDataArrayOne['prior_period']) && strpos($trafficDataArrayOne['prior_period'], '—') === FALSE ? checkPercentageValue($trafficDataArrayOne['prior_period']) : 0);
            $ar1_data['change_glance_view_last_year'] = (isset($trafficDataArrayOne['last_year']) && !empty($trafficDataArrayOne['last_year']) && strpos($trafficDataArrayOne['last_year'], '—') === FALSE ? checkPercentageValue($trafficDataArrayOne['last_year']) : 0);

            array_push($storeDataArrayOne, $ar1_data);
        }

        $trafficDataArrayTwo = $trafficData[1];
        if (!empty($trafficDataArrayTwo)) {

            $ar2_data['change_conversion_reported'] = (isset($trafficDataArrayTwo['reported']) && !empty($trafficDataArrayTwo['reported']) && strpos($trafficDataArrayTwo['reported'], '—') === FALSE ? checkPercentageValue($trafficDataArrayTwo['reported']) : 0);
            $ar2_data['change_conversion_prior_period'] = (isset($trafficDataArrayTwo['prior_period']) && !empty($trafficDataArrayTwo['prior_period']) && strpos($trafficDataArrayTwo['prior_period'], '—') === FALSE ? checkPercentageValue($trafficDataArrayTwo['prior_period']) : 0);
            $ar2_data['change_conversion_last_year'] = (isset($trafficDataArrayTwo['last_year']) && !empty($trafficDataArrayTwo['last_year']) && strpos($trafficDataArrayTwo['last_year'], '—') === FALSE ? checkPercentageValue($trafficDataArrayTwo['last_year']) : 0);

            array_push($storeDataArrayTwo, $ar2_data);

        }
        $trafficDataArrayThree = $trafficData[2];
        if (!empty($trafficDataArrayThree)) {
            $arr3_data['change_unique_visitors_reported'] = (isset($trafficDataArrayThree['reported']) && !empty($trafficDataArrayThree['reported']) && strpos($trafficDataArrayThree['reported'], '—') === FALSE ? checkPercentageValue($trafficDataArrayThree['reported']) : 0);
            $arr3_data['change_unique_visitors_prior_period'] = (isset($trafficDataArrayThree['prior_period']) && !empty($trafficDataArrayThree['prior_period']) && strpos($trafficDataArrayThree['prior_period'], '—') === FALSE ? checkPercentageValue($trafficDataArrayThree['prior_period']) : 0);
            $arr3_data['change_unique_visitors_last_year'] = (isset($trafficDataArrayThree['last_year']) && !empty($trafficDataArrayThree['last_year']) && strpos($trafficDataArrayThree['last_year'], '—') === FALSE ? checkPercentageValue($trafficDataArrayThree['last_year']) : 0);

            array_push($storeDataArrayThree, $arr3_data);
        }

        $trafficDataArrayFour = $trafficData[3];
        if (!empty($trafficDataArrayFour)) {
            $arr4_data['fast_track_glance_view_reported'] = (isset($trafficDataArrayFour['reported']) && !empty($trafficDataArrayFour['reported']) && strpos($trafficDataArrayFour['reported'], '—') === FALSE ? checkPercentageValue($trafficDataArrayFour['reported']) : 0);
            $arr4_data['fast_track_glance_view_prior_period'] = (isset($trafficDataArrayFour['prior_period']) && !empty($trafficDataArrayFour['prior_period']) && strpos($trafficDataArrayFour['prior_period'], '—') === FALSE ? checkPercentageValue($trafficDataArrayFour['prior_period']) : 0);
            $arr4_data['fast_track_glance_view_last_year'] = (isset($trafficDataArrayFour['last_year']) && !empty($trafficDataArrayFour['last_year']) && strpos($trafficDataArrayTwo['last_year'], '—') === FALSE ? checkPercentageValue($trafficDataArrayFour['last_year']) : 0);

            array_push($storeDataArrayFour, $arr4_data);
        }

        $trafficData = $storeDataArrayOne[0] + $storeDataArrayTwo[0] + $storeDataArrayThree[0] + $storeDataArrayFour[0];

        unset($storeDataArrayOne);
        unset($storeDataArrayTwo);
        unset($storeDataArrayThree);
        unset($storeDataArrayFour);
        unset($trafficDataArrayOne);
        unset($storeDataArrayTwo);
        unset($trafficDataArrayThree);
        unset($trafficDataArrayFour);

        return $trafficData;
    }

    /**
     * @param $trafficData
     * @param $fkVendorId
     * @param $batchId
     * @param $accountId
     * @param $trafficStartDate
     * @param $trafficEndDate
     * @return mixed
     */
    private function trafficDataUpdated($trafficData, $fkVendorId, $batchId, $accountId, $trafficStartDate, $trafficEndDate)
    {
        $returnTrafficData = array();
        foreach ($trafficData as $row) {
            $dbArray = array();
            $dbArray['asin'] = (isset($row['asin']) && !empty($row['asin'])) ? $row['asin'] : 'NA';
            $dbArray['product_title'] = (isset($row['product_title']) && !empty($row['product_title'])) ? $row['product_title'] : 'NA';
            $dbArray['subcategory'] = (isset($row['subcategory']) && !empty($row['subcategory'])) ? $row['subcategory'] : 'NA';
            $dbArray['category'] = (isset($row['category']) && !empty($row['category'])) ? $row['category'] : 'NA';
            $dbArray['percentage_total_gvs'] = (isset($row['_percentage_total_gvs']) && !empty($row['_percentage_total_gvs'])) ? checkPercentageValue($row['_percentage_total_gvs']) : 0.00;
            $dbArray['change_glance_view_prior_period'] = (isset($row['change_in_glance_view_prior_period']) && !empty($row['change_in_glance_view_prior_period'])) ? checkPercentageValue($row['change_in_glance_view_prior_period']) : 0.00;
            $dbArray['change_glance_view_last_year'] = (isset($row['change_in_gv_last_year']) && !empty($row['change_in_gv_last_year'])) ? checkPercentageValue($row['change_in_gv_last_year']) : 0.00;
            $dbArray['change_unique_visitors_prior_period'] = (isset($row['unique_visitors_prior_period']) && !empty($row['unique_visitors_prior_period'])) ? checkPercentageValue($row['unique_visitors_prior_period']) : 0.00;
            $dbArray['change_unique_visitors_last_year'] = (isset($row['unique_visitors_last_year']) && !empty($row['unique_visitors_last_year'])) ? checkPercentageValue($row['unique_visitors_last_year']) : 0.00;
            $dbArray['conversion_percentile'] = (isset($row['conversion_percentile']) && !empty($row['conversion_percentile'])) ? checkPercentageValue($row['conversion_percentile']) : 0.00;
            $dbArray['change_conversion_prior_period'] = (isset($row['change_in_conversion_prior_period']) && !empty($row['change_in_conversion_prior_period'])) ? checkPercentageValue($row['change_in_conversion_prior_period']) : 0.00;
            $dbArray['change_conversion_last_year'] = (isset($row['change_in_conversion_last_year']) && !empty($row['change_in_conversion_last_year'])) ? checkPercentageValue($row['change_in_conversion_last_year']) : 0.00;
            $dbArray['fast_track_glance_view'] = (isset($row['fast_track_glance_view']) && !empty($row['fast_track_glance_view'])) ? checkPercentageValue($row['fast_track_glance_view']) : 0.00;
            $dbArray['fast_track_glance_view_prior_period'] = (isset($row['fast_track_glance_view_prior_period']) && !empty($row['fast_track_glance_view_prior_period'])) ? checkPercentageValue($row['fast_track_glance_view_prior_period']) : 0.00;
            $dbArray['fast_track_glance_view_last_year'] = (isset($row['fast_track_glance_view_last_year']) && !empty($row['fast_track_glance_view_last_year'])) ? checkPercentageValue($row['fast_track_glance_view_last_year']) : 0.00;
            $dbArray['change_conversion_reported'] = 0.00;
            $dbArray['change_unique_visitors_reported'] = 0.00;
            $dbArray['change_glance_view_reported'] = 0.00;
            $dbArray['fast_track_glance_view_reported'] = 0.00;
            $dbArray['fk_vendor_id'] = $fkVendorId;
            $dbArray['batchId'] = $batchId;
            $dbArray['fkAccountId'] = $accountId;
            $dbArray['start_date'] = dateConversion($trafficStartDate);
            $dbArray['end_date'] = dateConversion($trafficEndDate);
            $dbArray['capture_date'] = date('Y-m-d');
            $dbArray['created_at'] = date('Y-m-d h:i:s');
            $dbArray['updated_at'] = date('Y-m-d h:i:s');
            array_push($returnTrafficData, $dbArray);
        }
        return $returnTrafficData;
    }

    private function forecastData($data)
    {
        $dbData['asin'] = (isset($data['asin']) && !empty($data['asin']) ? $data['asin'] : 'NA');
        $dbData['product_title'] = (isset($data['product_title']) && !empty($data['product_title']) ? $data['product_title'] : 'NA');
        $dbData['subcategory'] = (isset($data['subcategory']) && !empty($data['subcategory']) ? $data['subcategory'] : 'NA');
        $dbData['category'] = (isset($data['category']) && !empty($data['category']) ? $data['category'] : 'NA');
        $dbData['strCategory'] = (isset($data['category']) && !empty($data['category']) ? getOnlyStringValCatetgory($data['category']) : 'NA');
        $dbData['fkCategoryId'] = 0;
        $dbData['rep_oos'] = (isset($data['rep_oos']) && !empty($data['rep_oos']) ? checkPercentageValue($data['rep_oos']) : 0);
        $dbData['rep_oos_percentage_total'] = (isset($data['rep_oos_percentage_total']) && !empty($data['rep_oos_percentage_total']) ? checkPercentageValue($data['rep_oos_percentage_total']) : 0);
        $dbData['rep_oos_prior_period'] = (isset($data['rep_oos_prior_period']) && !empty($data['rep_oos_prior_period']) ? checkPercentageValue($data['rep_oos_prior_period']) : 0);
        $dbData['shipped_units'] = (isset($data['shipped_units']) && !empty($data['shipped_units']) && strpos($data['shipped_units'], '—') === FALSE ? removeDollarCommaSpace($data['shipped_units']) : 0);
        $dbData['shipped_units_prior_period'] = (isset($data['shipped_units_prior_period']) && !empty($data['shipped_units_prior_period']) ? checkPercentageValue($data['shipped_units_prior_period']) : 0);
        if (isset($data['unfilled_customer_ordered_units']) && !empty($data['unfilled_customer_ordered_units']) && strpos($data['unfilled_customer_ordered_units'], '—') === FALSE) {
            $dbData['unfilled_customer_ordered_units'] = removeDollarCommaSpace($data['unfilled_customer_ordered_units']);
        } elseif (isset($data['unfilled_customerordered_units']) && !empty($data['unfilled_customerordered_units']) && strpos($data['unfilled_customerordered_units'], '—') === FALSE) {
            $dbData['unfilled_customer_ordered_units'] = removeDollarCommaSpace($data['unfilled_customerordered_units']);
        } else {
            $dbData['unfilled_customer_ordered_units'] = 0;
        }

        $dbData['available_inventory'] = (isset($data['available_inventory']) && !empty($data['available_inventory']) && strpos($data['available_inventory'], '—') === FALSE ? removeDollarCommaSpace($data['available_inventory']) : 0);
        $dbData['available_inventory_prior_period'] = (isset($data['available_inventory_prior_period']) && !empty($data['available_inventory_prior_period']) ? checkPercentageValue($data['available_inventory_prior_period']) : 0);
        $dbData['weeks_on_hand'] = (isset($data['weeks_on_hand']) && !empty($data['weeks_on_hand']) && strpos($data['weeks_on_hand'], '—') === FALSE ? removeDollarCommaSpace($data['weeks_on_hand']) : 0);
        $dbData['open_purchase_order_quantity'] = (isset($data['open_purchase_order_quantity']) && !empty($data['open_purchase_order_quantity']) && strpos($data['open_purchase_order_quantity'], '—') === FALSE ? removeDollarCommaSpace($data['open_purchase_order_quantity']) : 0);
        $dbData['open_purchase_order_quantity_prior_period'] = (isset($data['open_purchase_order_quantity_prior_period']) && !empty($data['open_purchase_order_quantity_prior_period']) ? checkPercentageValue($data['open_purchase_order_quantity_prior_period']) : 0);
        $dbData['receive_fill_rate'] = (isset($data['receive_fill_rate_']) && !empty($data['receive_fill_rate_']) ? checkPercentageValue($data['receive_fill_rate_']) : 0);
        $dbData['overall_vendor_lead_time_days'] = (isset($data['overall_vendor_lead_time_days']) && !empty($data['overall_vendor_lead_time_days']) && strpos($data['overall_vendor_lead_time_days'], '—') === FALSE ? removeDollarCommaSpace($data['overall_vendor_lead_time_days']) : 0);
        $dbData['replenishment_category'] = (isset($data['replenishment_category']) && !empty($data['replenishment_category']) & strpos($data['replenishment_category'], '—') === FALSE ? $data['replenishment_category'] : 'NA');
        $dbData['week_1_mean_forecast'] = (isset($data['week_1_mean_forecast']) && !empty($data['week_1_mean_forecast']) ? removeDollarCommaSpace($data['week_1_mean_forecast']) : 0);
        $dbData['week_2_mean_forecast'] = (isset($data['week_2_mean_forecast']) && !empty($data['week_2_mean_forecast']) ? removeDollarCommaSpace($data['week_2_mean_forecast']) : 0);
        $dbData['week_3_mean_forecast'] = (isset($data['week_3_mean_forecast']) && !empty($data['week_3_mean_forecast']) ? removeDollarCommaSpace($data['week_3_mean_forecast']) : 0);
        $dbData['week_4_mean_forecast'] = (isset($data['week_4_mean_forecast']) && !empty($data['week_4_mean_forecast']) ? removeDollarCommaSpace($data['week_4_mean_forecast']) : 0);
        $dbData['week_5_mean_forecast'] = (isset($data['week_5_mean_forecast']) && !empty($data['week_5_mean_forecast']) ? removeDollarCommaSpace($data['week_5_mean_forecast']) : 0);
        $dbData['week_6_mean_forecast'] = (isset($data['week_6_mean_forecast']) && !empty($data['week_6_mean_forecast']) ? removeDollarCommaSpace($data['week_6_mean_forecast']) : 0);
        $dbData['week_7_mean_forecast'] = (isset($data['week_7_mean_forecast']) && !empty($data['week_7_mean_forecast']) ? removeDollarCommaSpace($data['week_7_mean_forecast']) : 0);
        $dbData['week_8_mean_forecast'] = (isset($data['week_8_mean_forecast']) && !empty($data['week_8_mean_forecast']) ? removeDollarCommaSpace($data['week_8_mean_forecast']) : 0);
        $dbData['week_9_mean_forecast'] = (isset($data['week_9_mean_forecast']) && !empty($data['week_9_mean_forecast']) ? removeDollarCommaSpace($data['week_9_mean_forecast']) : 0);
        $dbData['week_10_mean_forecast'] = (isset($data['week_10_mean_forecast']) && !empty($data['week_10_mean_forecast']) ? removeDollarCommaSpace($data['week_10_mean_forecast']) : 0);
        $dbData['week_11_mean_forecast'] = (isset($data['week_11_mean_forecast']) && !empty($data['week_11_mean_forecast']) ? removeDollarCommaSpace($data['week_11_mean_forecast']) : 0);
        $dbData['week_12_mean_forecast'] = (isset($data['week_12_mean_forecast']) && !empty($data['week_12_mean_forecast']) ? removeDollarCommaSpace($data['week_12_mean_forecast']) : 0);
        $dbData['week_13_mean_forecast'] = (isset($data['week_13_mean_forecast']) && !empty($data['week_13_mean_forecast']) ? removeDollarCommaSpace($data['week_13_mean_forecast']) : 0);
        $dbData['week_14_mean_forecast'] = (isset($data['week_14_mean_forecast']) && !empty($data['week_14_mean_forecast']) ? removeDollarCommaSpace($data['week_14_mean_forecast']) : 0);
        $dbData['week_15_mean_forecast'] = (isset($data['week_15_mean_forecast']) && !empty($data['week_15_mean_forecast']) ? removeDollarCommaSpace($data['week_15_mean_forecast']) : 0);
        $dbData['week_16_mean_forecast'] = (isset($data['week_16_mean_forecast']) && !empty($data['week_16_mean_forecast']) ? removeDollarCommaSpace($data['week_16_mean_forecast']) : 0);
        $dbData['week_17_mean_forecast'] = (isset($data['week_17_mean_forecast']) && !empty($data['week_17_mean_forecast']) ? removeDollarCommaSpace($data['week_17_mean_forecast']) : 0);
        $dbData['week_18_mean_forecast'] = (isset($data['week_18_mean_forecast']) && !empty($data['week_18_mean_forecast']) ? removeDollarCommaSpace($data['week_18_mean_forecast']) : 0);
        $dbData['week_19_mean_forecast'] = (isset($data['week_19_mean_forecast']) && !empty($data['week_19_mean_forecast']) ? removeDollarCommaSpace($data['week_19_mean_forecast']) : 0);
        $dbData['week_20_mean_forecast'] = (isset($data['week_20_mean_forecast']) && !empty($data['week_20_mean_forecast']) ? removeDollarCommaSpace($data['week_20_mean_forecast']) : 0);
        $dbData['week_21_mean_forecast'] = (isset($data['week_21_mean_forecast']) && !empty($data['week_21_mean_forecast']) ? removeDollarCommaSpace($data['week_21_mean_forecast']) : 0);
        $dbData['week_22_mean_forecast'] = (isset($data['week_22_mean_forecast']) && !empty($data['week_22_mean_forecast']) ? removeDollarCommaSpace($data['week_22_mean_forecast']) : 0);
        $dbData['week_23_mean_forecast'] = (isset($data['week_23_mean_forecast']) && !empty($data['week_23_mean_forecast']) ? removeDollarCommaSpace($data['week_23_mean_forecast']) : 0);
        $dbData['week_24_mean_forecast'] = (isset($data['week_24_mean_forecast']) && !empty($data['week_24_mean_forecast']) ? removeDollarCommaSpace($data['week_24_mean_forecast']) : 0);
        $dbData['week_25_mean_forecast'] = (isset($data['week_25_mean_forecast']) && !empty($data['week_25_mean_forecast']) ? removeDollarCommaSpace($data['week_25_mean_forecast']) : 0);
        $dbData['week_26_mean_forecast'] = (isset($data['week_26_mean_forecast']) && !empty($data['week_26_mean_forecast']) ? removeDollarCommaSpace($data['week_26_mean_forecast']) : 0);
        $dbData['week_1_p70_forecast'] = (isset($data['week_1_p70_forecast']) && !empty($data['week_1_p70_forecast']) ? removeDollarCommaSpace($data['week_1_p70_forecast']) : 0);
        $dbData['week_2_p70_forecast'] = (isset($data['week_2_p70_forecast']) && !empty($data['week_2_p70_forecast']) ? removeDollarCommaSpace($data['week_2_p70_forecast']) : 0);
        $dbData['week_3_p70_forecast'] = (isset($data['week_3_p70_forecast']) && !empty($data['week_3_p70_forecast']) ? removeDollarCommaSpace($data['week_3_p70_forecast']) : 0);
        $dbData['week_4_p70_forecast'] = (isset($data['week_4_p70_forecast']) && !empty($data['week_4_p70_forecast']) ? removeDollarCommaSpace($data['week_4_p70_forecast']) : 0);
        $dbData['week_5_p70_forecast'] = (isset($data['week_5_p70_forecast']) && !empty($data['week_5_p70_forecast']) ? removeDollarCommaSpace($data['week_5_p70_forecast']) : 0);
        $dbData['week_6_p70_forecast'] = (isset($data['week_6_p70_forecast']) && !empty($data['week_6_p70_forecast']) ? removeDollarCommaSpace($data['week_6_p70_forecast']) : 0);
        $dbData['week_7_p70_forecast'] = (isset($data['week_7_p70_forecast']) && !empty($data['week_7_p70_forecast']) ? removeDollarCommaSpace($data['week_7_p70_forecast']) : 0);
        $dbData['week_8_p70_forecast'] = (isset($data['week_8_p70_forecast']) && !empty($data['week_8_p70_forecast']) ? removeDollarCommaSpace($data['week_8_p70_forecast']) : 0);
        $dbData['week_9_p70_forecast'] = (isset($data['week_9_p70_forecast']) && !empty($data['week_9_p70_forecast']) ? removeDollarCommaSpace($data['week_9_p70_forecast']) : 0);
        $dbData['week_10_p70_forecast'] = (isset($data['week_10_p70_forecast']) && !empty($data['week_10_p70_forecast']) ? removeDollarCommaSpace($data['week_10_p70_forecast']) : 0);
        $dbData['week_11_p70_forecast'] = (isset($data['week_11_p70_forecast']) && !empty($data['week_11_p70_forecast']) ? removeDollarCommaSpace($data['week_11_p70_forecast']) : 0);
        $dbData['week_12_p70_forecast'] = (isset($data['week_12_p70_forecast']) && !empty($data['week_12_p70_forecast']) ? removeDollarCommaSpace($data['week_12_p70_forecast']) : 0);
        $dbData['week_13_p70_forecast'] = (isset($data['week_13_p70_forecast']) && !empty($data['week_13_p70_forecast']) ? removeDollarCommaSpace($data['week_13_p70_forecast']) : 0);
        $dbData['week_14_p70_forecast'] = (isset($data['week_14_p70_forecast']) && !empty($data['week_14_p70_forecast']) ? removeDollarCommaSpace($data['week_14_p70_forecast']) : 0);
        $dbData['week_15_p70_forecast'] = (isset($data['week_15_p70_forecast']) && !empty($data['week_15_p70_forecast']) ? removeDollarCommaSpace($data['week_15_p70_forecast']) : 0);
        $dbData['week_16_p70_forecast'] = (isset($data['week_16_p70_forecast']) && !empty($data['week_16_p70_forecast']) ? removeDollarCommaSpace($data['week_16_p70_forecast']) : 0);
        $dbData['week_17_p70_forecast'] = (isset($data['week_17_p70_forecast']) && !empty($data['week_17_p70_forecast']) ? removeDollarCommaSpace($data['week_17_p70_forecast']) : 0);
        $dbData['week_18_p70_forecast'] = (isset($data['week_18_p70_forecast']) && !empty($data['week_18_p70_forecast']) ? removeDollarCommaSpace($data['week_18_p70_forecast']) : 0);
        $dbData['week_19_p70_forecast'] = (isset($data['week_19_p70_forecast']) && !empty($data['week_19_p70_forecast']) ? removeDollarCommaSpace($data['week_19_p70_forecast']) : 0);
        $dbData['week_20_p70_forecast'] = (isset($data['week_20_p70_forecast']) && !empty($data['week_20_p70_forecast']) ? removeDollarCommaSpace($data['week_20_p70_forecast']) : 0);
        $dbData['week_21_p70_forecast'] = (isset($data['week_21_p70_forecast']) && !empty($data['week_21_p70_forecast']) ? removeDollarCommaSpace($data['week_21_p70_forecast']) : 0);
        $dbData['week_22_p70_forecast'] = (isset($data['week_22_p70_forecast']) && !empty($data['week_22_p70_forecast']) ? removeDollarCommaSpace($data['week_22_p70_forecast']) : 0);
        $dbData['week_23_p70_forecast'] = (isset($data['week_23_p70_forecast']) && !empty($data['week_23_p70_forecast']) ? removeDollarCommaSpace($data['week_23_p70_forecast']) : 0);
        $dbData['week_24_p70_forecast'] = (isset($data['week_24_p70_forecast']) && !empty($data['week_24_p70_forecast']) ? removeDollarCommaSpace($data['week_24_p70_forecast']) : 0);
        $dbData['week_25_p70_forecast'] = (isset($data['week_25_p70_forecast']) && !empty($data['week_25_p70_forecast']) ? removeDollarCommaSpace($data['week_25_p70_forecast']) : 0);
        $dbData['week_26_p70_forecast'] = (isset($data['week_26_p70_forecast']) && !empty($data['week_26_p70_forecast']) ? removeDollarCommaSpace($data['week_26_p70_forecast']) : 0);
        $dbData['week_1_p80_forecast'] = (isset($data['week_1_p80_forecast']) && !empty($data['week_1_p80_forecast']) ? removeDollarCommaSpace($data['week_1_p80_forecast']) : 0);
        $dbData['week_2_p80_forecast'] = (isset($data['week_2_p80_forecast']) && !empty($data['week_2_p80_forecast']) ? removeDollarCommaSpace($data['week_2_p80_forecast']) : 0);
        $dbData['week_3_p80_forecast'] = (isset($data['week_3_p80_forecast']) && !empty($data['week_3_p80_forecast']) ? removeDollarCommaSpace($data['week_3_p80_forecast']) : 0);
        $dbData['week_4_p80_forecast'] = (isset($data['week_4_p80_forecast']) && !empty($data['week_4_p80_forecast']) ? removeDollarCommaSpace($data['week_4_p80_forecast']) : 0);
        $dbData['week_5_p80_forecast'] = (isset($data['week_5_p80_forecast']) && !empty($data['week_5_p80_forecast']) ? removeDollarCommaSpace($data['week_5_p80_forecast']) : 0);
        $dbData['week_6_p80_forecast'] = (isset($data['week_6_p80_forecast']) && !empty($data['week_6_p80_forecast']) ? removeDollarCommaSpace($data['week_6_p80_forecast']) : 0);
        $dbData['week_7_p80_forecast'] = (isset($data['week_7_p80_forecast']) && !empty($data['week_7_p80_forecast']) ? removeDollarCommaSpace($data['week_7_p80_forecast']) : 0);
        $dbData['week_8_p80_forecast'] = (isset($data['week_8_p80_forecast']) && !empty($data['week_8_p80_forecast']) ? removeDollarCommaSpace($data['week_8_p80_forecast']) : 0);
        $dbData['week_9_p80_forecast'] = (isset($data['week_9_p80_forecast']) && !empty($data['week_9_p80_forecast']) ? removeDollarCommaSpace($data['week_9_p80_forecast']) : 0);
        $dbData['week_10_p80_forecast'] = (isset($data['week_10_p80_forecast']) && !empty($data['week_10_p80_forecast']) ? removeDollarCommaSpace($data['week_10_p80_forecast']) : 0);
        $dbData['week_11_p80_forecast'] = (isset($data['week_11_p80_forecast']) && !empty($data['week_11_p80_forecast']) ? removeDollarCommaSpace($data['week_11_p80_forecast']) : 0);
        $dbData['week_12_p80_forecast'] = (isset($data['week_12_p80_forecast']) && !empty($data['week_12_p80_forecast']) ? removeDollarCommaSpace($data['week_12_p80_forecast']) : 0);
        $dbData['week_13_p80_forecast'] = (isset($data['week_13_p80_forecast']) && !empty($data['week_13_p80_forecast']) ? removeDollarCommaSpace($data['week_13_p80_forecast']) : 0);
        $dbData['week_14_p80_forecast'] = (isset($data['week_14_p80_forecast']) && !empty($data['week_14_p80_forecast']) ? removeDollarCommaSpace($data['week_14_p80_forecast']) : 0);
        $dbData['week_15_p80_forecast'] = (isset($data['week_15_p80_forecast']) && !empty($data['week_15_p80_forecast']) ? removeDollarCommaSpace($data['week_15_p80_forecast']) : 0);
        $dbData['week_16_p80_forecast'] = (isset($data['week_16_p80_forecast']) && !empty($data['week_16_p80_forecast']) ? removeDollarCommaSpace($data['week_16_p80_forecast']) : 0);
        $dbData['week_17_p80_forecast'] = (isset($data['week_17_p80_forecast']) && !empty($data['week_17_p80_forecast']) ? removeDollarCommaSpace($data['week_17_p80_forecast']) : 0);
        $dbData['week_18_p80_forecast'] = (isset($data['week_18_p80_forecast']) && !empty($data['week_18_p80_forecast']) ? removeDollarCommaSpace($data['week_18_p80_forecast']) : 0);
        $dbData['week_19_p80_forecast'] = (isset($data['week_19_p80_forecast']) && !empty($data['week_19_p80_forecast']) ? removeDollarCommaSpace($data['week_19_p80_forecast']) : 0);
        $dbData['week_20_p80_forecast'] = (isset($data['week_20_p80_forecast']) && !empty($data['week_20_p80_forecast']) ? removeDollarCommaSpace($data['week_20_p80_forecast']) : 0);
        $dbData['week_21_p80_forecast'] = (isset($data['week_21_p80_forecast']) && !empty($data['week_21_p80_forecast']) ? removeDollarCommaSpace($data['week_21_p80_forecast']) : 0);
        $dbData['week_22_p80_forecast'] = (isset($data['week_22_p80_forecast']) && !empty($data['week_22_p80_forecast']) ? removeDollarCommaSpace($data['week_22_p80_forecast']) : 0);
        $dbData['week_23_p80_forecast'] = (isset($data['week_23_p80_forecast']) && !empty($data['week_23_p80_forecast']) ? removeDollarCommaSpace($data['week_23_p80_forecast']) : 0);
        $dbData['week_24_p80_forecast'] = (isset($data['week_24_p80_forecast']) && !empty($data['week_24_p80_forecast']) ? removeDollarCommaSpace($data['week_24_p80_forecast']) : 0);
        $dbData['week_25_p80_forecast'] = (isset($data['week_25_p80_forecast']) && !empty($data['week_25_p80_forecast']) ? removeDollarCommaSpace($data['week_25_p80_forecast']) : 0);
        $dbData['week_26_p80_forecast'] = (isset($data['week_26_p80_forecast']) && !empty($data['week_26_p80_forecast']) ? removeDollarCommaSpace($data['week_26_p80_forecast']) : 0);
        $dbData['week_1_p90_forecast'] = (isset($data['week_1_p90_forecast']) && !empty($data['week_1_p90_forecast']) ? removeDollarCommaSpace($data['week_1_p90_forecast']) : 0);
        $dbData['week_2_p90_forecast'] = (isset($data['week_2_p90_forecast']) && !empty($data['week_2_p90_forecast']) ? removeDollarCommaSpace($data['week_2_p90_forecast']) : 0);
        $dbData['week_3_p90_forecast'] = (isset($data['week_3_p90_forecast']) && !empty($data['week_3_p90_forecast']) ? removeDollarCommaSpace($data['week_3_p90_forecast']) : 0);
        $dbData['week_4_p90_forecast'] = (isset($data['week_4_p90_forecast']) && !empty($data['week_4_p90_forecast']) ? removeDollarCommaSpace($data['week_4_p90_forecast']) : 0);
        $dbData['week_5_p90_forecast'] = (isset($data['week_5_p90_forecast']) && !empty($data['week_5_p90_forecast']) ? removeDollarCommaSpace($data['week_5_p90_forecast']) : 0);
        $dbData['week_6_p90_forecast'] = (isset($data['week_6_p90_forecast']) && !empty($data['week_6_p90_forecast']) ? removeDollarCommaSpace($data['week_6_p90_forecast']) : 0);
        $dbData['week_7_p90_forecast'] = (isset($data['week_7_p90_forecast']) && !empty($data['week_7_p90_forecast']) ? removeDollarCommaSpace($data['week_7_p90_forecast']) : 0);
        $dbData['week_8_p90_forecast'] = (isset($data['week_8_p90_forecast']) && !empty($data['week_8_p90_forecast']) ? removeDollarCommaSpace($data['week_8_p90_forecast']) : 0);
        $dbData['week_9_p90_forecast'] = (isset($data['week_9_p90_forecast']) && !empty($data['week_9_p90_forecast']) ? removeDollarCommaSpace($data['week_9_p90_forecast']) : 0);
        $dbData['week_10_p90_forecast'] = (isset($data['week_10_p90_forecast']) && !empty($data['week_10_p90_forecast']) ? removeDollarCommaSpace($data['week_10_p90_forecast']) : 0);
        $dbData['week_11_p90_forecast'] = (isset($data['week_11_p90_forecast']) && !empty($data['week_11_p90_forecast']) ? removeDollarCommaSpace($data['week_11_p90_forecast']) : 0);
        $dbData['week_12_p90_forecast'] = (isset($data['week_12_p90_forecast']) && !empty($data['week_12_p90_forecast']) ? removeDollarCommaSpace($data['week_12_p90_forecast']) : 0);
        $dbData['week_13_p90_forecast'] = (isset($data['week_13_p90_forecast']) && !empty($data['week_13_p90_forecast']) ? removeDollarCommaSpace($data['week_13_p90_forecast']) : 0);
        $dbData['week_14_p90_forecast'] = (isset($data['week_14_p90_forecast']) && !empty($data['week_14_p90_forecast']) ? removeDollarCommaSpace($data['week_14_p90_forecast']) : 0);
        $dbData['week_15_p90_forecast'] = (isset($data['week_15_p90_forecast']) && !empty($data['week_15_p90_forecast']) ? removeDollarCommaSpace($data['week_15_p90_forecast']) : 0);
        $dbData['week_16_p90_forecast'] = (isset($data['week_16_p90_forecast']) && !empty($data['week_16_p90_forecast']) ? removeDollarCommaSpace($data['week_16_p90_forecast']) : 0);
        $dbData['week_17_p90_forecast'] = (isset($data['week_17_p90_forecast']) && !empty($data['week_17_p90_forecast']) ? removeDollarCommaSpace($data['week_17_p90_forecast']) : 0);
        $dbData['week_18_p90_forecast'] = (isset($data['week_18_p90_forecast']) && !empty($data['week_18_p90_forecast']) ? removeDollarCommaSpace($data['week_18_p90_forecast']) : 0);
        $dbData['week_19_p90_forecast'] = (isset($data['week_19_p90_forecast']) && !empty($data['week_19_p90_forecast']) ? removeDollarCommaSpace($data['week_19_p90_forecast']) : 0);
        $dbData['week_20_p90_forecast'] = (isset($data['week_20_p90_forecast']) && !empty($data['week_20_p90_forecast']) ? removeDollarCommaSpace($data['week_20_p90_forecast']) : 0);
        $dbData['week_21_p90_forecast'] = (isset($data['week_21_p90_forecast']) && !empty($data['week_21_p90_forecast']) ? removeDollarCommaSpace($data['week_21_p90_forecast']) : 0);
        $dbData['week_22_p90_forecast'] = (isset($data['week_22_p90_forecast']) && !empty($data['week_22_p90_forecast']) ? removeDollarCommaSpace($data['week_22_p90_forecast']) : 0);
        $dbData['week_23_p90_forecast'] = (isset($data['week_23_p90_forecast']) && !empty($data['week_23_p90_forecast']) ? removeDollarCommaSpace($data['week_23_p90_forecast']) : 0);
        $dbData['week_24_p90_forecast'] = (isset($data['week_24_p90_forecast']) && !empty($data['week_24_p90_forecast']) ? removeDollarCommaSpace($data['week_24_p90_forecast']) : 0);
        $dbData['week_25_p90_forecast'] = (isset($data['week_25_p90_forecast']) && !empty($data['week_25_p90_forecast']) ? removeDollarCommaSpace($data['week_25_p90_forecast']) : 0);
        $dbData['week_26_p90_forecast'] = (isset($data['week_26_p90_forecast']) && !empty($data['week_26_p90_forecast']) ? removeDollarCommaSpace($data['week_26_p90_forecast']) : 0);

        return $dbData;

    }

    private function productCatalogData($data)
    {
        $dbData['asin'] = (isset($data['asin']) && !empty($data['asin']) ? $data['asin'] : 'NA');
        $dbData['product_title'] = (isset($data['product_title']) && !empty($data['product_title']) ? $data['product_title'] : 'NA');
        $dbData['parent_asin'] = (isset($data['parent_asin']) && !empty($data['parent_asin']) ? $data['parent_asin'] : 'NA');
        $dbData['isbn13'] = (isset($data['isbn13']) && !empty($data['isbn13']) && strpos($data['isbn13'], '—') === FALSE ? $data['isbn13'] : 'NA');
        $dbData['ean'] = (isset($data['ean']) && !empty($data['ean']) ? $data['ean'] : 'NA');
        $dbData['upc'] = (isset($data['upc']) && !empty($data['upc']) ? $data['upc'] : 'NA');
        if (isset($data['release_date']) && !empty($data['release_date'])) {
            $dbData['release_date'] = dateConversion($data['release_date']);
        } elseif (isset($data['release_date_']) && !empty($data['release_date_'])) {
            $dbData['release_date'] = dateConversion($data['release_date_']);
        } elseif (isset($data['release_date_x000d_']) && !empty($data['release_date_x000d_'])) {
            $dbData['release_date'] = dateConversion($data['release_date_x000d_']);
        } else {
            $dbData['release_date'] = "NA";
        }

        $dbData['binding'] = (isset($data['binding']) && !empty($data['binding']) ? $data['binding'] : 'NA');
        $dbData['list_price'] = (isset($data['list_price']) && !empty($data['list_price']) && strpos($data['list_price'], '—') === FALSE ? removeDollarCommaSpace($data['list_price']) : 0);
        $dbData['author_artist'] = (isset($data['author_/_artist']) && !empty($data['author_/_artist']) ? $data['author_/_artist'] : 'NA');
        if (isset($data['sitbenabled']) && !empty($data['sitbenabled'])) {
            $dbData['sitbenabled'] = $data['sitbenabled'];
        } elseif (isset($data['sitb_enabled']) && !empty($data['sitb_enabled'])) {
            $dbData['sitbenabled'] = $data['sitb_enabled'];
        } else {
            $dbData['sitbenabled'] = "NA";
        }

        $dbData['apparel_size'] = (isset($data['apparel_size']) && !empty($data['apparel_size']) ? $data['apparel_size'] : 'NA');
        $dbData['apparel_size_width'] = (isset($data['apparel_size_width']) && !empty($data['apparel_size_width']) ? $data['apparel_size_width'] : 'NA');
        $dbData['product_group'] = (isset($data['product_group']) && !empty($data['product_group']) ? $data['product_group'] : 'NA');
        $dbData['replenishment_code'] = (isset($data['replenishment_code']) && !empty($data['replenishment_code']) && strpos($data['replenishment_code'], '—') === FALSE ? $data['replenishment_code'] : 'NA');
        $dbData['model_style_number'] = (isset($data['model_/_style_number']) && !empty($data['model_/_style_number']) ? $data['model_/_style_number'] : 'NA');
        if (isset($data['colour_']) && !empty($data['colour_'])) {
            $dbData['colour'] = $data['colour_'];

        } elseif (isset($data['color']) && !empty($data['color'])) {
            $dbData['colour'] = $data['color'];
        } elseif (isset($data['colour_x000d_x000d_x000d_x000d_x000d_x000d_']) && !empty($data['colour_x000d_x000d_x000d_x000d_x000d_x000d_'])) {
            $dbData['colour'] = $data['colour_x000d_x000d_x000d_x000d_x000d_x000d_'];
        } else {
            $dbData['colour'] = "NA";
        }
        if (isset($data['colour_count']) && !empty($data['colour_count']) && strpos($data['colour_count'], '—') === FALSE) {
            $dbData['colour_count'] = removeDollarCommaSpace($data['colour_count']);
        } elseif (isset($data['color_count']) && !empty($data['color_count']) && strpos($data['color_count'], '—') === FALSE) {
            $dbData['colour_count'] = removeDollarCommaSpace($data['color_count']);
        } else {
            $dbData['colour_count'] = 0;
        }
        $dbData['prep_instructions_required'] = (isset($data['prep_instructions_required']) && !empty($data['prep_instructions_required']) && strpos($data['prep_instructions_required'], '—') === FALSE ? $data['prep_instructions_required'] : 'NA');
        $dbData['prep_instructions_vendor_state'] = (isset($data['prep_instructions_vendor_state']) && !empty($data['prep_instructions_vendor_state']) && strpos($data['prep_instructions_vendor_state'], '—') === FALSE ? $data['prep_instructions_vendor_state'] : 'NA');
        $dbData['brand_code'] = (isset($data['brand_code']) && !empty($data['brand_code']) ? $data['brand_code'] : 'NA');
        $dbData['brand'] = (isset($data['brand']) && !empty($data['brand']) ? $data['brand'] : 'NA');
        $dbData['manufacturer_code'] = (isset($data['manufacturer_code']) && !empty($data['manufacturer_code']) ? $data['manufacturer_code'] : 'NA');
        $dbData['parent_manufacturer_code'] = (isset($data['parent_manufacturer_code']) && !empty($data['parent_manufacturer_code']) ? $data['parent_manufacturer_code'] : 'NA');

        return $dbData;
    }

    private function nonAggFileData($index)
    {
        if (isset($index['poid']) && !empty($index['poid'])) {
            $data['po'] = $index['poid'];
        } elseif (isset($index['po']) && !empty($index['po'])) {
            $data['po'] = $index['po'];
        } else {
            $data['po'] = 'NA';
        }
        $data['vendor'] = (isset($index['vendor']) && !empty($index['vendor']) ? $index['vendor'] : 'NA');
        $data['warehouse'] = (isset($index['warehouse']) && !empty($index['warehouse']) ? $index['warehouse'] : 'NA');
        if (isset($index['ship_to_location']) && !empty($index['ship_to_location'])) {
            $data['ship_to_location'] = $index['ship_to_location'];
        } elseif (isset($index['shiplocation']) && !empty($index['shiplocation'])) {
            $data['ship_to_location'] = $index['shiplocation'];
        } else {
            $data['ship_to_location'] = 'NA';
        }

        if (isset($index['model_number']) && !empty($index['model_number'])) {
            $data['model_number'] = $index['model_number'];
        } elseif (isset($index['modelnumber']) && !empty($index['modelnumber'])) {
            $data['model_number'] = $index['modelnumber'];
        } else {
            $data['model_number'] = 'NA';
        }
        $data['asin'] = (isset($index['asin']) && !empty($index['asin']) ? $index['asin'] : 'NA');
        $data['product_id_type'] = (isset($index['product_id_type']) && !empty($index['product_id_type']) ? $index['product_id_type'] : 'NA');
        if (isset($index['product_id_type']) && !empty($index['product_id_type'])) {
            $data['product_id_type'] = $index['product_id_type'];
        } elseif (isset($index['external_id_type']) && !empty($index['external_id_type'])) {
            $data['product_id_type'] = $index['external_id_type'];
        } else {
            $data['product_id_type'] = 'NA';
        }

        $data['availability'] = (isset($index['availability']) && !empty($index['availability']) ? $index['availability'] : 'NA');
        if (isset($index['vendorsku']) && !empty($index['vendorsku'])) {
            $data['sku'] = $index['vendorsku'];
        } elseif (isset($index['sku']) && !empty($index['sku'])) {
            $data['sku'] = $index['sku'];
        } else {
            $data['sku'] = 'NA';
        }
        $data['title'] = (isset($index['title']) && !empty($index['title']) ? $index['title'] : 'NA');

        $data['ack_code_translation_id'] = (isset($index['ackcodetranslationid']) && !empty($index['ackcodetranslationid']) ? $index['ackcodetranslationid'] : 'NA');
        if (isset($index['handofftype']) && !empty($index['handofftype'])) {
            $data['hand_off_type'] = $index['handofftype'];
        } elseif (isset($index['window_type']) && !empty($index['window_type'])) {
            $data['hand_off_type'] = $index['window_type'];
        } else {
            $data['hand_off_type'] = 'NA';
        }

        if (isset($index['externalid']) && !empty($index['externalid'])) {
            $data['externalid'] = $index['externalid'];
        } elseif (isset($index['external_id']) && !empty($index['external_id'])) {
            $data['externalid'] = $index['external_id'];
        } else {
            $data['externalid'] = 'NA';
        }
        $data['status'] = (isset($index['status']) && !empty($index['status']) ? $index['status'] : 'NA');

        if (isset($index['ship_window_start']) && !empty($index['ship_window_start'])) {
            $data['delivery_window_start'] = dateConversion($index['ship_window_start']);
        } elseif (isset($index['ship_from']) && !empty($index['ship_from'])) {
            $data['delivery_window_start'] = dateConversion($index['ship_from']);
        } elseif (isset($index['window_start']) && !empty($index['window_start'])) {
            $data['delivery_window_start'] = dateConversion($index['window_start']);
        } elseif (isset($index['deliver_from']) && !empty($index['deliver_from'])) {
            $data['delivery_window_start'] = dateConversion($index['deliver_from']);
        } elseif (isset($index['handoffstart']) && !empty($index['handoffstart'])) {
            $data['delivery_window_start'] = dateConversion($index['handoffstart']);
        } else {
            $data['delivery_window_start'] = 'NA';
        }

        if (isset($index['ship_window_end']) && !empty($index['ship_window_end'])) {
            $data['delivery_window_end'] = dateConversion($index['ship_window_end']);
        } elseif (isset($index['ship_to']) && !empty($index['ship_to'])) {
            $data['delivery_window_end'] = dateConversion($index['ship_to']);
        } elseif (isset($index['window_end']) && !empty($index['window_end'])) {
            $data['delivery_window_end'] = dateConversion($index['window_end']);
        } elseif (isset($index['deliver_to']) && !empty($index['deliver_to'])) {
            $data['delivery_window_end'] = dateConversion($index['deliver_to']);
        } elseif (isset($index['handoffend']) && !empty($index['handoffend'])) {
            $data['delivery_window_end'] = dateConversion($index['handoffend']);
        } else {
            $data['delivery_window_end'] = 'NA';
        }

        if (isset($index['backorder']) && !empty($index['backorder'])) {
            $data['backorder'] = $index['backorder'];
        } elseif (isset($index['backordered']) && !empty($index['backordered'])) {
            $data['backorder'] = $index['backordered'];
        } else {
            $data['backorder'] = 'NA';
        }

        if (isset($index['expected_ship_date']) && !empty($index['expected_ship_date'])) {
            $data['expected_delivery_date'] = dateConversion($index['expected_ship_date']);
        } elseif (isset($index['edd']) && !empty($index['edd'])) {
            $data['expected_delivery_date'] = dateConversion($index['edd']);
        } elseif (isset($index['expected_date']) && !empty($index['expected_date'])) {
            $data['expected_delivery_date'] = dateConversion($index['expected_date']);
        } elseif (isset($index['handoffend']) && !empty($index['handoffend'])) {
            $data['expected_delivery_date'] = dateConversion($index['handoffend']);
        } else {
            $data['expected_delivery_date'] = 'NA';
        }

        if (isset($index['confirmed_ship_date']) && !empty($index['confirmed_ship_date'])) {
            $data['confirmed_delivery_date'] = dateConversion($index['confirmed_ship_date']);
        } elseif (isset($index['confirmed_delivery_date']) && !empty($index['confirmed_delivery_date'])) {
            $data['confirmed_delivery_date'] = dateConversion($index['confirmed_delivery_date']);
        } else {
            $data['confirmed_delivery_date'] = 'NA';
        }

        $data['case_size'] = (isset($index['case_size']) && !empty($index['case_size']) ? $index['case_size'] : (isset($index['casesize']) && !empty($index['casesize']) ? $index['casesize'] : 'NA'));

        if (isset($index['submitted_cases']) && !empty($index['submitted_cases'])) {
            $data['submitted_cases'] = $index['submitted_cases'];
        } elseif (isset($index['qtysubmitted']) && !empty($index['qtysubmitted'])) {
            $data['submitted_cases'] = $index['qtysubmitted'];
        } elseif (isset($index['quantity_submitted']) && !empty($index['quantity_submitted'])) {
            $data['submitted_cases'] = $index['quantity_submitted'];
        } elseif (isset($index['quantity_requested']) && !empty($index['quantity_requested'])) {
            $data['submitted_cases'] = $index['quantity_requested'];
        } elseif (isset($index['submitted_quantity']) && !empty($index['submitted_quantity'])) {
            $data['submitted_cases'] = $index['submitted_quantity'];
        } else {
            $data['submitted_cases'] = 0;
        }

        if (isset($index['accepted_cases']) && !empty($index['accepted_cases'])) {
            $data['accepted_cases'] = $index['accepted_cases'];
        } elseif (isset($index['qtyaccepted']) && !empty($index['qtyaccepted'])) {
            $data['accepted_cases'] = $index['qtyaccepted'];
        } elseif (isset($index['accepted_quantity']) && !empty($index['accepted_quantity'])) {
            $data['accepted_cases'] = $index['accepted_quantity'];
        } else {
            $data['accepted_cases'] = 0;
        }

        if (isset($index['received_cases']) && !empty($index['received_cases'])) {
            $data['received_cases'] = $index['received_cases'];
        } elseif (isset($index['qtyreceived']) && !empty($index['qtyreceived'])) {
            $data['received_cases'] = $index['qtyreceived'];
        } elseif (isset($index['quantity_received']) && !empty($index['quantity_received'])) {
            $data['received_cases'] = $index['quantity_received'];
        } elseif (isset($index['received_quantity']) && !empty($index['received_quantity'])) {
            $data['received_cases'] = $index['received_quantity'];
        } else {
            $data['received_cases'] = 0;
        }

        if (isset($index['outstanding_cases']) && !empty($index['outstanding_cases'])) {
            $data['outstanding_cases'] = $index['outstanding_cases'];
        } elseif (isset($index['qtyoutstanding']) && !empty($index['qtyoutstanding'])) {
            $data['outstanding_cases'] = $index['qtyoutstanding'];
        } elseif (isset($index['outstanding_quantity']) && !empty($index['outstanding_quantity'])) {
            $data['outstanding_cases'] = $index['outstanding_quantity'];
        } else {
            $data['outstanding_cases'] = 0;
        }
        if (isset($index['unitcost']) && !empty($index['unitcost'])) {
            $data['case_cost'] = removeDollarCommaSpace($index['unitcost']);
        } elseif (isset($index['unit_cost']) && !empty($index['unit_cost'])) {
            $data['case_cost'] = removeDollarCommaSpace($index['unit_cost']);
        } elseif (isset($index['case_cost']) && !empty($index['case_cost'])) {
            $data['case_cost'] = removeDollarCommaSpace($index['case_cost']);

        } else {
            $data['case_cost'] = 0;
        }
        if (isset($index['totalcost']) && !empty($index['totalcost'])) {
            $data['total_cost'] = removeDollarCommaSpace($index['totalcost']);
        } elseif (isset($index['total_cost']) && !empty($index['total_cost'])) {
            $data['total_cost'] = removeDollarCommaSpace($index['total_cost']);
        } else {
            $data['total_cost'] = 0;
        }

        return $data;
    }

    /**
     * This function is used to validate uploade file.
     * @param $extension
     * @return bool
     */
    private function validateExcelFile($extension)
    {
        $validExtensions = array("xls", "xlsx", "csv");
        return in_array($extension, $validExtensions) ? true : false;
    }

    /**
     * This function is used to check BatchId file.
     * @param $extension
     * @return bool
     */
    private function isBatchIdExist($fkVendorId)
    {
        return VCModel::where('vendor_id', $fkVendorId)->has("accounts")->has("accounts.relationBatchId")->with('accounts')->with('accounts.relationBatchId');
    }

}
