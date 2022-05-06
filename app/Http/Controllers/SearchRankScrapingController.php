<?php

namespace App\Http\Controllers;

use DOMXPath;
use DOMDocument;
use App\Models\FailStatus;
use Illuminate\Http\Request;
use App\Libraries\ScraperConstant;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\File;
use Rap2hpoutre\FastExcel\FastExcel;
use App\Libraries\ScrapingController;
use Illuminate\Support\Facades\Validator;
use App\Models\ScrapingModels\GeneralModel;
use App\Models\ScrapingModels\SettingsModel;
use App\Models\ScrapingModels\DepartmentModel;
use App\Models\ScrapingModels\SearchTermModel;
use App\Models\ScrapingModels\TempSearchRankModel;
use App\Models\ScrapingModels\ActivityTrackerModel;
use App\Models\ScrapingModels\SearchRankCrawlerModel;
use App\Models\ScrapingModels\SearchRankFailStatuses;
use App\Models\ScrapingModels\SearchRankScrapedResultModel;

class SearchRankScrapingController extends Controller
{
    public  $totalPages;
    public $q_id;
    public function __construct()
    {
        //$this->middleware('auth');
        $this->middleware('auth.super_admin');
    }
    public function showDepartmentForm(){
        $data['pageTitle'] = 'Department';
        $data['pageHeading'] = 'Add Department';
        return view("subpages.searchRank.addDepartment")
        ->with($data);
    }//end function
    private function _verifyDepartmentAlias($alias){
        $url = "https://www.amazon.com/s?i=$alias";
        $sc = new ScrapingController();
        $data = $sc->get_data_scraped($url);
        if($data['status'] == TRUE && $data['http_code'] != 404 && $data['http_code'] != 503 && $data['http_code'] != 400 ){
            return true;
        }
        return false;
    }
    public function addDepartment(Request $request){
        $validator = Validator::make($request->all(), [
            'd_name' => 'required|string|max:190',
            'd_alias' => 'required|string|max:190'
        ]);
        if ($validator->fails()) {
            return $response = array(
                'status' => false,
                'title' => "Error Invalid Data",
                'message' => $validator->errors(),
            );
        }
        //validate the alias by calling amazon https://www.amazon.com/s?i=$alias
            if(!$this->_verifyDepartmentAlias($request->d_alias))
            return $response = array(
                'status' => false,
                'title' => "Error I nvalid Alias",
                'message' => "Please Provide Valid Alias Against The Department",
            );
        $data = array(
            "d_name"=> $request->d_name,
            "d_alias"=> $request->d_alias,
            "created_at"=>date('Y-m-d H:i:s')
        );
        
        if(!DepartmentModel::insert($data)){
            return $response = array(
                'status' => false,
                'title' => "Internal Server Error",
                'message' => "Not Able To Save Record",
            );
        }
        return $response = array(
            'status' => true,
            'title' => "Success",
            'message' => "Department Added Successfully",
        );
    }//end function

