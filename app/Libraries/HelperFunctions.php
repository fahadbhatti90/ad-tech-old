<?php

use App\Events\SendNotification;
use App\Mail\BuyBoxEmailAlertMarkdown;
use App\Models\AccountModels\AccountModel;
use App\Models\ScrapingModels\SettingsModel;
use App\Models\Brands\brandAssociation;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Log;
use PHPMailer\PHPMailer\PHPMailer;

if (!function_exists('to_time_ago')) {
    function to_time_ago( $time ) {
        $time = strtotime($time);
        // Calculate difference between current 
        // time and given timestamp in seconds 
        $diff = time() - $time;

        if( $diff < 1 ) {
            return '1 second ago';
        }

        $time_rules = array (
                    12 * 30 * 24 * 60 * 60 => 'year',
                    30 * 24 * 60 * 60       => 'month',
                    24 * 60 * 60           => 'day',
                    60 * 60                   => 'hour',
                    60                       => 'minute',
                    1                       => 'second'
        );

        foreach( $time_rules as $secs => $str ) {

            $div = $diff / $secs;

            if( $div >= 1 ) {

                $t = round( $div );

                return $t . ' ' . $str .
                    ( $t > 1 ? 's' : '' ) . ' ago';
            }
        }
    }
}
if (!function_exists('getHostForNoti')) {
    function getHostForNoti() {
      return SettingsModel::getHostName();
    }
}
/**
    *check user to redirect based on roleId
     * @return redirect to route
     */
if (!function_exists('redirectToDashboard')) {
    function redirectToDashboard() {
    //check if user is super admin
      if(auth()->user()->hasAnyRole(1))
        {
            session(['activeRole' => 1]);
            return ('adminDashboard');
        }
        elseif(auth()->user()->hasAnyRole(2)){
             //check if user is admin
            session(['activeRole' => 2]);
            if(!session()->has("m".auth()->user()->id)){
                $brands = brandAssociation::has("brand")
                    ->with("brand:id,name")
                    ->select("fkBrandId","fkManagerId")
                    ->where("fkManagerId",auth()->user()->id)
                    ->distinct();
                if($brands->exists()){
                    $brands = $brands->first();
                    session(["m$brands->fkManagerId" => $brands]);
                }
            }
            return ('admin.dashboard');
        }
        elseif(auth()->user()->hasAnyRole(3)){
            //check if user is manager
            session(['activeRole' => 3]);
            if(!session()->has("m".auth()->user()->id)){
                $brands = brandAssociation::has("brand")
                ->with("brand:id,name")
                ->select("fkBrandId","fkManagerId")
                ->where("fkManagerId",auth()->user()->id)
                ->distinct();
                if($brands->exists()){
                    $brands = $brands->first();
                    session(["m$brands->fkManagerId" => $brands]);
                }
            }
            return ('client.dashboard');
        }else{
            //check if user has no role then redirect to login page
            return ('login');
        }
    }
}
if (!function_exists('sendDummyNoti')) {
    function sendDummyNoti() {
        $notiDetails = $details = array();
        $notiDetails["crawler Ids"] = 1;
        $notiDetails["crawler Names"] = "test";
        $notiDetails["Total Search Terms"] = "test";
        $notiDetails["Details Download Link"] = 'Download Black List Search Terms File';
        $notiDetails["Completed At"] = date("Y-m-d H:i");

        $details["Created At:"] = date("Y-m-d H:i");
        broadcast(new SendNotification(2,"test","test", json_encode( $notiDetails),json_encode( $details),date("Y-m-d H:i")))->toOthers();
    }
}

