<?php

namespace App\Http\Controllers;

use App\Models\VCModel;
use App\Models\VCScrapModels\VCProductCatalogScrap;
use App\Models\ScrapingModels\ProxyModel;
use DOMDocument;
use DOMXPath;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Controllers\VCScrapControllerl;
use Google2FA;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;


class VCScrapController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth');
    }

    public function scrapCatalogView()
    {
        $data['pageTitle'] = 'Scrap Catalog';
        $data['pageHeading'] = 'Scrap Product Catalog';

        return view('subpages.vc.scrapcatalog.scrapcatalog')->with($data);
    }

    public function scrapCatalogStore()
    {
        Log::info('Good bye Scraping Paused');
        echo 'good bye :(';
        exit;

        // Set Memory Limit And Execution Time
        setMemoryLimitAndExeTime();
        $proxyRow = self::initProxy();
        $proxy = $proxyRow->proxy_ip;
        $proxyAuth = $proxyRow->proxy_auth;
        $loginData = NULL;
        //$loginData = isLoggedIn(VENDOR_DOMAIN, NULL, $proxy, $proxyAuth);

        //if($loginData['status']  == FALSE) {
        //
        $loginData = doVendorLogin(VENDOR_DOMAIN, $proxy, $proxyAuth);
        //}
        echo 'Login STATUS '. $loginData['status']. PHP_EOL;
        if ($loginData['status'] == TRUE){
            echo 'Login STATUS TRUE '. PHP_EOL;
            echo $getAllVendorListUrl = 'https://vendorcentral.amazon.com//hz/vendor/members/vendor-group-switcher-data/resource';
            echo PHP_EOL;
            $vendorListHtml = getContent($getAllVendorListUrl, NULL, $proxy, $proxyAuth, VENDOR_DOMAIN);
            File::put(public_path('vc/vendor.html'), $vendorListHtml['html']);
            $vendorDashboardDataPath = public_path('vc/vendorDashboard.html');
            if (File::exists($vendorDashboardDataPath)){
                $getDashboardData =    File::get($vendorDashboardDataPath);
                $getCustomerId = getCustomerId($getDashboardData);
                $vendorListPath = public_path('vc/vendor.html');
                if (File::exists($vendorListPath)){
                    $getVendorList =    File::get($vendorListPath);
                    $vendorList = json_decode($getVendorList);
                    $vendorListStoreDb = [];
                    foreach ($vendorList as $snVen => $value){
                        $dbData = [];
                        $dbData['customerId'] = $getCustomerId;
                        $dbData['vendorGroupId'] = json_decode($value)->vendorGroupId;
                        $dbData['marketscopeId'] = json_decode($value)->marketscopeId;
                        $dbData['businessName'] = json_decode($value)->businessName;
                        $dbData['url'] = 'https://vendorcentral.amazon.com/hz/vendor/members/user-management/switch-accounts-checker?vendorGroup='.json_decode($value)->vendorGroupId.'&customerId='.$getCustomerId;
                        $dbData['isScraped'] = 0;
                        $dbData['created_at'] = date('Y-m-d h:i:s');
                        array_push($vendorListStoreDb, $dbData);
                    }
                    VCProductCatalogScrap::insertScrapVendorsList($vendorListStoreDb);
                    File::delete($vendorListPath);
                }
                //File::delete($vendorDashboardDataPath);
                $getAllDbVendors = VCProductCatalogScrap::getAllScrapVendorsList();
                $totalNoProductsToScrap = 0;
                foreach ($getAllDbVendors as $vendor){
                    // Delete Previously Record of specific Vendor
                    VCProductCatalogScrap::deleteRecordsOfSpecificVendor($vendor->vendorGroupId);
                    sleep(5);
                    //$selectedVendorUrl = $vendor->url;
                    echo PHP_EOL.'Selected Url = ';
                    echo $selectedVendorUrl = $vendor->url;
                    Log::info('selectedVendorUrl ='. $selectedVendorUrl);
//                    echo $selectedVendorUrl = 'https://vendorcentral.amazon.com/hz/vendor/members/user-management/switch-accounts-checker?vendorGroup=4359430&customerId=A3TJA2CKN7MZ29';
                    echo PHP_EOL;
                    // Switch Vendor
                    getContent($selectedVendorUrl, NULL, $proxy, $proxyAuth, VENDOR_DOMAIN);

                    // Product Catalog Page Url
                    $scrapDataUrl = 'https://vendorcentral.amazon.com/hz/vendor/members/products/mycatalog?ref_=vc_ven-ven-home_subNav';
                    $data2 = getContent($scrapDataUrl, NULL, $proxy, $proxyAuth, VENDOR_DOMAIN);
                    if (stripos($data2['html'], 'id="logout_topRightNav"') !== FALSE){
                        echo 'logged in go ahead';

                    } else{
                        $proxyRow = self::initProxy();
                        $proxy = $proxyRow->proxy_ip;
                        $proxyAuth = $proxyRow->proxy_auth;
                        sleep(30);
                        $isAuthorized = self::notAuthorized($proxy, $proxyAuth);
                        if ($isAuthorized['status'] == TRUE){
                            // Product Catalog Page Url
                            $scrapDataUrl = 'https://vendorcentral.amazon.com/hz/vendor/members/products/mycatalog?ref_=vc_ven-ven-home_subNav';
                            $data2 = getContent($scrapDataUrl, NULL, $proxy, $proxyAuth, VENDOR_DOMAIN);
                        }
                    }

                    // Check this vendor has catalog ( For Info : There are some vendor who does not have catalogs but in future they might have)
                    if (stripos($data2['html'], 'id="mycat-filters-form"') !== FALSE){
                        File::put(public_path('vc/data2.html'), $data2);
                        echo 'Data 2';
                        Log::info('Data 2 ');
                        Log::info('Scrap Data Url = '. $scrapDataUrl);

                        // Post Data token, PageSize etc..
                        $postData = array();
                        $postData = getFormHiddenElements($data2['html']);
                        $postData['pageSize'] = $limit = 100;
                        $offset = 0;

                        $catalogRefer = 'https://vendorcentral.amazon.com/hz/vendor/members/products/mycatalog';

                        echo 'Post Curl To send Data with offset = '.$offset.PHP_EOL;
                        Log::info('Post Curl To send Data with offset='.$offset);

                        $requestHeaders = getCatalogHeaders($catalogRefer);
                        $postCurlUrlToSendData = 'https://vendorcentral.amazon.com/hz/vendor/members/products/mycatalog/ajax/query?offset='.$offset;
                        $response = postCurlRequestForScrapCatalog($postCurlUrlToSendData, $catalogRefer, $postData, $requestHeaders, $proxy, $proxyAuth);
                        // Put html from curl Response in file
                        File::put(public_path('vc/catalogScript.html'), $response['html']);
                        $content = File::get(public_path('vc/catalogScript.html'));

                        // Total No of Products E.g 10000 total products
                        $totalNoProductsToScrap = getTotalNoPagesCatalog($content);

                        echo "No Of Product To Scrap = ". $totalNoProductsToScrap.PHP_EOL;
                        Log::info('No Of Product To Scrap = '.$totalNoProductsToScrap);
                        $storeCatalogData = getScrapCatalogData($content, $vendor->vendorGroupId, $offset);

                        // Insertion In Temp Table
                        VCProductCatalogScrap::insertTmpScrapCatalog($storeCatalogData);
                        Log::info('Insertion offset'. $offset .'Done = ');

                        $filePathToDelete = public_path('vc/catalogScript.html');
//                    if(File::exists($filePathToDelete)) {
//                        File::delete($filePathToDelete);
//                    }

                        if($totalNoProductsToScrap > 100 ){

                            $offset = 100;
                            $isAuthorized = array();
                            do {
                                $requestHeaders = getCatalogHeaders($catalogRefer);
                                $postCurlUrlToSendData = 'https://vendorcentral.amazon.com/hz/vendor/members/products/mycatalog/ajax/query?offset='.$offset;
                                echo 'Post Curl To send Data with offset='.$offset.PHP_EOL;
                                Log::info('Post Curl To send Data with Normal Condition offset='.$offset);
                                usleep(300);

                                $response = postCurlRequestForScrapCatalog($postCurlUrlToSendData, $catalogRefer, $postData, $requestHeaders, $proxy, $proxyAuth);
                                if (is_null($response['error_text'])){
                                    echo 'Does not have Error Text ';
                                    // If Session Expired
                                    if (strpos($response['html'], 'Not authorized for resource') !== false){
                                        echo 'Not authorized for resource '.PHP_EOL;
                                        File::put(public_path('vc/elseIf.html'), $response['html']);

                                        Log::info('Session Expire');
                                        sleep(40);
                                        $proxyRow = self::initProxy();
                                        $proxy = $proxyRow->proxy_ip;
                                        $proxyAuth = $proxyRow->proxy_auth;
                                        $isAuthorized = self::notAuthorized($proxy, $proxyAuth);
                                        echo 'Authorization Check '.PHP_EOL;

                                        File::put(public_path('vc/isAuthorized.html'), $isAuthorized['html']);
                                        // If Authorization Passed during expiry of Session
                                        if ($isAuthorized['status'] == TRUE){
                                            echo 'Authorization Passed '.PHP_EOL;
                                            Log::info('Authorization passed');
                                            $getLastOffset = VCProductCatalogScrap::getLastOffsetToContinue();
                                            $offset = $getLastOffset->offset + 100;
                                            // Product Catalog Page
                                            $scrapDataUrl = 'https://vendorcentral.amazon.com/hz/vendor/members/products/mycatalog?ref_=vc_ven-ven-home_subNav';
                                            $data3 = getContent($scrapDataUrl, NULL, $proxy, $proxyAuth, VENDOR_DOMAIN);
                                            // check this vendor has catalog ( For Info : There are some vendor who does not have catalogs but in future they might have)
                                            if (stripos($data3['html'], 'id="mycat-filters-form"') !== FALSE){
                                                Log::info('Data 3 ');
                                                File::put(public_path('vc/data3.html'), $data3);
                                                //$isAuthorized = self::notAuthorized();
                                                Log::info('Scrap Data Url = '. $scrapDataUrl);

                                                // Post Data token, PageSize etc..
                                                $postData = array();
                                                $postData = getFormHiddenElements($data3['html']);
                                                $postData['pageSize'] = $limit = 100;


                                                $requestHeaders = getCatalogHeaders($catalogRefer);
                                                echo $postCurlUrlToSendData = 'https://vendorcentral.amazon.com/hz/vendor/members/products/mycatalog/ajax/query?offset='.$offset;
                                                echo 'Post Curl To send Data after authorization offset='.$offset.PHP_EOL;
                                                Log::info('Post Curl To send Data with offset='.$offset);


                                                $response = postCurlRequestForScrapCatalog($postCurlUrlToSendData, $catalogRefer, $postData, $requestHeaders, $proxy, $proxyAuth);
                                                File::put(public_path('vc/authorization-passed.html'), $response['html']);
                                                $content = File::get(public_path('vc/authorization-passed.html'));

                                                $storeCatalogData = getScrapCatalogData($content, $vendor->vendorGroupId, $offset);

                                                VCProductCatalogScrap::insertTmpScrapCatalog($storeCatalogData);
                                                Log::info('Insertion offset'. $offset .'Done in both tables = ');
                                                $offset = $offset + $limit;
                                                $filePathToDelete = public_path('vc/authorization-passed.html');

                                                /*                                       if(File::exists($filePathToDelete)) {
                                                                                           File::delete($filePathToDelete);
                                                                                       }*/
                                            }
                                        }else{
                                            echo 'Authorization failed '.PHP_EOL;
                                            Log::info('Authorization failed');
                                        }

                                    } else  if (strpos($response['html'], 'There was an error processing your') !== false){ // If there is processing datatables Errors
                                        echo 'processing datatables Errors'.PHP_EOL;
                                        Log::info('processing datatables Errors');
                                        File::put(public_path('vc/processor-error.html'), $response['html']);
                                        $offset = $offset + $limit;

                                    }else{ // Run normal Execution
                                        Log::info('Run Normal Execution Last Else condition = TRUE ');
                                        File::put(public_path('vc/normal-execution.html'), $response['html']);
                                        $content = File::get(public_path('vc/normal-execution.html'));

                                        $storeCatalogData = getScrapCatalogData($content, $vendor->vendorGroupId, $offset);

                                        VCProductCatalogScrap::insertTmpScrapCatalog($storeCatalogData);
                                        Log::info('Insertion offset'. $offset .'Done in both tables = ');
                                        $offset = $offset + $limit;
                                        $filePathToDelete = public_path('vc/normal-execution.html');
//                                    if(File::exists($filePathToDelete)) {
//                                        File::delete($filePathToDelete);
//                                    }
                                    }
                                }else{
                                    Log::info('Error Text ='. $response['error_text']);
                                }
                            } while ($offset < $totalNoProductsToScrap);    // End Of Do while Loop

                        } // If loop Ends
                        $vendorScrapedFlag['isScraped'] = 1;
                        // Insertion In Temp Table
                        VCProductCatalogScrap::updateScrapVendorList($vendor->id, $vendorScrapedFlag);
                    } // End condition for checking the catalogs page

                } // foreach Loop Ends
            }else{
                Log::info('Dashboard html Not Found! Check Permissions' );
            }

        }else{
            echo "something went wrong";
            Log::info('Something went wrong kindly check again!' );
        }

    }

    public static function initProxy(){
        $proxy = ProxyModel::getRandom();
        if($proxy !="NA"){
            return $proxy[0];
        }else{
            Log::info('no Proxy Available Kindly upload proxies to database');
            dd("No Proxy Avalialble");
        }

    }

    public static function notAuthorized($proxy, $proxyAuth){
        $loginData = doVendorLogin(VENDOR_DOMAIN, $proxy, $proxyAuth);
        return $loginData;
    }
}
