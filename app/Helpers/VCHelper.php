<?php

use App\Http\Resources\Decaptcha;
use Illuminate\Support\Facades\File;

if (!function_exists('remOf')) {

    function remOf($value)
    {
        return $result = str_replace('of', 'percentage', $value);
    }

}

if (!function_exists('remQuestionMark')) {

    function remQuestionMark($value)
    {
        return $result = str_replace('?', '', $value);
    }

}

if (!function_exists('redundant')) {

    /**
     * This function is used to Convert double Dash To Single Dash
     * @param $value
     * @return mixed
     */
    function redundant($value)
    {
        return $result = str_replace('__', '_', $value);
    }

}

if (!function_exists('redundantAll')) {

    /**
     * This function is used to Convert triple Dash To Single Dash
     * @param $value
     * @return mixed
     */

    function redundantAll($value)
    {
        return $result = str_replace('___', '_', $value);
    }

}

if (!function_exists('percentageToNull')) {

    /**
     * This function is used to Convert Percentage To Null
     * @param $value
     * @return mixed
     */

    function percentageToNull($value)
    {
        return $result = str_replace('%', '', $value);
    }

}

if (!function_exists('dashToNull')) {

    /**
     * This function is used to Convert Dash To Null
     * @param $value
     * @return mixed
     */
    function dashToNull($value)
    {
        return $result = str_replace('-', '', $value);
    }

}


if (!function_exists('spaceToUnderscore')) {

    /**
     * This function is used to  convert space to underscrore
     * @param $value
     * @return mixed
     */
    function spaceToUnderscore($value)
    {
        $result = str_replace(' ', '_', $value);
        $result = array_map('strtolower', $result);
        return $result;
    }

}


if (!function_exists('removeComma')) {
    /**
     * This function is used to Remove Comma
     * @param $value
     * @return mixed
     */
    function removeComma($value)
    {
        return $result = str_replace(',', '', $value);

    }

}

if (!function_exists('removeUnderscoreAndSlash')) {
    /**
     * This function is used to Remove Comma
     * @param $value
     * @return mixed
     */
    function removeUnderscoreAndSlash($value)
    {
        return $result = str_replace('_/_', '', $value);

    }

}

if (!function_exists('removeDollarSign')) {

    /**
     * This function is used to Remove Dollar Sign
     * @param $value
     * @return mixed
     */
    function removeDollarSign($value)
    {
        return $result = str_replace('$', '', $value);
    }

}


if (!function_exists('removeDollarCommaSpace')) {

    /**
     * This function is used to Remove Dollar Sign
     * @param $value
     * @return mixed
     */
    function removeDollarCommaSpace($value)
    {
        return removeDollarSign(removeComma(removeSpace(getIntegerValFromString($value))));

    }

}

if (!function_exists('getIntegerValFromString')) {

    /**
     * This function is used to Remove Dollar Sign
     * @param $value
     * @return mixed
     */
    function getIntegerValFromString($value)
    {
        return filter_var($value, FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);

    }

}

if (!function_exists('removeLeftParantesis')) {

    /**
     * This function is used to Remove Dollar Sign
     * @param $value
     * @return mixed
     */
    function removeLeftParantesis($value)
    {
        return $result = str_replace('(', '', $value);
    }

}

if (!function_exists('removeRightParantesis')) {
    /**
     * This function is used to Remove Dollar Sign
     * @param $value
     * @return mixed
     */
    function removeRightParantesis($value)
    {
        return $result = str_replace(')', '', $value);
    }

}

if (!function_exists('removeHash')) {

    /**
     * This function is used to Remove # Sign
     * @param $value
     * @return mixed
     */
    function removeHash($value)
    {
        return $result = str_replace('#', '', $value);
    }

}

if (!function_exists('removeSpace')) {

    /**
     * This function is used to Remove # Sign
     * @param $value
     * @return mixed
     */
    function removeSpace($value)
    {
        return $result = str_replace(' ', '', $value);
    }

}

if (!function_exists('getOnlyStringValCatetgory')) {

    /**
     * This function is used to Remove Integer Values from category
     * @param $value
     * @return mixed
     */
    function getOnlyStringValCatetgory($value)
    {
        return $result = ltrim(preg_replace("/[^A-Z a-z]/", "", $value));
    }

}


if (!function_exists('removeSlashnAndr')) {
    /**
     * This function is used to Remove \n\r
     * @param $value
     * @return mixed
     */
    function removeSlashnAndr($value)
    {
        return $result = preg_replace('/\s+/', ' ', $value);
    }

}