    public function showSearchRankCrawlerForm(){
        $data['status'] = true;
        $data['departments'] = DepartmentModel::all();
        $data['data'] = $this->GetAllSchedules();
        $data['scheduleTime'] = SettingsModel::where("name","SrScheduleTime")->select("id","value")->first();
        return $data;
    }//end function
    private function GetAllSchedules(){
        return SearchRankCrawlerModel::with("department")
        ->orderBy("id","desc")
        ->get()
        ->map(function($item, $index){
            return [
                "sr"=> $index+1,
                "id"=> $item->id,
                "departmentName"=> $item->department->d_name,
                "sName"=> $item->c_name,
                "frequency"=> $item->c_frequency,
                "lastRun"=> $item->c_lastRun,
                "nextRun"=> $item->c_nextRun,
                "isRunning"=> $item->isRunning ?  "Running" : "Pending",
                "created_at"=> $item->created_at,
            ];
        });
    }//end function
    public function addSearchRankCrawler(Request $request){
        $addedCrawler = null;
        try {

            if(!DepartmentModel::where("id",$request->d_id)->exists())
            {
                return $response = array(
                    'status' => false,
                    'message' => "No Such Department",
                );
            }
            
            $depart_alias = DepartmentModel::find($request->d_id)->d_alias;
        
            $data = array(
                "c_name"=> $request->crawlName,
                "d_id"=> $request->d_id,
                "c_frequency"=> $request->frequency,
                "c_nextRun"=>date('Y-m-d'),
                "created_at"=>date('Y-m-d H:i:s')
            );
            $addedCrawler = SearchRankCrawlerModel::create($data);
            if(!isset($addedCrawler) || empty($addedCrawler)){
                return $response = array(
                    'status' => false,
                    'message' => "Not Able To Save Record",
                );
            }
            $res = $this->_uploadSearchTerms($request,$depart_alias,$addedCrawler->id);
            if($res != "1")
            {
                $addedCrawler->delete();
                $response = array(
                    'status' => false,
                    'message' => $res,
                );
                return $response;
            }
            
            return [
                'status' => true,
                'message' => "Crawler Added Successfully",
                'tableData' => $this->GetAllSchedules()
            ];
            
        } catch (\Throwable $th) {
            if($addedCrawler != null)
            {
                $searchTerms = SearchTermModel::where("crawler_id",$addedCrawler->id);
                if($searchTerms->exists()){
                    $searchTerms->delete();
                }
                $addedCrawler->delete();
            }
            return $response = array(
                'status' => false,
                'title' => "Internal Server Error",
                'message' => $th->getMessage(),
            );
        }
    }
    public function deleteSearchRankCrawler($id){

        try 
        {
            $cronModel = SearchRankCrawlerModel::find($id);
            if($cronModel ==null){
                return [
                    'status' => false,
                    'message' => "Sorry, No Such Record Found",
                ];
            }
            $searchTerm = SearchTermModel::where("crawler_id",$id);
            if($searchTerm->exists()){
                $searchTerm->delete();
            }

            if(!$cronModel->delete()){
                return [
                    'status' => false,
                    'message' => "Sorry, Fail To Delete Record TryAgain",
                ];
            }
            
            // if(!GeneralModel::ResetTableStand("tbl_search_rank_crawler","id")){//in case column Not Exist
            //     GeneralModel::AddMissingPrimaryKeyColumn("tbl_search_rank_crawler","id");
            // }
            return [
                'status' => true,
                'message' => "Schedule cron deleted successfully",
                'tableData' => $this->GetAllSchedules()
            ];
            
        } catch (\Throwable $th) {
            return json_encode($response = array(
                'status' => false,
                'message' => $th->getMessage(),
            ));
        }
    }
    private function _uploadSearchTerms(Request $request,$depart_alias,$crawler_id){
        if($request->hasFile('searchTerm'))
        {
                $file = $request->file('searchTerm');
                $fileExt = $file->getClientOriginalExtension();
                
                if ($fileExt != 'csv') {
                        $respon["message"] = "Please Select A Valid CSV File Type";
                        return $respon["message"];
                }//end if

                $fullFileName = $file->getClientOriginalName();//getting Full File Name
                $fileNameOnly = pathinfo($fullFileName,PATHINFO_FILENAME);//getting File Name With out extension
                $newFileName = $fileNameOnly .'_'.time().'.'.$fileExt;//Foramting New Name with Time stamp for avoiding any duplicated names in databese
                $inputFileName =  public_path('uploads/'). $newFileName ;
                
                // request()->excel->move(public_path('uploads'), $newFileName );
                $file->move(public_path('uploads'), $newFileName);

                if (!File::exists($inputFileName)) {
                    $respon["message"] = "File Not Exist";
                    return $respon["message"];
                } //end if
                $fe = (new FastExcel);
                $fe->withoutHeaders(true);
                $content = $fe->import($inputFileName);
                File::delete($inputFileName);
                if(count($content)<=0){
                    $respon["message"] = "There Are No Search Terms In The File";
                    return $respon["message"];
                }
                $searchTerms = array();
                foreach ($content as $row) {
                    if(count($row)>0)
                    if(empty(trim($row[0]))){
                        continue;
                    }
                    $searchTerm = str_replace(" ", "+", trim(strtolower($row[0])));
                    $url = "https://www.amazon.com/s?k=$searchTerm&i=$depart_alias";
                    $searchTerm = array();
                    $searchTerm["st_term"] = ($row[0]);
                    $searchTerm["st_alias"] = str_replace(" ", "+", strtolower($row[0]));
                    $searchTerm["st_url"] = $url;
                    $searchTerm["crawler_id"] = $crawler_id;
                    $searchTerm["created_at"] = date('Y-m-d H:i:s');
                    array_push($searchTerms,$searchTerm);
                }//end foreach
                

                if(count($searchTerms)<=0){
                    $respon["message"] = "There Are No Search Terms In The File";
                    return $respon["message"];
                }

                if(!SearchTermModel::insert($searchTerms)){
                    $respon["message"] = "Fail To Insert Record In DB ";
                    return $respon["message"];
                }
                return "1";
        }
        else{
            $respon["message"] = "File not Exist";
            return $respon["message"];
        }
    }
    //Scraping
    
