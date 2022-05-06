<?php

namespace App\Http\Controllers;

use DOMXPath;
use DOMDocument;
use App\Libraries\ScraperConstant;
use App\Models\BuyBoxModels\BuyBoxActivityTrackerModel;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\File;
use App\Models\ScrapingModels\ProxyModel;
use App\Models\BuyBoxModels\BuyBoxAsinListModel;
use App\Models\BuyBoxModels\BuyBoxTempUrlsModel;
use App\Models\BuyBoxModels\BuyBoxFailStatusModel;
use App\Models\BuyBoxModels\BuyBoxScrapResultModel;

class BuyBoxScrapingController
{
    public function testScrap($asin_id){
        // $asinModle = BuyBoxAsinListModel::where("id",$asin_id)->first();
        $asinModle = BuyBoxTempUrlsModel::with("asin:id","asin.getAsinAccounts:fkAccountId,fkAsinId")
        ->first();
        // return $asinModle;
        return $this->Scraper($asinModle);
    }
    public function updateAsinStatus($asin,$status){
       $asinModel = BuyBoxTempUrlsModel::where("id","=",$asin->id)->update(["scrapStatus"=>"$status"]);
    }//end function 
    
    private function _set_buybox_fail_status($asin, $data, $reason, $crawler_id= null){
        $accounts = $asin->asin->getAsinAccounts;
        $fData = [];
        foreach ($accounts as $key => $value) {
            $fData[] = [
            'fkAccountId'=>$value->fkAccountId,
            "failed_data"=>$data,
            "failed_reason"=>$reason,
            "failed_at"=>date('Y-m-d H:i:s'),
            "crawler_id"=>is_null($crawler_id)?0:$crawler_id,
            "created_at"=>date('Y-m-d H:i:s'),
            ];
        } 
        BuyBoxFailStatusModel::insert($fData);
        BuyBoxTempUrlsModel::deleteTempUrl($asin->id);
    }//end function
   