if (!function_exists('setMemoryLimitAndExeTime')) {
    /**
     * This function is used to set memory limit and execution time
     * @param $value
     * @return mixed
     */
    function setMemoryLimitAndExeTime()
    {
        ini_set('memory_limit', '10000M');
        ini_set('max_execution_time', 0);
    }

}
if (!function_exists('getCustomerId')) {
    /**
     * This function is used to set memory limit and execution time
     * @param $value
     * @return mixed
     */
    function getCustomerId($getDashboardData)
    {
        $getCustomerId = NULL;
        if (strripos($getDashboardData, "customerId") > 0) {
            $str = substr($getDashboardData, strripos($getDashboardData, "customerId") + 17);
            $getCustomerId = substr($str, 0, strripos($str, '\"\n'));
        }

        return $getCustomerId;
    }

}

if (!function_exists('isDate')) {
    /**
     * @param $value
     * @return bool
     */
    function isDate($value)
    {
        if (!$value) {
            return false;
        }

        try {
            new \DateTime($value);
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }
}


if (!function_exists('dateConversion')) {
    /**
     * @param $value
     * @return false|null|string
     */
    function dateConversion($value)
    {
        $result = 'NA';
        if (isDate($value) != FALSE) {
            $result = date('Y-m-d', strtotime($value));
        }

//      if (is_numeric($value)) {
//          $result = gmdate("Y-m-d", ($value - 25569) * 86400);
//      }
        return $result;
    }
}

if (!function_exists('valuesMultiplyToHundered')) {
    /**
     * @param $value
     * @return false|null|string
     */
    function valuesMultiplyToHundered($value)
    {
        return $result = $value * 100;
    }
}

if (!function_exists('checkPercentageValue')) {
    /**
     * This function is used to check if value has percetange value then remove it else multiply by hundered to get original value
     * @param $value
     * @return false|null|string
     */
    function checkPercentageValue($value)
    {
        $defaultValue = 0;
        if (strpos($value, '%') !== false) {
            $result = percentageToNull(($value));
        } else if (is_float($value) || is_numeric($value)) {
            $result = valuesMultiplyToHundered($value);
        } else {
            $result = $defaultValue;
        }
        return (float)removeDollarCommaSpace($result);
    }
}


if (!function_exists('doVendorLogin')) {

    function doVendorLogin($domain, $proxy, $proxyAuth)
    {
        $data = array();
        $data['status'] = FALSE;
        $data['error_text'] = NULL;
        $data['error_code'] = NULL;
        $data['html'] = NULL;
        for ($i = 0; $i < 3; $i++) {
            Log::info('Loop starts = ' . $i);
            $data = vendorLogin($domain, $proxy, $proxyAuth);

            if (stripos($data['html'], 'id="logout_topRightNav"') !== FALSE) {
                echo 'logged In';
                File::put(public_path('vc/vendorDashboard.html'), $data['html']);
                return $data;
            }
        }
        return $data;
    }
}


function vendorLogin($domain, $proxy = NULL, $proxyAuth)
{
    $url = "https://" . $domain . "/gp/vendor/sign-in";
    $vendorCentralPage = getContent($url, NULL, $proxy, $proxyAuth, $domain);
    Log::info('Vendor Central Page == ' . $vendorCentralPage['html']);
    $response = array();
    $response['status'] = FALSE;
    $response['error_text'] = NULL;
    $response['error_code'] = NULL;
    $response['html'] = NULL;

    if ($vendorCentralPage['status'] == TRUE) {
        $pos1 = stripos($vendorCentralPage['html'], 'rightNavWidgetBody');
        if ($pos1 === FALSE) {
            $url = "https://" . $domain . "/gp/vendor/sign-in";
            $vendorCentralPage = getContent($url, NULL, $proxy, $proxyAuth, $domain);
        }
        $dom = new DOMDocument();
        libxml_use_internal_errors(true);
        $dom->loadHTML($vendorCentralPage['html']);

        $a = new DOMXPath($dom);
        echo $signInLink = $a->query("//a[@class='sprite-button-wrap']/@href")[0]->nodeValue;
        echo PHP_EOL;
        File::put(public_path('vc/signInLink.html'), $vendorCentralPage);
        Log::info('Sign In Link = ' . $signInLink);
        $signInDataPage = getContent($signInLink, NULL, $proxy, $proxyAuth, $domain);
        Log::info('Login Page Layout = ' . $signInDataPage['html']);
        if ($signInDataPage['status'] == TRUE) {
            preg_match_all('/<input type="hidden" name="(.*?)" value="(.*?)"/', $signInDataPage['html'], $matches);
            preg_match('/<input name="metadata1" type="hidden" value="(.*?)"/', $signInDataPage['html'], $meta_match);
            $post_data_sign_in = array();
            $post_data_sign_in['email'] = 'projectteam@orcapacific.net';
            $post_data_sign_in['password'] = 'cGxb]B"qV5dJ{GP';
            $post_data_sign_in['rememberMe'] = "true";
            if (count($matches[1]) > 0) {
                foreach ($matches[1] as $key => $match) {
                    $post_data_sign_in[$match] = $matches[2][$key];
                }
            }

            $request_headers = array();
            $request_headers[] = 'Accept: text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,image/apng,*/*;q=0.8';
            $request_headers[] = 'Accept-Language: en-US,en;q=0.8';
            $request_headers[] = 'Cache-Control: max-age=0';
            $request_headers[] = 'Connection: keep-alive';
            $request_headers[] = 'Host: vendorcentral.amazon.com';
            $request_headers[] = 'Origin: https://vendorcentral.amazon.com';
            $request_headers[] = 'Referer: ' . $signInLink;
            $request_headers[] = 'Upgrade-Insecure-Requests: 1';
            $request_headers[] = 'User-Agent: ' . GetRandomUserAgent();
            $request_headers[] = 'DNT:1';

            //init curl call
            $ch = curl_init();
            $curlUrlToSendData = 'https://vendorcentral.amazon.com/ap/signin';
            curl_setopt($ch, CURLOPT_URL, $curlUrlToSendData);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_TIMEOUT, 60);
            curl_setopt($ch, CURLOPT_HTTPHEADER, $request_headers);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_ENCODING, "");
            $cookie_name = str_replace(array(".", ":"), "-", $proxy);
            curl_setopt($ch, CURLOPT_COOKIEJAR, public_path('vc/cookies/' . $cookie_name . '.txt'));
            curl_setopt($ch, CURLOPT_COOKIEFILE, public_path('vc/cookies/' . $cookie_name . '.txt'));
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
            curl_setopt($ch, CURLOPT_CAINFO, public_path('vc/cacert.pem'));
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($post_data_sign_in));
            curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
            curl_setopt($ch, CURLOPT_REFERER, $signInLink);
            $responseSignInDataPage['html'] = curl_exec($ch);
            Log::info('post data sign in = ');
            if (curl_errno($ch)) {
                $responseSignInDataPage['error_text'] = "Error: " . curl_error($ch);
            } else {
                curl_close($ch);
            }
            //Log::info('Response Sign In Data Page = ' . $responseSignInDataPage['html']);
            if (!isset($responseSignInDataPage['error_text'])) {
                $authCaptcha = stripos($responseSignInDataPage['html'], 'auth-captcha-image-container');
                $foundOtp = stripos($responseSignInDataPage['html'], 'auth-mfa-otpcode');
                if ($authCaptcha !== FALSE) {
                    $capchaHtmlResponse = $responseSignInDataPage['html'];

                    unset($dom);
                    unset($a);
                    $dom = new DOMDocument();
                    libxml_use_internal_errors(true);
                    $dom->loadHTML($capchaHtmlResponse);
                    $a = new DOMXPath($dom);
                    echo $capcha_image = $a->query('//img[@id="auth-captcha-image"]/@src')[0]->nodeValue;
                    echo PHP_EOL;

                    preg_match_all('/<input type="hidden" name="(.*?)" value="(.*?)"/', $capchaHtmlResponse, $matches);
                    preg_match('/<input name="metadata1" type="hidden" value="(.*?)"/', $capchaHtmlResponse, $meta_match);

                    $post_data_captcha = array();
                    $post_data_captcha['email'] = 'projectteam@orcapacific.net';
                    $post_data_captcha['password'] = 'cGxb]B"qV5dJ{GP';
                    $post_data_captcha['rememberMe'] = "true";
                    $captcha_solved = solveCapcha($capcha_image);
                    Log::info('Captcha Solving Link = ' . $capcha_image);
                    Log::info('captcha solved = ' . $captcha_solved['message']);
                    //echo "<br> ".$captcha_solved['message'];
                    if ($captcha_solved['status'] == TRUE) {
                        $post_data_captcha['guess'] = $captcha_solved['message'];
                        if (count($matches[1]) > 0) {
                            foreach ($matches[1] as $key => $match) {
                                $post_data_captcha[$match] = $matches[2][$key];
                            }
                        }

                        $request_headers = array();
                        $request_headers[] = 'Accept: text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,image/apng,*/*;q=0.8,application/signed-exchange;v=b3';
                        $request_headers[] = 'Accept-Language: en-US,en;q=0.9,fr;q=0.8';
                        $request_headers[] = 'Cache-Control: max-age=0';
                        $request_headers[] = 'Connection: keep-alive';
                        $request_headers[] = 'Host: vendorcentral.amazon.com';
                        $request_headers[] = 'Origin: https://vendorcentral.amazon.com';
                        $request_headers[] = 'Referer: ' . $signInLink;
                        $request_headers[] = 'Upgrade-Insecure-Requests: 1';
                        $request_headers[] = 'User-Agent: ' . getRandomUserAgent();
                        $request_headers[] = 'DNT:1';

                        $curlUrlToSendData = 'https://vendorcentral.amazon.com/ap/signin';
                        $ch = curl_init();
                        curl_setopt($ch, CURLOPT_URL, $curlUrlToSendData);
                        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                        curl_setopt($ch, CURLOPT_TIMEOUT, 60);
                        curl_setopt($ch, CURLOPT_HTTPHEADER, $request_headers);
                        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
                        curl_setopt($ch, CURLOPT_ENCODING, "");
                        $cookie_name = str_replace(array(".", ":"), "-", $proxy);
                        curl_setopt($ch, CURLOPT_COOKIEJAR, public_path('vc/cookies/' . $cookie_name . '.txt'));
                        curl_setopt($ch, CURLOPT_COOKIEFILE, public_path('vc/cookies/' . $cookie_name . '.txt'));
                        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
                        curl_setopt($ch, CURLOPT_CAINFO, public_path('vc/cacert.pem'));
                        curl_setopt($ch, CURLOPT_POST, 1);
                        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($post_data_captcha));
                        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
                        curl_setopt($ch, CURLOPT_REFERER, $signInLink);
                        $captchaResponse['html'] = curl_exec($ch);
                        Log::info('post data captcha = ');
                        if (curl_errno($ch)) {
                            $captchaResponse['error_text'] = " Error: " . curl_error($ch);
                        } else {
                            curl_close($ch);
                        }
                        //Log::info('captcha Response = ' . $captchaResponse['html']);
                        if (!isset($captchaResponse['error_text'])) {

                            $otpResponse['status'] = TRUE;
                            $otpHtml = $captchaResponse['html'];
                            // Found OTP ( One Time Password )
                            preg_match_all('/<input type="hidden" name="(.*?)" value="(.*?)"/', $otpHtml, $matches);
                            preg_match('/<input name="metadata1" type="hidden" value="(.*?)"/', $otpHtml, $meta_match);
                            $post_data_otp = array();
                            $post_data_otp['otpCode'] = getCurrentOtpPassword();
                            if (count($matches[1]) > 0) {
                                foreach ($matches[1] as $key => $match) {
                                    $post_data_otp[$match] = $matches[2][$key];
                                }
                            }
                            $request_headers = array();
                            $request_headers[] = 'Accept: text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,image/apng,*/*;q=0.8';
                            $request_headers[] = 'Accept-Language: en-US,en;q=0.8';
                            $request_headers[] = 'Cache-Control: max-age=0';
                            $request_headers[] = 'Connection: keep-alive';
                            $request_headers[] = 'Host: vendorcentral.amazon.com';
                            $request_headers[] = 'Origin: https://vendorcentral.amazon.com';
                            $request_headers[] = 'Referer: ' . $signInLink;
                            $request_headers[] = 'Upgrade-Insecure-Requests: 1';
                            $request_headers[] = 'User-Agent: ' . GetRandomUserAgent();
                            $request_headers[] = 'DNT:1';

                            $ch = curl_init();
                            $curlUrlToSendData = 'https://vendorcentral.amazon.com/ap/signin';
                            curl_setopt($ch, CURLOPT_URL, $curlUrlToSendData);
                            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                            curl_setopt($ch, CURLOPT_TIMEOUT, 60);
                            curl_setopt($ch, CURLOPT_HTTPHEADER, $request_headers);
                            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
                            curl_setopt($ch, CURLOPT_ENCODING, "");
                            $cookie_name = str_replace(array(".", ":"), "-", $proxy);
                            curl_setopt($ch, CURLOPT_COOKIEJAR, public_path('vc/cookies/' . $cookie_name . '.txt'));
                            curl_setopt($ch, CURLOPT_COOKIEFILE, public_path('vc/cookies/' . $cookie_name . '.txt'));

                            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
                            curl_setopt($ch, CURLOPT_CAINFO, public_path('vc/cacert.pem'));
                            curl_setopt($ch, CURLOPT_POST, true);
                            curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($post_data_otp));
                            curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
                            curl_setopt($ch, CURLOPT_REFERER, $signInLink);
                            $otpResponse['html'] = curl_exec($ch);
                            Log::info('post data otp = ');

                            if (curl_errno($ch)) {
                                $otpResponse['error_text'] = "Error: " . curl_error($ch);
                            } else {
                                curl_close($ch);
                            }

                            //Log::info('otp Response = ' . $otpResponse['html']);
                            $is_logged_in = isLoggedIn($domain, $otpResponse, $proxy, $proxyAuth);

                            if ($is_logged_in['status'] == TRUE) {
                                $response['status'] = TRUE;
                                $response['html'] = $is_logged_in['html'];
                            } else {
                                $response['status'] = FALSE;
                            }

                        }

                    } else {
                        $response['error_text'] = $captcha_solved['message'];
                        $response['status'] = FALSE;
                        $response['error_code'] = CAPTCHA_STATUS;
                    }
                } elseif ($foundOtp !== FALSE) {
                    $otpResponse['status'] = TRUE;
                    $otpHtml = $responseSignInDataPage['html'];

                    // Found OTP ( One Time Password )
                    preg_match_all('/<input type="hidden" name="(.*?)" value="(.*?)"/', $otpHtml, $matches);
                    preg_match('/<input name="metadata1" type="hidden" value="(.*?)"/', $otpHtml, $meta_match);
                    $post_data_otp = array();
                    $post_data_otp['otpCode'] = getCurrentOtpPassword();
                    if (count($matches[1]) > 0) {
                        foreach ($matches[1] as $key => $match) {
                            $post_data_otp[$match] = $matches[2][$key];
                        }
                    }

                    $request_headers = array();
                    $request_headers[] = 'Accept: text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,image/apng,*/*;q=0.8';
                    $request_headers[] = 'Accept-Language: en-US,en;q=0.8';
                    $request_headers[] = 'Cache-Control: max-age=0';
                    $request_headers[] = 'Connection: keep-alive';
                    $request_headers[] = 'Host: vendorcentral.amazon.com';
                    $request_headers[] = 'Origin: https://vendorcentral.amazon.com';
                    $request_headers[] = 'Referer: ' . $signInLink;
                    $request_headers[] = 'Upgrade-Insecure-Requests: 1';
                    $request_headers[] = 'User-Agent: ' . GetRandomUserAgent();
                    $request_headers[] = 'DNT:1';

                    $ch = curl_init();
                    $curlUrlToSendData = 'https://vendorcentral.amazon.com/ap/signin';
                    curl_setopt($ch, CURLOPT_URL, $curlUrlToSendData);
                    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                    curl_setopt($ch, CURLOPT_TIMEOUT, 60);
                    curl_setopt($ch, CURLOPT_HTTPHEADER, $request_headers);
                    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
                    curl_setopt($ch, CURLOPT_ENCODING, "");
                    $cookie_name = str_replace(array(".", ":"), "-", $proxy);
                    curl_setopt($ch, CURLOPT_COOKIEJAR, public_path('vc/cookies/' . $cookie_name . '.txt'));
                    curl_setopt($ch, CURLOPT_COOKIEFILE, public_path('vc/cookies/' . $cookie_name . '.txt'));

                    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
                    curl_setopt($ch, CURLOPT_CAINFO, public_path('vc/cacert.pem'));
                    curl_setopt($ch, CURLOPT_POST, true);
                    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($post_data_otp));
                    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
                    curl_setopt($ch, CURLOPT_REFERER, $signInLink);
                    $otpResponse['html'] = curl_exec($ch);
                    Log::info('post data otp = ');

                    if (curl_errno($ch)) {
                        $otpResponse['error_text'] = "Error: " . curl_error($ch);
                    } else {
                        curl_close($ch);
                    }
                    //Log::info('otp Response = ' . $otpResponse['html']);
                    $is_logged_in = isLoggedIn($domain, $otpResponse, $proxy, $proxyAuth);

                    if ($is_logged_in['status'] == TRUE) {
                        $response['status'] = TRUE;
                        $response['html'] = $is_logged_in['html'];
                    } else {
                        $response['status'] = FALSE;
                    }

                }

            } else {
                echo $responseSignInDataPage['error_text'];
            }

        } else {
            $response = $signInDataPage;
        }
    } else {
        $response = $vendorCentralPage;
    }

    return $response;

}