if (!function_exists('SendMailViaPhpMailerLib')) {
   /**
    *   SendMailViaPhpMailerLib => Helper function that Sends email using phpMailer Library (usefull with outlook).
    *   In case of any error while sending mail it will be stored in Log file
    *
    *   PARAMETERS INFORMATION:
    *
    *   $data["toEmails"] = array();
    *
    *   $data["subject"] = string;
    *
    *   $data["bodyHTML"] = new MarkdownMailAbleObject()->render();
    *
    *  (optional) $data["attachments"] = array(array("path"=>public_path("Proxies/dummy.txt"),"name"=>"newName.txt"));
    *
    *  (optional) $data["cc"] = array();
    *
    *  (optional) $data["bcc"] = array();
    *
    *   Related Setting must be added in .env file prior to using this function
    *
    *   example:
    *
    *   CUSTOM_MAIL_CHARSET="utf-8"
    *
    *   CUSTOM_MAIL_SMTP_AUTH=true
    *
    *   CUSTOM_MAIL_SMTP_ENCRIPTION=tls
    *
    *   CUSTOM_MAIL_HOST=smtp.office365.com
    *
    *   CUSTOM_MAIL_PORT=25
    *
    *   CUSTOM_MAIL_USERNAME=no-reply@helo-tek.com
    *
    *   CUSTOM_MAIL_PASSWORD=FkzZF1BV2Xmp
    *
    *   CUSTOM_MAIL_FROM_NAME="no-reply"
    *
    *   CUSTOM_MAIL_FROM_EMAIL=no-reply@helo-tek.com
    *
    *   @param array $data
    *   @return bool
    *
    */
    function SendMailViaPhpMailerLib($data) {
        $mail = new PHPMailer(true); // notice the \  you have to use root namespace here
        $errorStatus = [];
        try {
            $mail->isSMTP(); // tell to use smtp
            $mail->CharSet = env('CUSTOM_MAIL_CHARSET', 'utf-8'); // set charset to utf8
            $mail->SMTPAuth = env('CUSTOM_MAIL_SMTP_AUTH', true);  // use smpt auth
            $mail->SMTPSecure = env('CUSTOM_MAIL_SMTP_ENCRIPTION', true); // or ssl
            $mail->Host = env('CUSTOM_MAIL_HOST', true);
            $mail->Port = env('CUSTOM_MAIL_PORT', true); // most likely something different for you. This is the mailtrap.io port i use for testing.
            $mail->Username = env('CUSTOM_MAIL_USERNAME', true);
            $mail->Password = env('CUSTOM_MAIL_PASSWORD', true);
            $mail->setFrom(env('CUSTOM_MAIL_FROM_EMAIL', true), env('CUSTOM_MAIL_FROM_NAME', true));
            $mail->Subject = $data["subject"];
            $mail->MsgHTML($data["bodyHTML"]);

            if(is_array($data["toEmails"])) {
                foreach ($data["toEmails"] as $value) {
                    $mail->addAddress($value);
                }
            }
            else
            {
                Log::error("Please provide toEmails key value as array");
                array_push($errorStatus ,"Please provide toEmails key value as array");
            }// end else

            if(isset($data["attachments"])) {
                if(is_array($data["attachments"])) {
                    foreach ($data["attachments"] as $value) {
                        $mail->addAttachment($value["path"], $value["name"]);
                    }
                }
                else
                {
                    Log::error("Attacments should be an array of path and custom name of file");
                    array_push($errorStatus ,"Attacments should be an array of path and custom name of file");
                }// end else
            }//end if
            if(isset($data["cc"])){
                if(is_array($data["cc"])) {
                    foreach ($data["cc"] as $value) {
                        $mail->addCC($value);
                    }
                }
                else
                {
                    Log::error("CC should be an array");
                    array_push($errorStatus ,"CC should be an array");
                }// end else
            }//end if
            if(isset($data["bcc"])){
                if(is_array($data["bcc"])){
                    foreach ($data["bcc"] as $value) {
                        $mail->addBCC($value);
                    }
                }
                else
                {
                    Log::error("Bcc should be an array");
                    array_push($errorStatus ,"Bcc should be an array");
                }// end else  
            }//end if
            $mail->send();
                if(count($errorStatus)>0){
                    return array(
                        "status"=>"Email Sent with some Errors",
                        "errors"=>$errorStatus

                    );
                }
            return array(
                "status"=>"Email Sent with Out Any Error",
                "errors"=>$errorStatus
            );
        }  catch (\Exception $e) {

            Log::error($e->getMessage());
            Log::error($e->getTraceAsString());
            Log::error($e->getFile());
            array_push($errorStatus ,$e->getMessage()."");
            return array(
                "status"=>"Error: Fail to send Email. See Log Files For Further details",
                "errors"=>$errorStatus
            );
        }//end catch block
    }//end function 
}//end if

