<?php

namespace App\Http\Controllers;

use App\Models\ScrapingModels\asinModel;
use Laracsv\Export;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Rap2hpoutre\FastExcel\FastExcel;
use App\Models\ScrapingModels\SearchRankScrapedResultModel;

class HistoricalDataPreviewController extends Controller
{
    public function __construct()
    {
        //$this->middleware('auth');
        $this->middleware('auth.super_admin');
    }

    public $var = "bilal";
    /**
     * Shows ASIN's Scraping Historical Data Form
     */
    public function showHistoryFrom()
    {
        $data['pageTitle'] = 'Export CSV';
        $data['pageHeading'] = 'History';
        return view("subpages.historicalData.asin_historical_data")->with($data);
    } //end function

    /**
     * varifies the that record exists against the current date and time
     *Post Request via Ajax
     * @param Request $request
     * @return void
     */
    public function checkHistory(Request $request)
    {
        ini_set('memory_limit', '2048M');
        ini_set('max_execution_time', 0);
        try {
         
            $totalData =  asinModel::checkASINData($request->startDate, $request->endDate)[0]->totalData;
            $response = array();
            $response["status"] = true;
    
            $response["exceptionStatus"] = false;
            if ($totalData > 0) {
                $response["url"] = url("download/" . $request->startDate . "/" . $request->endDate);
                $response["message"] = "Please wait your file will be downloaded in few seconds";
                return $response;
            }
    
            $response["status"] = false;
            $response["message"] = "No Data Found against This Date";
            return $response;
        } catch (\Throwable $th) {
            $response["status"] = false;
            
            $response["exceptionStatus"] = true;
            $response["message"] = "Sorry Some thing went worng please refresh and try again";
            $response["exception"] = $th->getMessage();
            return $response;
        }
       
    } //end function

    /**
     * downloadCSV a get request to this method will enables the download of the csv file with ASIN's Scraping Historical Data
     *
     * @param mixed $startDate
     * @param mixed $endDate
     * @return void
     */
    public function downloadCSV($startDate, $endDate)
    {
        ini_set('memory_limit', '2048M');
        ini_set('max_execution_time', 0);
        try {
                
            $asins =  asinModel::getASINData($startDate, $endDate);
            return (new FastExcel(collect($asins)))->download("$startDate-$endDate.csv", function ($result) {
                return  [
                    "Collection Name" =>$this->filterString($result->c_name),
                    "Collection Type" =>$result->c_type == 0 ? "Scraped Instantly ":"Scraped Daily",
                    "Product Title" =>$this->filterString($result->title),
                    "URL" =>$this->filterString($result->url),
                    "ASIN" =>$this->filterString($result->asin),
                    "Scraped At" =>$this->filterString($result->capturedAt),
                    "Meta Key Words" =>$this->filterString($result->metaKeywords),
                    "Brand" =>$this->filterString($result->brand),
                    "Offer Price Orignal" =>$result->offerPriceOrignal,
                    "Offer Price" =>$result->offerPrice,
                    "List Price Orignal" =>$result->listPriceOrignal,
                    "List Price" =>$result->listPrice,
                    "Offer Count" =>$result->offerCount,
                    "Model No" =>$this->filterString($result->modelNo),
                    "Bread Crumbs" =>$this->filterString($result->breadcrumbs),
                    "Category" =>$this->filterString($result->category),
                    "Images" =>$this->filterString($result->images),
                    "ImageCount" =>$result->imageCount,
                    "Video Count" =>$result->videoCount,
                    "Bullets" =>$this->filterString($result->bullets),
                    "Bullet Count" =>$result->bulletCount,
                    "Average Words Per Bullet Count" =>$result->avgWordsPerBulitCount,
                    "Aplus" =>$this->filterString($result->aplus),
                    "Aplus Module Exist" =>$result->aplusModule == 0 ? "No":"Yes",
                    "Average Review" =>$this->filterString($result->averageReview),
                    "Total Reviews" =>$result->totalReviews,
                    "Best Seller Rank" =>$this->filterString($result->bestSellerRank),
                    "Best Seller Category" =>$this->filterString($result->bestSellerCategory),
                    "Is Prime" => $result->isPrime ==0 ? "No":"Yes",
                    "Is Available" => $result->isAvailable ==0 ? "No":"Yes",
                    "Availability Message" =>$this->filterString($result->availabilityMessage),
                    "Is Amazon Choice" =>$result->isAmazonChoice == 0 ? "No":"Yes",
                    "Amazon Choice Term" =>$this->filterString($result->amazonChoiceTerm),
                    "Is Promo" => $result->isPromo ==0 ? "No":"Yes",
                    "Seller" =>$this->filterString($result->seller),
                    "Size" =>$this->filterString($result->size),
                    "Color" =>$this->filterString($result->color),
                    "Weight" =>$this->filterString($result->weight),
                    "Length" => $result->length,
                    "Width" => $result->width,
                    "Height" => $result->height,
                    "Ship Weight" =>trim(preg_replace("/[^a-zA-Z0-9-x.,_ ]+/", "", $result->shipWeight)),
                    "Date First Available" =>$result->dateFirstAvailable,
                ];
            });
        } catch (\Throwable $th) {
            Log::info($th);
        }
    } //end function
    private function filterString($string){
        return trim(preg_replace('/\s\s+/', '', trim($string)));
    }