function ddd($value)
{
    echo "<pre>";
    print_r($value);
    die;
}

function getContent($url, $header = NULL, $proxy = NULL, $proxyAuth, $domain = NULL)
{
    set_time_limit(0);
    $requestHeaders = array();
    if (!is_null($header)) {
        $requestHeaders = $header;
    } else {
        $requestHeaders[] = 'Accept: text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,*/*;q=0.8';
        $requestHeaders[] = 'Accept-Language:en-US,en;q=0.8';
        $requestHeaders[] = 'Cache-Control:max-age=0';
        $requestHeaders[] = 'Connection:keep-alive';
        $requestHeaders[] = 'Upgrade-Insecure-Requests:1';
        $requestHeaders[] = 'User-Agent: ' . GetRandomUserAgent();
    }

    $cookie_name = str_replace(array(".", ":"), "-", $proxy);
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_TIMEOUT, 500);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $requestHeaders);
    curl_setopt($ch, CURLOPT_COOKIEJAR, public_path('vc/cookies/' . $cookie_name . '.txt'));
    curl_setopt($ch, CURLOPT_COOKIEFILE, public_path('vc/cookies/' . $cookie_name . '.txt'));
    //curl_setopt($ch, CURLOPT_PROXY, $proxy);
    //curl_setopt($ch, CURLOPT_PROXYUSERPWD, $proxyAuth);
    curl_setopt($ch, CURLOPT_VERBOSE, false);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);

    curl_setopt($ch, CURLOPT_CAINFO, public_path('vc/cacert.pem'));
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);

    $response = array();
    $response['status'] = FALSE;
    $response['error_code'] = NULL;
    $response['error_text'] = NULL;
    $response['html'] = NULL;
    $response['url_check'] = NULL;
    $response['html'] = trim(curl_exec($ch));
    if (curl_errno($ch)) {
        $response['error_code'] = -2;
        $response['error_text'] = "Error: " . curl_error($ch);

    } else {
        curl_close($ch);
    }
    if (is_null($response['error_text'])) {
        $response['status'] = TRUE;
    }

    return $response;


}


