<?php

namespace App\Console\Commands\Ams\Profile;

use Artisan;
use DB;
use App\Models\AMSModel;
use GuzzleHttp\Client;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Log;


class ProfileCron extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'getprofileid:amsprofile';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'This command is used to get all profile list';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * @return mixed
     * @throws \GuzzleHttp\Exception\GuzzleException
     * Execute the console command.
     *
     */
    public function handle()
    {
        Log::info("filePath:Commands\Ams\Profile. Start Cron.");
        Log::info($this->description);

        $obaccess_token = new AMSModel();
        Log::info("AMS Auth token get from DB Start!");
        $getAllAmsApiCreds = $obaccess_token->getAllAmsApiCreds();
        if ($getAllAmsApiCreds != FALSE) {
            foreach ($getAllAmsApiCreds as $singleAmsApiCreds) {
                a:
               $fkConfigId = $singleAmsApiCreds->id;
                try {
                    $authCommandArray = array();
                    $authCommandArray['fkConfigId'] = $fkConfigId;
                    \Artisan::call('getaccesstoken:amsauth', $authCommandArray);
                    //$this->amsProfileApiCall($singleAmsApiCreds);
                    $obClientId = new AMSModel();
                    //get updated auth token for every request.
                    $getAMSTokenById = $obClientId->getAMSTokenById($fkConfigId);
                    $access_token = $getAMSTokenById->access_token;
                    $body = array();
                    $apiClient_id = $singleAmsApiCreds->client_id;
                    // Create a client with a base URI
                    $url = Config::get('constants.amsApiUrl') . '/' . Config::get('constants.apiVersion') . '/' . Config::get('constants.amsProfileUrl');
                    $client = new Client();
                    $response = $client->request('GET', $url, [
                        'headers' => [
                            'Authorization' => 'Bearer ' . $access_token,
                            'Content-Type' => 'application/json',
                            'Amazon-Advertising-API-ClientId' => $apiClient_id],
                        'delay' => Config::get('constants.delayTimeInApi'),
                        'connect_timeout' => Config::get('constants.connectTimeOutInApi'),
                        'timeout' => Config::get('constants.timeoutInApi'),
                    ]);

                    $body = json_decode($response->getBody()->getContents());
                    $totalValue = count($body);
                    if (!empty($body) && $totalValue > 0) {

                        $storeArray = [];
                        for ($i = 0; $i < $totalValue; $i++) {
                            $storeArray[$i]['profileId'] = $body[$i]->profileId;
                            $storeArray[$i]['countryCode'] = $body[$i]->countryCode;
                            $storeArray[$i]['currencyCode'] = $body[$i]->currencyCode;
                            $storeArray[$i]['timezone'] = $body[$i]->timezone;
                            $storeArray[$i]['marketplaceStringId'] = (isset($body[$i]->accountInfo->marketplaceStringId) ? $body[$i]->accountInfo->marketplaceStringId : 'NA');
                            $storeArray[$i]['entityId'] = (isset($body[$i]->accountInfo->id) ? $body[$i]->accountInfo->id : 'NA');
                            $storeArray[$i]['type'] = (isset($body[$i]->accountInfo->type) ? $body[$i]->accountInfo->type : 'NA');
                            $storeArray[$i]['name'] = (isset($body[$i]->accountInfo->name) ? $body[$i]->accountInfo->name : 'NA');
                            $storeArray[$i]['adGroupSpSixtyDays'] = 0; //0
                            $storeArray[$i]['aSINsSixtyDays'] = 0; //0
                            $storeArray[$i]['campaignSpSixtyDays'] = 0;
                            $storeArray[$i]['keywordSbSixtyDays'] = 0;
                            $storeArray[$i]['keywordSpSixtyDays'] = 0;
                            $storeArray[$i]['productAdsSixtyDays'] = 0;
                            $storeArray[$i]['productTargetingSixtyDays'] = 0;
                            $storeArray[$i]['SponsoredBrandCampaignsSixtyDays'] = 0;
                            $storeArray[$i]['SponsoredDisplayCampaignsSixtyDays'] = 0;
                            $storeArray[$i]['SponsoredDisplayAdgroupSixtyDays'] = 0;
                            $storeArray[$i]['SponsoredDisplayProductAdsSixtyDays'] = 0;
                            $storeArray[$i]['SponsoredBrandAdgroupSixtyDays'] = 0;
                            $storeArray[$i]['SponsoredBrandTargetingSixtyDays'] = 0;
                            $storeArray[$i]['creationDate'] = date('Y-m-d H:i:s');

                            $storeArray[$i]['isSandboxProfile'] = 0;
                            $storeArray[$i]['isActive'] = 1;
                            $storeArray[$i]['fkConfigId'] = $fkConfigId;

                        }

                        $AddProfileRecords = new AMSModel();
                        $AddProfileRecords->AddProfileRecords($storeArray,$fkConfigId);
                    } else {
                        // store status
                        AMSModel::insertTrackRecord('Profile Id', 'not record found');
                        Log::info("Response empty");
                    }

                } catch (\Exception $ex) {
                    $authCommandArray = array();
                    $authCommandArray['fkConfigId'] = $fkConfigId;
                    if ($ex->getCode() == 401) {
                        if (strstr($ex->getMessage(), '401 Unauthorized')) { // if auth token expire
                            Log::error('Refresh Access token. In file filePath:Commands\Ams\Profile');
                            \Artisan::call('getaccesstoken:amsauth', $authCommandArray);
                            //$this->amsProfileApiCall($singleAmsAuthToken);
                            goto a;
                        } elseif (strstr($ex->getMessage(), 'advertiser found for scope')) {
                            // store profile list not valid
                            Log::info("Invalid Profile Id: ");
                        }
                    } else if ($ex->getCode() == 429) { //https://advertising.amazon.com/API/docs/v2/guides/developer_notes#Rate-limiting
                        sleep(Config::get('constants.sleepTime'));
                        //$this->amsProfileApiCall($singleAmsAuthToken);
                        goto a;
                    }
                    // store status
                    AMSModel::insertTrackRecord('Profile Id', 'fail');
                    Log::error($ex->getMessage());
                }
            }
        }else {
            Log::info("AMS access token not found.");
        }
        Log::info("filePath:Commands\Ams\Profile. End Cron.");
    }

    private function amsProfileApiCall($singleAmsApiCreds){
        $fkConfigId = $singleAmsApiCreds->id;
        $obClientId = new AMSModel();
        //get updated auth token for every request.
        $getAMSTokenById = $obClientId->getAMSTokenById($fkConfigId);
        $access_token = $getAMSTokenById->access_token;
        $body = array();
        $apiClient_id = $singleAmsApiCreds->client_id;
      // Create a client with a base URI
        $url = Config::get('constants.amsApiUrl') . '/' . Config::get('constants.apiVersion') . '/' . Config::get('constants.amsProfileUrl');
        $client = new Client();
        $response = $client->request('GET', $url, [
            'headers' => [
                'Authorization' => 'Bearer ' . $access_token,
                'Content-Type' => 'application/json',
                'Amazon-Advertising-API-ClientId' => $apiClient_id],
            'delay' => Config::get('constants.delayTimeInApi'),
            'connect_timeout' => Config::get('constants.connectTimeOutInApi'),
            'timeout' => Config::get('constants.timeoutInApi'),
        ]);
        $body = json_decode($response->getBody()->getContents());
        $totalValue = count($body);
        if (!empty($body) && $totalValue > 0) {

            $storeArray = [];
            for ($i = 0; $i < $totalValue; $i++) {
                $storeArray[$i]['profileId'] = $body[$i]->profileId;
                $storeArray[$i]['countryCode'] = $body[$i]->countryCode;
                $storeArray[$i]['currencyCode'] = $body[$i]->currencyCode;
                $storeArray[$i]['timezone'] = $body[$i]->timezone;
                $storeArray[$i]['marketplaceStringId'] = (isset($body[$i]->accountInfo->marketplaceStringId) ? $body[$i]->accountInfo->marketplaceStringId : 'NA');
                $storeArray[$i]['entityId'] = (isset($body[$i]->accountInfo->id) ? $body[$i]->accountInfo->id : 'NA');
                $storeArray[$i]['type'] = (isset($body[$i]->accountInfo->type) ? $body[$i]->accountInfo->type : 'NA');
                $storeArray[$i]['name'] = (isset($body[$i]->accountInfo->name) ? $body[$i]->accountInfo->name : 'NA');
                $storeArray[$i]['adGroupSpSixtyDays'] = 0; //0
                $storeArray[$i]['aSINsSixtyDays'] = 0; //0
                $storeArray[$i]['campaignSpSixtyDays'] = 0;
                $storeArray[$i]['keywordSbSixtyDays'] = 0;
                $storeArray[$i]['keywordSpSixtyDays'] = 0;
                $storeArray[$i]['productAdsSixtyDays'] = 0;
                $storeArray[$i]['productTargetingSixtyDays'] = 0;
                $storeArray[$i]['SponsoredBrandCampaignsSixtyDays'] = 0;
                $storeArray[$i]['SponsoredDisplayCampaignsSixtyDays'] = 0;
                $storeArray[$i]['SponsoredDisplayAdgroupSixtyDays'] = 0;
                $storeArray[$i]['SponsoredDisplayProductAdsSixtyDays'] = 0;
                $storeArray[$i]['SponsoredBrandAdgroupSixtyDays'] = 0;
                $storeArray[$i]['SponsoredBrandTargetingSixtyDays'] = 0;
                $storeArray[$i]['creationDate'] = date('Y-m-d H:i:s');

                $storeArray[$i]['isSandboxProfile'] = 0;
                $storeArray[$i]['isActive'] = 1;
                $storeArray[$i]['fkConfigId'] = $fkConfigId;

            }

            $AddProfileRecords = new AMSModel();
            $AddProfileRecords->AddProfileRecords($storeArray,$fkConfigId);
        } else {
            // store status
            AMSModel::insertTrackRecord('Profile Id', 'not record found');
            Log::info("Response empty");
        }
    }
}
