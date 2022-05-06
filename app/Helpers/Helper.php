<?php
namespace App\Helpers;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\Resource;
use Illuminate\Http\Response;
use DOMDocument;
use DOMXPath;
use Illuminate\Http\Resources\Json\JsonResource;
use Symfony\Component\VarDumper\Caster\ResourceCaster;
use App\Http\Resources\Decaptcha;
use App\Http\Resources\cc_packet;
use App\Http\Resources\cc_pict_descr;
use App\Http\Resources\cc_balance_transfer_descr;
use Config\decaptchaconstants;

class Helper
{

    public static function get_data_curl($url)
    {

        $user_agent = ['Mozilla/5.0 (Windows NT 6.1; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/61.0.3163.100 Safari/537.36',
            'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_12_3) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/57.0.2987.110 Safari/537.36'
        ];

        $return  = array_rand($user_agent,1);
        //dd($return);
        $request_headers = array();
        $request_headers[] = 'Accept: text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,image/apng,*/*;q=0.8';
        $request_headers[] = 'Accept-Language: en-US,en;q=0.8';
        $request_headers[] = 'Connection: keep-alive';
        $request_headers[] = 'Upgrade-Insecure-Requests: 1';
        $request_headers[] = 'Host: www.amazon.com';
        $request_headers[] = 'User-Agent: ' . $return;


        $proxy = "184.175.219.19:80";
        //dd($proxy);
        $proxyauth = "code:c0d3HT";
        $ch = curl_init($url);
        //dd($url);
        $cookie_name = str_replace(array(".", ":"), "-", $proxy);
        $cookie_name = trim($cookie_name);
        //echo getcwd().'/uploads/cookies/'.$cookie_name.'.txt';exit;
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($ch, CURLOPT_TIMEOUT, 400);  //change this to constant
        curl_setopt($ch, CURLOPT_HTTPHEADER, $request_headers);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
        curl_setopt($ch, CURLOPT_CAINFO, getcwd() . "/cacert.pem");
        curl_setopt($ch, CURLOPT_COOKIEJAR, getcwd() . '/uploads/cookies/' . $cookie_name . '.txt');
        curl_setopt($ch, CURLOPT_COOKIEFILE, getcwd() . '/uploads/cookies/' . $cookie_name . '.txt');
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_PROXY, $proxy);
        curl_setopt($ch, CURLOPT_PROXYUSERPWD, $proxyauth);
        curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
        curl_setopt($ch, CURLOPT_ENCODING, '');

        //dd(CURL_HTTP_VERSION_1_1);
        $data = trim(curl_exec($ch));
        //dd($data);
        $return = array();
        $return['status'] = FALSE;
        $return['error_code'] = NULL;
        $return['error_text'] = NULL;
        $return['html'] = NULL;
        //dd($return);
        /* Check for 404 (file not found). */
        $return['http_code'] = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        //dd(CURLINFO_HTTP_CODE);
        if (curl_errno($ch)) {
            $return['status'] = FALSE;
            $return['error_code'] = -2;
            $return['error_text'] = curl_error($ch) . " - HTTP CODE: " . $return['http_code'];
        }//dd($return);
        curl_close($ch);