function getRandomUserAgent()
{
    // define list of agents
    $userAgent = [
        'Mozilla/5.0 (Windows NT 6.1; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/61.0.3163.100 Safari/537.36',
        'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_12_3) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/57.0.2987.110 Safari/537.36',
        'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/76.0.3809.132 Safari/537.36'
    ];
    $return = array_rand($userAgent, 1);
    return $userAgent[$return];
}

function getCurrentOtpPassword()
{
    // Secret Key
    $secret = 'QZUR5JYPGZOPOLJQTV65IM4NM4KWRI22ZYEYKRPMZ6IZ4YDAUZEQ';
    //
    Google2FA::setEnforceGoogleAuthenticatorCompatibility(false);

    $currentOtp = Google2FA::getCurrentOtp($secret);

    return $currentOtp;
}

function solveCapcha($image)
{

    $capcha_error_text = "Capacha API ";

    $return = array();
    $return['status'] = FALSE;
    $return['message'] = "";

    $ccp = new Decaptcha();
    $ccp->init();

    if ($ccp->login(DECAPTURE_HOST, DECAPTURE_PORT, DECAPTURE_USERNAME, DECAPTURE_PASSWORD) < 0) {
        $return['message'] = $capcha_error_text . "Login Failed";
    } else {
        $system_load = 0;
        if ($ccp->system_load($system_load) != ccERR_OK) {
            $return['message'] = $capcha_error_text . " system_load() FAILED";
        } else {
            $major_id = 0;
            $minor_id = 0;
            //     for( $i = 0; $i < 3; $i++ ) {
            $pict = file_get_contents($image);
            //$pict = $image;
            $text = '';


            $pict_to = ptoDEFAULT;
            $pict_type = ptUNSPECIFIED;

            $res = $ccp->picture2($pict, $pict_to, $pict_type, $text, $major_id, $minor_id);
            switch ($res) {
                // most common return codes
                case ccERR_OK:
                    //print( "got text for id=".$major_id."/".$minor_id.", type=".$pict_type.", to=".$pict_to.", text='".$text."'" );
                    $return['status'] = TRUE;
                    $return['message'] = $text;
                    break;
                case ccERR_BALANCE:
                    $return['message'] = $capcha_error_text . " not enough funds to process a picture, balance is depleted";
                    break;
                case ccERR_TIMEOUT:
                    $return['message'] = $capcha_error_text . " picture has been timed out on server (payment not taken)";
                    break;
                case ccERR_OVERLOAD:
                    $return['message'] = $capcha_error_text . " temporarily server-side error, server's overloaded, wait a little before sending a new picture";
                    break;

                // local errors
                case ccERR_STATUS:
                    $return['message'] = $capcha_error_text . "  local error., either ccproto_init() or ccproto_login() has not been successfully called prior to ccproto_picture()";
                    $return['message'] .= $capcha_error_text . " need ccproto_init() and ccproto_login() to be called";
                    break;

                // network errors
                case ccERR_NET_ERROR:
                    $return['message'] = $capcha_error_text . " network troubles, better to call ccproto_login() again";
                    break;

                // server-side errors
                case ccERR_TEXT_SIZE:
                    $return['message'] = $capcha_error_text . "size of the text returned is too big";
                    break;
                case ccERR_GENERAL:
                    $return['message'] = $capcha_error_text . "server-side error, better to call ccproto_login() again";
                    break;
                case ccERR_UNKNOWN:
                    $return['message'] = $capcha_error_text . " unknown error, better to call ccproto_login() again";
                    break;

                default:
                    // any other known errors?
                    $return['message'] = $capcha_error_text . " Unknown Error";
                    break;
            }

        }


    }
    $ccp->close();

    return $return;
}