if (!function_exists('getMonthLableByNumber')) {
   /**
    *   getMonthLableByNumber
    *   @param array $monthNumber
    *   @return string
    *
    */
    function getMonthLableByNumber($monthNumber) {
        $months = array(
            1=>"January",
            2=>"February",
            3=>"March",
            4=>"April",
            5=>"May",
            6=>"June",
            7=>"July",
            8=>"August",
            9=>"September",
            10=>"October",
            11=>"November",
            12=>"December",
        );
        return array_get($months, $monthNumber);
    }//end function 
}//end if
if (!function_exists('getWeekNumber')) {
   /**
    *   getMonthLableByNumber
    *   @param array $monthNumber
    *   @return string
    *
    */
    function getWeekNumber($date) {
        $date = new DateTime($date);
        $week = $date->format("W");
        $month = $date->format("m");
        if($month==12 && $week == 1){
            $week = 53;
        }
        return $week;
    }//end function 
}//end if
if (!function_exists('getArrayPreserveObject')) {
   /**
    *   getMonthLableByNumber
    *   @param array $monthNumber
    *   @return string
    *
    */
    function getArrayPreserveObject($data) {
        return json_decode(json_encode($data));
    }//end function 
}//end if
if (!function_exists('getProxiesForThreads')) {
    /**
     *   getMonthLableByNumber
     *   @param array $monthNumber
     *   @return string
     *
     */
     function getProxiesForThreads($proxies, $totalNumberOfThread) {
         $proxyPerThread = intval(round(count($proxies) / $totalNumberOfThread));

        $proxyPerThreadArray = [];
        $inc = 1;
        for ($i=0; $i < count($proxies); $i= $i + $proxyPerThread) {
            $tempArray = [];
            $inc++;
            for ($j=$i; $j < ($i + $proxyPerThread); $j++) {
                if(Arr::has($proxies,$j))
                array_push($tempArray, $proxies[$j]);
            }
            array_push($proxyPerThreadArray, $tempArray);
        }//end for loop
        return [$proxyPerThread,$proxyPerThreadArray];
     }//end function
 }//end if
if (!function_exists('getApiUrlForDiffEnv')) {
    /**
     * @param $env
     * @return mixed
     */
    function getApiUrlForDiffEnv($env) {
        switch ($env){
            case 'stage':
                $url =  Config::get('constants.amsApiUrl');
                break;
            case 'production':
                $url =  Config::get('constants.amsApiUrl');
                break;
            default:
                $url =  Config::get('constants.testingAmsApiUrl');
        }
        return $url;
    }//end function
}//end if
if (!function_exists('_sendEmailForScheduleCreated')) {
    /**
     * @param $env
     * @return bool
     * @throws ReflectionException
     */
    function _sendEmailForScheduleCreated($scheduleData, $toEmail) {
        $messages = [];
        $messages[0] = "<p><b>Hello,</p>";
        $messages[1] = "<p>This email notification is to inform you that new schedule is created successfully.</p>";
        $messages[2] = "<p>Campaign Start Time ".$scheduleData['startTime']." and End Time ".$scheduleData['endTime']."</p>";
        $bodyHTML = ((new BuyBoxEmailAlertMarkdown('', $messages))->render());

        $data = [];
        $data["toEmails"] = $toEmail;

        $ccEmailArray = explode(',', $scheduleData['ccEmails']);
        if (isset($scheduleData['ccEmails']) && $scheduleData['ccEmails'] != 'NA'  && !empty($scheduleData['ccEmails'])){
            $data["cc"] = $ccEmailArray;
        }

        $data["subject"] = "Schedule Created Successfully.";
        $data["bodyHTML"] = $bodyHTML;
        Log::info(json_encode($data));
        return SendMailViaPhpMailerLib($data);
    }//end function
}//end if
if (!function_exists('getNotifyWhichEnvToUse')) {
    /**
     * @param $env
     * @return mixed
     */
    function getNotifyWhichEnvDataToUse($env) {
        $response = FALSE;
        switch ($env){
            case 'stage':
                $response = TRUE;
                break;
            case 'production':
                $response = TRUE;
                break;
            default:
                $response = FALSE;
        }
        return $response;
    }//end function
}//end if
if (!function_exists('_sendEmailForEnabledCampaign')) {
    /**
     * @param $env
     * @return bool
     * @throws ReflectionException
     */
    function _sendEmailForEnabledCampaign($toEmail, $ccEmail, $scheduleName) {
        $messages = [];
        $messages[0] = "<p><b>Hello,</p>";
        $messages[1] = "<p>This email notification is to inform you that campaigns associated with schedule ".$scheduleName." are enabled successfully.</p>";
        $bodyHTML = ((new BuyBoxEmailAlertMarkdown('', $messages))->render());

        $data = [];
        $data["toEmails"] = $toEmail;

        $ccEmailArray = explode(',', $ccEmail);
        if (isset($ccEmail) && $ccEmail != 'NA' && !empty($ccEmail)){
            $data["cc"] = $ccEmailArray;
        }

        $data["subject"] = "Schedule Enabled Successfully.";
        $data["bodyHTML"] = $bodyHTML;

        return SendMailViaPhpMailerLib($data);
    }//end function
}//end if