        if (is_null($return['error_text'])) {

            if (Helper::check_captcha($data) == -1) {

                $dom = new DOMDocument();
                //dd($dom);
                libxml_use_internal_errors(true);
                //dd($dom);
                $dom->loadHTML($data);
                $c = new DOMXPath($dom);
                //dd($c);

                $capcha_image = $c->query("(//div[@class='a-box-inner'])//div[contains(concat(' ', normalize-space(@class), ' '), 'a-text-center')]//img/@src ")[0]->nodeValue;
                //dd($capcha_image);
                preg_match_all('/<input type=hidden name="(.*?)" value="(.*?)"/', $data, $matches);

                $post_data = array();
                $capctha = Helper::solve_capcha($capcha_image);
                //dd($capctha);
                if ($capctha['status'] == TRUE) {
                    if (count($matches[1]) > 0) {
                        foreach ($matches[1] as $key => $match) {
                            $post_data[$match] = $matches[2][$key];
                        }
                    }
                    $post_data['field-keywords'] = $capctha['message'];

                    $request_headers = array();
                    $request_headers[] = 'Accept: text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,*/*;q=0.8';
                    $request_headers[] = 'Accept-Language: en-US,en;q=0.8';
                    $request_headers[] = 'Cache-Control: max-age=0';
                    $request_headers[] = 'Connection: keep-alive';
                    $request_headers[] = 'Upgrade-Insecure-Requests: 1';
                    $request_headers[] = 'Host: www.amazon.com';
                    $request_headers[] = 'Referer: ' . $url;
                    //$request_headers[] = 'User-Agent: ' . get_random_user_agent();

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
                    curl_setopt($ch, CURLOPT_COOKIEJAR, getcwd() . '/uploads/cookies/' . $cookie_name . '.txt');
                    curl_setopt($ch, CURLOPT_COOKIEFILE, getcwd() . '/uploads/cookies/' . $cookie_name . '.txt');
                    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
                    //curl_setopt($ch, CURLOPT_REFERER, $signin);
                    $return['data'] = curl_exec($ch);
                    //	$captcha_check_text = getcwd()."/uploads/".time().'.txt';
                    //	file_put_contents($captcha_check_text,$return['data'],FILE_APPEND);

                    if (curl_errno($ch)) {
                        $return['error_text'] = " Error: " . curl_error($ch);
                        $return['error_code'] = -2;
                        $return['status'] = FALSE;
                        $return['html'] = NULL;
                        return $return;
                    }
                    curl_close($ch);

                } else {
                    //======= Capcha Not Guessed========
                    $return['error_text'] = $capctha['message'];
                    $return['error_code'] = -1;
                    $return['status'] = FALSE;
                }

                $return['error_code'] = -1;
                $return['error_text'] = "Captcha Found , proxy = " . $proxy;
                $return['status'] = FALSE;

            } else {

                if (Helper::validate_data($data)) {
                    $return['data'] = $data;
                    $return['status'] = TRUE;
                } else {
                    $return['error_code'] = -4;
                    $return['data'] = NULL;
                    $return['status'] = FALSE;
                    //$unknown_error = getcwd()."/uploads/".date("Y-m-d").'.txt';
                    //($unknown_error,print_r($return,true),FILE_APPEND);
                }


            }

            //echo  "No Capcha End";
            //print_r($return);
            //exit;
        }
        //print_r($return);
        //echo  "Simple End";
        //exit;
        return $return;
    }

    function get_data($url)
    {
       // Config::get('decaptchaconstants.ASINsReport');
        $CI =& get_instance();
        $data = NULL;
        $tries = 0;
        do {
            $data = get_data_curl($url);
            if ($data['status'] == FALSE) {
                $tries++;
            } else {
                break;
            }

        } while ($tries < 5);
        return $data;
    }

    function get_random_user_agent(){
        $user_agent = ['Mozilla/5.0 (Windows NT 6.1; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/61.0.3163.100 Safari/537.36',
            'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_12_3) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/57.0.2987.110 Safari/537.36'
        ];

        $return  = array_rand($user_agent,1);
        return $user_agent[$return];
    }

    public static function check_captcha($data){

        //dd($data);
        $pos2 = stripos($data, "Type the characters you see in this image");
        if ($pos2 !== false) {

            // $captcha_check_text = getcwd()."/uploads/".date("Y-m-d").'.txt';
            // file_put_contents($captcha_check_text,$data,FILE_APPEND);

            return -1;
        }else{
            return 1;
        }
    }

    public static function validate_data($data){

        $pos2 = stripos($data, "Sorry! Something went wrong!");
        if ($pos2 !== false) {

            return FALSE;
        }else{
            return TRUE;
        }

    }

    public static function gather_detail_bullets($productinformation){

        $product_info = array();
        foreach($productinformation as $key => $dt){

            if(stripos(trim($dt->nodeValue),"UPC:" ) !== FALSE){
                $product_info['upc'] =   str_replace('UPC:','',$dt->nodeValue);
            }elseif(stripos(trim($dt->nodeValue),"Item model number:" ) !== FALSE){
                $product_info['modelno'] =  str_replace('Item model number:','',$dt->nodeValue);
            }
        }
        return $product_info;
    }

    public static function solve_capcha($image){
        //dd($image);
        $capcha_error_text = "Capacha API ";
        //dd($capcha_error_text);
        $return  = array();
        $return['status'] = FALSE;
        $return['message'] = "";

        $ccp = new Decaptcha() ;
        //dd($ccp);
        $ccp->init();
        //dd($ccp);
        if( $ccp->login( 'api.de-captcher.com', '36541', 'codeinformatics', 'c0d3@@@@captcha' ) < 0 ) {
            $return['message'] = $capcha_error_text."Login Failed";
            //dd($ccp>login);
        } else {
            $system_load = 0;
            if( $ccp->system_load( $system_load ) != 0 ) {
                $return['message'] = $capcha_error_text." system_load() FAILED";
            }else{
                $major_id	= 0;
                $minor_id	= 0;
                //     for( $i = 0; $i < 3; $i++ ) {
                $pict = file_get_contents( $image );
                //dd($pict);
                //$pict = $image;
                $text = '';


                $pict_to	= 0;
                $pict_type	= 0;

                $res = $ccp->picture2( $pict, $pict_to, $pict_type, $text, $major_id, $minor_id );
                //dd($res);
                switch( $res ) {
                    // most common return codes
                    case 0:
                        //print( "got text for id=".$major_id."/".$minor_id.", type=".$pict_type.", to=".$pict_to.", text='".$text."'" );
                        $return['status'] = TRUE;
                        $return['message'] = $text;
                        break;
                    case -6:
                        $return['message'] = $capcha_error_text." not enough funds to process a picture, balance is depleted";
                        break;
                    case -7:
                        $return['message'] = $capcha_error_text." picture has been timed out on server (payment not taken)";
                        break;
                    case -5:
                        $return['message'] = $capcha_error_text." temporarily server-side error, server's overloaded, wait a little before sending a new picture";
                        break;

                    // local errors
                    case -2:
                        $return['message'] = $capcha_error_text."  local error., either ccproto_init() or ccproto_login() has not been successfully called prior to ccproto_picture()";
                        $return['message'] .= $capcha_error_text." need ccproto_init() and ccproto_login() to be called";
                        break;

                    // network errors
                    case -3:
                        $return['message'] = $capcha_error_text." network troubles, better to call ccproto_login() again";
                        break;

                    // server-side errors
                    case -4:
                        $return['message'] = $capcha_error_text."size of the text returned is too big";
                        break;
                    case -1:
                        $return['message'] = $capcha_error_text."server-side error, better to call ccproto_login() again";
                        break;
                    case -200:
                        $return['message'] = $capcha_error_text." unknown error, better to call ccproto_login() again";
                        break;

                    default:
                        // any other known errors?
                        $return['message'] = $capcha_error_text." Unknown Error";
                        break;
                }
                //dd($return);


                // process a picture and if it is badly recognized
                // call picture_bad2() to name it as error.
                // pictures named bad are not charged

                //$ccp->picture_bad2( $major_id, $minor_id );
                // }
            }



        }
        $ccp->close();

        return $return;
    }




}