function isLoggedIn($domain = "vendorcentral.amazon.com", $data = NULL, $proxy = NULL, $proxyAuth)
{
    if (is_null($data)) {
        $catalogScrapData = "https://vendorcentral.amazon.com/hz/vendor/members/home/ba";
        $data = getContent($catalogScrapData, NULL, $proxy, $proxyAuth, $domain);

    }
    if ($data['status'] == TRUE) {
        if (stripos($data['html'], 'id="logout_topRightNav"') !== FALSE) {
            Log::info('Login Found');
            //echo "\n===== Login Found ======= \n";
            $data['status'] = TRUE;
        } else {
            Log::info('Login Failed');
            //echo "\n===== Login Failed ======= \n";
            $data['status'] = FALSE;
        }
    }

    return $data;
}


function getScrapCatalogPostData($scrapData)
{
    $postData = array();
    preg_match_all('/<input type="hidden" name="(.*?)" value="(.*?)"/', $scrapData, $matches);
    if (count($matches[1]) > 0) {
        foreach ($matches[1] as $key => $match) {
            $postData[$match] = $matches[2][$key];
        }
    }
    return $postData;
}


function getTotalNoPagesCatalog($data)
{
    if (strripos($data, "var infoText") > 0) {
        $str = substr($data, strripos($data, "var infoText") + 16);
        $result = (substr($str, 0, strripos($str, "';")));
        $arrayData = explode("of", $result);
        $arrayData[0] = explode("-", $arrayData[0])[1];
        $arrayData[1] = preg_replace("/[^0-9]+/", "", $arrayData[1]);
        return $arrayData[1];
    }
}