   //Main Scraping function
    public function Scraper($asin)
    {
                $url = "https://www.amazon.com/dp/".$asin->asinCode;
                // requesting for scraped Data
                $errorCode = ScraperConstant::ASIN_STATUS_COMPLETED;
                $asin_data = $this->get_data_scraped($url);
                // print_r($asin_data);
                if($asin_data['status'] == TRUE && $asin_data['http_code'] != 404 && $asin_data['http_code'] != 503 ){
                    
                    $dom = new DOMDocument();
                    libxml_use_internal_errors(true);
                    $dom->loadHTML(mb_convert_encoding($asin_data['data'], 'HTML-ENTITIES', 'UTF-8'));
                    
                    $a = new DOMXPath($dom);
                    if ($a->query("//a[@id='brand']")->length > 0) {
                        $data['brand'] = trim($a->query("//a[@id='brand']")->item(0)->nodeValue);
                        $data['brand'] = $this->_removeStyleAndScriptTagsAndTheirContent($data['brand']);
                    } elseif ($a->query('//*[@id="bylineInfo"]')->length > 0) {
                        $data['brand'] = trim($a->query('//*[@id="bylineInfo"]')->item(0)->nodeValue);
                        $data['brand'] = $this->_removeStyleAndScriptTagsAndTheirContent($data['brand']);
                    } elseif ($a->query("//a[@id='brandteaser']//img/@src")->length > 0) {
                        $data['brand'] = trim($a->query("//a[@id='brandteaser']//img/@src")->item(0)->nodeValue);
                        $data['brand'] = $this->_removeStyleAndScriptTagsAndTheirContent($data['brand']);
                        $data['brand']= (str_after($data['brand'], '/brands/'));
                        $data['brand']= (str_before($data['brand'], '/'));
                
                    } elseif ($a->query("//*[@id=\"sellerProfileTriggerId\"]")->length > 0) {
                        $data['brand'] = trim($a->query("//*[@id=\"sellerProfileTriggerId\"]")->item(0)->nodeValue);
                        $data['brand'] = $this->_removeStyleAndScriptTagsAndTheirContent($data['brand']);
                    } elseif ($a->query("//*[@id=\"bylineInfo\"]")->length > 0) {
                        $data['brand'] = trim($a->query("//*[@id=\"bylineInfo\"]")->item(0)->nodeValue);
                        $data['brand'] = $this->_removeStyleAndScriptTagsAndTheirContent($data['brand']);
                    } else {
                        $data['brand'] = "NA";
                    }
                    // sold by
                    $data['soldBy'] = "NA";
                    $soldBy = $a->query('//*[@id="merchant-info"]');
                    if ($soldBy->length > 0) {
                        $data['soldBy'] = trim($a->query('//*[@id="merchant-info"]')->item(0)->nodeValue); //Get the Sold By
                        $data['soldBy'] = $this->_removeStyleAndScriptTagsAndTheirContent($data['soldBy']);
                        $data['soldBy'] = trim(preg_replace('/\s\s+/', ' ', $data['soldBy']));
                        $data['soldBy'] = str_replace('P.when("seller-register-popover").execute(function(sellerRegisterPopover) { sellerRegisterPopover(); });', '', $data['soldBy']);
                        $data['soldBy'] = preg_replace("/[^a-zA-Z0-9 ]+/", "", $data['soldBy']);
                    } else {
                        $data['soldBy'] = "NA";
                    }
                    $data['soldByAlert'] = 0;
                    // create alert for sold and brand
                    /**
                     * SoldeBy 
                     * 1. NA
                     * 2. ''
                     * 3. empty
                     * we dont have buybox
                     * 1. Asin must have brand
                     * Generate alert when 
                     * 2. Sold by string do not contains the brand  or
                     * 3. Sold by string contains 'Ships from and sold by Amazon'
                     */
                    if ($data['soldBy'] != 'NA' && $data['soldBy'] != '' && !empty($data['soldBy'])) {
                        $data['soldByAlert'] = 0;
                        if ($data['brand'] != 'NA') {
                            $data['soldByAlert'] = 1;
                            if ((strpos($data['brand'], $data['soldBy']) !== false) || (strpos($data['soldBy'], 'Ships from and sold by Amazon') !== false)) { //Solde by String na ho
                                $data['soldByAlert'] = 0;
                            }
                        }
                    }
                    else
                    {
                        $data['soldBy'] = "NA";
                    }
                    //prices
                    $data['price'] = 0;
                    // get product prices
                    $price = $a->query("//span[@id='priceblock_ourprice']");
                    if ($price->length > 0) {
                        $data['price'] = $a->query("//span[@id='priceblock_ourprice']")->item(0)->nodeValue; //Get the Product Price
                    } else {
                        $price = $a->query("//span[@id='priceblock_saleprice']");
                        if ($price->length > 0) {
                            $data['price'] = $a->query("//span[@id='priceblock_saleprice']")->item(0)->nodeValue; //Get the Product Price
                        } else {
                            $price = $a->query('//*[@id="price_inside_buybox"]');
                            if ($price->length > 0) {
                                $data['price'] = $a->query('//*[@id="price_inside_buybox"]')->item(0)->nodeValue; //Get the Product Price
                            }
                        }
                    }
                    if( str_is('*$*', $data["price"]))
                    {
                        $data['price'] = trim($data['price']);
                        $data['price'] = $this->_removeStyleAndScriptTagsAndTheirContent($data['price']);
                        $data['price'] = preg_replace("/[^0-9$.]+/", "",$data['price']);
                        $priceArray= $this->_filterPrice($data['price']);
                        $data['priceOrignal'] = $priceArray["problem"];
                        $data['price'] = $priceArray["problemFiltered"];
                    }
                    else{
                        $data['price'] = 0;
                        $data['priceOrignal'] = "NA";
                    }
                    $data['primeDesc'] = 'NA';
                    // prime product
                    $prime = $a->query('//*[@id="bbop-sbbop-container"]');
                    if ($prime->length > 0) {
                        $data['primeDesc'] = trim($a->query("//*[@id=\"bbop-sbbop-container\"]")->item(0)->nodeValue); //Get the prime
                        $data['primeDesc'] = $this->_removeStyleAndScriptTagsAndTheirContent($data['primeDesc']);
                        $data['primeDesc'] = trim(preg_replace('/\s\s+/', ' ', $data['primeDesc']));
                        $data['prime'] = 1;
                    } else {
                        $data['prime'] = 0;
                    }
                    // stock status
                    $StockDataAction = $a->query('//*[@id="availability"]//span'); //Get the Stock
                    if(isset($StockDataAction) && !empty($StockDataAction) && $StockDataAction->length > 0)
                    {
                        $Stock = $a->query('//*[@id="availability"]//span'); //Get the Stock
                        if ($Stock->length > 0) {
                        $data['stock'] = trim($a->query('//*[@id="availability"]//span')->item($Stock->length-1)->nodeValue); //Get the Sol
                            $data['stock'] = $this->_removeStyleAndScriptTagsAndTheirContent($data['stock']);
                        } else {
                            $data['stock'] = "NA";
                        }
                    }else{
                        $Stock = $a->query('//*[@id="availability"]/span'); //Get the Stock
                        if ($Stock->length > 0) {
                        $data['stock'] = trim($a->query('//*[@id="availability"]/span')->item(0)->nodeValue); //Get the Sol
                            $data['stock'] = $this->_removeStyleAndScriptTagsAndTheirContent($data['stock']);
                        } else {
                            $data['stock'] = "NA";
                        }
                    }
                    // create alert for stock
                    $data['stockAlert'] = 0;
                    if ($data['stock'] != 'NA') {
                        $data['stockAlert'] = 1;
                        if (strpos($data['stock'], 'In Stock') !== false) {
                            $data['stockAlert'] = 0;
                        }else{
                            
                        }
                    }

                    $data["stock"] = trim(preg_replace('/\s\s+/', '', $data['stock']));
                    $data["stock"] = trim($data['stock']);
                    $data["stock"] = empty($data['stock'])?"NA":$data['stock'];
                    if(\strlen($data["stock"]) > 190){
                        $data["stock"] = \substr($data["stock"], 0, 190);
                    }
                    if(\strlen($data["soldBy"]) > 190){
                        $data["soldBy"] = \substr($data["soldBy"], 0, 190);
                    }
                    $data["primeDesc"] = empty($data['primeDesc'])?"NA":$data['primeDesc'];

                    if(\strlen($data["primeDesc"]) > 190){
                        $data["primeDesc"] = \substr($data["primeDesc"], 0, 190);
                    }
                    if(\strlen($data["priceOrignal"]) > 99){
                        $data["priceOrignal"] = \substr($data["priceOrignal"], 0, 99);
                    }
                    $data['brand'] = !empty(trim($data['brand']))?trim($data['brand']):"NA";
                    $data['url'] = $url;
                    $data['asinCode'] = $asin->asinCode;
                    $data['fkAsinId'] = $asin->fk_bb_asin_list_id;
                    $data['createdAt'] = date('Y-m-d H:i:s');
                    $data['updatedAt'] = date('Y-m-d H:i:s');
                    $data['fkCollection'] = $asin->crons->cNameBuybox;
                    BuyBoxScrapResultModel::insert($data);
                    BuyBoxTempUrlsModel::deleteTempUrl($asin->id);
                    
                }
                elseif ($asin_data['error_code'] == ScraperConstant::ERROR_CAPTCHA) {
                    $data['error_code'] = ScraperConstant::ERROR_CAPTCHA;
                    $data['status'] = FALSE;
                    $data['error_text'] = str_limit($asin_data['error_text'],300);
                    $data['html'] = NULL;
                    print_r("Error Code =>".$asin_data['error_code']."\n New Line");
                    unset($asin_data);
                    $this->updateAsinStatus($asin, ScraperConstant::ERROR_CAPTCHA);
                    return $data;

                } elseif ($asin_data['error_code'] == ScraperConstant::ERROR_CURL) {
                    $data['error_code'] = ScraperConstant::ERROR_CURL;
                    $data['status'] = FALSE;
                    $data['error_text'] = str_limit($asin_data['error_text'],300);
                    $data['html'] = NULL;
                    print_r("Error Text =>".$data['error_text']);
                    BuyBoxActivityTrackerModel::setActivity("Error Text =>".$data['error_text'],"ERROR","BuyBoxScrapingController","App\Http\Controllers",date('Y-m-d H:i:s'));
                    $this->updateAsinStatus($asin, ScraperConstant::ERROR_CURL);
                    unset($asin_data);
                    return $data;

                } elseif ($asin_data['error_code'] == ScraperConstant::ERROR_SERVICE_NOT_AVAILABLE) {
                    
                    $data['error_code'] =  ScraperConstant::ERROR_SERVICE_NOT_AVAILABLE;
                    $data['status'] = FALSE;
                    $data['error_text'] = "Something Went Wrong! Amazon's 503 From Validate Data";
                    $data['html'] = NULL;
                    
                    print_r("Error Code 503 =>".$asin_data['error_code']);
                    $this->updateAsinStatus($asin, ScraperConstant::ERROR_SERVICE_NOT_AVAILABLE);
                    unset($asin_data);
                    return $data;
                }
                elseif ($asin_data['error_code'] == ScraperConstant::ERROR_PRODUCTS_NOT_FOUND) {
                    
                    $data['error_code'] =  ScraperConstant::ERROR_PRODUCTS_NOT_FOUND;
                    $data['status'] = FALSE;
                    $data['error_text'] = "404 Product Not Found";
                    $data['html'] = NULL;
                    $err = array(
                        "Asin Id"=>$asin->fk_bb_asin_list_id,
                        "Asin"=>$asin->asinCode,
                        "Cron Id"=>$asin->fk_bbc_id,
                        "Cron Title"=>isset($asin->crons) ? $asin->crons->cNameBuybox:"NA"
                    );
                    $failReasons = array(
                        "404 Product Not Found",
                    );
                    $crawler_id = $asin->fk_bbc_id;
                    $this->_set_buybox_fail_status(
                            $asin,
                            json_encode($err),
                            json_encode($failReasons),
                        $crawler_id);
                    unset($asin_data);
                    return $data;
                } elseif ($asin_data['http_code'] == 503) {
                    
                    $data['error_code'] =  ScraperConstant::ERROR_SERVICE_NOT_AVAILABLE;
                    $data['status'] = FALSE;
                    $data['error_text'] = "Something Went Wrong! Amazon's 503";
                    $data['html'] = NULL;
                    
                    
                    print_r("Error Code 503 =>".$asin_data['error_code']);
                    $this->updateAsinStatus($asin, ScraperConstant::ERROR_SERVICE_NOT_AVAILABLE);
                    unset($asin_data);
                    return $data;
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
       $return = array();
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
          $return["problem"] = $problem;
          $return["problemFiltered"] = preg_replace("/[^0-9.]+/", "",$problem);
          return $return;
    }
    public function  get_data_scraped($url){
        $data = NULL;
        $tries = 0;
        //First Try
          
        do{

            $data = $this->get_data_curl($url);
           
                if($data['status'] == FALSE){     
                    
                Log::info("filePath:App\Libraries\BuyBoxScrapingController Try ".$tries." for scraped Data " );
     
                $tries++;
                }else{
                    //ASIN Successfully Scraped Breaking Loop
                    Log::info("filePath:App\Libraries\BuyBoxScrapingController Successfully Scraped Breaking Loop" );
                    break;
                }

        }while($tries < 3);
        return $data;
    }
    
    public function initProxy(){
        $proxy = ProxyModel::getRandom();
        // return $proxy;
        if($proxy != "NA"){
            return $proxy;
        }else{
            Log::info("filePath:App\Libraries\BuyBoxScrapingController No Proxy Avalialble" );
            dd("No Proxy Avalialble");
        }
    
    }
    public $proxy_ip;

    function get_random_user_agent(){
        $user_agent = [
            'Mozilla/5.0 (Windows NT 6.1; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/61.0.3163.100 Safari/537.36',
            'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_12_3) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/57.0.2987.110 Safari/537.36','Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:66.0) Gecko/20100101 Firefox/66.0',
            'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/77.0.3865.90 Safari/537.36',
            'Mozilla/5.0 (X11; Linux i586; rv:31.0) Gecko/20100101 Firefox/69.0',
            'Mozilla/5.0 (Windows; U; Windows NT 6.1; en-US) AppleWebKit/533.20.25 (KHTML, like Gecko) Version/5.0.4 Safari/533.20.27 evaliant',
            'Mozilla/5.0 (Windows NT 6.1; Trident/7.0; rv:11.0) like Gecko',
            'Opera/9.80 (X11; Linux i686; Ubuntu/14.10) Presto/2.12.388 Version/12.16',
            'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/77.0.3865.90 Safari/537.36 OPR/63.0.3368.94',
            'Mozilla/5.0 (compatible; MSIE 10.0; Windows NT 6.1; WOW64; Trident/6.0)',
            'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/77.0.3865.90 Safari/537.36 Edg/44.18362.267.0',
            'Mozilla/5.0 (Windows NT 10.0; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/77.0.3865.90 Safari/537.36 Vivaldi/2.7.1628.33',
            'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_14_1) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/77.0.3865.90 Safari/537.36 Vivaldi/2.7.1628.33',
            "Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/77.0.3865.90 Safari/537.36 Vivaldi/2.7.1628.33",
            'Mozilla/5.0 (Windows NT 6.3) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/77.0.3865.90 YaBrowser/19.7.3.172 Yowser/2.5 Safari/537.36',
            'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_14_1) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/77.0.3865.90 YaBrowser/19.6.0.1583 Yowser/2.5 Safari/537.36'
            
        ];

        
        $return  = array_rand( $user_agent, 1);
        return $user_agent[$return];
    }

    public function check_captcha($data){

        $pos2 = stripos($data, "Type the characters you see in this image");
        if ($pos2 !== false) {

            return -1;
        }else{
            return 1;
        }
    }

    public function validate_data($data){
        //503
        $error = array();
        $error["status"] = FALSE;
        $pos2 = stripos($data, "Sorry! Something went wrong!");
        if ($pos2 !== false) {
            $error["error_code"] = ScraperConstant::ERROR_SERVICE_NOT_AVAILABLE;
        }else{
            $pos2 = stripos($data, "Page Not Found");
            if ($pos2 !== false) {
                $error["error_code"] = ScraperConstant::ERROR_PRODUCTS_NOT_FOUND;
            }
            else{        
                $error["status"] = TRUE;
                $error["error_code"] = NULL;
            }
        }//END ELSE
        return $error;
    }//END FUNCTION

  
   
    public $cok = 1;
    public function get_data_curl($url)
    {
        $isCaptchaFound  = false;
        do {
            $request_headers = array();
            $request_headers[] = 'Accept: text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,image/apng,*/*;q=0.8';
            $request_headers[] = 'Accept-Language: en-US,en;q=0.8';
            $request_headers[] = 'Connection: keep-alive';
            $request_headers[] = 'Upgrade-Insecure-Requests: 1';
            $request_headers[] = 'Host: www.amazon.com';
            $request_headers[] = 'User-Agent: ' .$this->get_random_user_agent();

            //getting proxy
            if(!$isCaptchaFound)
            $proxy_row = $this->initProxy();
            $this->proxy_ip = $proxy_row->proxy_ip;
            $proxy =  $proxy_row->proxy_ip;
            $proxyauth = $proxy_row->proxy_auth;

            $proxy_row->is_blocked = 1;
            $proxy_row->save();
            // $proxy =  "206.41.175.25:80";
            //    $proxyauth = "codeht:c0d3ht";
            //initializing Curl
   
            
            $ch = curl_init($url);
            $cookie_name = str_replace(array(".", ":"), "-", $proxy);
            $cookie_name = trim($cookie_name);
            $headers = [];
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
            curl_setopt($ch, CURLOPT_TIMEOUT, 800);  //change this to constant
            curl_setopt($ch, CURLOPT_HTTPHEADER, $request_headers);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
            curl_setopt($ch, CURLOPT_CAINFO, public_path("/buybox/cacert.pem"));
            curl_setopt($ch, CURLOPT_COOKIEJAR, getcwd() . '/public/uploads/cookies/' . $cookie_name .$this->cok. '.txt');
            curl_setopt($ch, CURLOPT_COOKIEFILE, getcwd() . '/public/uploads/cookies/' . $cookie_name .$this->cok. '.txt');
            curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
            curl_setopt($ch, CURLOPT_PROXY, $proxy);
            curl_setopt($ch, CURLOPT_PROXYUSERPWD, $proxyauth);
            curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
            curl_setopt($ch, CURLOPT_ENCODING, '');
        
            //Execution Curl
    
            $data = trim(curl_exec($ch));
            $proxy_row->is_blocked = 0;
            $proxy_row->save();

            $return = array();
            $return['status'] = FALSE;
            $return['error_code'] = NULL;
            $return['error_text'] = NULL;
            $return['html'] = NULL;
            /* Check for 404 (file not found). */
            $return['http_code'] = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            if (curl_errno($ch)) {
                $return['status'] = FALSE;
                $return['error_code'] = ScraperConstant::ERROR_CURL;
                $return['error_text'] = curl_error($ch) . " - HTTP CODE: " . $return['http_code'];
                if(str_contains(curl_error($ch), "HTTP code 407 from proxy") || str_contains(curl_error($ch), "Failed to connect to")){
                    $proxy_row->is_blocked = 1;
                    $proxy_row->save();
                }
            }
            if (empty(($data)) && is_null($return['error_text'])) {
                $return['status'] = FALSE;
                $return['error_code'] = ScraperConstant::ERROR_CURL;
                $return['error_text'] = "Empty string in response from amazon ".json_encode($data);
                $proxy_row->is_blocked = 1;
                $proxy_row->save();
            }
            curl_close($ch);

            $pathToFile = public_path('/uploads/cookies/' . $cookie_name .$this->cok. '.txt'); 
            if(File::delete($pathToFile)){
              
            }
           
            if (is_null($return['error_text'])) {
                
                if ($this->check_captcha($data) == ScraperConstant::ERROR_CAPTCHA) {
                    //Found Captcha
                    $isCaptchaFound = true;
                    $proxy_rowt = $this->initProxy();
                    $proxy_row->is_blocked = 0;
                    $proxy_row->save();
                    $proxy_row = $proxy_rowt; 
                    $this->cok++;
                    continue;
                } 
                else 
                {
                    $v_result = $this->validate_data($data);
                    if ($v_result["status"]) 
                    {
                        // Data is Valid status true
                    
                        $return['data'] = $data;
                        $return['status'] = TRUE;
                        break;
                    } else 
                    {
                    
                        //UnKnown Error Error Code Four(4)
                        $return['error_code'] = $v_result["error_code"];
                        $return['data'] = NULL;
                        $return['status'] = FALSE;
                        $isCaptchaFound = true;
                        $proxy_rowt = $this->initProxy();
                        $proxy_row->is_blocked = 0;
                        $proxy_row->save();
                        $proxy_row = $proxy_rowt; 
                        $this->cok++;
                        if($v_result["error_code"]== ScraperConstant::ERROR_PRODUCTS_NOT_FOUND){
                            // print_r("$url Product Not Found");
                            // echo "<br>"; 
                             break;
                        }
                        else{
                            // print_r("$url Service Not Available");
                            // echo "<br>";
                            break;
                        }
                      
                    }//end else
                }//end else
            }//end if
            else{
                // print_r("$url No Error Found");
                // echo "<br>";
                break;
            }
        } while (true);
        
        // return $url." || ".$return["http_code"];
         
        $proxy_row->is_blocked = 0;
        $proxy_row->save();
        return $return;
    }
   

}//end class
