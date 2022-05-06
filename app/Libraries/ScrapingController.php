<?php
namespace App\Libraries;

use DOMXPath;
use DOMDocument;
use App\Models\FailStatus;
use Illuminate\Support\Arr;
use App\Events\SendNotification;
use App\Http\Resources\Decaptcha;
use App\Libraries\ScraperConstant;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\File;
use Symfony\Component\Process\Process;
use Graze\ParallelProcess\PriorityPool;
use App\Models\ScrapingModels\asinModel;
use App\Models\ScrapingModels\CronModel;
use Graze\ParallelProcess\Display\Lines;
use App\Models\ScrapingModels\ProxyModel;
use App\Models\ScrapingModels\ScrapModel;
use App\Models\ScrapingModels\SettingsModel;
use App\Models\ScrapingModels\CollectionsModel;
use App\Models\ScrapingModels\ActivityTrackerModel;
use App\Models\ScrapingModels\InstantASINTempModel;
use Symfony\Component\Console\Output\ConsoleOutput;
use App\Models\ScrapingModels\SearchRankCrawlerModel;
use App\Models\ScrapingModels\InstantASINFailStatusModel;

class ScrapingController
{
    public static function getValidDepartmentId(){
        $crawler_id = array();
      
        $current_hour = date("H",strtotime(date("H:i")));

        if(!SearchRankCrawlerModel::checkAvailableCrawlers(date('Y-m-d')))
        return $crawler_id;
        $Crawlers = SearchRankCrawlerModel::getCrawlers(date('Y-m-d'));
        // return $Crawlers;
        ActivityTrackerModel::setActivity("Varification Result Positive Valid Cron Found","info","ScrapingController","App\Libraries\ScrapingController",date('Y-m-d H:i:s'));
        
        foreach ($Crawlers as $Crawler) {
            /**
             * 1. is current hour
             * 3. is last run date is not equal to today's date
             */
            $Crawler->c_nextRun = date('Y-m-d', strtotime("+$Crawler->c_frequency days"));
            $Crawler->c_lastRun = date('Y-m-d');
            $Crawler->isRunning = 1;
            $Crawler->save();
            $c = array(
                "crawler_id"=>$Crawler->id
            );
            array_push( $crawler_id,$c);
         }//end foreach
       
         ActivityTrackerModel::setActivity("checkAvailableSchedules ","info","ScrapingController","App\Libraries\ScrapingController",date('Y-m-d H:i:s'));
         return $crawler_id;
    }//end function
    public function updateAsinStatus($asin,$status){
       $asinModel = asinModel::where("asin_id","=",$asin->asin_id)->update(["asin_status"=>"$status"]);
    }
    //Main Scraping function
    private function Scraper($asin,$c_id, $crawler_id = 0)
    {
        $url = "https://www.amazon.com/dp/".$asin->asin_code;
        // requesting for scraped Data
       
        $errorCode = ScraperConstant::ASIN_STATUS_COMPLETED;
        $asin_data = $this->get_data_scraped($url,$c_id,$asin->asin_code, $crawler_id);
        
        if($asin_data['status'] == TRUE && $asin_data['http_code'] != 404 && $asin_data['http_code'] != 503 )
        {
            $dom = new DOMDocument();
            libxml_use_internal_errors(true);
            $dom->loadHTML(mb_convert_encoding($asin_data['data'], 'HTML-ENTITIES', 'UTF-8'));
            
            $a = new DOMXPath($dom);
            
            $this->setActivity("Preparing Records","check");
            $data = array();
            $data['url'] = $url;
            $data['asin'] = $asin->asin_code;
            $data['c_id'] = $c_id;
            $data['capturedAt'] =  date('Y-m-d');

            /* ASIN parsing starts from here. */
            //*[@id="buyNew_noncbb"]
            
           
            $offPriceArray = $this->_filterPrice($this->_getOfferPrice($a));
            
                
            $data['offerPrice'] = doubleval($offPriceArray["problemFiltered"]);
            $data['offerPriceOrignal'] = $offPriceArray["problemOrignal"];

            $data['offerPriceOrignal'] = !empty(trim($data['offerPriceOrignal'])) ? $data['offerPriceOrignal'] : "0.00";
            $data['offerPrice'] = !empty(trim($data['offerPrice'])) ? $data['offerPrice'] : 0.00;
            
            Log::info(json_encode([$data['offerPriceOrignal'],$data['offerPrice']]).".checkData");
            
            $aplustxt = $a->query("//div[@class='aplus-v2 desktop celwidget']")->item(0);

            $data['aplusModule'] = isset($a->query("//div[contains(@class, 'celwidget ') and contains(@class, 'aplus-module ') or contains(@class, 'module-1')]")[0]->nodeValue) ? 1 : 0;

            //$this->_filterPrice($this->_removeStyleAndScriptTagsAndTheirContent(trim($q[0]->nodeValue))):0;
            /* Cases for Brand Start */
                $data['brand'] = $this->_getBrand($a);
            /* Cases for Brand End */
            $prime = $a->query('//*[@id="bbop-sbbop-container"]');
            $data["isPrime"]=  count($prime)>0?true:false;
            $data['metaKeywords'] = isset($a->query ("//meta[@name='keywords']/@content")[0]->value) ? $a->query ("//meta[@name='keywords']/@content")[0]->value : "NA"; // Get the Keywords

            $data['breadcrumbs'] = $this->_getBreadCrumbs($a);
            
            $data["isPromo"] = count($a->query("//i[contains(concat(' ', normalize-space(@class), ' '), 'couponBadge')]")) > 0 ? TRUE : FALSE; //Check Is Promotional

            $data['offerCount'] = $this->_getOfferCount($a);
         
            
            $data['title'] = $this->_getTitle($a);
            $data['title'] = $this->_filterString($data['title']);
            // $data['description'] = $this->_getDescription($a);
           
           
            $data['averageReview'] = isset($a->query("(//div[@id='averageCustomerReviews'])//span[contains(concat(' ', normalize-space(@class), ' '), 'a-icon-alt')] ")[0]->nodeValue) ? $a->query("(//div[@id='averageCustomerReviews'])//span[contains(concat(' ', normalize-space(@class), ' '), 'a-icon-alt')] ")[0]->nodeValue : 00000.0; // Get Average Rating

            $data['totalReviews'] = isset($a->query("(//div[@id='averageCustomerReviews'])//span[contains(concat(' ', normalize-space(@id), ' '), 'acrCustomerReviewText')] ")[0]->nodeValue) ? $a->query("(//div[@id='averageCustomerReviews'])//span[contains(concat(' ', normalize-space(@id), ' '), 'acrCustomerReviewText')] ")[0]->nodeValue : 0; // Get Total Reviews
            
          
            $loopdata['listPrice'] = $a->query("//div[@id='price']//span[contains(concat(' ', normalize-space(@class), ' '), 'a-text-strike')]"); // Get List Price
         
            $data['bestSellerRank'] = $this->_getBestSellerRank($a,$dom);
           
            $data['availabilityMessage'] = $this->_getAvailabilityMessage($a);
           
            $merchant_info = $a->query("(//div[@id='availability-brief'])//span[contains(concat(' ', normalize-space(@id), ' '), 'merchant-info')]"); // Get Merchent Info
            if($merchant_info->length == 0){
                $data['seller'] = isset($a->query("(//div[@id='merchant-info'])")[0]->nodeValue) ? $a->query("(//div[@id='merchant-info'])")[0]->nodeValue : "NA";// Get Merchent Info
            }
            if(!str_is("Sold by", $data['seller'] )){
                if(count($a->query('//*[@id="merchant-info"]/a[1]'))>0){
                    $soldBy  = $a->query('//*[@id="merchant-info"]/a[1]');
                     $soldBy = count($soldBy)>0?"Sold by ".$soldBy[0]->nodeValue:"";
                     $seller  = $a->query('//*[@id="merchant-info"]/a[2]');
                     $seller = count($seller)>0?" and ".$seller[0]->nodeValue:"";
                     
                 $data['seller'] = $soldBy.$seller;
                 }
                else{
                    $soldBy  = $a->query('//*[@id="sellerProfileTriggerId"]');
                    $soldBy = count($soldBy)>0?"Sold by ".$soldBy[0]->nodeValue:"";
                    $seller  = $a->query('//*[@id="SSOFpopoverLink_ubb"]');
                    $seller = count($seller)>0?" and ".$seller[0]->nodeValue:"";
                    
                 $data['seller'] = $soldBy.$seller;
                }
                 
             }
             else{
                 $data['seller'] = str_replace("Fulfilled", "and Fulfilled", $data['seller']);
             }
          

            foreach ($loopdata as $key => $node) {
                $data[$key] = isset($node[$key]->nodeValue) ? $node[$key]->nodeValue : "NA";
            }

            $res = $this->_getImageCount($a);
            $images = $res["images"];
            $data["imageCount"] = count($images);
            $data["videoCount"] = $res["videoCount"];
            
            $atext = $a->query("//div[@id='aplus']//h3 | //div[@id='aplus']//p | //div[@id='aplus']//h4 | //div[@id='aplus']//h5 | //div[@id='aplus']//span[contains(concat(' ', normalize-space(@class), ' '), 'a-size-base')] | //div[@id='aplus']//table[contains(concat(' ', normalize-space(@class), ' '), 'apm-tablemodule-table')]//a"); // A+ Text

            $res = $this->_getBullets($a);
            $data["bullets"] = $res["bullets"];
            $data["bullets"] = !empty($data["bullets"])?$data["bullets"]:"NA";
            $data["bulletCount"] =$res["bulletCount"];
            $data["avgWordsPerBulitCount"] = $res["avgWordsPerBulitCount"];
         
            // ~Parsing Logic for ModelNo and UPC Starts For detail-bullets Template

            $data['modelNo'] = "NA"; // Set default modelNo
         
            $detail_bullets_info = $a->query("(//*[@id='detail-bullets'])//div[contains(concat(' ', normalize-space(@class), ' '), 'content')]//li");
            if($detail_bullets_info->length > 0){
                $detail_info =  $this->_gather_detail_bullets2($detail_bullets_info,$dom);
                 $data['modelNo'] = isset($detail_info['modelNo']) ? $detail_info['modelNo'] : "NA";
                $dimensions = isset($detail_info['dimensions']) ? $detail_info['dimensions'] : "NA" ;
                $data['bestSellerRank'] = isset($detail_info['rank']) ? $detail_info['rank'] : "NA" ;
                $data['bestSellerRank'] = preg_replace("/\(([^()]*+|(?R))*\)/","",$data['bestSellerRank']);
                $data['shipWeight'] = isset($detail_info['shipWeight']) ? $detail_info['shipWeight'] : "NA" ;
            }

            // ~Parsing Logic for ModelNo and UPC Ends
            
            
             
            $productinformation = $a->query('//*[@id="prodDetails"]//table//tr');
            if($productinformation->length > 0){
               $product_info = $this->_gather_product_info($productinformation);
            //    dump($product_info);
                $data['bestSellerRank'] = isset($product_info['rank']) ? $product_info['rank'] : "NA" ;
                $data['bestSellerRank'] = preg_replace("/\(([^()]*+|(?R))*\)/","",$data['bestSellerRank']);
                $data['shipWeight'] = isset($product_info['shipWeight']) ? $product_info['shipWeight'] : "NA" ;
                $data['dateFirstAvailable'] = isset($product_info['dateFirstAvailable']) ? $product_info['dateFirstAvailable'] : "NA" ;
                $data['size'] = str_limit(isset($product_info['size']) ? $product_info['size'] : "NA",100) ;
                $data['color'] = isset($product_info['color']) ? $product_info['color'] : "NA" ;
                $data['weight'] = isset($product_info['weight']) ? $product_info['weight'] : "NA" ;
                $dimensions = isset($product_info['dimensions']) ? $product_info['dimensions'] : "NA" ;
                $data['modelNo'] = isset($product_info['modelNo']) ? $product_info['modelNo'] : "NA" ;
            }
            else{
                $product_info = $this->_get_Missing_table_data($asin_data['data']);
                if(count($product_info) > 0){
                    $data['bestSellerRank'] = isset($product_info['rank']) ? $product_info['rank'] : "NA" ;
                    $data['bestSellerRank'] = preg_replace("/\(([^()]*+|(?R))*\)/","",$data['bestSellerRank']);
                    $data['dateFirstAvailable'] = isset($product_info['dateFirstAvailable']) ? $product_info['dateFirstAvailable'] : "NA" ;
                    $data['size'] = isset($product_info['size']) ? $product_info['size'] : "NA" ;
                    $data['color'] = isset($product_info['color']) ? $product_info['color'] : "NA" ;
                    $data['shipWeight'] = isset($product_info['shipWeight']) ? $product_info['shipWeight'] : "NA" ;
                    $data['weight'] = isset($product_info['weight']) ? $product_info['weight'] : "NA" ;
                    $dimensions = isset($product_info['dimensions']) ? $product_info['dimensions'] : "NA" ;
                    $data['modelNo'] = isset($product_info['modelNo']) ? $product_info['modelNo'] : "NA" ;
                }
            }
            
            $multicolor = $a->query('//*[@id="variation_color_name"]//ul//li');
            $color = "";
            if(count($multicolor)>0){
                for ($i=1; $i <= count($multicolor); $i++) { 
                    $color .= " - ".trim(str_ireplace("Click to select","", $a->query('//*[@id="variation_color_name"]//ul//li['.$i.']//@title')[0]->nodeValue));
                }
                $data["color"] = (str_replace_first(" - ","",$color));
            }
            
            
            // ~Start Cleaning data, removing extra text/spaces.
          
           
            //category 
            if(isset($data["shipWeight"]))
            $data['shipWeight'] = str_replace("(View shipping rates and policies)","",$data['shipWeight']);
            $data['category'] = $this->_get_catrgory_from_breadcrumb($data['breadcrumbs']);

            $data['averageReview'] = isset($data['averageReview']) ? str_replace('out of 5 stars','',$data['averageReview']) : 00000.0;

            $data['totalReviews'] = isset($data['totalReviews']) ? str_replace('customer reviews','',$data['totalReviews']) : 0;
            $data['totalReviews'] = str_replace('1 customer review','1',$data['totalReviews']); // 1 Customer Review case handling

            // $data['description'] = preg_replace('/\s+/', ' ', $data['description']);
             // $data['description'] = preg_replace("/[^a-zA-Z0-9 ]+/", "", $data['description']);
          
            $data['seller'] = preg_replace('/\s+/', ' ', $data['seller']);
            $data['seller'] = preg_replace("/[^a-zA-Z0-9. ]+/", "", $data['seller']);
            $data['seller'] = strlen($data['seller'])>0?$data['seller']:"NA";
            $data['bestSellerCategory'] = $this->_get_best_seller_catrgory($data['bestSellerRank']);

            $data['isAvailable'] = !empty($data['availabilityMessage']) && $data['availabilityMessage']!="NA"?1:0;
            $data['availabilityMessage'] = !empty($data['availabilityMessage']) && $data['availabilityMessage']!="NA"?$data['availabilityMessage']:"NA";

            $data['modelNo'] = trim($data['modelNo']);

            $data['bestSellerRank'] = (preg_replace("/[^a-zA-Z0-9#,.& ]+/", "", trim($data['bestSellerRank'])));
            $data['bestSellerRank'] = (str_replace("  ","",$data['bestSellerRank']));

            $data['bestSellerCategory'] = preg_replace("/[^a-zA-Z0-9#,.&| ]+/", "", $data['bestSellerCategory']);
           
            $data['bestSellerCategory'] = empty(trim( $data['bestSellerCategory'] ))?"NA": $data['bestSellerCategory'] ;
             // 1 Customer Review case handling
           
            $data['modelNo'] =  preg_replace("/[^a-zA-Z0-9- ]+/", "", $data['modelNo']);
          
            // ~End
            
           
           $amazonChoise = count($a->query('//*[@id="acBadge_feature_div"]/div/span[1]')) > 0 ? true : false;
            $amazonChoiseTerm ="NA";
            if($amazonChoise){
                $amazonChoiseTerm = count($a->query('//*[@id="acBadge_feature_div"]/div/span[2]/span/span/a')) > 0 ?$a->query('//*[@id="acBadge_feature_div"]/div/span[2]/span/span/a')[0]->nodeValue : "NA";
            }
            $amzChoice = $this->_handleAmazonChoice($a);

            $data["isAmazonChoice"] =  $amzChoice["isAmazonChoice"];
            $data["amazonChoiceTerm"] = $amzChoice["amazonChoiceTerm"];
            if(isset($data["dateFirstAvailable"]))
            $data['dateFirstAvailable'] = date("Y-m-d",strtotime($data['dateFirstAvailable']));
            // Get Merchent Info
            $atxt = array();
            foreach($atext as $key => $txt){  // For AText
                $atxt[$key] = $txt->nodeValue;
            }
            
            $data['images'] = implode(",",$images);
            unset($image);
            $data['aplus'] = trim(implode("",$atxt));
            $data['aplus'] = preg_replace('#<script(.*?)>(.*?)</script>#is', '', $data['aplus']);
            $data['aplus'] = preg_replace('#<style(.*?)>(.*?)</style>#is', '', $data['aplus']);
            $data['aplus'] = preg_replace('/\s+/', ' ',$data['aplus']);
            $data['aplus'] = preg_replace('/\r+/', ' ',$data['aplus']);
            $data['aplus'] =  preg_replace("/[^a-zA-Z0-9 ]+/", "", $data['aplus']);
            $data['aplus'] = !empty(trim($data['aplus']))?trim($data['aplus']):"NA";
            $data['listPrice'] =  $data['listPrice'] == "NA" ? 0 : $data['listPrice'];
            unset($atxt);
            $listPriceArray = $this->_filterPrice(trim($data['listPrice']));
            $data['listPriceOrignal'] = !empty(trim($data['listPrice']))? $listPriceArray["problemOrignal"]:0.00;
            $data['listPrice'] = !empty(trim($data['listPrice']))? $listPriceArray["problemFiltered"]:0.00;

            $data['amazonChoiceTerm'] = !empty($data['amazonChoiceTerm'])?$data['amazonChoiceTerm']:"NA";
            $data['dateFirstAvailable'] = !empty($data['dateFirstAvailable'])?$data['dateFirstAvailable']:"NA";
            $data['modelNo'] = !empty($data['modelNo'])?$data['modelNo']:"NA";
            $data['bestSellerRank'] = !empty($data['bestSellerRank'])?$data['bestSellerRank']:"NA";
            $data['size'] = !empty($data['size'])?$data['size']:"NA";
            $data['color'] = !empty($data['color'])?$data['color']:"NA";
            $data['shipWeight'] = !empty($data['shipWeight'])?$data['shipWeight']:"NA";
            $data['weight'] = !empty($data['weight'])?$data['weight']:"NA";
            $dimensions = !empty($dimensions)?$dimensions:"NA";
            $data['length'] = $this->_filterDimension($dimensions,"l");
            $data['width'] =  $this->_filterDimension($dimensions,"w");
            $data['height'] = $this->_filterDimension($dimensions,"h");

           
            //e
            $data['asinExists'] = 1; // Success Status
            unset($asin_data);


            return $data;

        }
        elseif ($asin_data['error_code'] == ScraperConstant::ERROR_CAPTCHA) {
            $data['error_code'] = ScraperConstant::ERROR_CAPTCHA;
            $data['status'] = FALSE;
            $data['error_text'] = str_limit($asin_data['error_text'],300);
            $data['html'] = NULL;
            print_r("Error Code =>".$asin_data['error_code']);
            unset($asin_data);
            $this->setActivity("From Error Check".$data['error_text'] ,"Constant ERROR");
                
            $this->updateAsinStatus($asin, ScraperConstant::ERROR_CAPTCHA);
            return $data;

        } elseif ($asin_data['error_code'] == ScraperConstant::ERROR_CURL) {
            $data['error_code'] = ScraperConstant::ERROR_CURL;
            $data['status'] = FALSE;
            $data['error_text'] = str_limit($asin_data['error_text'],300);
            $data['html'] = NULL;
            $this->setActivity("From Error Check".$data['error_text'] ,"Constant ERROR");
                
            print_r("Error Code =>".$asin_data['error_code']);
            $this->updateAsinStatus($asin, ScraperConstant::ERROR_CURL);
            unset($asin_data);
            return $data;

        } elseif ($asin_data['error_code'] == ScraperConstant::ERROR_SERVICE_NOT_AVAILABLE) {
            
            $data['error_code'] =  ScraperConstant::ERROR_SERVICE_NOT_AVAILABLE;
            $data['status'] = FALSE;
            $data['error_text'] = "Something Went Wrong! Amazon's 503 From Validate Data";
            $data['html'] = NULL;
            
            $this->setActivity("From Error Check".$data['error_text'] ,"Constant ERROR");
          
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
            
            $this->setActivity("From Error Check".$data['error_text'] ,"Constant ERROR");
            
            print_r("Error Code 404 =>".$asin_data['error_code']);
            $err = array(
                "id"=>$asin->asin_id,
                "asin"=>$asin->asin_code,
                "CollectionId"=>isset($asin->collection->id) ? $asin->collection->id:0,
                "CollectionName"=>isset($asin->collection->c_name) ? $asin->collection->c_name:0,
                "CollectionType"=>"Daily"
            );
            $failReasons = array(
                "404 Product Not Found",
            );
            $crawler_id = isset($asin->collection->asin_cron) ? $asin->collection->asin_cron->id:0;
              $this->_set_fail_status(
                    json_encode($err),
                    json_encode($failReasons),
                   $crawler_id);
            $this->updateAsinStatus($asin, ScraperConstant::ERROR_PRODUCTS_NOT_FOUND);
            unset($asin_data);
            return $data;
        } elseif ($asin_data['http_code'] == 503) {
            
            $data['error_code'] =  ScraperConstant::ERROR_SERVICE_NOT_AVAILABLE;
            $data['status'] = FALSE;
            $data['error_text'] = "Something Went Wrong! Amazon's 503";
            $data['html'] = NULL;
            
            $this->setActivity("From Error Check".$data['error_text'] ,"Constant ERROR");
            
            print_r("Error Code 503 =>".$asin_data['error_code']);
            $this->updateAsinStatus($asin, ScraperConstant::ERROR_SERVICE_NOT_AVAILABLE);
            unset($asin_data);
            return $data;
        }
    }//end function
  
