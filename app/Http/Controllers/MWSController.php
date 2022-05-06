<?php

namespace App\Http\Controllers;

use App\Libraries\mws\AmazonReport as AmazonReport;
use App\Libraries\mws\AmazonReportRequest;
use App\Libraries\mws\AmazonReportRequestList;
use App\Libraries\mws\AmazonProductList;
use App\Libraries\mws\AmazonProductInfo;
use App\Libraries\mws\AmazonProduct;

use Illuminate\Http\Request;
use App\Models\MWSModel;
use Illuminate\Support\Facades\Log;
use Sonnenglas\AmazonMws\AmazonXmlReport;
use Config;
use Session;
use View;
use DateTime;
use Rap2hpoutre\FastExcel\FastExcel;
use Rap2hpoutre\FastExcel\SheetCollection;


class MWSController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function dashboard()
    {

        return redirect('/dashboard');
        /*$data['pageTitle'] = 'MWS Dashboard';
        $data['pageHeading'] = 'Seller Central Dashboard';
        return view('subpages.mws.dashboard')->with($data);*/
    }

    public function apiConfig()
    {
        //$data['pageTitle'] = 'Api Config';
        //$data['pageHeading'] = 'API Configuration';
        $APIParametr = new MWSModel();
        return $APIParametr->getParameter();

        //return view('subpages.mws.apiconfig')->with($data);
    }

    public function addConfig(Request $request)
    {
        $obj = new MWSModel();
        $CountSellers = $obj->CountSellers($request->seller_id);
        if ($CountSellers) {
            $seller_count = count($CountSellers);
            if ($seller_count == 0) {
                $apiConfig = array();
                $apiConfig['merchant_name'] = $request->merchant_name;
                $apiConfig['seller_id'] = $request->seller_id;
                $apiConfig['mws_access_key_id'] = $request->mws_access_key_id;
                $apiConfig['mws_authtoken'] = $request->mws_authtoken;
                $apiConfig['mws_secret_key'] = $request->mws_secret_key;
                //$apiConfig['marketplace_id'] = $request->marketplace_id;

                $response = $obj->addRecord($apiConfig);

                if ($response) {
                    //Session::flash('success', 'Configuration added successfully!');
                    $return_response = array(
                        'status' => true,
                        'title' => 'Configuration added successfully!',
                        'message' => ''
                    );

                } else {
                    //Session::flash('error', 'There is some error.Please try again!');
                    $return_response = array(
                        'status' => false,
                        'title' => 'There is some error.Please try again!',
                        'message' => ''
                    );
                }
            } else {
                //Session::flash('error', 'This Merchant Already Exist!!');
                //echo '<pre>';
                //print_r($CountSellers);
                if ($CountSellers[0]->is_active == '0') {
                    $apiConfig = array();
                    $apiConfig['merchant_name'] = $request->merchant_name;
                    $apiConfig['seller_id'] = $request->seller_id;
                    $apiConfig['mws_access_key_id'] = $request->mws_access_key_id;
                    $apiConfig['mws_authtoken'] = $request->mws_authtoken;
                    $apiConfig['mws_secret_key'] = $request->mws_secret_key;
                    $apiConfig['is_active'] = 1;

                    //$apiConfig['marketplace_id'] = $request->marketplace_id;
                    $config_id = $CountSellers[0]->mws_config_id;
                    $response = $obj->update_api_config($apiConfig, $config_id);

                    if ($response) {
                        //Session::flash('success', 'Configuration added successfully!');
                        $return_response = array(
                            'status' => true,
                            'title' => 'Configuration added successfully!',
                            'message' => ''
                        );

                    } else {
                        //Session::flash('error', 'There is some error.Please try again!');
                        $return_response = array(
                            'status' => false,
                            'title' => 'There is some error.Please try again!',
                            'message' => ''
                        );
                    }
                } else {
                    $return_response = array(
                        'status' => false,
                        'title' => 'This Merchant Already Exist!',
                        'message' => ''
                    );
                }

            }

        } else {
            //Session::flash('error', 'There is some error.Please try again!');
            $return_response = array(
                'status' => false,
                'title' => 'There is some error.Please try again!',
                'message' => ''
            );
        }

        return response()->json($return_response);
        //return View::make('partials/flash-messages');


    }

    public function editConfig(Request $request)
    {
        $obj = new MWSModel();
        $CountSellers = $obj->CountExistingSellers($request->seller_id, $request->mws_config_id);

        if ($CountSellers) {
            $seller_count = count($CountSellers);
            if ($seller_count == 0) {
                $obj = new MWSModel();
                $apiConfig = array();
                $config_id = $request->mws_config_id;
                $apiConfig['merchant_name'] = $request->merchant_name;
                $apiConfig['seller_id'] = $request->seller_id;
                $apiConfig['mws_access_key_id'] = $request->mws_access_key_id;
                $apiConfig['mws_authtoken'] = $request->mws_authtoken;
                $apiConfig['mws_secret_key'] = $request->mws_secret_key;
                //$apiConfig['marketplace_id'] = $request->marketplace_id;
                $response = $obj->update_api_config($apiConfig, $config_id);
                if ($response) {
                    //Session::flash('success', 'Configuration updated successfully!');
                    $return_response = array(
                        'status' => true,
                        'title' => 'Configuration updated successfully!',
                        'message' => ''
                    );
                } else {
                    //Session::flash('error', 'There is some error.Please try again!');
                    $return_response = array(
                        'status' => false,
                        'title' => 'There is some error.Please try again!',
                        'message' => ''
                    );
                }
            } else {
                //Session::flash('error', 'This Merchant Already Exist!!');
                $return_response = array(
                    'status' => false,
                    'title' => 'This Merchant Already Exist!',
                    'message' => ''
                );
            }

        } else {
            //Session::flash('error', 'There is some error.Please try again!');
            $return_response = array(
                'status' => false,
                'title' => 'There is some error.Please try again!',
                'message' => ''
            );
        }
        return response()->json($return_response);
        // return View::make('partials/flash-messages');
    }

    public function deleteApiConfig(Request $request)
    {

        $config_id = $request->id;
        $obj = new MWSModel();
        $response = $obj->delete_mws_config($config_id);
        //MWSModel::delete_mws_config($config_id);
        if ($response) {
            //Session::flash('success', 'Configuration deleted successfully!');
            $return_response = array(
                'status' => true,
                'title' => 'Configuration deleted successfully',
                'message' => ''
            );

        } else {
            // Session::flash('error', 'There is some error.Please try again!');
            $return_response = array(
                'status' => false,
                'title' => 'There is some error.Please try again!',
                'message' => ''
            );
        }
        return response()->json($return_response);
        //return redirect()->back()->with('success','Item created successfully!');
        // return redirect()->back();

    }

    public function scheduling()
    {
        $getAllCrons = new MWSModel();
        $data['allCrons'] = $getAllCrons->getMwsCrons()->map(function ($item) {
            $status = 'stop';
            if ($item->status == 1) {
                $status = 'run';
            }
            return [
                'task_id' => $item->task_id,
                'report_type' => $item->report_type,
                'cronStartTime' => $item->cronStartTime,
                'isCronRunning' => $item->isCronRunning,
                'frequency' => $item->frequency,
                'status' => $status,
                'requestReportTime' => $item->requestReportTime,
                'requestReportLastRun' => $item->requestReportLastRun,
                'requestReportCompletedTime' => $item->requestReportCompletedTime,
                'requestReportLISTTime' => $item->requestReportLISTTime,
                'requestReportListLastRun' => $item->requestReportListLastRun,
                'requestReportLISTCompletedTime' => $item->requestReportLISTCompletedTime,
                'getReportTime' => $item->getReportTime,
                'getReportLastRun' => $item->getReportLastRun,
                'getReportCompletedTime' => $item->getReportCompletedTime,
                'createdAt' => $item->createdAt,
                'updatedAt' => $item->updatedAt,
            ];
        });
        return response()->json($data);
    }

    public function addCron(Request $request)
    {
        $time_overlap = 0;
        $requestReportTime = $request->cron_time;
        $requestReportTimecheck = DateTime::createFromFormat('H:i', $requestReportTime);
        $day_end_check_from = DateTime::createFromFormat('H:i', '23:30');
        $day_end_check_to = DateTime::createFromFormat('H:i', '23:59');
        if ($requestReportTimecheck >= $day_end_check_from && $requestReportTimecheck <= $day_end_check_to) {
            $Day_end_time_convert = strtotime("-60 minutes", strtotime($requestReportTime));
            $requestReportTime = date('H:i', $Day_end_time_convert);
        }
        /*sales variables  starts*/
        $getSalesCronTime = MWSModel::checkCronTimeOverlap('Sales');
        $count_sales_crons = count($getSalesCronTime);
        if ($count_sales_crons > 0) {
            $sales_request_time = $getSalesCronTime[0]->cronStartTime;
            $sales_start_time = strtotime("-3 minutes", strtotime($sales_request_time));
            $sales_start_time_converted = date('H:i', $sales_start_time);
            $sales_start_time_check = DateTime::createFromFormat('H:i', $sales_request_time);
            $sales_end_time = strtotime("+3 minutes", strtotime($sales_request_time));
            $sales_end_time_converted = date('H:i', $sales_end_time);
            $sales_end_time_check = DateTime::createFromFormat('H:i', $sales_end_time_converted);
        }
        /* sales variables  ends*/
        /* Inventory variables  starts*/
        $getInventoryTime = MWSModel::checkCronTimeOverlap('Inventory');
        $count_inventory_crons = count($getInventoryTime);
        if ($count_inventory_crons > 0) {
            $Inventory_request_time = $getInventoryTime[0]->cronStartTime;
            $Inventory_start_time = strtotime("-3 minutes", strtotime($Inventory_request_time));
            $Inventory_start_time_converted = date('H:i', $Inventory_start_time);
            $Inventory_start_time_check = DateTime::createFromFormat('H:i', $Inventory_request_time);
            $Inventory_end_time = strtotime("+3 minutes", strtotime($Inventory_request_time));
            $Inventory_end_time_converted = date('H:i', $Inventory_end_time);
            $Inventory_end_time_check = DateTime::createFromFormat('H:i', $Inventory_end_time_converted);
        }
        /* Inventory variables  ends*/
        /* Catalog variables  starts*/
        $getCatalogTime = MWSModel::checkCronTimeOverlap('Catalog');
        $count_catalog_crons = count($getCatalogTime);
        if ($count_catalog_crons > 0) {
            $Catalog_request_time = $getCatalogTime[0]->cronStartTime;
            $Catalog_start_time = strtotime("-3 minutes", strtotime($Catalog_request_time));
            $Catalog_start_time_converted = date('H:i', $Catalog_start_time);
            $Catalog_start_time_check = DateTime::createFromFormat('H:i', $Catalog_request_time);
            $Catalog_end_time = strtotime("+3 minutes", strtotime($Catalog_request_time));
            $Catalog_end_time_converted = date('H:i', $Catalog_end_time);
            $Catalog_end_time_check = DateTime::createFromFormat('H:i', $Catalog_end_time_converted);
        }
        //echo count($getCatalogTime);
        $report_type = $request->report_type;
        if ($report_type == 'Sales') {
            $sales_catalog_match = 0;
            $sales_inventory_match = 0;
            if ($count_inventory_crons > 0) {
                if ($requestReportTimecheck >= $Inventory_start_time_check && $requestReportTimecheck <= $Inventory_end_time_check) {
                    $sales_inventory_match = 1;
                }
            }
            if ($count_catalog_crons > 0) {
                if ($requestReportTimecheck >= $Catalog_start_time_check && $requestReportTimecheck <= $Catalog_end_time_check) {
                    $sales_catalog_match = 1;
                }
            }
            if ($sales_catalog_match == 1 || $sales_inventory_match == 1) {
                $time_overlap = 1;
            }

        }
        //exit;
        if ($report_type == 'Inventory') {
            $inventory_catalog_match = 0;
            $inventory_sales_match = 0;
            if ($count_sales_crons > 0) {
                if ($requestReportTimecheck >= $sales_start_time_check && $requestReportTimecheck <= $sales_end_time_check) {
                    $inventory_sales_match = 1;
                }
            }
            if ($count_catalog_crons > 0) {
                if ($requestReportTimecheck >= $Catalog_start_time_check && $requestReportTimecheck <= $Catalog_end_time_check) {
                    $inventory_catalog_match = 1;
                }
            }
            if ($inventory_catalog_match == 1 || $inventory_sales_match == 1) {
                $time_overlap = 2;
            }
        }
        if ($report_type == 'Catalog') {
            $catalog_inventory_match = 0;
            $catalog_sales_match = 0;
            if ($count_sales_crons > 0) {
                if ($requestReportTimecheck >= $sales_start_time_check && $requestReportTimecheck <= $sales_end_time_check) {
                    $catalog_sales_match = 1;
                }
            }
            if ($count_inventory_crons > 0) {
                if ($requestReportTimecheck >= $Inventory_start_time_check && $requestReportTimecheck <= $Inventory_end_time_check) {
                    $catalog_inventory_match = 1;
                }
            }
            if ($catalog_sales_match == 1 || $catalog_inventory_match == 1) {
                $time_overlap = 3;
            }
        }

        if ($time_overlap == 1) {
            $UpdateReportRequestTime = strtotime("+8 minutes", strtotime($requestReportTime));
            $requestReportTime = date('H:i', $UpdateReportRequestTime);
        }
        if ($time_overlap == 2) {
            $UpdateReportRequestTime = strtotime("+4 minutes", strtotime($requestReportTime));
            $requestReportTime = date('H:i', $UpdateReportRequestTime);
        }
        if ($time_overlap == 3) {
            $UpdateReportRequestTime = strtotime("+12 minutes", strtotime($requestReportTime));
            $requestReportTime = date('H:i', $UpdateReportRequestTime);
        }
        $requestReportLISTTimeConvert = strtotime("+15 minutes", strtotime($requestReportTime));
        $requestReportLISTTime = date('H:i', $requestReportLISTTimeConvert);
        // $reportLISTTimeConvert = strtotime("+5 minutes", strtotime($requestReportLISTTime));
        //$reportLISTTime=date('H:i', $reportLISTTimeConvert);
        $getReportTimConvert = strtotime("+5 minutes", strtotime($requestReportLISTTime));
        $getReportTime = date('H:i', $getReportTimConvert);
        $getAsinsFromReports = strtotime("+10 minutes", strtotime($getReportTime));
        $getAsinsFromReportsTime = date('H:i', $getAsinsFromReports);
        $getProductDetails = strtotime("+5 minutes", strtotime($getAsinsFromReportsTime));
        $getProductDetailsTime = date('H:i', $getProductDetails);
        $getProductCategoriesDetails = strtotime("+5 minutes", strtotime($getProductDetailsTime));
        $getProductCategoriesDetailsTime = date('H:i', $getProductCategoriesDetails);
        $obj = new MWSModel();
        //$data['title'] = $request->title;
        $data['report_type'] = $request->report_type;
        $data['cronStartTime'] = $request->cron_time;
        $data['frequency'] = 'daily';
        $data['status'] = $request->cronstatus;
        if($request->cronstatus == 'stop'){
            $data['status'] = 0;
        }elseif($request->cronstatus == 'run'){
            $data['status'] = 1;
        }
        $data['requestReportTime'] = $requestReportTime;
        $data['requestReportLISTTime'] = $requestReportLISTTime;
        //$data['reportLISTTime'] = $reportLISTTime;
        $data['getReportTime'] = $getReportTime;
        //$data['getAsinsFromReportsTime'] = $getAsinsFromReportsTime;
        // $data['getProductDetailsTime'] = $getProductDetailsTime;
        // $data['getProductCategoriesDetailsTime'] = $getProductCategoriesDetailsTime;
        // echo $request->report_type; echo '<br>';
        $checkCronExist = MWSModel::checkCronExist(trim($request->report_type));
        $count_crons = count($checkCronExist);
        $return_response = array();
        if ($count_crons > 0) {
            $response = MWSModel::updateExistingCron($data, $request->report_type);
            if ($response) {
                $message = '';
                if($request->cronstatus == 'stop'){
                    $message = 'Schedule stopped successfully';
                }elseif($request->cronstatus == 'run'){
                    $message = 'Schedule ran successfully';
                }
                $return_response = array(
                    'status' => true,
                    'title' => 'Schedule updated Successfully',
                    'message' => $message
                );
            } else {
                if($request->cronstatus == 'stop'){
                    $message = 'Schedule stopped successfully';
                }elseif($request->cronstatus == 'run'){
                    $message = 'Schedule ran successfully';
                }
                $return_response = array(
                    'status' => false,
                    'title' => $request->report_type,
                    'message' => $message
                );
            }
        } else {
            $response = $obj->addCron($data);
            if ($response) {
                $message = '';
                if($request->cronstatus == 'stop'){
                    $message = 'Schedule stopped successfully';
                }elseif($request->cronstatus == 'run'){
                    $message = 'Schedule ran successfully';
                }
                $return_response = array(
                    'status' => true,
                    'title' => 'Schedule Added Successfully',
                    'message' => $message
                );

            } else {
                $return_response = array(
                    'status' => false,
                    'title' => $request->report_type,
                    'message' => ''
                );
            }
        }
        return response()->json($return_response);
    }

    public function editCron(Request $request)
    {
        $task_id = $request->cron_task_id;
        $time_overlap = 0;
        $requestReportTime = $request->cron_time;
        $requestReportTimecheck = DateTime::createFromFormat('H:i', $requestReportTime);
        $day_end_check_from = DateTime::createFromFormat('H:i', '23:30');
        $day_end_check_to = DateTime::createFromFormat('H:i', '23:59');
        if ($requestReportTimecheck >= $day_end_check_from && $requestReportTimecheck <= $day_end_check_to) {
            $Day_end_time_convert = strtotime("-60 minutes", strtotime($requestReportTime));
            $requestReportTime = date('H:i', $Day_end_time_convert);
        }
        /*sales variables  starts*/
        $getSalesCronTime = MWSModel::checkCronTimeOverlap('Sales');
        $count_sales_crons = count($getSalesCronTime);
        if ($count_sales_crons > 0) {
            $sales_request_time = $getSalesCronTime[0]->cronStartTime;
            $sales_start_time = strtotime("-3 minutes", strtotime($sales_request_time));
            $sales_start_time_converted = date('H:i', $sales_start_time);
            $sales_start_time_check = DateTime::createFromFormat('H:i', $sales_request_time);
            $sales_end_time = strtotime("+3 minutes", strtotime($sales_request_time));
            $sales_end_time_converted = date('H:i', $sales_end_time);
            $sales_end_time_check = DateTime::createFromFormat('H:i', $sales_end_time_converted);
        }
        /* sales variables  ends*/
        /* Inventory variables  starts*/
        $getInventoryTime = MWSModel::checkCronTimeOverlap('Inventory');
        $count_inventory_crons = count($getInventoryTime);
        if ($count_inventory_crons > 0) {
            $Inventory_request_time = $getInventoryTime[0]->cronStartTime;
            $Inventory_start_time = strtotime("-3 minutes", strtotime($Inventory_request_time));
            $Inventory_start_time_converted = date('H:i', $Inventory_start_time);
            $Inventory_start_time_check = DateTime::createFromFormat('H:i', $Inventory_request_time);
            $Inventory_end_time = strtotime("+3 minutes", strtotime($Inventory_request_time));
            $Inventory_end_time_converted = date('H:i', $Inventory_end_time);
            $Inventory_end_time_check = DateTime::createFromFormat('H:i', $Inventory_end_time_converted);
        }
        /* Inventory variables  ends*/
        /* Catalog variables  starts*/
        $getCatalogTime = MWSModel::checkCronTimeOverlap('Catalog');
        $count_catalog_crons = count($getCatalogTime);
        if ($count_catalog_crons > 0) {
            $Catalog_request_time = $getCatalogTime[0]->cronStartTime;
            $Catalog_start_time = strtotime("-3 minutes", strtotime($Catalog_request_time));
            $Catalog_start_time_converted = date('H:i', $Catalog_start_time);
            $Catalog_start_time_check = DateTime::createFromFormat('H:i', $Catalog_request_time);
            $Catalog_end_time = strtotime("+3 minutes", strtotime($Catalog_request_time));
            $Catalog_end_time_converted = date('H:i', $Catalog_end_time);
            $Catalog_end_time_check = DateTime::createFromFormat('H:i', $Catalog_end_time_converted);
        }
        //echo count($getCatalogTime);
        $report_type = $request->report_type_value;
        if ($report_type == 'Sales') {
            $sales_catalog_match = 0;
            $sales_inventory_match = 0;
            if ($count_inventory_crons > 0) {
                if ($requestReportTimecheck >= $Inventory_start_time_check && $requestReportTimecheck <= $Inventory_end_time_check) {
                    $sales_inventory_match = 1;
                }
            }
            if ($count_catalog_crons > 0) {
                if ($requestReportTimecheck >= $Catalog_start_time_check && $requestReportTimecheck <= $Catalog_end_time_check) {
                    $sales_catalog_match = 1;
                }
            }
            if ($sales_catalog_match == 1 || $sales_inventory_match == 1) {
                $time_overlap = 1;
            }

        }
        //exit;
        if ($report_type == 'Inventory') {
            $inventory_catalog_match = 0;
            $inventory_sales_match = 0;
            if ($count_sales_crons > 0) {
                if ($requestReportTimecheck >= $sales_start_time_check && $requestReportTimecheck <= $sales_end_time_check) {
                    $inventory_sales_match = 1;
                }
            }
            if ($count_catalog_crons > 0) {
                if ($requestReportTimecheck >= $Catalog_start_time_check && $requestReportTimecheck <= $Catalog_end_time_check) {
                    $inventory_catalog_match = 1;
                }
            }
            if ($inventory_catalog_match == 1 || $inventory_sales_match == 1) {
                $time_overlap = 2;
            }
        }
        if ($report_type == 'Catalog') {
            $catalog_inventory_match = 0;
            $catalog_sales_match = 0;
            if ($count_sales_crons > 0) {
                if ($requestReportTimecheck >= $sales_start_time_check && $requestReportTimecheck <= $sales_end_time_check) {
                    $catalog_sales_match = 1;
                }
            }
            if ($count_inventory_crons > 0) {
                if ($requestReportTimecheck >= $Inventory_start_time_check && $requestReportTimecheck <= $Inventory_end_time_check) {
                    $catalog_inventory_match = 1;
                }
            }
            if ($catalog_sales_match == 1 || $catalog_inventory_match == 1) {
                $time_overlap = 3;
            }
        }

        if ($time_overlap == 1) {
            $UpdateReportRequestTime = strtotime("+8 minutes", strtotime($requestReportTime));
            $requestReportTime = date('H:i', $UpdateReportRequestTime);
        }
        if ($time_overlap == 2) {
            $UpdateReportRequestTime = strtotime("+4 minutes", strtotime($requestReportTime));
            $requestReportTime = date('H:i', $UpdateReportRequestTime);
        }
        if ($time_overlap == 3) {
            $UpdateReportRequestTime = strtotime("+12 minutes", strtotime($requestReportTime));
            $requestReportTime = date('H:i', $UpdateReportRequestTime);
        }
        $requestReportLISTTimeConvert = strtotime("+15 minutes", strtotime($requestReportTime));
        $requestReportLISTTime = date('H:i', $requestReportLISTTimeConvert);
        // $reportLISTTimeConvert = strtotime("+5 minutes", strtotime($requestReportLISTTime));
        // $reportLISTTime=date('H:i', $reportLISTTimeConvert);
        $getReportTimConvert = strtotime("+5 minutes", strtotime($requestReportLISTTime));
        $getReportTime = date('H:i', $getReportTimConvert);
        $obj = new MWSModel();
        //$data['title'] = $request->title;
        //$data['report_type'] = $request->report_type;
        $data['cronStartTime'] = $request->cron_time;
        $data['frequency'] = 'daily';
        $data['status'] = 1;
        $data['requestReportTime'] = $requestReportTime;
        $data['requestReportLISTTime'] = $requestReportLISTTime;
        //$data['reportLISTTime'] = $reportLISTTime;
        $data['getReportTime'] = $getReportTime;
        $response = $obj->update_cron_status($data, $task_id);
        $return_response = array();
        if ($response) {
            $return_response = array(
                'status' => 'success',
                'title' => $request->report_type,
                'message' => 'Cron job updated successfully!'
            );
        } else {
            $return_response = array(
                'status' => 'fail',
                'title' => $request->report_type,
                'message' => 'There is some error.Please try again!'
            );
        }
        return response()->json($return_response);
    }

    public function changeCronStatus(Request $request)
    {
        $return_response = array();
        $obj = new MWSModel();
        // $apiConfig = array();
        $task_id = $request->task_id;
        $data['status'] = $request->status;
        $response = $obj->update_cron_status($data, $task_id);
        if ($response) {
            $return_response = array(
                'status' => 'success',
                'message' => 'Cron status updated successfully!'
            );

        } else {
            $return_response = array(
                'status' => 'fail',
                'message' => 'There is some error.Please try again!'
            );
        }
        return response()->json($return_response);
    }

    public function runCron()
    {
        $current_date = date('Y-m-d');
        echo $current_time = date("H:i", time());

        echo '<br>';
        echo $to_time = date("H:i", strtotime("+5 minutes"));
        echo '<br>';
        echo $from_tim = date("H:i", strtotime("-5 minutes"));
        exit;
        $Crons = new MWSModel();
        $active_crons = $Crons->get_active_crons();

        if ($active_crons) {

            foreach ($active_crons as $value) {

                if ($current_date != $value->requestReportLastRun) {
                    echo '<br>';
                    echo $value->requestReportTime . 'cron time';
                    echo '<br>';
                    $cron_time = DateTime::createFromFormat('H:i', $value->requestReportTime);
                    $start = DateTime::createFromFormat('H:i', $from_tim);
                    $end = DateTime::createFromFormat('H:i', $to_time);
                    if ($cron_time >= $start && $cron_time <= $end) {
                        //echo 'run';
                        if ($value->report_type == 'Catalog') {
                            //Artisan::call('catalogReportsRequest:cron');
                            $data['requestReportLastRun'] = $current_date;
                            //$updateCronLastRunDate = MWSModel::updateCronLastRunDate($data,$value->task_id);
                        } elseif ($value->report_type == 'Inventory') {
                            //Artisan::call('inventoryReportsRequest:cron');
                            $data['requestReportLastRun'] = $current_date;
                            //$updateCronLastRunDate = MWSModel::updateCronLastRunDate($data,$value->task_id);
                        } elseif ($value->report_type == 'Sales') {
                            echo 'sales run';
                            //Artisan::call('salesReportsRequest:cron');
                            $data['requestReportLastRun'] = $current_date;
                            //$updateCronLastRunDate = MWSModel::updateCronLastRunDate($data,$value->task_id);
                        }
                    } else {
                        echo 'not run';
                        echo '<br>';
                    }
                } else {
                    echo 'already run';
                }


            }
        }

    }

    public function deleteCron(Request $request)
    {
        $task_id = $request->id;
        $return_response = array();
        $obj = new MWSModel();
        $response = $obj->delete_mws_cron($task_id);
        //MWSModel::delete_mws_config($config_id);
        if ($response) {
            $return_response = array(
                'status' => 'success',
                'message' => 'Cron deleted successfully!'
            );

        } else {
            $return_response = array(
                'status' => 'fail',
                'message' => 'There is some error.Please try again!'
            );
        }
        //return redirect()->back()->with('success','Item created successfully!');
        return response()->json($return_response);
    }

    public function MWSGetXmlReport()
    {
        $get_mws_done_reports = MWSModel::get_mws_done_reports('_GET_FLAT_FILE_RETURNS_DATA_BY_RETURN_DATE_');

        $mws_done_report_id = '15969725669018109';
        $amz = new AmazonXmlReport("store1"); //store name matches the array key in the config file
        $amz->setReportId($mws_done_report_id);
        $report_data = $amz->fetchXmlReport();
        //$report_data=$amz->saveReport();
        foreach ($report_data as $insert_record) {

            $data['ReportId'] = $mws_done_report_id;
            $data['country'] = '';
            //Country/Region
            $data['snapshotDate'] = '';
            $data['productName'] = 'Product Name';
            $data['FNSKU'] = 'FNSKU';
            $data['merchant'] = 'Merchant';
            $data['sku'] = 'SKU';
            $data['asin'] = 'asin';
            $data['condition'] = 'Condition';
            //$data['snapshotDate']= '';Supplier
            $data['supplier'] = 'Supplier';
            $data['partNo'] = 'part no.';
            $data['currencyCode'] = 'Currency code';
            $data['price'] = 'Price';
            $data['SalesLast30Days'] = 'Sales last 30 days';
            $data['unitsSoldLast30Days'] = 'Units Sold Last 30 Days';
            $data['TotalUnits'] = 'Total Units';
            $data['InboundAvailable'] = 'Inbound	Available';
            $data['fCTransfer'] = 'FC transfer';
            $data['fCProcessing'] = 'FC Processing';
            $data['customerOrder'] = 'Customer Order';
            $data['unfulfillable'] = 'Unfulfillable';
            $data['fulfilledBy'] = 'Fulfilled by';
            $data['daysofsupply'] = 'Days of Supply';
            $data['alert'] = 'Alert';
            $data['recommendedOrderQty'] = 'Recommended Order Qty';
            $data['recommendedOrderDate'] = 'Recommended Order Date';
            $data['eligibleForStorageFee'] = 'Eligible for Storage Fee Discount Current Month';
            $data['currentMonthVeryHighInventoryThreshold'] = 'Current Month - Very High Inventory Threshold';
            $data['eligibleForStorageFeeDiscountNextMonth'] = 'Eligible for Storage Fee Discount Next Month';
            $data['nextMonthVeryLowInventoryThreshold'] = 'Next Month - Very Low Inventory Threshold';
            $data['nextMonthStorageDiscount'] = 'Next month - Storage discount';
            //$data['minimumInventoryThreshold'] = 'minimum inventory threshold';
            $data['nextMonthStorageDiscount'] = 'Next month - Storage discount';
            //$data['maximumInventoryThreshold'] = 'maximum inventory threshold';
            $data['nextMonthVeryHighInventoryThreshold'] = 'Next month - Very high inventory threshold';
            MWSModel::insert_mws_ScFbaRestockReport($data);
        }
        //  MWSModel::update_mws_report_acknowledgement($data);
        // }

    }

    public function acknowledgeReports()
    {
        // echo $this->acknowledgeReportss();
        $amz = new AmazonReportAcknowledger("store1");
        $amz->setReportIds('15909139281018105');
        $amz->setAcknowledgedFilter('TRUE');
        $amz->acknowledgeReports();
        $response = $amz->getList();

    }

    public function scHistoryForm()
    {
        $data['pageTitle'] = 'Historical Data';
        $data['pageHeading'] = 'History';
        //return view("subpages.historicalData.schistory")->with($data);
        return view('subpages.mws.schistory')->with($data);

    }

    public function checkScHistory(Request $request)
    {
        scSetMemoryLimitAndExeTime();
        $str_arr = explode(" - ", $_POST['daterange']);
        $start_date = date('Y-m-d 00:00:00', strtotime('+1 day', strtotime(trim($str_arr[0]))));
        $end_date = date('Y-m-d 23:59:59', strtotime('+1 day', strtotime(trim($str_arr[1]))));
        //$start_date=trim($str_arr[0]).' 00:00:00';
        //$end_date=trim($str_arr[1].' 23:59:59');
        $report_type = $_POST['report_type'];
        if ($report_type == 'Catalog') {
            $tbl_catalogsccatactivereport = MWSModel::get_report_excel($start_date, $end_date, 'tbl_sc_catalog_cat_active_report');
            $tbl_catalogsccatactivereport_count = count($tbl_catalogsccatactivereport);
            $tbl_catalogsccatactivereport = 0;
            if ($tbl_catalogsccatactivereport_count > 0) {
                $tbl_catalogsccatactivereport = 1;
            }
            $tbl_sccatinactivereport = MWSModel::get_report_excel($start_date, $end_date, 'tbl_sc_catalog_cat_inactive_report');
            $tbl_sccatinactivereport_count = count($tbl_sccatinactivereport);
            $tbl_sccatinactivereport = 0;
            if ($tbl_sccatinactivereport_count > 0) {
                $tbl_sccatinactivereport = 1;
            }
            $tbl_catalogscfbahealthreport = MWSModel::get_report_excel($start_date, $end_date, 'tbl_sc_catalog_fba_health_report');
            $tbl_catalogscfbahealthreport_count = count($tbl_catalogscfbahealthreport);
            $tbl_catalogscfbahealthreport = 0;
            if ($tbl_catalogscfbahealthreport_count > 0) {
                $tbl_catalogscfbahealthreport = 1;
            }
            $response["status"] = true;
            if ($tbl_catalogsccatactivereport == 1 || $tbl_sccatinactivereport == 1 || $tbl_catalogscfbahealthreport == 1) {
                $response["url"] = url('mws/download/' . $report_type . '/' . $start_date . '/' . $end_date);
                $response["title"] = $report_type;
                $response["message"] = "Please click here to download file.";
                return $response;
            }
            $response["status"] = false;
            $response["title"] = $report_type;
            $response["message"] = "No data found against the selected  date.";
            return response()->json($response);
        } elseif ($report_type == 'Inventory') {

            $tbl_sccatactivereport = MWSModel::get_report_excel($start_date, $end_date, 'tbl_sc_inventory_cat_active_report');
            $tbl_sccatactivereport_count = count($tbl_sccatactivereport);
            $tbl_sccatactivereport = 0;
            if ($tbl_sccatactivereport_count > 0) {
                $tbl_sccatactivereport = 1;
            }

            $tbl_scfbareceiptreport = MWSModel::get_report_excel($start_date, $end_date, 'tbl_sc_inventory_fba_receipt_report');
            $tbl_scfbareceiptreport_count = count($tbl_scfbareceiptreport);
            $tbl_scfbareceiptreport = 0;
            if ($tbl_scfbareceiptreport_count > 0) {
                $tbl_scfbareceiptreport = 1;
            }
            $tbl_scfbahealthreport = MWSModel::get_report_excel($start_date, $end_date, 'tbl_sc_inventory_fba_health_report');
            $tbl_scfbahealthreport_count = count($tbl_scfbahealthreport);
            $tbl_scfbahealthreport = 0;
            if ($tbl_scfbahealthreport_count > 0) {
                $tbl_scfbahealthreport = 1;
            }
            //return $tbl_sccatactivereport_count.'test'.$tbl_scfbareceiptreport.'test'.$tbl_scfbahealthreport;

            $response["status"] = true;
            if ($tbl_sccatactivereport == 1 || $tbl_scfbareceiptreport == 1 || $tbl_scfbahealthreport == 1) {
                $response["url"] = url('mws/download/' . $report_type . '/' . $start_date . '/' . $end_date);
                $response["title"] = $report_type;
                $response["message"] = "Please click here to download file.";
                return $response;
            }
            $response["status"] = false;
            $response["title"] = $report_type;
            $response["message"] = "No data found against the selected  date";
            return response()->json($response);
        } elseif ($report_type == 'Sales') {
            $tbl_scfbareturnsreport = MWSModel::get_report_excel($start_date, $end_date, 'tbl_sc_sales_fba_returns_report');
            $tbl_scfbareturnsreport_count = count($tbl_scfbareturnsreport);
            $tbl_scfbareturnsreport = 0;
            if ($tbl_scfbareturnsreport_count > 0) {
                $tbl_scfbareturnsreport = 1;
            }

            $tbl_scmfnreturnsreport = MWSModel::get_report_excel($start_date, $end_date, 'tbl_sc_sales_mfn_returns_report');
            $tbl_scmfnreturnsreport_count = count($tbl_scmfnreturnsreport);
            $tbl_scmfnreturnsreport = 0;
            if ($tbl_scmfnreturnsreport_count > 0) {
                $tbl_scmfnreturnsreport = 1;
            }
            $tbl_scordersupdtreport = MWSModel::get_report_excel($start_date, $end_date, 'tbl_sc_sales_orders_updt_report');
            $tbl_scordersupdtreport_count = count($tbl_scordersupdtreport);
            $tbl_scordersupdtreport = 0;
            if ($tbl_scordersupdtreport_count > 0) {
                $tbl_scordersupdtreport = 1;
            }
            $tbl_scordersreport = MWSModel::get_report_excel($start_date, $end_date, 'tbl_sc_sales_orders_report');
            $tbl_scordersreport_count = count($tbl_scordersreport);
            $tbl_scordersreport = 0;
            if ($tbl_scordersreport_count > 0) {
                $tbl_scordersreport = 1;
            }
            $response["status"] = true;
            if ($tbl_scfbareturnsreport == 1 || $tbl_scmfnreturnsreport == 1 || $tbl_scordersupdtreport == 1 || $tbl_scordersreport == 1) {
                $response["url"] = url('mws/download/' . $report_type . '/' . $start_date . '/' . $end_date);
                $response["title"] = $report_type;
                $response["message"] = "Please click here to download file.";
                return $response;
            }
            $response["status"] = false;
            $response["title"] = $report_type;
            $response["message"] = "No data found against the selected  date.";
            return response()->json($response);

        }

    }

    public function scDownloadCsv($reportType, $startDate, $endDate)
    {
        scSetMemoryLimitAndExeTime();
        $start_date = trim($startDate);
        $end_date = trim($endDate);
        $file_name = date('Y-m-d', strtotime('-1 day', strtotime(trim($start_date)))) . '-' . date('Y-m-d', strtotime('-1 day', strtotime(trim($end_date)))) . '.xlsx';
        $report_type = $reportType;
        if ($report_type == 'Catalog') {
            $tbl_catalogsccatactivereport = MWSModel::get_report_excel($start_date, $end_date, 'tbl_sc_catalog_cat_active_report');
            $tbl_catalogsccatactivereport_count = count($tbl_catalogsccatactivereport);

            if ($tbl_catalogsccatactivereport_count == 0) {
                $tbl_catalogsccatactivereport = '';
            }

            $tbl_sccatinactivereport = MWSModel::get_report_excel($start_date, $end_date, 'tbl_sc_catalog_cat_inactive_report');
            $tbl_sccatinactivereport_count = count($tbl_sccatinactivereport);

            if ($tbl_sccatinactivereport_count == 0) {
                $tbl_sccatinactivereport = '';
            }
            $tbl_catalogscfbahealthreport = MWSModel::get_report_excel($start_date, $end_date, 'tbl_sc_catalog_fba_health_report');
            $tbl_catalogscfbahealthreport_count = count($tbl_catalogscfbahealthreport);

            if ($tbl_catalogscfbahealthreport_count == 0) {
                $tbl_catalogscfbahealthreport = '';
            }
            //echo 'catalog';
            // exit;

            $sheets = new SheetCollection([
                //'Users1' => MWSModel::get_report_excel(),
                'Active Listings Report' => $tbl_catalogsccatactivereport,
                'Inactive Listings Report' => $tbl_sccatinactivereport,
                'FBA Inventory Health Report' => $tbl_catalogscfbahealthreport
            ]);
            return (new FastExcel($sheets))->download($file_name);
        } elseif ($report_type == 'Inventory') {

            $tbl_sccatactivereport = MWSModel::get_report_excel($start_date, $end_date, 'tbl_sc_inventory_cat_active_report');
            $tbl_sccatactivereport_count = count($tbl_sccatactivereport);

            if ($tbl_sccatactivereport_count == 0) {
                $tbl_sccatactivereport = '';
            }

            $tbl_scfbareceiptreport = MWSModel::get_report_excel($start_date, $end_date, 'tbl_sc_inventory_fba_receipt_report');
            $tbl_scfbareceiptreport_count = count($tbl_scfbareceiptreport);

            if ($tbl_scfbareceiptreport_count == 0) {
                $tbl_scfbareceiptreport = '';
            }
            $tbl_scfbahealthreport = MWSModel::get_report_excel($start_date, $end_date, 'tbl_sc_inventory_fba_health_report');
            $tbl_scfbahealthreport_count = count($tbl_scfbahealthreport);

            if ($tbl_scfbahealthreport_count == 0) {
                $tbl_scfbahealthreport = '';
            }
            $sheets = new SheetCollection([
                //'Users1' => MWSModel::get_report_excel(),
                'Active Listings Report' => $tbl_sccatactivereport,
                'FBA Received Inventory Report' => $tbl_scfbareceiptreport,
                'FBA Inventory Health Report' => $tbl_scfbahealthreport
            ]);
            return (new FastExcel($sheets))->download($file_name);
        } elseif ($report_type == 'Sales') {
            $tbl_scfbareturnsreport = MWSModel::get_report_excel($start_date, $end_date, 'tbl_sc_sales_fba_returns_report');


            $tbl_scfbareturnsreport_count = count($tbl_scfbareturnsreport);

            if ($tbl_scfbareturnsreport_count == 0) {
                $tbl_scfbareturnsreport = '';
            }

            $tbl_scmfnreturnsreport = MWSModel::get_report_excel($start_date, $end_date, 'tbl_sc_sales_mfn_returns_report');

            $tbl_scmfnreturnsreport_count = count($tbl_scmfnreturnsreport);

            if ($tbl_scmfnreturnsreport_count == 0) {
                $tbl_scmfnreturnsreport = '';
            }
            $tbl_scordersupdtreport = MWSModel::get_report_excel($start_date, $end_date, 'tbl_sc_sales_orders_updt_report');

            $tbl_scordersupdtreport_count = count($tbl_scordersupdtreport);

            if ($tbl_scordersupdtreport_count == 0) {
                $tbl_scordersupdtreport = '';
            }
            $tbl_scordersreport = MWSModel::get_report_excel($start_date, $end_date, 'tbl_sc_sales_orders_report');
            // echo '<pre>';
            // print_r($tbl_scordersreport);
            $tbl_scordersreport_count = count($tbl_scordersreport);

            if ($tbl_scordersreport_count == 0) {
                $tbl_scordersreport = '';
            }

            $sheets = new SheetCollection([
                //'Users1' => MWSModel::get_report_excel(),
                'FBAReturnsReport' => $tbl_scfbareturnsreport,
                'ReturnsReportByReturnDate' => $tbl_scmfnreturnsreport,
                'OrdersReportByLastUpdate' => $tbl_scordersupdtreport,
                'OrdersReportByOrderDate' => $tbl_scordersreport
            ]);
            return (new FastExcel($sheets))->download($file_name);

        }

    }

    public function MWSReportRequest()
    {


        scSetMemoryLimitAndExeTime();
        $product_ids = MWSModel::get_sc_product_ids();

        foreach ($product_ids as $values) {

            Config::set('amazon-mws.store.store1.merchantId', trim($values->seller_id));
            //Config::set('amazon-mws.store.store1.marketplaceId', trim($api_parameter->marketplace_id));
            Config::set('amazon-mws.store.store1.marketplaceId', 'ATVPDKIKX0DER');
            Config::set('amazon-mws.store.store1.keyId', trim($values->mws_access_key_id));
            Config::set('amazon-mws.store.store1.secretKey', trim($values->mws_secret_key));
            Config::set('amazon-mws.store.store1.authToken', trim($values->mws_authtoken));

            $amz = new AmazonProductList("store1");
            /*
             $amz->setIdType('ASIN');
             $amz->setProductIds('B0029NYQFG');*/

            if ($values->idType == 'ASIN') {
                $product_id = $values->asin;
                $amz->setIdType('ASIN');
                $amz->setProductIds($values->asin);
                //$amz->setProductIds('B001EPQG2G');
            }
            /*if ($values->idType=='SellerSKU'){
                $amz->setIdType('SellerSKU');
                $amz->setProductIds($values->sku);
            }*/


            $products_list = $amz->fetchProductList();
            $products_details = $amz->getProduct();
            if (!empty($products_details)) {
                foreach ($products_details as $products_details_values) {

                    $product_data['parentAsinMarketplaceId'] = $products_details_values->data['Relationships']['VariationParent']['Identifiers']['MarketplaceASIN']['MarketplaceId'];

                    $product_data['parentAsin'] = $products_details_values->data['Relationships']['VariationParent']['Identifiers']['MarketplaceASIN']['ASIN'];


                    $product_data = array();
                    $product_data['fkPdocutTblId'] = $values->id;
                    $product_data['fkSellerConfigId'] = $values->fkSellerConfigId;
                    //$product_data['fkRequestId'] = $values->fkRequestId;
                    if (isset($products_details_values->data['Identifiers']['MarketplaceASIN']['MarketplaceId']) && !is_array($products_details_values->data['Identifiers']['MarketplaceASIN']['MarketplaceId'])) {
                        $product_data['marketplaceId'] = trim($products_details_values->data['Identifiers']['MarketplaceASIN']['MarketplaceId']) != '' ? $products_details_values->data['Identifiers']['MarketplaceASIN']['MarketplaceId'] : 'NA';
                    }
                    if (isset($products_details_values->data['Identifiers']['MarketplaceASIN']['ASIN']) && !is_array($products_details_values->data['Identifiers']['MarketplaceASIN']['ASIN'])) {
                        $product_data['asin'] = trim($products_details_values->data['Identifiers']['MarketplaceASIN']['ASIN']) != '' ? $products_details_values->data['Identifiers']['MarketplaceASIN']['ASIN'] : 'NA';
                    }
                    if (isset($products_details_values->data['AttributeSets'][0]['Binding']) && !is_array($products_details_values->data['AttributeSets'][0]['Binding'])) {
                        $product_data['Binding'] = trim($products_details_values->data['AttributeSets'][0]['Binding']) != '' ? $products_details_values->data['AttributeSets'][0]['Binding'] : 'NA';
                    }
                    if (isset($products_details_values->data['AttributeSets'][0]['Brand']) && !is_array($products_details_values->data['AttributeSets'][0]['Brand'])) {
                        $product_data['brand'] = trim($products_details_values->data['AttributeSets'][0]['Brand']) != '' ? $products_details_values->data['AttributeSets'][0]['Brand'] : 'NA';
                    }
                    if (isset($products_details_values->data['AttributeSets'][0]['Color']) && !is_array($products_details_values->data['AttributeSets'][0]['Color'])) {
                        $product_data['color'] = trim($products_details_values->data['AttributeSets'][0]['Color']) != '' ? $products_details_values->data['AttributeSets'][0]['Color'] : 'NA';
                    }
                    if (isset($products_details_values->data['AttributeSets'][0]['Department']) && !is_array($products_details_values->data['AttributeSets'][0]['Department'])) {
                        $product_data['department'] = trim($products_details_values->data['AttributeSets'][0]['Department']) != '' ? $products_details_values->data['AttributeSets'][0]['Department'] : 'NA';
                    }
                    if (isset($products_details_values->data['AttributeSets'][0]['ItemDimensions']['Height']) && !is_array($products_details_values->data['AttributeSets'][0]['ItemDimensions']['Height'])) {
                        $product_data['itemHeight'] = trim($products_details_values->data['AttributeSets'][0]['ItemDimensions']['Height']) != '' ? get_sc_decimel_value($products_details_values->data['AttributeSets'][0]['ItemDimensions']['Height']) : 0.00;
                    }
                    if (isset($products_details_values->data['AttributeSets'][0]['ItemDimensions']['Length']) && !is_array($products_details_values->data['AttributeSets'][0]['ItemDimensions']['Length'])) {
                        $product_data['itemLength'] = trim($products_details_values->data['AttributeSets'][0]['ItemDimensions']['Length']) != '' ? get_sc_decimel_value($products_details_values->data['AttributeSets'][0]['ItemDimensions']['Length']) : 0.00;
                    }
                    if (isset($products_details_values->data['AttributeSets'][0]['ItemDimensions']['Width']) && !is_array($products_details_values->data['AttributeSets'][0]['ItemDimensions']['Width'])) {
                        $product_data['itemWidth'] = trim($products_details_values->data['AttributeSets'][0]['ItemDimensions']['Width']) != '' ? get_sc_decimel_value($products_details_values->data['AttributeSets'][0]['ItemDimensions']['Width']) : 0.00;
                    }
                    if (isset($products_details_values->data['AttributeSets'][0]['ItemDimensions']['Weight']) && !is_array($products_details_values->data['AttributeSets'][0]['ItemDimensions']['Weight'])) {
                        $product_data['itemWeight'] = trim($products_details_values->data['AttributeSets'][0]['ItemDimensions']['Weight']) != '' ? get_sc_decimel_value($products_details_values->data['AttributeSets'][0]['ItemDimensions']['Weight']) : 0.00;
                    }
                    if (isset($products_details_values->data['AttributeSets'][0]['Label']) && !is_array($products_details_values->data['AttributeSets'][0]['Label'])) {
                        $product_data['itemLabel'] = trim($products_details_values->data['AttributeSets'][0]['Label']) != '' ? $products_details_values->data['AttributeSets'][0]['Label'] : 'NA';
                    }
                    if (isset($products_details_values->data['AttributeSets'][0]['ListPrice']['Amount']) && !is_array($products_details_values->data['AttributeSets'][0]['ListPrice']['Amount'])) {
                        $product_data['itemAmount'] = trim($products_details_values->data['AttributeSets'][0]['ListPrice']['Amount']) != '' ? $products_details_values->data['AttributeSets'][0]['ListPrice']['Amount'] : 0.00;
                    }
                    if (isset($products_details_values->data['AttributeSets'][0]['ListPrice']['CurrencyCode']) && !is_array($products_details_values->data['AttributeSets'][0]['ListPrice']['CurrencyCode'])) {
                        $product_data['currencyCode'] = trim($products_details_values->data['AttributeSets'][0]['ListPrice']['CurrencyCode']) != '' ? $products_details_values->data['AttributeSets'][0]['ListPrice']['CurrencyCode'] : 'NA';
                    }
                    if (isset($products_details_values->data['AttributeSets'][0]['Manufacturer']) && !is_array($products_details_values->data['AttributeSets'][0]['Manufacturer'])) {
                        $product_data['manufacturer'] = trim($products_details_values->data['AttributeSets'][0]['Manufacturer']) != '' ? $products_details_values->data['AttributeSets'][0]['Manufacturer'] : 'NA';
                    }

                    if (isset($products_details_values->data['AttributeSets'][0]['MaterialType']) && !is_array($products_details_values->data['AttributeSets'][0]['MaterialType'])) {
                        $product_data['materialType'] = trim($products_details_values->data['AttributeSets'][0]['MaterialType']) != '' ? $products_details_values->data['AttributeSets'][0]['MaterialType'] : 'NA';
                    }
                    if (isset($products_details_values->data['AttributeSets'][0]['Model']) && !is_array($products_details_values->data['AttributeSets'][0]['Model'])) {
                        $product_data['model'] = isset($products_details_values->data['AttributeSets'][0]['Model']) && trim($products_details_values->data['AttributeSets'][0]['Model']) != '' ? $products_details_values->data['AttributeSets'][0]['Model'] : 'NA';
                    }
                    if (isset($products_details_values->data['AttributeSets'][0]['NumberOfItems']) && !is_array($products_details_values->data['AttributeSets'][0]['NumberOfItems'])) {
                        $product_data['numberOfItems'] = trim($products_details_values->data['AttributeSets'][0]['NumberOfItems']) != '' ? $products_details_values->data['AttributeSets'][0]['NumberOfItems'] : 0;
                    }

                    if (isset($products_details_values->data['AttributeSets'][0]['PackageDimensions']['Height']) && !is_array($products_details_values->data['AttributeSets'][0]['PackageDimensions']['Height'])) {
                        $product_data['packageHeight'] = trim($products_details_values->data['AttributeSets'][0]['PackageDimensions']['Height']) != '' ? $products_details_values->data['AttributeSets'][0]['PackageDimensions']['Height'] : 0.00;
                    }
                    if (isset($products_details_values->data['AttributeSets'][0]['PackageDimensions']['Length']) && !is_array($products_details_values->data['AttributeSets'][0]['PackageDimensions']['Length'])) {
                        $product_data['packageLength'] = trim($products_details_values->data['AttributeSets'][0]['PackageDimensions']['Length']) != '' ? $products_details_values->data['AttributeSets'][0]['PackageDimensions']['Length'] : 0.00;
                    }
                    if (isset($products_details_values->data['AttributeSets'][0]['PackageDimensions']['Width']) && !is_array($products_details_values->data['AttributeSets'][0]['PackageDimensions']['Width'])) {
                        $product_data['packageWidth'] = trim($products_details_values->data['AttributeSets'][0]['PackageDimensions']['Width']) != '' ? $products_details_values->data['AttributeSets'][0]['PackageDimensions']['Width'] : 0.00;
                    }
                    if (isset($products_details_values->data['AttributeSets'][0]['PackageDimensions']['Weight']) && !is_array($products_details_values->data['AttributeSets'][0]['PackageDimensions']['Weight'])) {
                        $product_data['packageWeight'] = trim($products_details_values->data['AttributeSets'][0]['PackageDimensions']['Weight']) != '' ? $products_details_values->data['AttributeSets'][0]['PackageDimensions']['Weight'] : 0.00;
                    }
                    if (isset($products_details_values->data['AttributeSets'][0]['PackageQuantity']) && !is_array($products_details_values->data['AttributeSets'][0]['PackageQuantity'])) {
                        $product_data['packageQuantity'] = trim($products_details_values->data['AttributeSets'][0]['PackageQuantity']) != '' ? $products_details_values->data['AttributeSets'][0]['PackageQuantity'] : 0;
                    }
                    if (isset($products_details_values->data['AttributeSets'][0]['PartNumber']) && !is_array($products_details_values->data['AttributeSets'][0]['PartNumber'])) {
                        $product_data['partNumber'] = trim($products_details_values->data['AttributeSets'][0]['PartNumber']) != '' ? $products_details_values->data['AttributeSets'][0]['PartNumber'] : 'NA';
                    }
                    if (isset($products_details_values->data['AttributeSets'][0]['ProductGroup']) && !is_array($products_details_values->data['AttributeSets'][0]['ProductGroup'])) {
                        $product_data['productGroup'] = trim($products_details_values->data['AttributeSets'][0]['ProductGroup']) != '' ? $products_details_values->data['AttributeSets'][0]['ProductGroup'] : 'NA';
                    }
                    if (isset($products_details_values->data['AttributeSets'][0]['ProductTypeName']) && !is_array($products_details_values->data['AttributeSets'][0]['ProductTypeName'])) {
                        $product_data['productTypeName'] = trim($products_details_values->data['AttributeSets'][0]['ProductTypeName']) != '' ? $products_details_values->data['AttributeSets'][0]['ProductTypeName'] : 'NA';
                    }
                    if (isset($products_details_values->data['AttributeSets'][0]['Publisher']) && !is_array($products_details_values->data['AttributeSets'][0]['Publisher'])) {
                        $product_data['publisher'] = trim($products_details_values->data['AttributeSets'][0]['Publisher']) != '' ? $products_details_values->data['AttributeSets'][0]['Publisher'] : 'NA';
                    }

                    if (isset($products_details_values->data['AttributeSets'][0]['ReleaseDate']) && !is_array($products_details_values->data['AttributeSets'][0]['ReleaseDate'])) {
                        $product_data['releaseDate'] = trim($products_details_values->data['AttributeSets'][0]['ReleaseDate']) != '' ? $products_details_values->data['AttributeSets'][0]['ReleaseDate'] : '0000-00-00';
                    }
                    if (isset($products_details_values->data['AttributeSets'][0]['Size']) && !is_array($products_details_values->data['AttributeSets'][0]['Size'])) {
                        $product_data['size'] = trim($products_details_values->data['AttributeSets'][0]['Size']) != '' ? $products_details_values->data['AttributeSets'][0]['Size'] : 'NA';
                    }
                    if (isset($products_details_values->data['AttributeSets'][0]['SmallImage']['URL']) && !is_array($products_details_values->data['AttributeSets'][0]['SmallImage']['URL'])) {
                        $product_data['smallImageURL'] = trim($products_details_values->data['AttributeSets'][0]['SmallImage']['URL']) != '' ? $products_details_values->data['AttributeSets'][0]['SmallImage']['URL'] : 'NA';
                    }
                    if (isset($products_details_values->data['AttributeSets'][0]['SmallImage']['Height']) && !is_array($products_details_values->data['AttributeSets'][0]['SmallImage']['Height'])) {
                        $product_data['smallImageHeight'] = trim($products_details_values->data['AttributeSets'][0]['SmallImage']['Height']) != '' ? $products_details_values->data['AttributeSets'][0]['SmallImage']['Height'] : 0.00;
                    }
                    if (isset($products_details_values->data['AttributeSets'][0]['SmallImage']['Width']) && !is_array($products_details_values->data['AttributeSets'][0]['SmallImage']['Width'])) {
                        $product_data['smallImageWidth'] = trim($products_details_values->data['AttributeSets'][0]['SmallImage']['Width']) != '' ? $products_details_values->data['AttributeSets'][0]['SmallImage']['Width'] : 0.00;
                    }
                    if (isset($products_details_values->data['AttributeSets'][0]['Studio']) && !is_array($products_details_values->data['AttributeSets'][0]['Studio'])) {
                        $product_data['Studio'] = trim($products_details_values->data['AttributeSets'][0]['Studio']) != '' ? $products_details_values->data['AttributeSets'][0]['Studio'] : 'NA';
                    }
                    if (isset($products_details_values->data['AttributeSets'][0]['Title']) && !is_array($products_details_values->data['AttributeSets'][0]['Title'])) {
                        $product_data['title'] = trim($products_details_values->data['AttributeSets'][0]['Title']) != '' ? $products_details_values->data['AttributeSets'][0]['Title'] : 'NA';
                    } else {
                        $product_data['title'] = 'NA';
                    }
                    if (isset($products_details_values->data['AttributeSets'][0]['Warranty']) && !is_array($products_details_values->data['AttributeSets'][0]['Warranty'])) {
                        $product_data['warranty'] = trim($products_details_values->data['AttributeSets'][0]['Warranty']) != '' ? $products_details_values->data['AttributeSets'][0]['Warranty'] : 'NA';
                    } else {
                        $product_data['warranty'] = 'NA';
                    }
                    if (isset($products_details_values->data['Relationships']['VariationParent']['Identifiers']['MarketplaceASIN']['MarketplaceId']) && !is_array($products_details_values->data['Relationships']['VariationParent']['Identifiers']['MarketplaceASIN']['MarketplaceId'])) {
                        $product_data['parentAsinMarketplaceId'] = trim($products_details_values->data['Relationships']['VariationParent']['Identifiers']['MarketplaceASIN']['MarketplaceId']) != '' ? $products_details_values->data['Relationships']['VariationParent']['Identifiers']['MarketplaceASIN']['MarketplaceId'] : 'NA';

                    }
                    if (isset($products_details_values->data['Relationships']['VariationParent']['Identifiers']['MarketplaceASIN']['MarketplaceId']) && !is_array($products_details_values->data['Relationships']['VariationParent']['Identifiers']['MarketplaceASIN']['MarketplaceId'])) {
                        $product_data['parentAsin'] = trim($products_details_values->data['Relationships']['VariationParent']['Identifiers']['MarketplaceASIN']['ASIN']) != '' ? $products_details_values->data['Relationships']['VariationParent']['Identifiers']['MarketplaceASIN']['ASIN'] : 'NA';

                    }

                    $product_data['createdAt'] = date('Y-m-d H:i:s');

                    $result = MWSModel::insert_product_details($product_data);
                    unset($product_data);
                    if ($result) {
                        //echo 'inserted';
                        $storeArray['productDetailsDownloaded'] = 1;
                        $result_downloded_status = MWSModel::update_product_download_status($storeArray, $values->id);
                        if ($result_downloded_status) {
                            if (isset($products_details_values->data['SalesRankings']) && is_array($products_details_values->data['SalesRankings']) && !empty($products_details_values->data['SalesRankings'])) {
                                $sales_rank_array = $products_details_values->data['SalesRankings'];
                                $sales_rank_data = array();
                                $sales_rank_count = 1;
                                foreach ($sales_rank_array as $sales_rank_values) {
                                    $sales_rank_array = array();
                                    $sales_rank_array['fkPdocutTblId'] = $values->id;
                                    $sales_rank_array['fkSellerConfigId'] = $values->fkSellerConfigId;
                                    if (isset($products_details_values->data['Identifiers']['MarketplaceASIN']['ASIN']) && !is_array($products_details_values->data['Identifiers']['MarketplaceASIN']['ASIN'])) {
                                        $sales_rank_array['asin'] = trim($products_details_values->data['Identifiers']['MarketplaceASIN']['ASIN']) != '' ? $products_details_values->data['Identifiers']['MarketplaceASIN']['ASIN'] : 'NA';
                                    }
                                    if (isset($sales_rank_values['ProductCategoryId']) && !is_array($sales_rank_values['ProductCategoryId'])) {
                                        $sales_rank_array['productCategoryId'] = trim($sales_rank_values['ProductCategoryId']) != '' ? $sales_rank_values['ProductCategoryId'] : 'NA';
                                    }
                                    // $sales_rank_array['salesRank'] = $sales_rank_values['Rank'];
                                    if (isset($sales_rank_values['Rank']) && !is_array($sales_rank_values['Rank'])) {
                                        $sales_rank_array['salesRank'] = trim($sales_rank_values['Rank']) != '' ? $sales_rank_values['Rank'] : 'NA';
                                    }
                                    $sales_rank_array['salesRankCount'] = $sales_rank_count;
                                    $sales_rank_array['createdAt'] = date('Y-m-d H:i:s');
                                    $sales_rank_data[] = $sales_rank_array;
                                    unset($sales_rank_array);
                                }
                                if (!empty($sales_rank_data)) {
                                    $sales_rank_result = MWSModel::insert_product_sales_rank($sales_rank_data);
                                }
                                unset($sales_rank_data);
                            }
                        }


                    }
                }
            } else {
                $storeArray['productDetailsDownloaded'] = 2;
                MWSModel::update_product_download_status($storeArray, $values->id);
            }

        }
    }

    /* public function MWSGetReportRequestList(){
         Config::set('amazon-mws.store.store1.merchantId','A3MEMZVRRLCL7A');
         //Config::set('amazon-mws.store.store1.marketplaceId', trim($api_parameter->marketplace_id));
         Config::set('amazon-mws.store.store1.marketplaceId','ATVPDKIKX0DER');
         Config::set('amazon-mws.store.store1.keyId','AKIAI4VW7BAXJPB72M5A');
         Config::set('amazon-mws.store.store1.secretKey', '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');
         Config::set('amazon-mws.store.store1.authToken', 'amzn.mws.54b37aea-faaf-9487-9e3d-6c1ae65d4ab4');

         $amz = new AmazonProductInfo("store1");
         $amz->setASINs('B07G1D936C');
         $amz->fetchCategories();
         $categories=$amz->getProduct();
         echo '<pre>';
         print_r($categories);
        exit;
      }*/

    public function MWSGetReportRequestList()
    {
        scSetMemoryLimitAndExeTime();
        $product_ids = MWSModel::get_sc_product_ids_for_categories();
        $cat = array();
        foreach ($product_ids as $values) {
            Config::set('amazon-mws.store.store1.merchantId', trim($values->seller_id));
            //Config::set('amazon-mws.store.store1.marketplaceId', trim($api_parameter->marketplace_id));
            Config::set('amazon-mws.store.store1.marketplaceId', 'ATVPDKIKX0DER');
            Config::set('amazon-mws.store.store1.keyId', trim($values->mws_access_key_id));
            Config::set('amazon-mws.store.store1.secretKey', trim($values->mws_secret_key));
            Config::set('amazon-mws.store.store1.authToken', trim($values->mws_authtoken));

            $amz = new AmazonProductInfo("store1");
            /* echo $values->asin;
             exit;*/
            /*
             $amz->setIdType('ASIN');
             $amz->setProductIds('B0029NYQFG');*/
            if ($values->idType == 'ASIN') {
                if ($values->idType == 'ASIN') {
                    $amz->setASINs($values->asin);
                    //$amz->setASINs('B07G1D936C');
                }
                if ($values->idType == 'SellerSKU') {
                    $amz->setASINs($values->sku);
                }
                $amz->fetchCategories();
                $products_category_details = $amz->getProduct();
                if (isset($products_category_details[0]->data['Categories']) && is_array($products_category_details[0]->data['Categories'])) {
                    $category_array = end($products_category_details[0]->data['Categories']);
                    if (isset($category_array) && is_array($category_array)) {
                        $catagories = array();
                        $currentArray = $category_array;
                        $parent_value = 1;
                        doagain:
                        //if ($currentArray["ProductCategoryName"] != 'Categories') {
                        $tempCat = array(
                            "fkPdocutTblId" => $values->id,
                            "fkSellerConfigId" => $values->fk_seller_config_id,
                            //"fkRequestId" => $values->fkRequestId,
                            "asin" => $values->asin,
                            "productCategoryId" => $currentArray["ProductCategoryId"],
                            "productCategoryName" => $currentArray["ProductCategoryName"],
                            "categoryTreeSequence" => $parent_value,
                            "createdAt" => date('Y-m-d H:i:s')
                        );
                        array_push($cat, $tempCat);
                        $parent_value++;
                        // }
                        if (isset($currentArray["Parent"])) {
                            $currentArray = $currentArray["Parent"];
                            goto doagain;
                        }
                        $storeArray['productCategoryDetailsDownloaded'] = 1;
                        MWSModel::update_product_download_status($storeArray, $values->id);
                    }
                }
//exit;

            }
        }

        /*echo '<pre>';
        print_r($cat);*/
        if (isset($cat)) {
            $result = MWSModel::insert_product_category_details($cat);
        }
    }


    public function MWSGetReport()
    {

        $report_data = MWSModel::get_asin_from_reports();
        foreach ($report_data as $value) {
            $report_asins['fk_seller_config_id'] = $value->fk_merchant_id;
            //$report_asins['fkRequestId']=$value->fkRequestId;
            $report_asins['asin'] = $value->asin1;
            $report_asins['idType'] = 'ASIN';
            $report_asins['createdAt'] = date('Y-m-d H:i:s');
            $data[] = $report_asins;
            /*echo '<pre>';
            print_r($value);
            echo $value->asin1;
            echo '<br>';
            exit;*/
        }

        $report_data = MWSModel::insert_report_asin($data);
        /*echo '<pre>';
        print_r($data);
        exit;*/


    }


}
