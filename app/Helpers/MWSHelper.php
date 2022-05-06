<?php

use App\Models\MWSModel;

if(!function_exists('ScDashToNull')) {

    /**
     * This function is used to Convert Dash To Null
     * @param $value
     * @return mixed
     */
    function ScDashToNull($value)
    {
        return $result = str_replace('-', '', $value);
    }

}




if(!function_exists('ScRemoveLeftParantesis')) {

    /**
     * This function is used to Remove Dollar Sign
     * @param $value
     * @return mixed
     */
    function ScRemoveLeftParantesis($value)
    {
        //return $result = str_replace('(', '', $value);
        return $value;
    }

}

if(!function_exists('ScRemoveRightParantesis')) {
    /**
     * This function is used to Remove Dollar Sign
     * @param $value
     * @return mixed
     */
    function ScRemoveRightParantesis($value)
    {
        //return $result = str_replace(')', '', $value);
        return $result = $value;
    }

}

if(!function_exists('ScRemQuestionMark')) {

    function ScRemQuestionMark($value)
    {
        //return $result = str_replace('?', '', $value);
        return $value;
    }

}


if(!function_exists('scRemoveSlashnAndr')) {
    /**
     * This function is used to Remove \n\r
     * @param $value
     * @return mixed
     */
    function scRemoveSlashnAndr($value)
    {
        return $result = preg_replace('/\s+/', ' ', $value);
    }

}




if(!function_exists('scPercentageToNull')) {

    /**
     * This function is used to Convert Percentage To Null
     * @param $value
     * @return mixed
     */

    function scPercentageToNull($value)
    {
        return $result = str_replace('%', '', $value);
    }

}







if(!function_exists('scRemoveComma')) {
    /**
     * This function is used to Remove Comma
     * @param $value
     * @return mixed
     */
    function scRemoveComma($value)
    {
        return $result = str_replace(',', '', $value);

    }

}

if(!function_exists('scRemoveUnderscoreAndSlash')) {
    /**
     * This function is used to Remove Comma
     * @param $value
     * @return mixed
     */
    function scRemoveUnderscoreAndSlash($value)
    {
        return $result = str_replace('_/_', '', $value);

    }

}

if(!function_exists('ScRemoveDollarSign')) {

    /**
     * This function is used to Remove Dollar Sign
     * @param $value
     * @return mixed
     */
    function ScRemoveDollarSign($value)
    {
        //return $result = str_replace('$', '', $value);
        return $value;
    }

}


if(!function_exists('scLimitStringLength')) {
    /**
     * This function is used to set memory limit and execution time
     * @param $value
     * @return mixed
     */
    function scLimitStringLength($value)
    {
        return $result = substr($value, 0, 100);
    }

}
if(!function_exists('scConvertToUtf8Strings')) {
    /**
     * This function is used to set memory limit and execution time
     * @param $value
     * @return mixed
     */
    function scConvertToUtf8Strings($value)
    {
        //return $result = preg_replace("/[^a-zA-Z0-9%\/\s]/", "", $value);
        //$result=preg_replace("/[^a-zA-Z0-9.&() ]+/", "", $value);
        //$result=preg_replace("/[^a-zA-Z0-9\/.&() ]+/", "", $value);

       //return $result=preg_replace("/[^a-zA-Z0-9\/.&() ]+/", "", $value);
        /*referance link https://www.php.net/manual/en/mysqli.real-escape-string.php*/

        //$result=preg_replace("/[^a-zA-Z0-9\/.&() ]+/", "", $value);
        //return preg_replace('~[\x00\x0A\x0D\x1A\x22\x27\x5C]~u', '\\\$0', $value);

        /*https://www.toptal.com/php/a-utf-8-primer-for-php-and-mysql*/
       // $result=htmlspecialchars($value, ENT_QUOTES | ENT_SUBSTITUTE, "UTF-8");
        //$result= $result=htmlspecialchars_decode($result, ENT_NOQUOTES, "UTF-8");
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
       // header("Content-Type: text/html; charset=ISO-8859-1");
        $result=  preg_replace($regex, '$1', $value);
         return $result;

    }

}



if(!function_exists('scSetMemoryLimitAndExeTime')) {
    /**
     * This function is used to set memory limit and execution time
     * @param $value
     * @return mixed
     */
    function scSetMemoryLimitAndExeTime()
    {
        //ini_set('memory_limit', '2048M');
        ini_set('memory_limit', '-1');
        ini_set('max_execution_time', 0);
    }

}
if (!function_exists('scRemoveSinglQuote')) {

    /**
     * This function is used to Convert Dash To Null
     * @param $value
     * @return mixed
     */
    function scRemoveSinglQuote($value)
    {
        return $result = str_replace("'", "", $value);
    }

}
if (!function_exists('scRemoveDoubleQuote')) {

    /**
     * This function is used to Convert Dash To Null
     * @param $value
     * @return mixed
     */
    function scRemoveDoubleQuote($value)
    {
        return $result = str_replace('"', '', $value);
    }

}

if (!function_exists('get_fullfilment_chanell_value')) {
    function get_fullfilment_chanell_value($fullfilment_chanell)
    {

        switch ($fullfilment_chanell) {
            case "DEFAULT":
                return $fullfilment_chanell_value = 'MFN';
                break;
            default:
                return $fullfilment_chanell_value = 'FBA';
        }
    }
}

if (!function_exists('get_sc_decimel_value')) {
    function get_sc_decimel_value($value)
    {
        $round_value=round($value,2);
                return $round_value;
    }
}

function sc_array_flatten($array = null) {
    $result = array();

    if (!is_array($array)) {
        $array = func_get_args();
    }

    foreach ($array as $key => $value) {
        if (is_array($value)) {
            $result = array_merge($result, sc_array_flatten($value));
        } else {
            $result = array_merge($result, array($key => $value));
        }
    }

    return $result;
}
if (!function_exists('sc_generate_batch_id')) {
    function sc_generate_batch_id($account_id)
    {
        $generated_batch_id = date('Ymd').$account_id;

        return $generated_batch_id;
    }
}

if (!function_exists('sc_clean_product_attributes_strings')) {
    function sc_clean_product_attributes_strings($attribute)
    {

        $attribute=trim($attribute, '-');
        $attribute=trim($attribute, '.');
        if ($attribute=='na' || $attribute=='n /a' || $attribute=='N/a' || $attribute=='0' || $attribute=='#NA' || $attribute=='no color' ){
            $attribute='NA';
        }
        return $attribute;

    }
}
/**
 * @return bool
 */
if (!function_exists('scUpdateHistoricalDataStatus')) {
    function scUpdateHistoricalDataStatus()
    {
        $APIParametr = new MWSModel();
        $api_data = MWSModel::get_merchants_historical_data();

        if ($api_data) {
            foreach ($api_data as $api_parameter) {

                $mws_config_id = trim($api_parameter->mws_config_id);
                $get_sc_account_id = MWSModel::get_sc_account_id($mws_config_id);

                if ($get_sc_account_id) {
                    $sc_count_account_id = count($get_sc_account_id);
                    if ($sc_count_account_id > 0) {
                        $account_id = $get_sc_account_id[0]->id;
                        $get_sc_daily_batch_id = MWSModel::get_sc_daily_batch_id($account_id);

                        $sc_count_batch_id = count($get_sc_daily_batch_id);
                        if ($sc_count_batch_id > 0) {
                            $updateCustomerHistoricalDataStatus = MWSModel::update_customer_historical_data_status($mws_config_id);
                        }
                    }
                }
            }
        }
    }
}


?>