    public function setTotalNumberOfPages($url){
        $sc = new ScrapingController();

        $data =  $sc->get_data_scraped($url);
          if($data['status'] == TRUE && $data['http_code'] != 404 && $data['http_code'] != 503 && $data['http_code'] != 400 ){
          
            $dom = new DOMDocument();
            libxml_use_internal_errors(true);
            $dom->loadHTML(mb_convert_encoding($data['data'], 'HTML-ENTITIES', 'UTF-8'));
            $a = new DOMXPath($dom);
            $result = array();

            $totalPageNode = $a->query('//*[@id="search"]//span[@class="rush-component"]//div[contains(concat(" ", normalize-space(@class), " "), "s-breadcrumb")]//div[@class="a-section a-spacing-small a-spacing-top-small"]//span[1]');

            $this->totalPages = count($totalPageNode) > 0 ? $totalPageNode[0]->nodeValue : "NA";
            if($this->totalPages != "NA"){
                $this->totalPages = $this->_getTPagesFromString($this->totalPages);
            }

            ActivityTrackerModel::setActivity('TotalPage From Total Number of Pages Funcation '.$this->totalPages,"info","SearchRankScrapingController","App\Libraries\SearchRankScrapingController",date('Y-m-d H:i:s'));
            ActivityTrackerModel::setActivity($url,"info","SearchRankScrapingController","App\Libraries\SearchRankScrapingController",date('Y-m-d H:i:s'));
            return $this->totalPages;
            return $this->totalPages != "NA";
        }
        if($data['http_code'] == 400)
        {
            
            Log::error("filePath:App\Libraries\SearchRankScrapingController Alias provided is wrong No Product Found against this url ");
            ActivityTrackerModel::setActivity("Alias provided is wrong No Product Found against this url","Error","SearchRankScrapingController","App\Libraries\SearchRankScrapingController",date('Y-m-d H:i:s'));
          
        }
        else
        {
            Log::info("filePath:App\Libraries\SearchRankScrapingController No Data Found While Getting Page Count ");
            ActivityTrackerModel::setActivity("No Data Found While Getting Page Count","info","SearchRankScrapingController","App\Libraries\SearchRankScrapingController",date('Y-m-d H:i:s'));
           
        }
        return false;
    }
    private function _getUrls($d_id){
        return SearchTermModel::where("crawler_id",$d_id)->select("id","st_url")->get();
    }
    public function scrapSearchRankOfferPrice($asin){
        $url = "https://www.amazon.com/dp/".$asin;
        $sc =  new ScrapingController();
       $asin_data = $sc->get_data_scraped($url,$asin);
     
        if($asin_data['status'] == TRUE && $asin_data['http_code'] != 404 && $asin_data['http_code'] != 503 ){
           
            $dom = new DOMDocument();
            libxml_use_internal_errors(true);
            $dom->loadHTML(mb_convert_encoding($asin_data['data'], 'HTML-ENTITIES', 'UTF-8'));
            
            $a = new DOMXPath($dom);
            
            $data = array();
            
            $price = $a->query("//span[@id='priceblock_ourprice']");
            if($price->length > 0){
                $data['price'] = $a->query("//span[@id='priceblock_ourprice']")[0]->nodeValue; //Get the Product Price
            }else{

                $price = $a->query("//span[@id='priceblock_saleprice']");
                if(isset($price)){
                    $data['price'] = isset($a->query("//span[@id='priceblock_ourprice']")[0]->nodeValue); //Get the Product Price
                }

            }
            $data['brand'] ="NA";
            if(isset($a->query("//a[@id='brand']")[0]->nodeValue)){

                $data['brand'] = trim($a->query("//a[@id='brand']")[0]->nodeValue);
            }elseif(isset($a->query("//a[@id='bylineInfo']")[0]->nodeValue)){

                $data['brand'] = trim($a->query("//a[@id='bylineInfo']")[0]->nodeValue);
            }elseif(isset($a->query("//a[@id='brandteaser']//img/@src")[0])){

                $data['brand'] = trim($a->query("//a[@id='brandteaser']//img/@src")[0]->nodeValue);
            }else{
                $data['brand'] = "NA";
            }

            if($data['brand'] == "NA"){
                if(isset($a->query("//a[@id='brand']//img/@src")[0])){
                    $data['brand'] = trim($a->query("//a[@id='brand']//img/@src")[0]->nodeValue);

                }else{
                    $data['brand'] == "NA";
                }
            }
            /* Cases for Brand End */
            $data["status"] = true;
            return $data;

        }//end if
        else{
           return $data["status"] = false;
        }
    }
    public function scrapSearchRankBrand($asin){
        $url = "https://www.amazon.com/dp/".$asin;
        $sc =  new ScrapingController();
       $asin_data = $sc->get_data_scraped($url,$asin);
     
        if($asin_data['status'] == TRUE && $asin_data['http_code'] != 404 && $asin_data['http_code'] != 503 ){
           
            $dom = new DOMDocument();
            libxml_use_internal_errors(true);
            if(empty($asin_data["data"])){
               return "NA";
            }
            $dom->loadHTML(mb_convert_encoding($asin_data['data'], 'HTML-ENTITIES', 'UTF-8'));
            
            $a = new DOMXPath($dom);
            
            $data = array();
            
     

            if(isset($a->query("//a[@id='brand']")[0]->nodeValue)){

                $data['brand'] = trim($a->query("//a[@id='brand']")[0]->nodeValue);
            }elseif(isset($a->query("//a[@id='bylineInfo']")[0]->nodeValue)){

                $data['brand'] = trim($a->query("//a[@id='bylineInfo']")[0]->nodeValue);
            }elseif(isset($a->query("//a[@id='brandteaser']//img/@src")[0])){

                $data['brand'] = trim($a->query("//a[@id='brandteaser']//img/@src")[0]->nodeValue);
            }else{
                $data['brand'] = "NA";
            }

            if($data['brand'] == "NA"){
                if(isset($a->query("//a[@id='brand']//img/@src")[0])){
                    $data['brand'] = trim($a->query("//a[@id='brand']//img/@src")[0]->nodeValue);

                }else{
                    $data['brand'] == "NA";
                }
            }
            /* Cases for Brand End */

            return $data['brand'];

        }//end if
        else{
           return "NA";
        }
    }
    public function getBrands($st_id){
        if(SearchRankScrapedResultModel::checkCrawlerExists($st_id)){
           $crawlers =  SearchRankScrapedResultModel::getCrawlerByStId($st_id);
           foreach ($crawlers as $key => $value) {
              $brand =  $this->scrapSearchRankBrand($value->asin);
              if($brand != "NA"){
                $value->brand = $brand;
                $value->save();
              }
           }
        }
    }
    //Call in 2nd Command
    private function _getTPagesFromString($string){
        try {
            $res["totalRec"] = substr($string,stripos($string,"of"));
           $res["perPage"] = trim(substr($string,stripos($string,"-")+1,stripos($string,"of")-2));
           $res["totalRec"] = preg_replace("/[^0-9]+/", "", $res["totalRec"]);
           $res["totalNumberOfPages"] = intval(round($res["totalRec"]/$res["perPage"]));
           return $res["totalNumberOfPages"];
        } catch (\Throwable $th) {
            return "NA" ;
        }
          
    }
 