    private function _filterDimension($dimensions,$type = "d"){
        if($dimensions == "NA"){
            return "NA";
        }
        $dimensionRaw =  explode(";",$dimensions); 
        $dimensionRawCount = count($dimensionRaw);
        $dimension = $dimensions;
        if($dimensionRawCount == 2){
            $dimension = trim(str_ireplace(":","",$dimensionRaw[0]));
        }

        $dbreaked = explode("x",$dimension);
        $dbreakedCount = count($dbreaked);
        $data = array();
        if($dbreakedCount > 0 && $dbreakedCount <=3 )
        {
            $data["length"] = preg_replace("/[^a-zA-Z0-9x.,_-]+/", "", trim($dbreaked[0]));
            $data["width"] = trim($dbreaked[1]);
            $data["height"] = trim($dbreaked[2]);
            $data["height"] = preg_replace("/[^0-9.,_-]+/", "", $data["height"]);
            $data["unit"] = preg_replace("/[^a-zA-Z]+/", "", $dbreaked[2]);
            $data["length"] = $data["length"]." ".$data["unit"];
            $data["width"] = $data["width"]." ".$data["unit"];
            $data["height"] = $data["height"]." ".$data["unit"];
            switch ($type) {
                case 'l':
                    return $data["length"];
                    break;
                case 'w':
                    return $data["width"];
                    break;
                case 'h':
                    return $data["height"];
                    break;
                case 'u':
                    return $data["unit"];
                    break;
                default:
                    return trim(preg_replace("/[^a-zA-Z0-9-x.,_ ]+/", "", $dimension));
                    break;
            }//end switch case
        }//end if
        return trim(preg_replace("/[^a-zA-Z0-9-x.,_ ]+/", "", $dimension));
        
    }//end functon
    private function _filterString($value){
        // $regex = <<<'END'
        // /
        //   (
        //     (?: [\x00-\x7F]                 # single-byte sequences   0xxxxxxx
        //     |   [\xC0-\xDF][\x80-\xBF]      # double-byte sequences   110xxxxx 10xxxxxx
        //     |   [\xE0-\xEF][\x80-\xBF]{2}   # triple-byte sequences   1110xxxx 10xxxxxx * 2
        //     |   [\xF0-\xF7][\x80-\xBF]{3}   # quadruple-byte sequence 11110xxx 10xxxxxx * 3 
        //     ){1,100}                        # ...one or more times
        //   )
        // | .                                 # anything else
        // /x
        // END;
              
        $result=  preg_replace($regex, '$1', $value);
        return $result;
    }
    private function _filterPrice($problem){
        $problem = substr($problem,strpos($problem, "$"));
        $res = array();
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
            $res["problemOrignal"] = $problem;
            $res["problemFiltered"] = preg_replace("/[^0-9.]+/", "",$res["problemOrignal"]);
            return $res;
    }
    private function _getOfferPrice($a){
        $price = $a->query("//span[@id='priceblock_ourprice']");
        $OfferPrice = "0";
        if($price->length > 0){
            $OfferPrice = $a->query("//span[@id='priceblock_ourprice']")[0]->nodeValue; //Get the Product Price
        }else{
            $price = $a->query("//span[@id='priceblock_saleprice']");
            if($price->length > 0){
                $OfferPrice = ($a->query("//span[@id='priceblock_saleprice']")[0]->nodeValue); //Get the Product Price
            }else {
                $price = $a->query("//*[@id='buyNew_noncbb']");
                if(count($price)>0){
                    $OfferPrice = ($price[0]->nodeValue); //Get the Product Price
                }else{
                    $price = $a->query("//*[@id='newBuyBoxPrice']");
                    if(count($price)>0){
                        $OfferPrice = ($price[0]->nodeValue); //Get the Product Price
                    }
                    else{
                        $price = $a->query("//*[@id='price_inside_buybox']");
                        if(count($price)>0){
                            $OfferPrice = ($price[0]->nodeValue); //Get the Product Price
                        }
                    } 
                } 
            }
        }
        return trim(preg_replace("/[^0-9,$. ]+/", "",$OfferPrice)) ;
    }//end function
    private function _getBrand($a){

        if(isset($a->query("//a[@id='brand']")[0]->nodeValue)){
            $brand = trim($a->query("//a[@id='brand']")[0]->nodeValue);
        }elseif(isset($a->query("//a[@id='bylineInfo']")[0]->nodeValue)){

            $brand = trim($a->query("//a[@id='bylineInfo']")[0]->nodeValue);
        }elseif(isset($a->query("//a[@id='brandteaser']//img/@src")[0])){

            $brand = trim($a->query("//a[@id='brandteaser']//img/@src")[0]->nodeValue);
        }else{
            $brand = "NA";
        }

        if($brand == "NA"){
            if(isset($a->query("//a[@id='brand']//img/@src")[0])){
                $brand = trim($a->query("//a[@id='brand']//img/@src")[0]->nodeValue);

            }else{
                $brand == "NA";
            }
        }
        return $brand;
    }//end function
    private function _getBreadCrumbs($a){
        $breadcrumbs = $a->query("//div[@id='wayfinding-breadcrumbs_feature_div']//ul[contains(concat(' ', normalize-space(@class), ' '), 'a-unordered-list')]"); //Get Category

        if($breadcrumbs->length > 0){
            foreach($breadcrumbs as $cat){
                $breadcrumbs = $cat->nodeValue;
            }
        }else{

            $category = $a->query("//img[@id='pantry-badge']"); //Get pantry
            if($category->length > 0){
                $breadcrumbs = "Prime Pantry";

            }else{
                $breadcrumbs = "NA";
            }
        }
        $breadcrumbs = preg_replace('/\s+/', ' ', $breadcrumbs);
        return $breadcrumbs;
    }//end function
    private function _getOfferCount($a){
        $q = count($a->query('//*[@id="mbc-upd-olp-link"]//a'))>0?$a->query('//*[@id="mbc-upd-olp-link"]//a')[0]->nodeValue : "NA"; //Check Is Promotional
        if($q !="NA"){
            $oc = substr($q,stripos($q,"(")+1);
            $oc = substr($oc,0,stripos($oc,")"));
            return $oc;
        }
        else{
            $q = count($a->query('//*[@id="olpLinkWidget_feature_div"]//a'))>0?$a->query('//*[@id="olpLinkWidget_feature_div"]//a')[0]->nodeValue : "NA"; //Check Is Promotional
            if($q != "NA"){
                $oc = substr($q,stripos($q,"(")+1);
                $oc = substr($oc,0,stripos($oc,")"));
                return $oc;
            }
            else
            {
                $q = 0;
            }
        }
        
        return preg_replace("/[^0-9]+/", "",$q);
    }//end function
    private function _getTitle($a){
        $title = isset($a->query("//span[@id='productTitle']")[0]->nodeValue)?$a->query("//span[@id='productTitle']")[0]->nodeValue : "NA"; //Get Product Title
        $title= trim($title);
        $title=  $this->_filterString($title);
        return $title ;
    }//end function
    private function _getDescription($a){
         
        $description = isset($a->query("//*[@id='productDescription']/p")[0]->nodeValue)?$a->query("//*[@id='productDescription']/p")[0]->nodeValue:"NA";
        $description = preg_replace('#<script(.*?)>(.*?)</script>#is', '', $description);
        $description = preg_replace('#<style(.*?)>(.*?)</style>#is', '', $description);

        $description = trim($description); //Get description.
        
        return $description ;
    }//end function
    private function _removeStyleAndScriptTagsAndTheirContent($problem){
        
        $problem =preg_replace("#<style(.*?)>(.*?)</style>#is","",$problem);
        $problem =preg_replace("#<script(.*?)>(.*?)</script>#is","",$problem);
        return strip_tags($problem);
    }
    private function _getBestSellerRank($a,$dom){
        $rank = $a->query("(//li[@id='SalesRank'])"); // Get Rank
            if(count($rank)>0){
                $rank_value = $this->_removeStyleAndScriptTagsAndTheirContent( $a->query("(//li[@id='SalesRank'])")[0]->nodeValue); // Get Rank
                $rank_value = preg_replace('/(<(script|style)\b[^>]*>).*?(<\/\2>)/is', "", $rank_value);
                $rank_value = str_replace("Â", "", $rank_value);
                $bestSellerRank = $rank_value;
            }elseif($a->query("(//tr[@id='SalesRank'])")[0]){

                $rank_value =$this->_removeStyleAndScriptTagsAndTheirContent( $a->query("(//tr[@id='SalesRank'])")[0]->nodeValue); // Get Rank
                $rank_value = str_replace("Â", "", $rank_value);
                $bestSellerRank = $rank_value;
            }else{
                $bestSellerRank = "NA";
            }
            $bestSellerRank = trim($bestSellerRank);
            $bestSellerRank = preg_replace('/\s+/', ' ',$bestSellerRank );
            $bestSellerRank = str_replace("a hrefgpbestsellerskitchenrefpdzghrsr","",$bestSellerRank);
            return preg_replace("/[^a-zA-Z0-9#&,. ]+/", "", $bestSellerRank);
    }//end function
    private function _getAvailabilityMessage($a){
        $availability = $a->query("(//div[@id='availability-brief'])//span[contains(concat(' ', normalize-space(@id), ' '), 'availability')]"); // Get Availability
            $availability2 = $a->query("(//span[@id='pantry-availability'])");
                $availabilityMessage="NA";
            if($availability->length == 0){

                $availabilityMessage = isset($a->query("(//div[@id='availability'])//span[contains(concat(' ', normalize-space(@class), ' '), 'a-size-medium')]")[0]->nodeValue) ? $a->query("(//div[@id='availability'])//span[contains(concat(' ', normalize-space(@class), ' '), 'a-size-medium')]")[0]->nodeValue : "NA" ; // Get Availability

            }elseif($availability->length > 0){
                $availabilityMessage = $availability;
            }elseif($availability2->length > 0){

                $availabilityMessage = $a->query("(//span[@id='pantry-availability'])")[0]->nodeValue;
            }else{
                $availabilityMessage = "NA";
            }//end else
            if($availabilityMessage == "NA"){
                $avb = $a->query("(//span[@id='pantry-availability'])");
                if($avb->length > 0 ){
                    $availabilityMessage = $a->query("(//span[@id='pantry-availability'])")[0]->nodeValue;
                }else{
                    $availabilityMessage = "NA";
                }
            }//end if
            $availabilityMessage = preg_replace('/\s+/', ' ', trim($availabilityMessage));
            return preg_replace("/[^a-zA-Z0-9 ]+/", "", trim($availabilityMessage));
    }//end function
    private function _getImageCount($a){
        
        $images = $a->query("//div[@id='altImages']//img/@src"); //Get Images
        $image = array();
        $videoCount = 0;
        foreach($images as $key => $img){  // For Images
            if($this->_is_video($img->nodeValue)){
             $videoCount++;
             continue; 
            }
            if(str_is('*.gif', $img->nodeValue))
            continue;
            $image[$key] = str_ireplace("40_.", "679_.", $img->nodeValue);
        }
        $res = array();
        $res["images"]=$image;
        $res["videoCount"] = $videoCount;

        return $res;
    }//end function
    private function _getBullets($a){
        $rawBullets = $a->query("(//div[@id='feature-bullets'])//span[contains(concat(' ', normalize-space(@class), ' '), 'a-list-item')] "); //Get Bullets
        $bullets = array();
        $allBulletsWordSum = 0;
        foreach($rawBullets as $key => $blt){  // For Bullets
            if($key == 0){
                continue;
            }
            $array = explode(" ",str_replace("\n","",trim($blt->nodeValue)));
            $filtered = Arr::where($array, function ($value, $key) {
                return ($value !="");
            });
            $allBulletsWordSum += count($filtered);
            // $bullets[$key] = "\"". preg_replace("/[^a-zA-Z0-9.,-_()$~: ]+/", "", $blt->nodeValue)."\"";
            $bullets[$key] = "\"".$this->_filterString($blt->nodeValue)."\"";
        }
        $res = array();
        $res["bulletCount"]=count($bullets);

        $bulletString = !empty($bullets) ? "[".trim(implode(",",$bullets))."]" : '';
        $bulletString = preg_replace('/\s+/', ' ',$bulletString);
        $bulletString =  str_ireplace('"Enter your model number to make sure this fits. ",','',$bulletString);

        $res["bullets"]=$bulletString;
        $res["avgWordsPerBulitCount"] =$res["bulletCount"] > 0? ($allBulletsWordSum/$res["bulletCount"]):0.0;

        return $res;
    }//end function
    private function _handleAmazonChoice($a){
        $amazonChoise = count($a->query('//*[@id="acBadge_feature_div"]/div/span[1]')) > 0 ? true : false;
        $amazonChoiseTerm ="NA";
        if($amazonChoise){
            $amazonChoiseTerm = count($a->query('//*[@id="acBadge_feature_div"]/div/span[2]/span/span/a')) > 0 ?$a->query('//*[@id="acBadge_feature_div"]/div/span[2]/span/span/a')[0]->nodeValue : "NA";
        }
        $res["isAmazonChoice"] =  $amazonChoise;
        $res["amazonChoiceTerm"] = trim($amazonChoiseTerm);
        $res["amazonChoiceTerm"] = preg_replace("/[^a-zA-Z0-9 ]+/", "", $res['amazonChoiceTerm']);

        return $res;
    }//end function
    public function  get_data_scraped($url, $c_id=null, $asin=null, $crawler_id = 0){
        $data = NULL;
        $tries = 0;
        //First Try
   
        $this->setActivity(" First Try for scraped Data ","info");
          
        do{

            $data = $this->ASINScrapingCustom($url,$c_id,$asin);
           
                if($data['status'] == FALSE){   
                    $this->setActivity($tries." try for scraped Data ","info");
                $tries++;
                }else{
                    //ASIN Successfully Scraped Breaking Loop
                  
        
                    $this->setActivity("Successfully Scraped Breaking Loop","info");
            
                    break;
                }

        }while($tries < 3);
        return $data;
    }
    private function setActivity($activity, $activity_type){
        ActivityTrackerModel::setActivity(
            $activity, 
            $activity_type, 
            "ScrapingController",
            "App\Libraries\ScrapingController", 
            date('Y-m-d H:i:s')
        );
    }
    public function  get_search_rank_data_scraped($url){
        $data = NULL;
        $tries = 0;
        //First Try
   
        $this->setActivity(" First Try for scraped Data ","info");
          
        do{
            $data = $this->searchRankScrapingCustom($url);
           
            if($data['status'] == FALSE){     
                $this->setActivity($tries." try for scraped Data ","info");
                $tries++;
            } else {
                //ASIN Successfully Scraped Breaking Loop
                $this->setActivity("Successfully Scraped Breaking Loop","info");
                break;
            }
        }while($tries < 3);

        return $data;
    }
    public function initProxy(){
        $proxy = ProxyModel::getRandom();
        if($proxy !="NA")
        {
            return $proxy;
        }
        else
        {
            
            $this->setActivity(" No Proxy Avalialble","info");
            dd("No Proxy Avalialble");
        }
    
    }
    public $proxy_ip;
    public function get_data_curl($url,$c_id=null,$asin=null)
    {
        $request_headers = array();
        $request_headers[] = 'Accept: text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,image/apng,*/*;q=0.8';
        $request_headers[] = 'Accept-Language: en-US,en;q=0.8';
        $request_headers[] = 'Connection: keep-alive';
        $request_headers[] = 'Upgrade-Insecure-Requests: 1';
        $request_headers[] = 'Host: www.amazon.com';
        $request_headers[] = 'User-Agent: ' .$this->get_random_user_agent();

        //getting proxy
      
        $this->setActivity(" Getting proxy ","info");
          
        $proxy_row = $this->initProxy();
        $this->proxy_ip = $proxy_row->proxy_ip;
        $proxy =  $proxy_row->proxy_ip;
        $proxyauth = $proxy_row->proxy_auth;

        $proxy_row->is_blocked = 1;
        $proxy_row->save();
        // $proxy =  "206.41.175.25:80";
        //    $proxyauth = "codeht:c0d3ht";
        //initializing Curl
      
        $this->setActivity("  Initializing Curl ","info");
          
        $ch = curl_init($url);
        $cookie_name = str_replace(array(".", ":"), "-", $proxy);
        $cookie_name = trim($cookie_name);
        $headers = [];
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($ch, CURLOPT_TIMEOUT, 800);  //change this to constant
        curl_setopt($ch, CURLOPT_HTTPHEADER, $request_headers);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
        curl_setopt($ch, CURLOPT_CAINFO, public_path("/crt/cacert.pem"));
        curl_setopt($ch, CURLOPT_COOKIEJAR, getcwd() . '/public/uploads/cookies/' . $cookie_name . '.txt');
        curl_setopt($ch, CURLOPT_COOKIEFILE, getcwd() . '/public/uploads/cookies/' . $cookie_name . '.txt');
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_PROXY, $proxy);
        curl_setopt($ch, CURLOPT_PROXYUSERPWD, $proxyauth);
        curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
        curl_setopt($ch, CURLOPT_ENCODING, '');
      