if (!function_exists('_sendEmailForErrorCampaign')) {
    /**
     * @param $env
     * @return bool
     * @throws ReflectionException
     */
    function _sendEmailForErrorCampaign($toEmail, $ccEmail, $scheduleName) {

        $messages = [];
        $messages[0] = "<p><b>Hello,</p>";
        $messages[1] = "<p>This email notification is to inform you that campaigns associated with schedule ".$scheduleName." having error. kindly check again!</p>";
        $bodyHTML = ((new BuyBoxEmailAlertMarkdown('', $messages))->render());

        $data = [];
        $data["toEmails"] = $toEmail;

        $ccEmailArray = explode(',', $ccEmail);
        if (isset($ccEmail) && $ccEmail != 'NA' && !empty($ccEmail)){
            $data["cc"] = $ccEmailArray;
        }

        $data["subject"] = "Schedule Error.";
        $data["bodyHTML"] = $bodyHTML;

        return SendMailViaPhpMailerLib($data);
    }//end function
}//end if

if (!function_exists('_sendEmailForPausedCampaign')) {
    /**
     * @param $env
     * @return bool
     * @throws ReflectionException
     */
    function _sendEmailForPausedCampaign($toEmail, $ccEmail, $scheduleName) {
        $messages = [];
        $messages[0] = "<p><b>Hello,</p>";
        $messages[1] = "<p>This email notification is to inform you that campaigns associated with schedule ".$scheduleName." are paused successfully.</p>";
        $bodyHTML = ((new BuyBoxEmailAlertMarkdown('', $messages))->render());

        $data = [];
        $data["toEmails"] = $toEmail;

        $ccEmailArray = explode(',', $ccEmail);
        if (isset($ccEmail) && $ccEmail != 'NA' && !empty($ccEmail)){
            $data["cc"] = $ccEmailArray;
        }

        $data["subject"] = "Schedule Paused Successfully.";
        $data["bodyHTML"] = $bodyHTML;

        return SendMailViaPhpMailerLib($data);
    }//end function
}//end if