    private function _getOfferCount($str){
       
        if($str != 0){
            $oc = substr($str,stripos($str,"(")+1);
            $oc = substr($oc,0,stripos($oc,")"));
            return preg_replace("/[^0-9]+/", "",$oc);
        }
        return preg_replace("/[^0-9]+/", "",$str);
    }//end function

    public function updateTempUrlStatus($tempUrlId, $status){
       TempSearchRankModel::where("id","=",$tempUrlId)->update(["urlStatus"=>"$status"]);
    }//end function
    public function _set_fail_status($data,$status,$crawler_id= null){
        SearchRankFailStatuses::create(array(
            "failed_data"=>$data,
            "failed_reason"=>$status,
            "failed_at"=>date('Y-m-d H:i:s'),
            "crawler_id"=>is_null($crawler_id)?0:$crawler_id,
            "created_at"=>date('Y-m-d H:i:s'),
        ));
    }
    /**
     * scrapSearchRank
     *
     * @param mixed $url
     * @param mixed $st_id
     * @param mixed $pageNumber
     * @param mixed $tempUrlId
     * @param mixed $crawler_id
     * @return void
     */
    public function scrapSearchRank($tempSearchTerm, $st_id = null, $pageNumber = null, $crawler_id = 0, $tempUrlId = null){
        
        // $url = "$tempSearchTerm->searchRankUrl";
        $sc =  new ScrapingController();
        $data = $sc->get_search_rank_data_scraped($tempSearchTerm);
        $proxy_ip = $sc->proxy_ip;
        if($data['status'] == TRUE && $data['http_code'] != 404 && $data['http_code'] != 503 ){ 
            Log::info('Verified');
            $dom = new DOMDocument();
            libxml_use_internal_errors(true);
           
            $dom->loadHTML(mb_convert_encoding($data['data'] , 'HTML-ENTITIES', 'UTF-8'));
            
            $fp = fopen($_SERVER['DOCUMENT_ROOT'] . "/appliances1.html","wb");
            fwrite($fp,$data['data']);
            $a = new DOMXPath($dom);
            $result = array();
            $totalProductsInPage = count($a->query('//div[@id="search"]//div[@class="s-main-slot s-result-list s-search-results sg-row"]//div[@data-component-type="s-search-result"]'));
           
            Log::info("totalProductsInPage => ".$totalProductsInPage);
            Log::info("Search Results".count($a->query('//div[@id="search"]//div[@class="s-main-slot s-result-list s-search-results sg-row"]')));
            // Log::info(($a->query('//div[@id="search"]//div[@class="s-main-slot s-result-list s-search-results sg-row"]')));
            
            //*[@id="search"]//span[@class="rush-component"]//div[contains(concat(" ", normalize-space(@class), " "), "s-breadcrumb")]//div[@class="a-section a-spacing-small a-spacing-top-small"]//span[1]
            $PageProductResults = array(); 
            $startXPath = '//div[@id="search"]//div[@class="s-main-slot s-result-list s-search-results sg-row"]//div[@data-component-type="s-search-result"]';
            for ($i=1; $i < $totalProductsInPage; $i++) { 
                $q =  ($a->query("$startXPath[$i]//h2"));
                $result["title"] = count($q) > 0 ? $this->_removeStyleAndScriptTagsAndTheirContent(trim($q[0]->nodeValue)):"NA";
                
                $q =  ($a->query("$startXPath[$i]/@data-asin"));
                $result["asin"] = count($q) > 0 ? $this->_removeStyleAndScriptTagsAndTheirContent(trim($q[0]->nodeValue)):"NA";

                if($result["asin"]=="NA")
                continue;

                $result["rank"] = $i;
                //offer_price Discount Price
                $q =  ($a->query("$startXPath[$i]//span[@class='a-price']//span[@class='a-offscreen']"));
               
                $offerPriceArray =  count($q) > 0 ? $this->_filterPrice($this->_removeStyleAndScriptTagsAndTheirContent(trim($q[0]->nodeValue))):0;
                
                $result['offerPriceOrignal'] = $offerPriceArray["problem"];
                $result["offer_price"] =  $offerPriceArray["problemFiltered"];


                if($result["offer_price"] == 0){
                    $q =  ($a->query("$startXPath[$i]//span[@class='a-color-base']"));
                      
                    $offerPriceArray =  count($q) > 0 ?$this->_filterPrice($this->_removeStyleAndScriptTagsAndTheirContent(trim($q[0]->nodeValue))):0;
                    
                    $result['offerPriceOrignal'] = $offerPriceArray["problem"];
                    $result["offer_price"] =  $offerPriceArray["problemFiltered"];
                }
                $result["brand"] = "NA";
                // if($result["offer_price"] == 0){
                //     $data= $result["asin"] != "NA"?$this->scrapSearchRankOfferPrice($result["asin"]):$data["status"] = false;
                //   if($data["status"]){
                //       $result["offer_price"] = strlen(trim($data["price"])) <= 0 ?0:trim($data["price"]);
                //       $result["brand"] = $data["brand"];
                //   }
                //   else {
                //     $result["offer_price"] = 0;
                //   }
                // }
                
                //list_price Line through
                $q = ($a->query("$startXPath[$i]//span[@class='a-price a-text-price']//span[@class='a-offscreen']"));
                    $listPriceArray =  count($q) > 0 ?$this->_filterPrice($this->_removeStyleAndScriptTagsAndTheirContent(trim($q[0]->nodeValue))):0;
                
                    $result['listPriceOrignal'] = $listPriceArray["problem"];
                    $result["list_price"]  =  $listPriceArray["problemFiltered"];
                //offers
                    $q =  ($a->query("$startXPath[$i]//div[contains(concat(' ', normalize-space(@class), ' '), 'a-spacing-top-mini')]//a[@class='a-link-normal']"));
                    $result["Offers"] =  count($q) > 0 ?$this->_removeStyleAndScriptTagsAndTheirContent(trim($q[0]->nodeValue)):0;
                    $result["Offers"] = $this->_getOfferCount($result["Offers"]);
                    $result["Offers"] = preg_replace("/[^0-9]+/", "",$result["Offers"]);
                //Sponsered
                    $q =  ($a->query("$startXPath[$i]//div[contains(concat(' ', normalize-space(@class), ' '), 'a-spacing-top-small')]//span[@class='a-size-base a-color-secondary']"));
                    $result["IsSponsered"] =  count($q) > 0 ?trim($q[0]->nodeValue):"NA";
                    $result["IsSponsered"] = (str_is("sponsored*", strtolower(trim($result["IsSponsered"]))));//0 / 1
                    
                //rating
                    $q =  ($a->query("$startXPath[$i]//div[contains(concat(' ', normalize-space(@class), ' '), 'a-spacing-top-micro')]//i[contains(concat(' ', normalize-space(@class), ' '), 'a-icon a-icon-star-small')]"));
                    $result["review_score"] =  count($q) > 0 ?$this->_removeStyleAndScriptTagsAndTheirContent(trim($q[0]->nodeValue)):"NA";
                //review_count
                    $q =  ($a->query("$startXPath[$i]//div[contains(concat(' ', normalize-space(@class), ' '), 'a-spacing-top-micro')]//span[contains(concat(' ', normalize-space(@class), ' '), 'a-size-base')]"));
                    $result["review_count"] =  count($q) > 0 ?$this->_removeStyleAndScriptTagsAndTheirContent(trim($q[0]->nodeValue)):0;
                    
                //copn
                    $q =  ($a->query("$startXPath[$i]//span[contains(concat(' ', normalize-space(@class), ' '), 's-coupon-unclipped')]"));
                    $result["copn"] =  count($q) > 0 ? $this->_removeStyleAndScriptTagsAndTheirContent(trim($q[0]->nodeValue)) : "NA";
                    $result["copn"] = trim($result["copn"]);
                //prime
                    $q =  ($a->query("$startXPath[$i]//i[contains(concat(' ', normalize-space(@class), ' '), 'a-icon-prime')]"));
                    $result["isPrime"] =  count($q) > 0 ? true:false;
                //best_seller
                    $resultAsin = $result['asin'];
                    $q =  ($a->query("$startXPath[$i]//span[@id='$resultAsin-label']//span[contains(concat(' ', normalize-space(@class), ' '), 'a-badge-text')]"));
                    $tag =  count($q) > 0 ?trim($q[0]->nodeValue):"NA";
                    $result["isBestSeller"] = $result["isAmazonChoice"] = false;
                    
                    if($tag !="NA")
                    {
                        $result["isAmazonChoice"] =(str_is("amazon's*", strtolower(trim($tag))));

                        $result["isBestSeller"] = (str_is("best seller*", strtolower(trim($tag))));
                    }  

                    $result['offerPriceOrignal'] = ($result['offerPriceOrignal'] != "NA") ? trim($result['offerPriceOrignal']) : 0;
                    $result['listPriceOrignal'] = ($result['listPriceOrignal'] != "NA")  ? trim($result['listPriceOrignal']) : 0;
                    $result['list_price'] = ($result['list_price'] != "NA")  ? trim($result['list_price']) : 0;
                    
                    $result['title'] = !empty(trim($result['title'])) ? $this->_filterString(trim($result['title'])) : "NA";
                    $result['Offers'] = !empty(trim($result['Offers'])) ? trim($result['Offers']) : 0;
                    $result['offerPriceOrignal'] = !empty(trim($result['offerPriceOrignal'])) ? trim($result['offerPriceOrignal']) : 0;
                    $result['listPriceOrignal'] = !empty(trim($result['listPriceOrignal'])) ? trim($result['listPriceOrignal']) : 0;
                    $result['list_price'] = !empty(trim($result['list_price'])) ? trim($result['list_price']) : 0.00;
                    $result['offer_price'] = !empty(trim($result['offer_price'])) ? trim($result['offer_price']) : 0.00;
                    $result['review_count'] = !empty(trim($result['review_count'])) ? trim($result['review_count']) : 0 ;
                    $result['brand'] = !empty(trim($result['brand'])) ? trim($result['brand']) : "NA" ;
                    $result['asin'] = !empty(trim($result['asin'])) ? trim($result['asin']):"NA";
                    $result['rank'] = !empty(trim($result['rank'])) ? trim($result['rank']):"NA";
                    $result['review_score'] = !empty($result['review_score'])?trim($result['review_score']):"NA";
                    // dd($result);
                    $result["offerPriceOrignal"] = $result["offerPriceOrignal"]=="NA"?0.00:$result["offerPriceOrignal"];

                    if(\strlen($result["offerPriceOrignal"]) > 99){
                        $result["offerPriceOrignal"] = \substr($result["offerPriceOrignal"], 0, 99);
                    }
                    if(\strlen($result["listPriceOrignal"]) > 99){
                        $result["listPriceOrignal"] = \substr($result["listPriceOrignal"], 0, 99);
                    }
                    if(\strlen($result["brand"]) > 299){
                        $result["brand"] = \substr($result["brand"], 0, 299);
                    }
                    if(\strlen($result["review_count"]) > 99){
                        $result["review_count"] = \substr($result["review_count"], 0, 99);
                    }
                    if(\strlen($result["review_score"]) > 99){
                        $result["review_score"] = \substr($result["review_score"], 0, 99);
                    }
                    $products = [
                        "st_id"=>$st_id,
                        "title"=> $result['title'],
                        "proxyIp"=> $proxy_ip,
                        "brand"=>preg_replace("/[^a-zA-Z0-9 ]+/", "", $result['brand']),
                        "asin"=>preg_replace("/[^a-zA-Z0-9]+/", "", $result['asin']),
                        "rank"=>preg_replace("/[^a-zA-Z0-9 ]+/", "", $result['rank']),
                        "offerPriceOrignal"=>preg_replace("/[^0-9.$]+/", "", $result['offerPriceOrignal']),//Discount Price
                        "offerPrice"=>preg_replace("/[^0-9.]+/", "", $result['offer_price']),//Discount Price
                        "listPriceOrignal"=>$result["listPriceOrignal"]=="NA"?0.00:$result["listPriceOrignal"],//Strike Through Price
                        "listPrice"=>$result["list_price"]=="NA"?0.00:$result["list_price"],//Strike Through Price
                        "offerCount"=>preg_replace("/[^0-9]+/", "", $result['Offers']),
                        "isPromo"=>$result["copn"]!="NA",
                        "isSponsered"=>$result["IsSponsered"],
                        "isBestSeller"=>$result["isBestSeller"],
                        "isAmazonChoice"=>$result["isAmazonChoice"],
                        "isPrime"=>$result["isPrime"],
                        "reviewCount"=>$result["review_count"],
                        "reviewScore"=>$result["review_score"],
                        "pageNo"=>$pageNumber,
                        "created_at" => date('Y-m-d')
                    ];
                    array_push($PageProductResults, $products);
            }//end for loop
            print_r($PageProductResults);
            Log::info($totalProductsInPage);
            Log::info(json_encode($PageProductResults));
            SearchRankScrapedResultModel::insert($PageProductResults);
            TempSearchRankModel::DeleteTempUrl($tempUrlId);
        }
        elseif ($data['error_code'] == ScraperConstant::ERROR_CAPTCHA) {
            $errorData['error_code'] = ScraperConstant::ERROR_CAPTCHA;
            $errorData['status'] = FALSE;
            $errorData['error_text'] = str_limit($data['error_text'],300);
            $errorData['html'] = NULL;
            print_r("Error Code =>".$data['error_code']);
            unset($data);
            ActivityTrackerModel::setActivity("From Error Check".$errorData['error_text'] ,"Constant ERROR","SearchRankScrapingController","App\Libraries\ScrapingController",date('Y-m-d H:i:s'));
            $this->updateTempUrlStatus($tempUrlId, ScraperConstant::CAPTCHA_STATUS);
            return $errorData;

        } elseif ($data['error_code'] == ScraperConstant::ERROR_CURL) {
            $errorData['error_code'] = ScraperConstant::ERROR_CURL;
            $errorData['status'] = FALSE;
            $errorData['error_text'] = str_limit($data['error_text'],300);
            $errorData['html'] = NULL;
            ActivityTrackerModel::setActivity("From Error Check".$errorData['error_text'] ,"Constant ERROR","SearchRankScrapingController","App\Libraries\ScrapingController",date('Y-m-d H:i:s'));
                
            print_r("Error Code =>".$data['error_code']);
            $this->updateTempUrlStatus($tempUrlId, ScraperConstant::ERROR_CURL);
            unset($data);
            return $errorData;

        } elseif ($data['error_code'] == ScraperConstant::ERROR_SERVICE_NOT_AVAILABLE) {
            
            $errorData['error_code'] =  ScraperConstant::ERROR_SERVICE_NOT_AVAILABLE;
            $errorData['status'] = FALSE;
            $errorData['error_text'] = "Something Went Wrong! Amazon's 503 From Validate Data";
            $errorData['html'] = NULL;
            
            ActivityTrackerModel::setActivity("From Error Check".$errorData['error_text'] ,"Constant ERROR","SearchRankScrapingController","App\Libraries\ScrapingController",date('Y-m-d H:i:s'));
          
            print_r("Error Code 503 =>".$data['error_code']);
            $this->updateTempUrlStatus($tempUrlId, ScraperConstant::ERROR_SERVICE_NOT_AVAILABLE);
            unset($data);
            return $errorData;
        }
        elseif ($data['error_code'] == ScraperConstant::ERROR_PRODUCTS_NOT_FOUND) {
            
            $errorData['error_code'] =  ScraperConstant::ERROR_PRODUCTS_NOT_FOUND;
            $errorData['status'] = FALSE;
            $errorData['error_text'] = "Something Went Wrong! Amazon's 404";
            $errorData['html'] = NULL;
            //$tempSearchTerm->department->crawler->id
            $err = array(
                "id"=>$tempSearchTerm->searchTerm_id,
                "url"=>$tempSearchTerm->searchRankUrl,
                "departmentId"=>isset($tempSearchTerm->department_id) ? $tempSearchTerm->department_id:0,
                "departmentName"=>isset($tempSearchTerm->department) ? $tempSearchTerm->department->d_name:"No Department Found",
                "departmentAlias"=>isset($tempSearchTerm->department) ? $tempSearchTerm->department->d_alias:"No Department Found",
                "crawlerId"=>isset($tempSearchTerm->department->crawler) ? $tempSearchTerm->department->crawler->id:0,
                "crawlerName"=>isset($tempSearchTerm->department->crawler) ? $tempSearchTerm->department->crawler->c_name:"No Crawler Found",
            );
            $failReasons = array(
                "Search Term is not right against the department",
                "Their are no results found on amazon against this search term",
                "Amazon servers were not available for service at the time of request(503)",
            );
            $crawler_id = isset($tempSearchTerm->department->crawler) ? $tempSearchTerm->department->crawler->id:0;
           
            $this->_set_fail_status(
                json_encode($err),
                json_encode($failReasons),
                $crawler_id
            );
            ActivityTrackerModel::setActivity("From Error Check ".$errorData['error_text'] ,"Constant ERROR","SearchRankScrapingController","App\Libraries\ScrapingController",date('Y-m-d H:i:s'));
            
            print_r("Error Code 404 =>".$data['error_code']);
            
            TempSearchRankModel::DeleteTempUrl($tempUrlId);
            unset($data);
            return $errorData;
        } elseif ($data['http_code'] == 503) {
            
            $errorData['error_code'] =  ScraperConstant::ERROR_SERVICE_NOT_AVAILABLE;
            $errorData['status'] = FALSE;
            $errorData['error_text'] = "Something Went Wrong! Amazon's 503";
            $errorData['html'] = NULL;
            
            ActivityTrackerModel::setActivity("From Error Check".$errorData['error_text'] ,"Constant ERROR","SearchRankScrapingController","App\Libraries\ScrapingController",date('Y-m-d H:i:s'));
            
            print_r("Error Code 503 =>".$data['error_code']);
            $this->updateTempUrlStatus($tempUrlId, ScraperConstant::ERROR_SERVICE_NOT_AVAILABLE);
            unset($data);
            return $errorData;
        }
    }//end function