function getScrapCatalogData($content, $vendorGroupId, $offset)
{
    $foundVendorCode = 0;
    if (strpos($content, 'Vendor code') !== false) {
        $foundVendorCode = 1;
    }
    $dom = new DOMDocument();
    libxml_use_internal_errors(true);
    $dom->loadHTML($content);
    $a = new DOMXPath($dom);
    $totalElements = $a->query("//table[@class='a-bordered mycat-table']//tr");
    $trCount = count($totalElements);
    $results = array();
    for ($i = 2; $i <= $trCount; $i++) {
        $image = $a->query("//table[@class='a-bordered mycat-table']//tr[$i]//td[2]//img/@src")[0];
        $data['image'] = (!empty($image->value)) ? $image->value : 'NA';
        $title = $a->query("//table[@class='a-bordered mycat-table']//tr[$i]//td[3]//a/text()")[0];
        $data['title'] = (!empty($title->data)) ? $title->data : 'NA';
        $asin = $a->query("//table[@class='a-bordered mycat-table']//tr[$i]/td[4]/dl/dd[1]/text()")[0];
        $data['asin'] = (!is_null($asin)) ? $asin->data : 'NA';
        $upc = $a->query("//table[@class='a-bordered mycat-table']//tr[$i]/td[4]/dl/dd[2]/text()")[0];
        $data['upc'] = (!is_null($upc)) ? $upc->data : 'NA';
        $sku = $a->query("//table[@class='a-bordered mycat-table']//tr[$i]/td[4]/dl/dd[3]/div/span/text()")[0];
        $data['sku'] = (!is_null($sku)) ? trim($sku->data) : 'NA';
        if ($foundVendorCode == 1) {
            $vdrCode = $a->query("//table[@class='a-bordered mycat-table']//tr[$i]/td[5]/text()")[0];
            $data['vendorCode'] = (!empty($vdrCode->data)) ? $vdrCode->data : 'NA';
            $lastModifiedDate = $a->query("//table[@class='a-bordered mycat-table']//tr[$i]/td[6]/text()")[0]->data;
            $data['lastModifiedDate'] = date_format(date_create($lastModifiedDate), "Y-m-d");
            $cost = $a->query("//table[@class='a-bordered mycat-table']//tr[$i]/td[7]/text()")[0]->data;
            $data['cost'] = (preg_match('/\d+\.?\d*/', $cost, $matches)) ? $matches[0] : 0;
            $available = $a->query("//table[@class='a-bordered mycat-table']//tr[$i]/td[8]/span/text()")[0];
            $data['available'] = (!empty($available->data)) ? $available->data : 'NA';
        } else {

            $data['vendorCode'] = 'NA';
            $lastModifiedDate = $a->query("//table[@class='a-bordered mycat-table']//tr[$i]/td[5]/text()")[0]->data;
            $data['lastModifiedDate'] = date_format(date_create($lastModifiedDate), "Y-m-d");
            $cost = $a->query("//table[@class='a-bordered mycat-table']//tr[$i]/td[6]/text()")[0]->data;
            $data['cost'] = (preg_match('/\d+\.?\d*/', $cost, $matches)) ? $matches[0] : 0;
            $available = $a->query("//table[@class='a-bordered mycat-table']//tr[$i]/td[7]/span/text()")[0];
            $data['available'] = (!empty($available->data)) ? $available->data : 'NA';
        }

        $data['fkVendorGroupId'] = $vendorGroupId;
        $data['offset'] = $offset;
        $data['created_at'] = date('Y-m-d h:i:s');
        array_push($results, $data);
    }

    return $results;

}

