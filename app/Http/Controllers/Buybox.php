<?php

namespace App\Http\Controllers;

use Artisan;
use DOMDocument;
use DOMXPath;
use Illuminate\Support\Facades\Log;
use Mail;
use GuzzleHttp\Client;
use Illuminate\Http\Request;
use App\Models\BuyBoxModel;
use App\Http\Resources\Decaptcha;
use App\Http\Resources\cc_packet;
use App\Http\Resources\cc_pict_descr;
use App\Http\Resources\cc_balance_transfer_descr;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\File;
use Rap2hpoutre\FastExcel\FastExcel;
use Illuminate\Support\Facades\Storage;

class Buybox extends Controller
{
    public function __construct()
    {
        //$this->middleware('auth');
        $this->middleware('auth.super_admin');
    }

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function dashboard()
    {
        $data['pageTitle'] = 'BuyBox Dashboard';
        $data['pageHeading'] = 'BuyBox Dashboard';
        return view('subpages.buybox.dashboard')->with($data);
    }

    public function scheduling()
    {
//         $currentCronTime = date("Y-m-d H:i");
//         $time = "24:00";
//         $CronArrayResponse = BuyBoxModel::geBuyBoxCronList();
//         if (!$CronArrayResponse->isEmpty()) {
//             // get enable cron lists
//             foreach ($CronArrayResponse as $singleCron) {
//                 $frequency = $singleCron->frequency;
//                 list($hours, $minutes) = explode(":", $time);
//                 $hours = $hours / $frequency;
//                 $lastCronTime = date("Y-m-d H:i", strtotime($singleCron->nextRun)); // last cron time
//                 $currentCronTime = date("Y-m-d H:i");
//                 $nextCronTime = date("Y-m-d H:i", strtotime("+" . $hours . " hours"));

//                 echo "hour dffer: " . $hours;
//                 echo '<br/>';
//                 echo "last cron time: " . $lastCronTime;
//                 echo '<br/>';

//                 $currentCronTimeDiffer = date("Y-m-d");
//                 $nextCronTimeDiffer = date("Y-m-d", strtotime("+" . $hours . " hours"));
//                 echo "current cron time: " . $currentCronTime;
//                 echo '<br/>';
//                 echo "next cron time: " . $nextCronTime;
//                 if ($nextCronTimeDiffer > $currentCronTimeDiffer) {
//                     echo '<br/>';
//                     echo 'chnage frquency status';
//                     echo '<br/>';
//                     echo "next cron time: " . $nextCronTime;

//                 }
//                 dd($singleCron);
//                 // check Current system Time equal to Cron Set Time
//                 // Check Last run cron time less than coming next Cron time
//                 if (1) {
//                     dd($singleCron);
//                     // tracker code
//                     AMSModel::insertTrackRecord('got enabled crons type ' . $cronType, 'record found');
//                     // Update Token
//                     Artisan::call('getaccesstoken:amsauth');
//                     // call function gathering api data
//                     $this->innerFunction($singleCron);
//                 } elseif ($cronRunStaus == 1 && $CronTime < $currentTimeNow) { // change cronRun status again 0
//                     // tracker code
//                     AMSModel::insertTrackRecord('change enabled crons type ' . $cronType, 'success');
//                     Log::info('start update query for update CronRun status to 0');
//                     $updateArray = array(
//                         'modifiedDate' => Config::get('constants.dateTimeFormat'),
//                         'cronRun' => '0',
//                     );
//                     AMSModel::updateCronRunStatus($cronType, $updateArray);
//                     Log::info('end update query for update CronRun status to 0');
//                 } else {
//                     // tracker code
//                     AMSModel::insertTrackRecord('Currently no cron time occur ' . $cronType, 'success');
//                     echo 'Currently no cron time occur.';
//                     Log::info('Currently no cron time occur.');
//                 }
//             }// end foreach loop
//             Log::info('End foreach loop');
//         } else {
//             dd($CronArrayResponse);
//             // store track data
//             //AMSModel::insertTrackRecord('not get enable crons list', 'not record found');
//             echo 'not record found';
//             //Log::info('not record found');
//         }
//         Log::info('End Cron for AMS');
//         exit;
// //        $frequency = 4;
//        $time = "24:00";
//        list($hours, $minutes) = explode(":", $time);
//        $hours = $hours / $frequency;
//        $currentCronTime = date("Y-m-d H:i");
//        $nextCronTime = date("Y-m-d H:i", strtotime("+" . $hours . " hours"));
//        $currentCronTimeDiffer = date("Y-m-d");
//        $nextCronTimeDiffer = date("Y-m-d", strtotime("+" . $hours . " hours"));
//        echo "current cron time: " . $currentCronTime;
//        echo '<br/>';
//        echo "next cron time: " . $nextCronTime;
//        if($nextCronTimeDiffer > $currentCronTimeDiffer){
//            echo '<br/>';
//            echo 'chnage frquency status';
//            echo '<br/>';
//            echo "next cron time: " . $nextCronTime;
//
//        }
//        exit;
        $data['status'] = true;
        $data['data'] = $this->getBuyBoxCrons();
        return $data;
    }
    private function getBuyBoxCrons(){
        return BuyBoxModel::orderBy("id")
        ->get()
        ->map(function($item, $index){
            return [
                "sr" => $index + 1,
                "id" => $item->id,
                "email" => $item->email,
                "cName" => $item->cNameBuybox,
                "frequency" => $item->frequency,
                "frequencyRemaining" => $item->currentFrequency,
                "duration" => $item->duration,
                "nextRun" => $item->nextRun,
                "isRunning" => $item->cronStatus ? "Running" : "Pending",
                "createdAt" => $item->createdAt,
            ];
        });
    }
    private function getNextRunTimeAndDate($frequency){
       $data = array();
        $time = "24:00";
        list($hours, $minutes) = explode(":", $time);
        $hours = (round($hours / $frequency));
        $data["hoursToAdd"] = $hours;
        return $data;
    }//end function

   
    /**
     * @param Request $request
     * @return false|\Illuminate\Http\JsonResponse|string
     * @throws \Box\Spout\Common\Exception\IOException
     * @throws \Box\Spout\Common\Exception\UnsupportedTypeException
     * @throws \Box\Spout\Reader\Exception\ReaderNotOpenedException
     */
    public function addbatch(Request $request)
    {
        ini_set('memory_limit', '2048M');
        ini_set('max_execution_time', 0);
        $response = array();
        $response["status"] = false;
        if ($request->hasFile('asinfiles')) {
            $file = $request->file('asinfiles');
            $c_name_buybox = $request->input('c_name_buybox', true);
            $email = $request->input('buybox_email', true);
            $frequency = $request->input('frequency', true);
            $duration = $request->input('duration', true);
            $fileExt = $file->getClientOriginalExtension();
            

            if ($fileExt != 'xls' && $fileExt != 'xlsx' && $fileExt != 'csv') {
                $response["message"] = "Please Select A Valid File Type";
                $response["status"] = FALSE;
                return json_encode($response);
            }
            $fullFileName = $file->getClientOriginalName(); //getting Full File Name
            $fileNameOnly = pathinfo($fullFileName, PATHINFO_FILENAME); //getting File Name With out extension
            $newFileName = $fileNameOnly . '_' . time() . '.' . $fileExt; //Foramting New Name with Time stamp for avoiding any duplicated names in databese
            $inputFileName = request()->asinfiles->move(public_path('uploads'), $newFileName);
            $collection = (new FastExcel)->import($inputFileName);
            if (isset($collection[0]['asin']) && !empty($collection[0]['asin'])) {
                // write insertion code here
                $runData = $this->getNextRunTimeAndDate($frequency);
                $responseInsert = BuyBoxModel::insertRecord($email, $c_name_buybox, $frequency, $duration, $collection, $runData);
                if ($responseInsert == FALSE) {
                    $response["message"] = "This collection name already exist.";
                    $response["status"] = FALSE;
                    return json_encode($response);
                }
            } else {
                $response["message"] = "ASIN column is missing. ";
                $response["status"] = FALSE;
                return json_encode($response);
            }

            if (File::exists($inputFileName)) {
                File::delete($inputFileName);
            }

            $response["status"] = true;
            $response["message"] = "Schedule added successfully";
            $response["tableData"] =  $this->getBuyBoxCrons();
        } else {
            $response["message"] = "Fail To Upload File, File Not Found Try Again";
            $response["status"] = false;
        }
        // return json response
        return response()->json($response);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function deletebatch(BuyBoxModel $collection)
    {
        try
        {
            $response = array();
            $response["status"] = false;
            $responseDeletion = $collection->delete();
            if ($responseDeletion == FALSE) {
                $response["message"] = "Fail to delete the schedule refresh and try again";
                $response["consoleMessage"] = "";
                $response["status"] = false;
            } else {
                $response["status"] = true;
                $response["message"] = "Record Deleted Successfully";
                $response["tableData"] =  $this->getBuyBoxCrons();
            }
            // return json response
            return response()->json($response);
        } 
        catch (\Throwable $th) {
                $response["message"] = $th->getMessage();
                $response["status"] = false;
        }
    }

    public function dashboard2()
    {
        ini_set('max_execution_time', -1);
        $AsinCollection = BuyBoxModel::getAsinBatch();
        $DBArray = [];
        $data = [];
        if (!$AsinCollection->isEmpty()) {
            foreach ($AsinCollection as $asin) {
                $url = "https://www.amazon.com/dp/B074R5HPPB";
                // get data from curl call
                $asin_data = $this->get_data_curl($url);
                if (isset($asin_data['data']) && !empty($asin_data['data'])) {
                    // get data from curl call
                    // create dom Object
                    $dom = new DOMDocument();
                    // remove xml error from html code
                    libxml_use_internal_errors(true);
                    // load html code
                    $dom->loadHTML(mb_convert_encoding($asin_data['data'], 'HTML-ENTITIES', 'UTF-8'));
                    $a = new DOMXPath($dom);
                    if ($a->query("//a[@id='brand']")->length > 0) {
                        $data['brand'] = trim($a->query("//a[@id='brand']")->item(0)->nodeValue);
                    } elseif ($a->query("//a[@id='bylineInfo']")->length > 0) {
                        $data['brand'] = trim($a->query("//a[@id='bylineInfo']")->item(0)->nodeValue);
                    } elseif ($a->query("//a[@id='brandteaser']//img/@src")->length > 0) {
                        $data['brand'] = trim($a->query("//a[@id='brandteaser']//img/@src")->item(0)->nodeValue);
                    } elseif ($a->query("//*[@id=\"sellerProfileTriggerId\"]")->length > 0) {
                        $data['brand'] = trim($a->query("//*[@id=\"sellerProfileTriggerId\"]")->item(0)->nodeValue);
                    } elseif ($a->query("//*[@id=\"bylineInfo\"]")->length > 0) {
                        $data['brand'] = trim($a->query("//*[@id=\"bylineInfo\"]")->item(0)->nodeValue);
                    } else {
                        $data['brand'] = "NA";
                    }
                    // sold by
                    $data['soldBy'] = "NA";
                    $soldBy = $a->query("//*[@id=\"merchant-info\"]");
                    if ($soldBy->length > 0) {
                        $data['soldBy'] = trim($a->query("//*[@id=\"merchant-info\"]")->item(0)->nodeValue); //Get the Sold By
                        $data['soldBy'] = trim(preg_replace('/\s\s+/', ' ', $data['soldBy']));
                        $data['soldBy'] = str_replace('P.when("seller-register-popover").execute(function(sellerRegisterPopover) { sellerRegisterPopover(); });', '', $data['soldBy']);
                        $data['soldBy'] = utf8_encode($data['soldBy']);
                        $data['soldBy'] = utf8_decode($data['soldBy']);
                    } else {
                        $data['soldBy'] = "NA";
                    }
                    $data['soldByAlert'] = 0;
                    // create alert for sold and brand
                    if ($data['soldBy'] != 'NA' && $data['soldBy'] != '' && !empty($data['soldBy'])) {
                        $data['soldByAlert'] = 0;
                        if ($data['brand'] != 'NA') {
                            $data['soldByAlert'] = 1;
                            if ((strpos($data['brand'], $data['soldBy']) !== false) || (strpos($data['soldBy'], 'Ships from and sold by Amazon') !== false)) {
                                $data['soldByAlert'] = 0;
                            }
                        }
                    }
                    //prices
                    $data['price'] = 'NA';
                    // get product prices
                    $price = $a->query("//span[@id='priceblock_ourprice']");
                    if ($price->length > 0) {
                        $data['price'] = $a->query("//span[@id='priceblock_ourprice']")->item(0)->nodeValue; //Get the Product Price
                    } else {
                        $price = $a->query("//span[@id='priceblock_saleprice']");
                        if ($price->length > 0) {
                            $data['price'] = $a->query("//span[@id='priceblock_saleprice']")->item(0)->nodeValue; //Get the Product Price
                        }
                    }
                    $data['primeDesc'] = 'NA';
                    // prime product
                    $prime = $a->query('//*[@id="bbop-sbbop-container"]');
                    if ($prime->length > 0) {
                        $data['primeDesc'] = trim($a->query("//*[@id=\"bbop-sbbop-container\"]")->item(0)->nodeValue); //Get the prime
                        $data['primeDesc'] = trim(preg_replace('/\s\s+/', ' ', $data['primeDesc']));
                        $data['prime'] = 1;
                    } else {
                        $data['prime'] = 0;
                    }
                    // stock status
                    $Stock = $a->query("//*[@id=\"availability\"]/span"); //Get the Stock
                    if ($Stock->length > 0) {
                        $data['stock'] = trim($a->query("//*[@id=\"availability\"]/span")->item(0)->nodeValue); //Get the Sol
                    } else {
                        $data['stock'] = "NA";
                    }
                    // create alert for stock
                    $data['stockAlert'] = 0;
                    if ($data['stock'] != 'NA') {
                        $data['stockAlert'] = 1;
                        if (strpos($data['stock'], 'In Stock') !== false) {
                            $data['stockAlert'] = 0;
                        }
                    }
                    $data['url'] = $url;
                    $data['asinCode'] = $asin->asinCode;
                    $data['createdAt'] = date('Y-m-d H:i:s');
                    $data['updatedAt'] = date('Y-m-d H:i:s');
                    $data['fkCollection'] = date('Y-m-d H:i:s');
                    echo $asin->asinCode . PHP_EOL;
                    dd($data);
                    BuyBoxModel::insertScrapedRecord($data);
                    //array_push($DBArray, $data);
                } else {
                    BuyBoxModel::updateAsinStatus($asin->asinCode, 2);
                }
            }
        } else {
            echo 'it empty' . PHP_EOL;
        }
    }

    // Generate Excel file
    public function dashboard1()
    {
        $fileData = BuyBoxModel::allScrapData();
        $fileName = $fileData[0]->collectionName . '-' . date('Y-m-d-h-i-s-A');
        $response = (new FastExcel($fileData))->export('public/buybox/scrapedDataFile/' . $fileName . '.csv');
        exit;
        return (new FastExcel($fileData))->download('public/buybox/scrapedDataFile/' . $fileName . '.csv');
        exit;
    }

    /**
     * This function is used to send Email with CSV file
     */
    public function attachment_email()
    {
        Artisan::call('view:clear');
        Artisan::call('route:clear');
        Artisan::call('config:clear');
        Artisan::call('cache:clear');
        Artisan::call('config:cache');
        $data = array('name' => "Abdul Waqar");
        Mail::send('mail', $data, function ($message) {
            $message->to('abdul.waqar@codeinformatics.com', 'Office Account')->subject('Laravel Testing Mail with Attachment');
            $message->attach(public_path('buybox/scrapedDataFile/testing-2019-09-20-03-05-05-PM.csv'));
        });
        echo "Email Sent with attachment. Check your inbox.";
    }

    public function dashboard0()
    {
        echo 'Slack Post Message';
        $DataArray = BuyBoxModel::getAlertScrapeData();
        foreach ($DataArray as $single) {

            dd($single);
        }
        $Array = [
            "brand" => "Oster",
            "soldBy" => "Ships from and sold by ????: Pick. Buy. Enjoy. ?MONEY BACK GUARANTEE?. ",
            "price" => "$124.46",
            "prime" => 0,
            "stock" => "In stock.",
            "asinCode" => "B078SD1JT8",
        ];
        $Array = implode("\r\n", $Array);

        $client = new Client();
        $response = $client->request('POST', 'https://slack.com/api/chat.postMessage', [
            'headers' => [
                'Authorization' => 'Bearer ' . 'xoxp-753123464978-767757534359-765915259488-3fd78cafca1d95699ba968f9f89d7e32',
                'Content-Type' => 'application/x-www-form-urlencoded'],
            'form_params' => [
                'channel' => "CNHSF2U3S",
                'text' => $Array,
            ],
        ]);

        $body = json_decode($response->getBody()->getContents());
        dd($body);


        echo 'Slack Chanels';
        $client = new Client();
        $response = $client->request('GET', 'https://slack.com/api/channels.list', [
            'headers' => [
                'Authorization' => 'Bearer ' . 'xoxp-753123464978-767757534359-765915259488-3fd78cafca1d95699ba968f9f89d7e32',
                'Content-Type' => 'application/json',
            ],
        ]);
        $body = json_decode($response->getBody()->getContents());
        dd($body);
    }
}