    private function _filterString($value){
$regex = <<<'END'
/
    (
    (?: [\x00-\x7F]                 # single-byte sequences   0xxxxxxx
    |   [\xC0-\xDF][\x80-\xBF]      # double-byte sequences   110xxxxx 10xxxxxx
    |   [\xE0-\xEF][\x80-\xBF]{2}   # triple-byte sequences   1110xxxx 10xxxxxx * 2
    |   [\xF0-\xF7][\x80-\xBF]{3}   # quadruple-byte sequence 11110xxx 10xxxxxx * 3 
    ){1,100}                        # ...one or more times
    )
| .                                 # anything else
/x
END;
              
        $result=  preg_replace($regex, '$1', $value);
        return $result;
    }
    private function _removeStyleAndScriptTagsAndTheirContent($problem){
        
            $problem =preg_replace("#<style(.*?)>(.*?)</style>#is","",$problem);
            $problem =preg_replace("#<script(.*?)>(.*?)</script>#is","",$problem);
        return strip_tags($problem);
    }
    private function _filterPrice($problem){
        $problem = substr($problem,strpos($problem, "$"));
        
        if( str_is('*$*', ($problem)))
            {
                $problem = trim($problem);
                $problem = preg_replace("/[^0-9.$]+/", "",$problem);
                if(substr_count($problem,".")>1){
                    $problem = substr($problem,0,strpos($problem, ".",strpos($problem, ".")+1));
                }
            }
            else{
            $problem = 0;
            }
            $returns["problem"] = $problem;
            $returns["problemFiltered"] = preg_replace("/[^0-9.]+/", "",$problem);;
            return $returns;
    }
}//end class
