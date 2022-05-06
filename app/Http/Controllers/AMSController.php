<?php

namespace App\Http\Controllers;

use Artisan;
use Session;
use GuzzleHttp\Client;
use App\Models\AMSModel;
use App\Models\LWAModel;
use App\Models\AMSApiModel;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Rap2hpoutre\FastExcel\FastExcel;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Validator;

class AMSController extends Controller
{
    public function dashboard()
    {
        $data['pageTitle'] = 'AMSDashboard';
        $data['pageHeading'] = 'Amazon Marketing Services Dashboard';
        $data['allData'] = AMSModel::getAllAMSDashboard();
        return view('subpages.ams.dashboard')->with($data);
    }

    /**
     * This function is used to Render API CONFIG Page
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function apiConfig()
    {
        $APIParametr = new AMSModel();
        $data['api_parameter'] = AMSApiModel::select(
            "id",
            "grant_type", 
            'refresh_token', 
            'client_id', 
            "client_secret",
            "created_at"
        )->orderBy("id", "desc")
        ->get();
        return $data;
    }

    /**
     * This is used to Store API Config Value
     * Only Call on POST Call
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function addConfig(Request $request)
    {
        $res = ["status"=>true, "validationStatus" => false];
        $validator = Validator::make($request->all(), [
            'client_id' => 'required|regex:/^amzn1\.application-oa2-client\.[a-z0-9]{32}$/i',
            'client_secret' => 'required|regex:/^[a-z0-9]{64}$/i',
            'refreshToken' => 'required',
        ]);
        if ($validator->fails())
        {
            // The given data did not pass validation
            $res["status"] = false;
            $res["validationStatus"] = true;
            $res["errors"] = $validator->messages();
            return $res;
        }

        $apiConfig = array();
        $apiConfig['client_id'] = $request->client_id;
        $apiConfig['client_secret'] = $request->client_secret;
        $apiConfig['refresh_token'] = $request->refreshToken;
        $getLastInsertedId = AMSModel::addRecord($apiConfig);
        $response = array();
        if ($getLastInsertedId) {
            $authCommandArray = array();
            $authCommandArray['fkConfigId'] = $getLastInsertedId;
            \Artisan::call('getaccesstoken:amsauth', $authCommandArray);
             Artisan::call('getprofileid:amsprofile');
            $response["status"] = true;
            $response["message"] = "Successfully API Parameter Added!";

        } else {
            $response["status"] = false;
            $response["message"] = "API Parameter not Added!";
        }
        return response()->json($response);
        //return redirect('/ams/apiconfig');
    }
    public function editConfig(Request $request){
        $res = ["status"=>true];
        $validator = Validator::make($request->all(), [
            'client_id' => 'required|regex:/^amzn1\.application-oa2-client\.[a-z0-9]{32}$/i',
            'client_secret' => 'required|regex:/^[a-z0-9]{64}$/i',
            'refreshToken' => 'required',
        ]);
        if ($validator->fails())
        {
            // The given data did not pass validation
            $res["status"] = false;
            $res["validationStatus"] = true;
            $res["errors"] = $validator->messages();
            return $res;
        }
        $config = AMSApiModel::find($request->id);
        if($config){
            $config->client_id = $request->client_id;
            $config->client_secret = $request->client_secret    ;
            $config->refresh_token = $request->refreshToken;
            $saveStat = $config->save();
            if($saveStat)
            {
                $authCommandArray = array();
                $authCommandArray['fkConfigId'] = $request->id;
                \Artisan::call('getaccesstoken:amsauth', $authCommandArray);
                Artisan::call('getprofileid:amsprofile');
                $res["message"] = "Configuration Updated Successfully";
            } else {
                $res["status"] = false;     
                $res["saveStat"] = $saveStat;     
                $res["message"] = "Not able to save configuration in database";
            }
        } else {
           $res["status"] = false;     
           $res["message"] = "No Such Configuration Found to Edit";
        }
        return $res;
    }
    public function deleteConfig(Request $request) {
        $config = AMSApiModel::find($request->id);
        $res = ["status"=>true];
        if($config && $config->delete()){
            $res["message"] = "Configuration Deleted Successfully";
        }else{
           $res["status"] = false;     
           $res["message"] = "Fail To Delete Configuration";
        }
        return $res;
    }
    // API Working
    public function runApi()
    {
        $client = new Client();
        $response = $client->request('GET', 'https://advertising-api.amazon.com/v1/reports/amzn1.clicksAPI.v1.p1.5E2983E7.7406e135-d2aa-4d9b-ba93-5eabd1e3d59f/download', [
            'headers' => [
                'Authorization' => 'Bearer ' . 'Atza|IwEBIAhc8bpE3G7WPq1M3XhMlSbm-Z3NE7tMCz7a1N-OIRdk7aZuM6WgNVuZkPcrabvSHhQ1H7p69YrSWzptAIzWWyfVCHOCh_5uuHn8fSP2xl_VKiFEJ9QJd3DwOmNkkFAc8Tb2ptJme6sumZpQWBuFPjO0P7q6vStdPbK_qPYqSEATTt91r4zgFzIjxs138B5jpYj7fnDw8mjIeBas338R0rKv9RFtrXpAWJNoC0Xv5DPrQoRf58KHxmVcgJBQgaD1RJk4Ye4rTylxt4emfbaVxqooN4JDJLk4JK3gGE47VbrsSceER4tHquqNfSgcog2RSlx69nZDyYezOxXokcThZnWAAymGVKzo45_xmhIG0Mij3v9qeLDUXKyo8AZ7_GcgqOXOwN3L8b9eag3lJ8tv8PhmzDqxbKFkxTyF0tc_OX-h11751FILNq78IrR6PVZHPECfOCPOT_Gsan8zXbCKdMg8O_9EBWhpCfXxWR6PzDY9eA2RnHDNotSLHhNhPCJQrFBSxDA5wL0U5a3D4-1p3kNyfWDc674grRQ7NobaCRmyEcHg3aKOpSoW08SwTg3vMeRxnHqg6CNISWIPmEXiYJHd',
                'Amazon-Advertising-API-ClientId' => 'amzn1.application-oa2-client.71a9af3683d247449d06e1982a114496',
                'Amazon-Advertising-API-Scope' => '4244459731657723'],
            'delay' => Config::get('constants.delayTimeInApi'),
            'connect_timeout' => Config::get('constants.connectTimeOutInApi'),
            'timeout' => Config::get('constants.timeoutInApi'),
        ]);
        $body = json_decode(gzdecode($response->getBody()->getContents()));
        dd($body);
        $reportDateSingleDay = date('Ymd', strtotime('-1 day', time()));
        $client = new Client();
        $response = $client->request('GET', 'https://advertising-api.amazon.com/v1/reports/amzn1.clicksAPI.v1.p1.5E2975FC.11a50d9f-80a7-425f-aaa1-4a60462fa9c1/download', [
            'headers' => [
                'Authorization' => 'Bearer ' . 'Atza|IwEBIN0FZPnMiO56VthVWIcDI1tqAn9bR7LnCiKpAhHOQjt35PrqfhivVi7jeqegsHv0j1eC5yQenJT0or4sZAPR6-wGqFBSiREFXxy7yzgxXmFMAK0jN8eNpdpRKC58hfI03TMQWl8g40zfcTDknLHuJaJMmpMpL061ESkTb6ZgOyk4V_M-dIumWyhWzB1gtop0X0B3ftHJG9yM8b-NDtnPFnK9dHICcV2B6MGVIm6O36BymrfnDzzbV5SMrpyfKPlUUrlZUdg1rVvPSUfEySuosL8G6JiESIbmjddckK-KOeV7bDpjFGZwSzkUTn39GgoBanBJm9c2ksFcErr0-NNRnlHq46ihIOyR19Mw74sI8CF6Jmc5Wy9dvLL15Ks-vihmX8odzTNX9Gcp9B-a80_vRY0QVcTXgdkysUylPnl1OnqPBvjZYPEgOW5wh1fDsqYrJ8YdSCYqUheh655GapGFIl-5Wv9V-orYnYpzM6d2tE3puVYYQqL8EejGf-D8ho0gxSpWSA8ctOQ8QfgTjdPYo0eH9Usy50jJxecDzJyBZe3HVK69hpmlmQ1brC5oXd79qvZmVTu3OsNGvcG7V60D7XWbxphWXoVB2DzBsFG0lIYHCg',
                'Content-Type' => 'application/json',
                'Amazon-Advertising-API-ClientId' => 'amzn1.application-oa2-client.71a9af3683d247449d06e1982a114496',
                'Amazon-Advertising-API-Scope' => '4244459731657723'],
            'delay' => Config::get('constants.delayTimeInApi'),
            'connect_timeout' => Config::get('constants.connectTimeOutInApi'),
            'timeout' => Config::get('constants.timeoutInApi'),
        ]);
        $body = json_decode($response->getBody()->getContents());
        dd($body);

//        $checkWeeklyStatus = array(0);
//        $checkWeeklyStatus[0] = new \stdClass();
//        $checkWeeklyStatus[0]->count = 0; // default value 0 means weekly table empty
//        $DB2 = 'mysqlDb2'; // layer 1 BI database
//        $checkWeeklyStatus = \DB::connection($DB2)->select('SELECT COUNT(1) as count WHERE EXISTS (SELECT * FROM `tbl_cat_subcat_asin_vew_weekly`)');
////        $checkMonthlyStatus = \DB::connection($DB2)->select('SELECT COUNT(1) as count WHERE EXISTS (SELECT * FROM `tbl_cat_subcat_asin_vew_monthly`)');
//        echo '<pre>';
//        print_r($checkWeeklyStatus);
////        print_r($checkMonthlyStatus);
//        exit;
        $body = array();
        $code = '9900133';
        $params = [
            'query' => [
                'client_id' => 'amzn1.application-oa2-client.71a9af3683d247449d06e1982a114496',
                'scope' => 'cpc_advertising:campaign_management',
                'response_type' => 'code',
                'redirect_uri' => 'https://dev.helo-tek.com/amazonlogin/' . $code,
            ]
        ];
        // Create a client with a base URI
        $url = 'https://www.amazon.com/ap/oa';
//        try {
        $client = new Client();
        $response = $client->request('GET', $url, $params);
        $body = json_decode($response->getBody()->getContents());
        Log::error(json_encode($body));
        redirect('/ams/apiconfig');
        if (!empty($body) && $body != null) {
            print_r($body);
            dd('found');
        } else {
            dd('not record found');
        }
//        } catch (\Exception $ex) {
//            print_r($ex->getMessage());
//            dd($ex->getMessage());
//            Log::error($ex->getMessage());
//        }
    }

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function showHistoryFrom()
    {
        $data['pageTitle'] = 'AMS Export CSV';
        $data['pageHeading'] = 'History';
        return view("subpages.ams.historical_data_retrieval")->with($data);
    }

    /**
     * @param Request $request
     * @return array
     */
    public function checkHistory(Request $request)
    {
        $response = array();
        $separetDateRange = explode(' - ', $request->daterange);
        $startDate = $separetDateRange[0];
        $endDate = $separetDateRange[1];
        $reportType = $request->reporttype;
        $response["title"] = str_replace('_', ' ', $reportType);
        $Object = new AMSModel();
        $getDataFromDB = $Object->checkDataFromDB($startDate, $endDate, $reportType);
        if ($getDataFromDB) {
            $response["status"] = true;
            $response["url"] = url('ams-download/' . $reportType . '/' . $startDate . '/' . $endDate);
            $response["message"] = "Please click down there to download file.";
        } else {
            $response["status"] = false;
            $response["message"] = "No Data Found against This Date";
        }
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
    public function downloadCSV($reportType, $startDate, $endDate)
    {
        ini_set('max_execution_time', 0);
        ini_set("memory_limit", "2048M");
        $Object = new AMSModel();
        $getDataFromDB = $Object->getDataFromDB($startDate, $endDate, $reportType);
        return (new FastExcel($getDataFromDB))->download($reportType . '-' . $startDate . '-' . $endDate . '.csv');
    }

    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public function scheduling()
    {
        $ResponseArray = AMSModel::getAMSCronList();
        $data = array();
        if(!$ResponseArray){
            $data['CronListData'] = '';
        }else{
            $returnValue = collect($ResponseArray)->map(function ($cronlist){
                return [
                    'id' =>  $cronlist->id,
                    'cronType' =>  str_replace("_"," ",$cronlist->cronType),
                    'cronTime' =>  $cronlist->cronTime,
                    'cronStatus' =>  $cronlist->cronStatus,
                    'lastRun'=>  $cronlist->lastRun,
                    'modifiedDate'=>  $cronlist->modifiedDate,
                    'cronRun' =>  $cronlist->cronRun,
                    'nextRunTime' =>  $cronlist->nextRunTime
                ];
            });
            $data['CronListData']= $returnValue ;
        }
        return response()->json($data);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function CronCall(Request $request)
    {
        $response = array();
        $validator = Validator::make($request->all(), [
            "crontype" => 'required',
            'crontime' => 'required',
            'cronstatus' => 'required',
        ]);
        $type = $request->input('crontype');
        $cronstatus = $request->input('cronstatus');
        $titleTypeName = str_replace('_', ' ', $type);
        if ($validator->fails()) {
            $response = array(
                'status' => false,
                'title' => $titleTypeName,
                'message' => $validator->errors()
            );
        } else {
            $RequestData = $request->all();
            // Store Cron record into DB
            $response = AMSModel::addCronTimeStatus($RequestData);
            if($response == false && !empty($response)){
                $response = array(
                    'status' => false,
                    'title' => $titleTypeName,
                    'message' => ''
                );
            }else{
                $message = '';
                if($cronstatus == 'stop'){
                    $message = 'Report stopped successfully';
                }elseif($cronstatus == 'run'){
                    $message = 'Report ran successfully';
                }
                $response = array(
                    'status' => true,
                    'title' => $titleTypeName,
                    'message' => $message
                );
            }
        }
        return response()->json($response);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function AmazonLogin(Request $request)
    {
        $message = '';
        $class = '';
        try {
            $access_token = $request['access_token'];
            $urlTokenValid = 'https://api.amazon.com/auth/o2/tokeninfo?access_token=' . $access_token;
            $urlProfile = 'https://api.amazon.com/user/profile';
            $clientId = 'amzn1.application-oa2-client.c9f64774daa347ad8f741984216ace51';
            // Get Response CURL call
            $client = new Client();
            $responseToken = $client->request('GET', $urlTokenValid, [
                'headers' => [
                    'Content-Type' => 'application/json'],
                'delay' => Config::get('constants.delayTimeInApi'),
                'connect_timeout' => Config::get('constants.connectTimeOutInApi'),
                'timeout' => Config::get('constants.timeoutInApi'),
            ]);
            $bodyToken = json_decode($responseToken->getBody()->getContents());
            Log::info('Token Detail :' . json_encode($bodyToken));
            if (!empty($bodyToken)) {
                if ($bodyToken->exp == 0) {
                    Session::flash('lwamessage', 'Your Session Expired');
                    Session::flash('alert-class', 'alert-danger');
                    return redirect()->route('amsApiConfig');
                }
                if ($bodyToken->aud != $clientId) {
                    // the access token does not belong to us
                    Session::flash('lwamessage', 'Your Client Not map With it.');
                    Session::flash('alert-class', 'alert-danger');
                    return redirect()->route('amsApiConfig');
                }
                if ($bodyToken->exp > 0) {
                    $dataArray = array(
                        'aud' => $bodyToken->aud,
                        'user_id' => $bodyToken->user_id,
                        'iss' => $bodyToken->iss,
                        'exp' => $bodyToken->exp,
                        'app_id' => $bodyToken->app_id,
                        'iat' => $bodyToken->iat,
                        'createdAt' => date('Y-m-d H:i:s'),
                        'updatedAt' => date('Y-m-d H:i:s')
                    );
                    LWAModel::storeTokenDetailData($dataArray);
                }
                // exchange the access token for user profile
                $responseProfile = $client->request('GET', $urlProfile, [
                    'headers' => [
                        'Authorization' => 'Bearer ' . $access_token,
                        'Content-Type' => 'application/json'],
                    'delay' => Config::get('constants.delayTimeInApi'),
                    'connect_timeout' => Config::get('constants.connectTimeOutInApi'),
                    'timeout' => Config::get('constants.timeoutInApi'),
                ]);
                $bodyProfile = json_decode($responseProfile->getBody()->getContents());
                Log::info('Profile Detail :' . json_encode($bodyToken));
                $dataProfileArray = array(
                    'user_id' => $bodyProfile->user_id,
                    'name' => $bodyProfile->name,
                    'email' => $bodyProfile->email,
                    'createdAt' => date('Y-m-d H:i:s'),
                    'updatedAt' => date('Y-m-d H:i:s')
                );
                $response = LWAModel::storeProfileDetailData($dataProfileArray);
                if ($response['status'] == 'true') {
                    $class = 'alert-success';
                    $message = 'Thank you for your login!. Your ' . $bodyProfile->user_id . ' , ' . $bodyProfile->name . ' and ' . $bodyProfile->email . ' stored. session time:' . $bodyToken->exp;
                } else if ($response['status'] == 'already') {
                    $class = 'alert-success';
                    $message = 'Thank you for your login!. Your ' . $bodyProfile->user_id . ' , ' . $bodyProfile->name . ' and ' . $bodyProfile->email . ' stored. session time:' . $bodyToken->exp;
                } else if ($response['status'] == 'false') {
                    $class = 'alert-danger';
                    $message = 'record not insert in DB.';
                }
            }
            Session::flash('lwamessage', $message);
            Session::flash('bodyToken', json_encode($bodyToken));
            Session::flash('bodyProfile', json_encode($bodyProfile));
            Session::flash('alert-class', $class);
            return redirect()->route('amsApiConfig');
        } catch (\Exception $ex) {
            Session::flash('lwamessage', $ex->getMessage());
            Session::flash('alert-class', 'alert-danger');
            return redirect()->route('amsApiConfig');
        }
    }

    public function AccountSetup(Request $request)
    {
        dd($request);
    }

    public function AmazonLogin1($code = NULL, Request $request)
    {
        dd($request->all());
        if ($code != NULL) {
            try {
                $codeValues = $request->code;
                $post_data = ['grant_type' => 'authorization_code',
                    'refresh_token' => '',
                    'code' => $codeValues,
                    'redirect_uri' => 'https://devapi.helo-tek.com/ams/amazonlogin/9900133',
                    'client_id' => 'amzn1.application-oa2-client.c9f64774daa347ad8f741984216ace51',
                    'client_secret' => '80eae1ed9301705920b250c3faa4a3277c03bca24c680df20ed8e8e5a1c7396e'];
                // Get Response CURL call
                $client = new Client();
                $response = $client->request('POST', 'https://api.amazon.com/auth/o2/token', [
                    'headers' => ['Content-Type' => 'application/x-www-form-urlencoded',
                        'charset' => 'UTF-8'],
                    'form_params' => $post_data,
                    'delay' => Config::get('constants.delayTimeInApi'),
                    'connect_timeout' => Config::get('constants.connectTimeOutInApi'),
                    'timeout' => Config::get('constants.timeoutInApi'),
                ]);
                $body = json_decode($response->getBody()->getContents());
                Log::info('Login With Amazon');
                Log::info(json_encode($body));
                // send call to get access token
                $url = Config::get('constants.amsAuthUrl');
                $post_data_auth = ['grant_type' => 'refresh_token',
                    'refresh_token' => $body->refresh_token,
                    'client_id' => 'amzn1.application-oa2-client.c9f64774daa347ad8f741984216ace51',
                    'client_secret' => '80eae1ed9301705920b250c3faa4a3277c03bca24c680df20ed8e8e5a1c7396e'];
                // Get Response CURL call
                $responseAuth = $client->request('POST', $url, [
                    'headers' => ['Content-Type' => 'application/x-www-form-urlencoded'],
                    'form_params' => $post_data_auth,
                    'delay' => Config::get('constants.delayTimeInApi'),
                    'connect_timeout' => Config::get('constants.connectTimeOutInApi'),
                    'timeout' => Config::get('constants.timeoutInApi'),
                ]);
                $bodyAuth = json_decode($responseAuth->getBody()->getContents());
                echo '<pre>';
                echo 'get authorization code' . '<br/>';
                print_r($bodyAuth->access_token);
                if (!empty($bodyAuth)) {
                    try {
                        // verify that the access token belongs to us
                        $url = 'https://api.amazon.com/auth/o2/tokeninfo?access_token=' . urlencode($body->access_token);
                        $clientVerify = new Client();
                        $responseVerify = $clientVerify->request('GET', $url, [
                            'headers' => [
                                'Content-Type' => 'application/json'],
                            'delay' => Config::get('constants.delayTimeInApi'),
                            'connect_timeout' => Config::get('constants.connectTimeOutInApi'),
                            'timeout' => Config::get('constants.timeoutInApi'),
                        ]);
                        $bodyVerify = json_decode($responseVerify->getBody()->getContents());
                        if ($bodyVerify->aud != 'amzn1.application-oa2-client.71a9af3683d247449d06e1982a114496') {
                            // the access token does not belong to us
                            header('HTTP/1.1 404 Not Found');
                            echo 'Page not found';
                            exit;
                        }
                        Log::info('the access token does not belong to us');
                        Log::info(json_encode($bodyVerify));
                        // exchange the access token for user profile
                        $url = 'https://advertising-api.amazon.com/v2/profiles';
                        $clientProfile = new Client();
                        $responseProfile = $clientProfile->request('GET', $url, [
                            'headers' => [
                                'Authorization' => 'Bearer ' . $bodyAuth->access_token,
                                'Amazon-Advertising-API-ClientId' => 'amzn1.application-oa2-client.71a9af3683d247449d06e1982a114496',
                                'Content-Type' => 'application/json'],
                            'delay' => Config::get('constants.delayTimeInApi'),
                            'connect_timeout' => Config::get('constants.connectTimeOutInApi'),
                            'timeout' => Config::get('constants.timeoutInApi'),
                        ]);
                        $bodyProfile = json_decode($responseProfile->getBody()->getContents());
                        Log::info('exchange the access token for user profile');
                        Log::info(json_encode($bodyProfile));
                        $request->session()->flash('message', 'Successfully Added.');
                        return redirect('/ams/apiconfig');
                        echo '<br>';
                        echo '21312';
                        exit;
                    } catch (\Exception $ex) {
                        echo 'get profile information';
                        dd($ex);
                    }
                } else {
                    dd('not found data.');
                }
            } catch (\Exception $ex) {
                dd($ex);
            }
        }
    }
}