if (!function_exists('managerHasBrand')) {
        /**
        *   getBrandName
        *   @return string
        *
        */
        function managerHasBrand() {
            return session()->has("m".auth()->user()->id);
        }//end function
}//end if
if (!function_exists('getBrandId')) {
        /**
        *   getBrandName
        *   @return string
        *
        */
        function getBrandId() { 
            return \managerHasBrand() ? session()->get("m".auth()->user()->id)->brand->id : 0;
        }//end function 
}//end if
if (!function_exists('getBrandName')) {
        /**
        *   getBrandName
        *   @return string
        *
        */
        function getBrandName() { 
            return \managerHasBrand() ? session()->get("m".auth()->user()->id)->brand->name : "NA";
        }//end function 
}//end if
if (!function_exists('ifSessionBrandGotUnassigned')) {
        /**
        *   getBrandName
        *   @return string
        *      
        */
        function ifSessionBrandGotUnassigned() {
            if(\managerHasBrand()){
                $brands = brandAssociation::select("fkBrandId","fkManagerId")
                ->where("fkManagerId",auth()->user()->id)
                ->where("fkBrandId", \getBrandId());
                if(!$brands->exists()){
                    $brands = brandAssociation::has("brand")
                    ->with("brand:id,name")
                    ->select("fkBrandId","fkManagerId")
                    ->where("fkManagerId",auth()->user()->id)
                    ->distinct();
                    if($brands->exists()){
                        $brands = $brands->first();
                        session(["m$brands->fkManagerId" => $brands]);
                    }
                    else{
                        session()->forget("m".auth()->user()->id);
                    }
                }
            }else{
                //if brand not exist then select first brand
                $brands = brandAssociation::has("brand")
                    ->with("brand:id,name")
                    ->select("fkBrandId","fkManagerId")
                    ->where("fkManagerId",auth()->user()->id)
                    ->distinct();
                if($brands->exists()){
                    $brands = $brands->first();
                    session(["m$brands->fkManagerId" => $brands]);
                }else{
                    session()->forget("m".auth()->user()->id);
                }
            }
        }//end function 
}//end if
if (!function_exists('getFirstDbConnectionName')) {
   
   /**
    * getDbAndConnectionName : pass db2, c1, c2 any of these to get respective results
    * Default this will return db1 name 
    * @param mixed $what => 
    * @return void
    */
    function getDbAndConnectionName($what) {
        $connectionDB1 = array_keys(config("database.connections"))[1];
        $connectionDB2 = array_keys(config("database.connections"))[2];
        $Db1 = config("database.connections.$connectionDB1.database");
        $Db2 = config("database.connections.$connectionDB2.database");
        switch ($what) {
            case 'db1':
                return $Db1;
                break;
            case 'db2':
                return $Db2;
                break;
            case 'c1':
                return $connectionDB1;
                break;
            case 'c2':
                return $connectionDB2;
                break;
            default:
                return $Db1;
                break;
        } 
       return array_keys(config("database.connections"))[1];
    }//end function 
}//end if

if (!function_exists('getPercentage')){
    /**
     * @param $actualVal
     * @param $newValue
     * @return float|int
     */
    function getPercentage($actualVal, $newValue){

       // $decreaseValue = $actualVal - $newValue;
        $increaseValue = $newValue - $actualVal;
        return round(($increaseValue / $actualVal) * 100, 2);
    }
}

if (!function_exists('checkSimilarText')){
    /**
     * @param $actualVal
     * @param $newValue
     * @return float|int
     */
    function checkSimilarText($oldSeller, $newSeller){

        similar_text($oldSeller, $newSeller, $perc);

        return intval($perc);
    }
}


if (!function_exists('getAssetUrl')) {
    /**
     * @param $env
     * @return mixed
     */
    function getAssetUrl() {
        if(app()->isLocal()){
            return substr(asset('/'),0,strlen(asset('/'))-1) ;
        }
        return asset('/public');;
    }//end function
}//end if

if (!function_exists('reactAsset')) {
    /**
     * @param $env
     * @return mixed
     */
    function reactAsset($assetPath) {
        
        return getAssetUrl()."/$assetPath";
    }//end function
}//end if
if (!function_exists('getActiveBrandAccountIds')) {
    /**
     *   getMonthLableByNumber
     *   @param array $monthNumber
     *   @return string
     *
     */
     function getActiveBrandAccountIds() {
        return AccountModel::where("fkBrandId", getBrandId())
        ->select("id")
        ->get()
        ->map(function($item,$value){
            return $item->id;
        });
     }//end function 
 }//end if
?>