function postCurlRequestForScrapCatalog($url, $referer, $postData, $requestHeaders, $proxy, $proxyAuth)
{
    $cookieName = str_replace(array(".", ":"), "-", $proxy);
    //init curl call
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_TIMEOUT, 60);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $requestHeaders);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_ENCODING, "");
    curl_setopt($ch, CURLOPT_COOKIEJAR, public_path('vc/cookies/' . $cookieName . '.txt'));
    curl_setopt($ch, CURLOPT_COOKIEFILE, public_path('vc/cookies/' . $cookieName . '.txt'));
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
    //curl_setopt($ch, CURLOPT_PROXY, $proxy);
    //curl_setopt($ch, CURLOPT_PROXYUSERPWD, $proxyAuth);
    curl_setopt($ch, CURLOPT_CAINFO, public_path('vc/cacert.pem'));
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($postData));
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($ch, CURLOPT_REFERER, $referer);
    $response['status'] = FALSE;
    $response['error_code'] = NULL;
    $response['error_text'] = NULL;
    $response['html'] = NULL;
    $response['url_check'] = NULL;
    $response['html'] = trim(curl_exec($ch));

    if (curl_errno($ch)) {
        $response['error_code'] = -2;
        $response['error_text'] = "Error: " . curl_error($ch);

    } else {
        curl_close($ch);
    }
    if (is_null($response['error_text'])) {
        $response['status'] = TRUE;
    }
    return $response;
}


