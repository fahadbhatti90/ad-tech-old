<?php

namespace App\Console\Commands\Ams\Profile;

use Artisan;
use App\Models\AMSModel;
use GuzzleHttp\Client;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Log;

class SandboxProfileCron extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'getprofileid:amssandboxprofile';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'This command is used to get all sand box profile list';

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
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        Log::info("filePath:Commands\Ams\Profile. Start Cron.");
        Log::info($this->description);
        $obaccess_token = new AMSModel();
        Log::info("AMS get all ams api credentials from DB Start!");
        $getAllAmsApiCreds = $obaccess_token->getAllAmsApiCreds();
        if ($getAllAmsApiCreds != FALSE) {
            foreach ($getAllAmsApiCreds as $singleAmsApiCreds) {
                $fkConfigId = $singleAmsApiCreds->id;
                $apiClient_id = $singleAmsApiCreds->client_id;
                $accessToken = $singleAmsApiCreds->access_token;
                // Create a client with a base URI
                a:
                $url = Config::get('constants.testingAmsApiUrl') . '/' . Config::get('constants.apiVersion') . '/' . Config::get('constants.amsProfileUrl');
                try {
                    $client = new Client();
                    $response = $client->request('GET', $url, [
                        'headers' => [
                            'Authorization' => 'Bearer ' . $accessToken,
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
                            $storeArray[$i]['fkConfigId'] = $fkConfigId;
                            $storeArray[$i]['countryCode'] = $body[$i]->countryCode;
                            $storeArray[$i]['currencyCode'] = $body[$i]->currencyCode;
                            $storeArray[$i]['timezone'] = $body[$i]->timezone;

                            if (isset($body[$i]->accountInfo->marketplaceStringId)) {
                                $storeArray[$i]['marketplaceStringId'] = $body[$i]->accountInfo->marketplaceStringId;
                                $storeArray[$i]['entityId'] = $body[$i]->accountInfo->id;
                                $storeArray[$i]['type'] = $body[$i]->accountInfo->type;
                                $storeArray[$i]['name'] = (isset($body[$i]->accountInfo->name) ? $body[$i]->accountInfo->name : 'NA');
                                $storeArray[$i]['adGroupSpSixtyDays'] = 1;
                                $storeArray[$i]['aSINsSixtyDays'] = 1;
                                $storeArray[$i]['campaignSpSixtyDays'] = 1;
                                $storeArray[$i]['keywordSbSixtyDays'] = 1;
                                $storeArray[$i]['keywordSpSixtyDays'] = 1;
                                $storeArray[$i]['productAdsSixtyDays'] = 1;
                                $storeArray[$i]['productTargetingSixtyDays'] = 1;
                                $storeArray[$i]['SponsoredBrandCampaignsSixtyDays'] = 1;
                                $storeArray[$i]['SponsoredDisplayCampaignsSixtyDays'] = 1;
                                $storeArray[$i]['SponsoredDisplayAdgroupSixtyDays'] = 1;
                                $storeArray[$i]['SponsoredDisplayProductAdsSixtyDays'] = 1;
                                $storeArray[$i]['SponsoredBrandAdgroupSixtyDays'] = 0;
                                $storeArray[$i]['SponsoredBrandTargetingSixtyDays'] = 0;
                                $storeArray[$i]['creationDate'] = date('Y-m-d H:i:s');
                            }
                            $storeArray[$i]['isActive'] = 0;
                            $storeArray[$i]['isSandboxProfile'] = 1;
                        }
                        AMSModel::addSandboxProfiles($storeArray);

                    } else {
                        // store status
                        AMSModel::insertTrackRecord('Profile Id', 'not record found');
                        Log::info("Response empty");
                    }
                } catch (\Exception $ex) {
                    if ($ex->getCode() == 401) {
                        Log::error('Refresh Access token. In file filePath:Commands\Ams\Profile');
                        $authCommandArray = array();
                        $authCommandArray['fkConfigId'] = $fkConfigId;
                        \Artisan::call('getaccesstoken:amsauth', $authCommandArray);
                        $getAMSTokenById = $obaccess_token->getAMSTokenById($fkConfigId);
                        $accessToken = $getAMSTokenById->access_token;
                        goto a;
                    } else if ($ex->getCode() == 429) { //https://advertising.amazon.com/API/docs/v2/guides/developer_notes#Rate-limiting
                        sleep(Config::get('constants.sleepTime'));
                        goto a;
                    }
                    // store status
                    AMSModel::insertTrackRecord('Profile Id', 'fail');
                    Log::error($ex->getMessage());
                }
            }
        } else {
            Log::info("AMS api credentials not found.");
        }
        Log::info("filePath:Commands\Ams\Profile. End Cron.");
    }
}