    public function showSearchRankHistoryFrom()
    { 
        $data['pageTitle'] = 'Export CSV';
        $data['pageHeading'] = 'Export CSV';
        return view("subpages.historicalData.sr_historical_data")->with($data);
    }

    /**
     * varifies the that record exists against the current date
     *Post Request via Ajax
     * @param Request $request
     * @return void
     */
    public function checkSearchRankHistory(Request $request)
    { 
        ini_set('memory_limit', '2048M');
        ini_set('max_execution_time', 0);
        $totalResults = SearchRankScrapedResultModel::checkSearchRankData($request->startDate, $request->endDate)[0]->totalResults;

        $response = array();
        $response["status"] = true;

        if ($totalResults > 0) {
            $response["url"] = url("sr/download/" . $request->startDate . "/" . $request->endDate);
            $response["message"] = "Please wait your file will be downloaded in few seconds";
            return $response;
        }

        $response["status"] = false;
        $response["message"] = "No Data Found against This Date";
        return $response;
    }
     /**
     * downloadCSV a get request to this method will enables the download of the csv file with ASIN's Scraping Historical Data
     *
     * @param mixed $startDate
     * @param mixed $endDate
     * @return void
     */
    public function downloadSearchRankCSV($startDate, $endDate)
    {
        ini_set('memory_limit', '2048M');
        ini_set('max_execution_time', 0);
        try{
            $asins =  SearchRankScrapedResultModel::getSearchRankData($startDate, $endDate);
          return (new FastExcel(collect($asins)))->download("$startDate-$endDate.csv", function ($result) {
            return  [
                    "Search Term" =>$this->filterString($result->st_term),
                    "Product Title" =>$this->filterString($result->title),
                    "Brand" =>$this->filterString($result->brand),
                    "ASIN" =>$this->filterString( $result->asin),
                    "Rank" =>( $result->rank),
                    "Offer Price Orignal" =>($result->offerPriceOrignal),
                    "Offer Price" =>($result->offerPrice),
                    "List Price Orignal" =>($result->listPriceOrignal),
                    "List Price" =>($result->listPrice),
                    "Offer Count" =>($result->offerCount),
                    "Is Sponsered" =>( $result->isSponsered) ==0 ? "No":"Yes",
                    "Is Promo" =>( $result->isPromo) ==0 ? "No":"Yes",
                    "Is Best Seller" =>( $result->isBestSeller) ==0 ? "No":"Yes",
                    "Is Amazon Choice" =>($result->isAmazonChoice) == 0 ? "No":"Yes",
                    "Is Prime" =>( $result->isPrime) ==0 ? "No":"Yes",
                    "Review Count" =>($result->reviewCount),
                    "Created At" =>$this->filterString($result->created_at),
                ];
            });
        } catch (\Throwable $th) {
            Log::info($th);
        }
      
    } //end function
    
    private function _filterWeight($result){
        if($result->weight != "NA"){
            return $result->weight;
        }
        $dimensionRaw =  explode(";",$result->dimensions); 
        $dimensionRawCount = count($dimensionRaw);
        if($dimensionRawCount != 2){
            return "NA";
        }
        $weight = trim($dimensionRaw[1]);
      
        return $weight;
        
    }//end functon
}//end class