        //Execution Curl
       
        $this->setActivity("Execution Curl","info");

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
           
            $this->setActivity("  Error Found Line Number 860 HTTP CODE:" . str_limit(($return['error_text']), 500) ,"Error");
        }
            
        curl_close($ch);

        if (is_null($return['error_text'])) {

            // No Error Found Checking captcha
        
            
            if ($this->check_captcha($data) == ScraperConstant::ERROR_CAPTCHA) {
                //Found Captcha
                ActivityTrackerModel::setActivity(
                    "Capcha Found ",
                    "Captcha",
                    "ScrapingController",
                    "App\Libraries\ScrapingController",
                    date('Y-m-d H:i:s')
                );
                $dom = new DOMDocument();
                libxml_use_internal_errors(true);
                 $dom->loadHTML($data);
                $c = new DOMXPath($dom);
                //Extracting Captcha Image
            
                $capcha_image = $c->query("(//div[@class='a-box-inner'])//div[contains(concat(' ', normalize-space(@class), ' '), 'a-text-center')]//img/@src ")[0]->nodeValue;
                 preg_match_all('/<input type=hidden name="(.*?)" value="(.*?)"/', $data, $matches);

                $post_data = array();
                //Requesting For Solving Captcha
             
                $capctha = $this->solve_capcha($capcha_image,$c_id);
                 if ($capctha['status'] == TRUE) {
                     if (count($matches[1]) > 0) {
                        foreach ($matches[1] as $key => $match) {
                            $post_data[$match] = $matches[2][$key];
                        }
                    }
                    $post_data['field-keywords'] = $capctha['message'];
                    //prepareing headers for captcha submission to amason
                
          
                    $request_headers = array();
                    $request_headers[] = 'Accept: text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,*/*;q=0.8';
                    $request_headers[] = 'Accept-Language: en-US,en;q=0.8';
                    $request_headers[] = 'Cache-Control: max-age=0';
                    $request_headers[] = 'Connection: keep-alive';
                    $request_headers[] = 'Upgrade-Insecure-Requests: 1';
                    $request_headers[] = 'Host: www.amazon.com';
                    $request_headers[] = 'Referer: ' . $url;
                   
                    $custom_captcha_url = "https://www.amazon.com/errors/validateCaptcha?" . http_build_query($post_data);

                    //		$url_file = getcwd()."/uploads/".'url.txt';
                    //		file_put_contents($url_file,$custom_captcha_url,FILE_APPEND);

                    $ch = curl_init($custom_captcha_url);
                    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                    curl_setopt($ch, CURLOPT_TIMEOUT, 400);  //change this to constant
                    curl_setopt($ch, CURLOPT_HTTPHEADER, $request_headers);


                    curl_setopt($ch, CURLOPT_PROXY, $proxy);
                    curl_setopt($ch, CURLOPT_PROXYUSERPWD, $proxyauth);

                    $cookie_name = str_replace(array(".", ":"), "-", $proxy);
                    $cookie_name = trim($cookie_name);
                    curl_setopt($ch, CURLOPT_COOKIEJAR, getcwd() . '/public/uploads/cookies/' . $cookie_name . '.txt');
                    curl_setopt($ch, CURLOPT_COOKIEFILE, getcwd() . '/public/uploads/cookies/' . $cookie_name . '.txt');
                    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
                    //curl_setopt($ch, CURLOPT_REFERER, $signin);
                    $return['data'] = curl_exec($ch);
                  
                    //Submitting Captcha request to amazon
                  
                    //	$captcha_check_text = getcwd()."/uploads/".time().'.txt';
                    //	file_put_contents($captcha_check_text,$return['data'],FILE_APPEND);

                    if (curl_errno($ch)) {
                        $return['error_text'] = " Error: " . curl_error($ch);
                        $return['error_code'] = ScraperConstant::ERROR_CURL;
                        $return['status'] = FALSE;
                        $return['html'] = NULL;
                        //Found Error .json_encode($return)
                      
                        return $return;
                    }
                    curl_close($ch);

                } else {
                    //======= Capcha Not Guessed========
                    
                    $return['error_text'] = $capctha['message'];
                    $return['error_code'] = ScraperConstant::ERROR_CAPTCHA;
                    $return['status'] = FALSE;
                    
                   $this->setActivity("Capcha Not Guessed Line Number 705 ".str_limit(json_encode($return) , 500),"info");
                }

                $return['error_code'] = ScraperConstant::ERROR_CAPTCHA;
                $return['error_text'] = "Captcha Found , proxy = " . $proxy;
                $return['status'] = FALSE;

            } else {
                $v_result = $this->validate_data($data);
                if ($v_result["status"]) {
                    // Data is Valid status true
                 
                    $this->setActivity("Data is Valid status true","info");
          
                    $return['data'] = $data;
                    $return['status'] = TRUE;
                } else {
                   
                    //UnKnown Error Error Code Four(4)
                    $return['error_code'] = $v_result["error_code"];
                    $return['data'] = NULL;
                    $return['status'] = FALSE;
     
                    // $this->setActivity($v_result["error_code"]." Error","Error");
                }


            }

        }
      
        return $return;
    }
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
    public function solve_capcha($image,$c_id, $asin=null ){

        $capcha_error_text = "Capacha API ";

        $return  = array();
        $return['status'] = FALSE;
        $return['message'] = "";

        $ccp = new Decaptcha() ;
        // Initializing Decaptcha Library

        $ccp->init();
        if( $ccp->login( ScraperConstant::DECAPTURE_HOST, ScraperConstant::DECAPTURE_PORT, ScraperConstant::DECAPTURE_USERNAME, ScraperConstant::DECAPTURE_PASSWORD) < 0 ) {
            //Login Attempt to de_Captch Failed
        
            $return['message'] = $capcha_error_text."Login Failed";

        } else {
             //Login To Captcha Success Full
         
            $system_load = 0;
            if( $ccp->system_load( $system_load ) != 0 ) {
                //System Load Fail $ccp->system_load( $system_load ) 
              
                $return['message'] = $capcha_error_text." system_load() FAILED";
            }else{
                $major_id	= 0;
                $minor_id	= 0;
                $pict = file_get_contents( $image );
                $text = '';


                $pict_to	= 0;
                $pict_type	= 0;

                $res = $ccp->picture2( $pict, $pict_to, $pict_type, $text, $major_id, $minor_id );

                switch( $res ) {
                    case ScraperConstant::ccERR_OK:
                    //picture found success
          
                        $return['status'] = TRUE;
                        $return['message'] = $text;
                        break;
                    case ScraperConstant::ccERR_BALANCE:
                        $return['message'] = $capcha_error_text." not enough funds to process a picture, balance is depleted";
                        break;
                    case ScraperConstant::ccERR_TIMEOUT:
                        $return['message'] = $capcha_error_text." picture has been timed out on server (payment not taken)";
                        break;
                    case ScraperConstant::ccERR_OVERLOAD:
                        $return['message'] = $capcha_error_text." temporarily server-side error, server's overloaded, wait a little before sending a new picture";
                        break;

                    // local errors
                    case ScraperConstant::ccERR_STATUS:
                        $return['message'] = $capcha_error_text."  local error., either ccproto_init() or ccproto_login() has not been successfully called prior to ccproto_picture()";
                       
                        $return['message'] .= $capcha_error_text." need ccproto_init() and ccproto_login() to be called";
                        break;

                    // network errors
                    case ScraperConstant::ccERR_NET_ERROR:
                        $return['message'] = $capcha_error_text." network troubles, better to call ccproto_login() again";
                        break;

                    // server-side errors
                    case ScraperConstant::ccERR_TEXT_SIZE:
                      
                        $return['message'] = $capcha_error_text."size of the text returned is too big";
                        break;
                    case ScraperConstant::ccERR_GENERAL:
                      
                        $return['message'] = $capcha_error_text."server-side error, better to call ccproto_login() again";
                        break;
                    case ScraperConstant::ccERR_UNKNOWN:
                       
                        $return['message'] = $capcha_error_text." unknown error, better to call ccproto_login() again";
                        break;

                    default:
                        // any other known errors?
                      
                        $return['message'] = $capcha_error_text." Unknown Error";
                        break;
                }
               
                // process a picture and if it is badly recognized
                // call picture_bad2() to name it as error.
                // pictures named bad are not charged

                
            }



        }
        $ccp->close();

        return $return;
    }
    private function _set_fail_status($url,$status,$crawler_id= null){
        FailStatus::create(array(
            "failed_data"=>$url,
            "failed_reason"=>$status,
            "failed_at"=>date('Y-m-d H:i:s'),
            "crawler_id"=>is_null($crawler_id)?0:$crawler_id,
            "created_at"=>date('Y-m-d H:i:s'),
        ));
    }
    private function _set_Instant_ASIN_fail_status($url,$status,$crawler_id= null){
        InstantASINFailStatusModel::create(array(
            "failed_data"=>$url,
            "failed_reason"=>$status,
            "failed_at"=>date('Y-m-d H:i:s'),
            "c_id"=>is_null($crawler_id)?0:$crawler_id
        ));
    }
    private function _is_video($src){
        return strripos($src,"dp-play-icon-overlay__") > 0 ? true : false;
    }
    private function _get_catrgory_from_breadcrumb($breadCrumb){
        if($breadCrumb=="NA")
        return $breadCrumb;
        return trim(substr($breadCrumb,strripos($breadCrumb,"›")+3));
    }
    private function _get_best_seller_catrgory($bsrank){
        if($bsrank=="NA")
        return $bsrank;
        $r = explode(" in ",$bsrank);
        $cat = "";
        for ($i=1; $i < count($r); $i++) { 
            $cat .= trim(preg_replace("/[^a-zA-Z& ]+/", "", $r[$i]));
            $cat .= " | ";
        }
     
        return str_replace_last(" | ","",$cat);
       
    }
    private function _clearBestSellerRank($string){
        
        $string = trim($string);
        
        preg_replace("/<a href=\'([^\"]*)\'>/iU","",$string);
        // dd($string);
        $startPoint = stripos($string,"<a");
        $endPoint = stripos($string,"</a>");
        $length = $endPoint-$startPoint;
        $string =   substr_replace($string,"", $startPoint, $length);
        $string = str_replace("<span>","",$string);
        $string = str_replace("</span>","",$string);
        $string = (str_replace("<br>","",$string));
        $string = (str_replace("</a>","",$string));
        $string = (str_replace("<td>","",$string));
        $string = (str_replace("(","",$string));
        $string = (str_replace(")","",$string));
        $string = (str_replace("See Top 100","",$string));
        $string =  trim($string);
        $startPoint = stripos($string,"<a");
        $endPoint = stripos($string,"'>")+2;
        $length = $endPoint-$startPoint;
        $string =   substr_replace($string,"", $startPoint, $length);
        $string = (str_replace("  ","",$string));
        return trim($string);
    }
    /**
     * When DomXpath fails to identify product details table then
     * this function retrives the products details using preg_match to
     * identify the product details table and then string manipulation
     * alogrithem to extract the information
     *
     * @param mixed $data Takes raw curl response data
     * @return void
     */
    private function _get_Missing_table_data($data){
       
        preg_match('/(productDetails_techSpec_section_1)/', $data, $matches,PREG_OFFSET_CAPTURE);
           $product_info=array();
        if(count($matches)>0){
            $of1 = $matches[0][1];
            $newData =  substr($data,$of1);
            preg_match('/(<\/table>)/',$newData, $matches1,PREG_OFFSET_CAPTURE);

            if(count($matches1)>0){
                $of2 = $matches1[1][1];
                $product_details = substr($newData,0,  $of2);
                $product_details = trim(str_replace("\n","",$product_details));
                // $product_details = trim(substr($product_details,stripos($product_details,"prodDetSectionEntry")));
                $dom = new DOMDocument();
                $dom->loadHTML($product_details);
                $xpath = new DOMXPath($dom);
                $productinformation =  $xpath->query("//tr");
                $product_info = $this->_gather_product_info_of_missing_table($productinformation);
                
            }
        }
        preg_match('/(productDetails_detailBullets_sections1)/', $data, $matches,PREG_OFFSET_CAPTURE);
     
        if(count($matches)>0){
            $of1 = $matches[0][1];
            $newData =  substr($data,$of1);
            preg_match('/(<\/table>)/',$newData, $matches1,PREG_OFFSET_CAPTURE);

            if(count($matches1)>0){
                $of2 = $matches1[1][1];
                $product_details = substr($newData,0,  $of2);
                $product_details = trim(str_replace("\n","",$product_details));
                // $product_details = trim(substr($product_details,stripos($product_details,"prodDetSectionEntry")));
                $dom = new DOMDocument();
                $dom->loadHTML($product_details);
                $xpath = new DOMXPath($dom);
                $productinformation =  $xpath->query("//tr");
                $product_info1 =array();
                $product_info1 = $this->_gather_product_info_of_missing_table($productinformation);
               
                $product_info = array_merge($product_info,$product_info1);
                
            }
        }
        preg_match('/(productDetails_techSpec_section_2)/', $data, $matches,PREG_OFFSET_CAPTURE);
     
        if(count($matches)>0){
            $of1 = $matches[0][1];
            $newData =  substr($data,$of1);
            preg_match('/(<\/table>)/',$newData, $matches1,PREG_OFFSET_CAPTURE);

            if(count($matches1)>0){
                $of2 = $matches1[1][1];
                $product_details = substr($newData,0,  $of2);
                $product_details = trim(str_replace("\n","",$product_details));
                // $product_details = trim(substr($product_details,stripos($product_details,"prodDetSectionEntry")));
                $dom = new DOMDocument();
                $dom->loadHTML($product_details);
                $xpath = new DOMXPath($dom);
                $productinformation =  $xpath->query("//tr");
                $product_info1 =array();
                $product_info1 = $this->_gather_product_info_of_missing_table($productinformation);
               
                $product_info = array_merge($product_info,$product_info1);
                
            }
        }
              
        return $product_info;
    }
    private function _gather_product_info_of_missing_table($productinformation){

        $product_info = array();
        $detailArr = array();
        foreach($productinformation as $key => $dt){
            $keyDetail = "";
            $valDetail = "";
            foreach ($dt->childNodes as $key => $value) {
                // dump($value->nodeName);
                if($value->nodeName == "th")
                {
                    $val = (preg_replace('/\t+/', ' ', $value->nodeValue));
                    $val = (preg_replace('/\n+/', ' ', $val));
                    $val = (preg_replace('/\s\s+/', ' ', $val));
                    $keyDetail = $this->_removeStyleAndScriptTagsAndTheirContent(trim($val));
                    // dump($val);
                    // dump(($value->ownerDocument->textContent));
                }
                if($value->nodeName == "td")
                {
                    $val = (preg_replace('/\t+/', ' ', $value->nodeValue));
                    $val = (preg_replace('/\n+/', ' ', $val));
                    $val = (preg_replace('/\s\s+/', ' ', $val));
                    $valDetail = $this->_removeStyleAndScriptTagsAndTheirContent(trim($val));
                    $valDetail = preg_replace("/\(([^()]*+|(?R))*\)/","",$valDetail);
                    // dump($val);
                    // dump(($value->ownerDocument->textContent));
                }

            }
            $detailArr[$keyDetail] = $valDetail;
            
        }
        foreach($detailArr as $key => $dt){
           
            // $detailArr[$keyDetail] = $valDetail;
            if(stripos(trim($key),"Best Sellers Rank" ) !== FALSE){
                $product_info['rank'] =   $dt;
            }
            elseif(stripos(trim($key),"Amazon Best Sellers Rank" ) !== FALSE){
                $product_info['rank'] =  $dt;
            }
            elseif(stripos(trim($key),"Item model number" ) !== FALSE){
                $product_info['modelNo'] =  $dt;
            }
            elseif(stripos(trim($key),"Shipping Weight" ) !== FALSE){
                $product_info['shipWeight'] =  $dt;
            }elseif(stripos(trim($key),"Date first listed on Amazon" ) !== FALSE){
                $product_info['dateFirstAvailable'] = $dt;
            }
            elseif(stripos(trim($key),"Date First Available" ) !== FALSE){
                $product_info['dateFirstAvailable'] =  $dt;
            }  
            elseif(stripos(trim($key),"Size of the product" ) !== FALSE){
                $product_info['size'] =   $dt;
            }
            elseif(stripos(trim($key),"Size" ) !== FALSE){
                $product_info['size'] = $dt;
            }
            elseif(stripos(trim($key),"Color" ) !== FALSE){
                $product_info['color'] = $dt;
            }
            elseif(stripos(trim($key),"Item Weight" ) !== FALSE){
                $product_info['weight'] = $dt;
            }
            elseif(stripos(trim($key),"Package Weight" ) !== FALSE){
                $product_info['weight'] =  $dt;
            }
            elseif(stripos(trim($key),"Product Dimensions") !== FALSE){
                $product_info['dimensions'] = $dt;
            }
            elseif(stripos(trim($key),"Item Dimensions" ) !== FALSE){
                $product_info['dimensions'] = $dt;
            }
            elseif(stripos(trim($key),"Package Dimensions" ) !== FALSE){
                $product_info['dimensions'] = $dt;
            }
            elseif(stripos(trim($key),"Item model number" ) !== FALSE){
                $product_info['modelNo'] =  $dt;
            }
        }
        dump($product_info);
        return $product_info;
    }
    private function _gather_product_info($productinformation)
    {

        $product_info = array();
        foreach ($productinformation as $key => $dt) {

            if (stripos(trim($dt->nodeValue), "Best Sellers Rank") !== FALSE) {
                $product_info['rank'] =   str_replace('Best Sellers Rank', '', $this->_removeStyleAndScriptTagsAndTheirContent($dt->nodeValue));
            } elseif (stripos(trim($dt->nodeValue), "Amazon Best Sellers Rank") !== FALSE) {
                $product_info['rank'] =  str_replace('Amazon Best Sellers Rank', '', $this->_removeStyleAndScriptTagsAndTheirContent($dt->nodeValue));
            } elseif (stripos(trim($dt->nodeValue), "Item model number") !== FALSE) {
                $product_info['modelNo'] =  str_replace('Item model number', '', $dt->nodeValue);
            } elseif (stripos(trim($dt->nodeValue), "Shipping Weight") !== FALSE) {
                $product_info['shipWeight'] =  str_replace('Shipping Weight', '', $dt->nodeValue);
                $product_info['shipWeight'] = trim(preg_replace('/\n+/', ' ', $product_info['shipWeight']));
            } elseif (stripos(trim($dt->nodeValue), "Date first listed on Amazon") !== FALSE) {
                $product_info['dateFirstAvailable'] =  str_replace('Date first listed on Amazon', '', $dt->nodeValue);
                $product_info['dateFirstAvailable'] = trim(preg_replace('/\n+/', ' ', $product_info['dateFirstAvailable']));
            } elseif (stripos(trim($dt->nodeValue), "Date First Available") !== FALSE) {
                $product_info['dateFirstAvailable'] =  str_replace('Date First Available', '', $dt->nodeValue);
                $product_info['dateFirstAvailable'] = trim(preg_replace('/\n+/', ' ', $product_info['dateFirstAvailable']));
            } elseif (stripos(trim($dt->nodeValue), "Size of the product") !== FALSE) {
                $product_info['size'] =   str_replace('Size of the product', '', $dt->nodeValue);
                $product_info['size'] =  trim(preg_replace('/\n+/', ' ', $product_info['size']));
            } elseif (stripos(trim($dt->nodeValue), "Size") !== FALSE) {
                $product_info['size'] =   str_replace('Size', '', $dt->nodeValue);
                $product_info['size'] =  trim(preg_replace('/\n+/', ' ', $product_info['size']));
            } elseif (stripos(trim($dt->nodeValue), "Color") !== FALSE) {
                $product_info['color'] =  str_replace('Color', '', $dt->nodeValue);
                $product_info['color'] =  trim(preg_replace('/\n+/', ' ', $product_info['color']));
            } elseif (stripos(trim($dt->nodeValue), "Item Weight") !== FALSE) {
                $product_info['weight'] =  str_replace('Item Weight', '', $dt->nodeValue);
                $product_info['weight'] =   trim(preg_replace('/\n+/', ' ', $product_info['weight']));
            } elseif (stripos(trim($dt->nodeValue), "Package Weight") !== FALSE) {
                $product_info['weight'] =  str_replace('Package Weight', '', $dt->nodeValue);
                $product_info['weight'] =   trim(preg_replace('/\n+/', ' ', $product_info['weight']));
            } elseif (stripos(trim($dt->nodeValue), "Product Dimensions") !== FALSE) {
                $product_info['dimensions'] =  str_replace('Product Dimensions', '', $dt->nodeValue);
                $product_info['dimensions'] =  trim(preg_replace('/\n+/', ' ', $product_info['dimensions']));
            } elseif (stripos(trim($dt->nodeValue), "Item Dimensions") !== FALSE) {
                $product_info['dimensions'] =  str_replace('Item Dimensions', '', $dt->nodeValue);
                $product_info['dimensions'] =  trim(preg_replace('/\n+/', ' ', $product_info['dimensions']));
            } elseif (stripos(trim($dt->nodeValue), "Package Dimensions") !== FALSE) {
                $product_info['dimensions'] =  str_replace('Package Dimensions', '', $dt->nodeValue);
                $product_info['dimensions'] =  trim(preg_replace('/\n+/', ' ', $product_info['dimensions']));
            } elseif (stripos(trim($dt->nodeValue), "Item model number") !== FALSE) {
                $product_info['modelNo'] =  str_replace('Item model number', '', $dt->nodeValue);
                $product_info['modelNo'] =  trim(preg_replace('/\n+/', ' ', $product_info['modelNo']));
            }
        }
        return $product_info;
    }
    
    private function _gather_detail_bullets2($productinformation,$dom){
        
        $product_info = array();
        foreach($productinformation as $key => $dt){

            if(stripos(trim($dt->nodeValue),"UPC:" ) !== FALSE){
                $product_info['upc'] =   str_replace('UPC:','',$this->_removeStyleAndScriptTagsAndTheirContent($dt->nodeValue));
                $product_info['upc'] =   preg_replace("/[^0-9a-zA-Z& ]+/", ' ', $product_info['upc']);
            }elseif(stripos(trim($dt->nodeValue),"Item model number:" ) !== FALSE){
                $product_info['modelNo'] =  str_replace('Item model number:','',$this->_removeStyleAndScriptTagsAndTheirContent($dt->nodeValue));
                $product_info['upc'] =   preg_replace("/[^0-9a-zA-Z& ]+/", ' ', $product_info['modelNo']);
            }
            elseif(stripos(trim($dt->nodeValue),"Product Dimensions" ) !== FALSE){
                $product_info['dimensions'] =  str_replace('Product Dimensions','',($dt->nodeValue));
                $product_info['dimensions'] =  trim(  preg_replace('/\n+/', ' ', $product_info['dimensions'])) ;
                // $product_info['dimensions'] =   preg_replace("/[^0-9a-zA-Z& ]+/", ' ', $product_info['dimensions']);
            } 
            elseif(stripos(trim($dt->nodeValue),"Amazon Best Sellers Rank" ) !== FALSE){
                
                $rankHTML = ($dom->saveHTML($dt));
                $rankHTML = $this->_removeStyleAndScriptTagsAndTheirContent($rankHTML);
                if(stripos(trim($rankHTML),"Amazon Best Sellers Rank" ) !== FALSE){
                    $product_info['rank'] =  str_replace('Amazon Best Sellers Rank','',trim($rankHTML));
                }
                $product_info['rank'] =   preg_replace("/[^0-9a-zA-Z,#&() ]+/", ' ', $product_info['rank']);
                $product_info['rank'] = preg_replace("/\(([^()]*+|(?R))*\)/","", $product_info['rank']);
                $product_info['rank'] = trim(preg_replace('/amp+/', ' ', $product_info['rank'])) ;
                $product_info['rank'] = trim(preg_replace('/\s+/', ' ', $product_info['rank'])) ;
            }
            elseif(stripos(trim($dt->nodeValue),"Shipping Weight" ) !== FALSE){
                $product_info['shipWeight'] =  str_replace('Shipping Weight','',$this->_removeStyleAndScriptTagsAndTheirContent($dt->nodeValue));
                $product_info['shipWeight'] = trim(  preg_replace('/\n+/', ' ', $product_info['shipWeight'])) ;
                $product_info['shipWeight'] = trim(  preg_replace('/:+/', ' ', $product_info['shipWeight'])) ;
                // $product_info['shipWeight'] =   preg_replace("/[^0-9a-zA-Z& ]+/", ' ', $product_info['shipWeight']);
            }
        }
        return $product_info;
    }
    /**
     * @param 
     */
    public function gather_detail_bullets($productinformation){

        $product_info = array();
        foreach($productinformation as $key => $dt){

            if(stripos(trim($dt->nodeValue),"UPC:" ) !== FALSE){
                $product_info['upc'] =   str_replace('UPC:','',$dt->nodeValue);
            }elseif(stripos(trim($dt->nodeValue),"Item model number:" ) !== FALSE){
                $product_info['modelNo'] =  str_replace('Item model number:','',$dt->nodeValue);
            }
        }
        return $product_info;
    }
    public $cok;
    public function ASINScrapingCustom($url,$c_id=null,$asin=null)
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
        
            $this->setActivity(" Getting proxy ","info");
            
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
        
            $this->setActivity("Curl Started ","info");
            
            $ch = curl_init($url);
            $cookie_name = str_replace(array(".", ":"), "-", $proxy);
            $cookie_name = trim($cookie_name);
            $headers = [];
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
            curl_setopt($ch, CURLOPT_TIMEOUT, 300);  //change this to constant
            curl_setopt($ch, CURLOPT_HTTPHEADER, $request_headers);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
            curl_setopt($ch, CURLOPT_CAINFO, public_path("/crt/cacert.pem"));
            curl_setopt($ch, CURLOPT_COOKIEJAR, getcwd() . '/public/uploads/cookies/' . $cookie_name .$this->cok. '.txt');
            curl_setopt($ch, CURLOPT_COOKIEFILE, getcwd() . '/public/uploads/cookies/' . $cookie_name .$this->cok. '.txt');
            curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
            curl_setopt($ch, CURLOPT_PROXY, $proxy);
            curl_setopt($ch, CURLOPT_PROXYUSERPWD, $proxyauth);
            curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
            curl_setopt($ch, CURLOPT_ENCODING, '');
        
            //Execution Curl
        
            
            $data = trim(curl_exec($ch));
            $this->setActivity("Curl Completed ","info");
         
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
                    ActivityTrackerModel::setActivity("Ip Block $proxy_row->proxy_ip", "ipBlocked", "InstantScrapingController", "App\Libraries\InstantScrapingController", date('Y-m-d H:i:s'));
                }
            }
            if (empty(($data)) && is_null($return['error_text'])) {
                $return['status'] = FALSE;
                $return['error_code'] = ScraperConstant::ERROR_CURL;
                $return['error_text'] = "Empty string in response from amazon ".json_encode($data);
                $proxy_row->is_blocked = 1;
                $proxy_row->save();
                ActivityTrackerModel::setActivity("Ip Block $proxy_row->proxy_ip", "ipBlocked", "InstantScrapingController", "App\Libraries\InstantScrapingController", date('Y-m-d H:i:s'));
            }
            curl_close($ch);

            $pathToFile = public_path('/uploads/cookies/' . $cookie_name .$this->cok. '.txt'); 
            if(File::delete($pathToFile)){
              
            }
           
            if (is_null($return['error_text'])) {
                if ($this->check_captcha($data) == ScraperConstant::ERROR_CAPTCHA) {
                    //Found Captcha
                    // print_r("$url Captcha Found");
                    // echo "<br>";
                    $this->setActivity("Captcha Found Retrying","Captcha D");
                    $isCaptchaFound = true;
                    $proxy_rowt = $this->initProxy();
                    $proxy_row->is_blocked = 0;
                    $proxy_row->save();
                    $proxy_row = $proxy_rowt; 
                    $this->cok++;
                    continue;
                } else {
                    $v_result = $this->validate_data($data);
                    if ($v_result["status"]) {
                        // Data is Valid status true
                        ActivityTrackerModel::setActivity("Success $proxy_row->proxy_ip", "Success", "InstantScrapingController", "App\Libraries\InstantScrapingController", date('Y-m-d H:i:s'));
                        $return['data'] = $data;
                        $return['status'] = TRUE;
                        break;
                    } else {
                    
                        //UnKnown Error Error Code Four(4)
                        $return['error_code'] = $v_result["error_code"];
                        $return['data'] = NULL;
                        $return['status'] = FALSE;
                        ActivityTrackerModel::setActivity("Error Occureds $proxy_row->proxy_ip ".$return['error_code'], "Captcha D", "InstantScrapingController", "App\Libraries\InstantScrapingController", date('Y-m-d H:i:s'));
                        $isCaptchaFound = true;
                        $proxy_rowt = $this->initProxy();
                        $proxy_row->is_blocked = 0;
                        $proxy_row->save();
                        $proxy_row = $proxy_rowt; 
                        $this->cok++;
                        // if($v_result["error_code"]== ScraperConstant::ERROR_PRODUCTS_NOT_FOUND){
                            
                        //      break;
                        // }
                        // else{
                        //     // print_r("$url Service Not Available");
                        //     // echo "<br>";
                           
                        //     break;
                        // }
                         break;
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
         
        $isCaptchaFound = false;
        $proxy_row->is_blocked = 0;
       $proxy_row->save();
        return $return;
    }
    public function searchRankScrapingCustom($url)
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
        
            $this->setActivity(" Getting proxy ","info");
            
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
        
            $this->setActivity("Curl Started ","info");
            
            $ch = curl_init($url);
            $cookie_name = str_replace(array(".", ":"), "-", $proxy);
            $cookie_name = trim($cookie_name);
            $headers = [];
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
            curl_setopt($ch, CURLOPT_TIMEOUT, 800);  //change this to constant
            curl_setopt($ch, CURLOPT_HTTPHEADER, $request_headers);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
            curl_setopt($ch, CURLOPT_CAINFO, public_path("/crt/cacert.pem"));
            curl_setopt($ch, CURLOPT_COOKIEJAR, getcwd() . '/public/uploads/cookies/' . $cookie_name .$this->cok. '.txt');
            curl_setopt($ch, CURLOPT_COOKIEFILE, getcwd() . '/public/uploads/cookies/' . $cookie_name .$this->cok. '.txt');
            curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
            curl_setopt($ch, CURLOPT_PROXY, $proxy);
            curl_setopt($ch, CURLOPT_PROXYUSERPWD, $proxyauth);
            curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
            curl_setopt($ch, CURLOPT_ENCODING, '');
        
            //Execution Curl
        
            $this->setActivity("Execution Curl","info");

            $data = trim(curl_exec($ch));

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
                    ActivityTrackerModel::setActivity("Ip Block $proxy_row->proxy_ip", "ipBlocked", "InstantScrapingController", "App\Libraries\InstantScrapingController", date('Y-m-d H:i:s'));
                }
            }
            if (empty(($data)) && is_null($return['error_text'])) {
                $return['status'] = FALSE;
                $return['error_code'] = ScraperConstant::ERROR_CURL;
                $return['error_text'] = "Empty string in response from amazon ".json_encode($data);
                $proxy_row->is_blocked = 1;
                $proxy_row->save();
                ActivityTrackerModel::setActivity("Ip Block $proxy_row->proxy_ip", "ipBlocked", "ScrapingController", "App\Libraries\ScrapingController", date('Y-m-d H:i:s'));
            }

            curl_close($ch);

            $pathToFile = public_path('/uploads/cookies/' . $cookie_name .$this->cok. '.txt'); 
            File::delete($pathToFile);
           
            if (is_null($return['error_text'])) {
                
                if ($this->check_captcha($data) == ScraperConstant::ERROR_CAPTCHA) {
                    //Found Captcha
                    $this->setActivity("Captcha Found Retrying","Captcha D");
                    $isCaptchaFound = true;
                    $proxy_rowt = $this->initProxy();
                    $proxy_row->is_blocked = 0;
                    $proxy_row->save();
                    $proxy_row = $proxy_rowt; 
                    $this->cok++;
                    continue;
                } else {
                    $v_result = $this->validate_data($data);
                    if ($v_result["status"]) {
                        // Data is Valid status true
                        $return['data'] = $data;
                        $return['status'] = TRUE;
                        break;
                    } else {
                    
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
