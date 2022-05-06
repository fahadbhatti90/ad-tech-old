<?php
namespace App\Libraries;

class ScraperConstant {
   const SUCCESS_CODE = 1;
   const FAILURE_CODE = 0;
   const ASIN_STATUS_PENDING = 0;
   const ASIN_STATUS_PROCESSING = 1;
   const ASIN_STATUS_COMPLETED = 2;
   const ASIN_STATUS_FAILED_404 = 3;
   
   const ERROR_CAPTCHA = -1;
   const ERROR_DATA_CLEAN_FAILURE = -3;
   const ERROR_PRODUCTS_NOT_FOUND = -404;
   const ERROR_SERVICE_NOT_AVAILABLE = -503;
   const ERROR_INTERNAL_SERVER = -8;
   const ERROR_UNKNOWN = -9;
   const ERROR_EMPTY_RESPONSE = -10;
   const ERROR_CURL = -11;
   const DECAPTURE_HOST = "api.de-captcher.com";
   const DECAPTURE_USERNAME = "helotek";
   const DECAPTURE_PASSWORD = '2$xLKuso*i!7';
   const DECAPTURE_PORT = 36541;

   const ccERR_OK = 0;
   const ccERR_BALANCE =-6;
   const ccERR_TIMEOUT = -7;
   const ccERR_OVERLOAD =-5;
   const ccERR_STATUS = -2;
   const ccERR_NET_ERROR = -3;
   const ccERR_TEXT_SIZE = -4;
   const ccERR_GENERAL = -1;
   const ccERR_UNKNOWN = -200;
}
?>