function getCatalogHeaders($catalogRefer)
{
    $requestHeaders = array();
    $requestHeaders[] = 'Accept: text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,image/apng,*/*;q=0.8';
    $requestHeaders[] = 'Accept-Language: en-US,en;q=0.8';
    $requestHeaders[] = 'Cache-Control: max-age=0';
    $requestHeaders[] = 'Connection: keep-alive';
    $requestHeaders[] = 'Host: vendorcentral.amazon.com';
    $requestHeaders[] = 'Origin: https://vendorcentral.amazon.com';
    $requestHeaders[] = 'Referer: ' . $catalogRefer;
    $requestHeaders[] = 'Upgrade-Insecure-Requests: 1';
    $requestHeaders[] = 'User-Agent: ' . GetRandomUserAgent();
    $requestHeaders[] = 'DNT:1';

    return $requestHeaders;
}


function getFormHiddenElements($data)
{
    preg_match_all('/<input type="hidden" name="(.*?)" value="(.*?)"/', $data, $matches);
    if (count($matches[1]) > 0) {
        foreach ($matches[1] as $key => $match) {
            $postData[$match] = $matches[2][$key];
        }
    }
    return $postData;
}

if (!function_exists('strComparing')) {

    /**
     * @param $string1
     * @param $string2
     * @return bool|string
     */
    function strComparing($string1, $string2)
    {
        $result = strcmp($string1, $string2);
        if ($result == -1) {
            $result2 = strcmp($string1, $string2);
            if ($result2 == 1) {
                return $result2;
            }else{
                return $result2;
            }
        } else if ($result == 1) {
            return $result;
        }else if ($result == 0) {
            return $result;
        }
    }
}

if (!function_exists('storeAsinHelper')) {

    /**
     * @param $data
     * @return mixed
     */
    function storeAsinHelper($asinsData)
    {
        $storeAsins = [];
        $allStoreAsin = [];
        foreach ($asinsData as $data ){
            $storeAsins['fkAccountId'] = $data->fkAccountId;
            $storeAsins['fkBatchId'] = $data->batchId;
            $storeAsins['fkSellerConfigId'] = $data->fk_vendor_id;
            $storeAsins['asin'] = (isset($data->asin) && !empty($data->asin) ? $data->asin : 'NA');
            $storeAsins['idType'] = 'ASIN';
            $storeAsins['productDetailsDownloaded'] = 0;
            $storeAsins['productCategoryDetailsDownloaded'] = 0;
            $storeAsins['source'] = 'VC';
            $storeAsins['createdAt'] = date('Y-m-d h:i:s');
            $storeAsins['updatedAt'] = date('Y-m-d h:i:s');
            array_push($allStoreAsin, $storeAsins);

        }
        return $allStoreAsin;
    }
}