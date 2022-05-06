<?php

namespace App\Console\Commands\AMS\Portfolio;

use Artisan;
use App\Models\AMSModel;
use App\Models\DayPartingModels\Portfolios;
use GuzzleHttp\Client;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Log;

class getPortfolioList extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'getPortfolioDetailData:portfolio';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'This command is used to get portfolio details';

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
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function handle()
    {
        Log::info("filePath:Commands\Ams\Portfolio. Start Cron.");
        Log::info($this->description);
        Log::info("Auth Access token get from DB Start!");
                $allProfileIdsObject = new AMSModel();
                $responseForProfile = getNotifyWhichEnvDataToUse(env('APP_ENV'));
                if ($responseForProfile == TRUE) {
                    $allProfileIds = $allProfileIdsObject->getAllProfiles();
                } elseif ($responseForProfile == FALSE) {
                    $allProfileIds = $allProfileIdsObject->getAllSandboxProfiles();
                }

                if (!empty($allProfileIds)) {

                    foreach ($allProfileIds as $single) {

                        $fkConfigId = $single->fkConfigId;
                        $clientId = $single->client_id;
                        $accessToken = $single->access_token;
                        $responseBody = [];
                        // Defined Url to get all portfolio against profiles
                        $apiUrl = getApiUrlForDiffEnv(env('APP_ENV'));
                        $url = $apiUrl . '/' . Config::get('constants.apiVersion') . '/' . Config::get('constants.amsPortfolioUrl');
                        Log::info('Url = ' . $url);
                        $client = new Client();
                        // Goto Statement used
                        b:
                        try {

                            $response = $client->request('GET', $url, [
                                'headers' => [
                                    'Authorization' => 'Bearer ' . $accessToken,
                                    'Content-Type' => 'application/json',
                                    'Amazon-Advertising-API-ClientId' => $clientId,
                                    'Amazon-Advertising-API-Scope' => $single->profileId
                                ],
                                'delay' => Config::get('constants.delayTimeInApi'),
                                'connect_timeout' => Config::get('constants.connectTimeOutInApi'),
                                'timeout' => Config::get('constants.timeoutInApi'),
                            ]);

                            $responseBody = json_decode($response->getBody()->getContents());

                            if (!empty($responseBody) && !is_null($responseBody)) {
                                $PortfolioDataArray = [];
                                $PortfolioDataInsert = [];
                                Log::info('Portfolio Record Found');
                                foreach ($responseBody as $singleResponseRecord) {
                                    $PortfolioDataArray['amount'] = 0;
                                    $PortfolioDataArray['currencyCode'] = 'NA';
                                    $PortfolioDataArray['policy'] = 'NA';
                                    $PortfolioDataArray['profileId'] = $single->profileId;
                                    $PortfolioDataArray['fkProfileId'] = $single->id;
                                    $PortfolioDataArray['portfolioId'] = $singleResponseRecord->portfolioId;
                                    $PortfolioDataArray['name'] = $singleResponseRecord->name;
                                    if (isset($singleResponseRecord->budget)) {
                                        $PortfolioDataArray['amount'] = $singleResponseRecord->budget->amount;
                                        $PortfolioDataArray['currencyCode'] = $singleResponseRecord->budget->currencyCode;
                                        $PortfolioDataArray['policy'] = $singleResponseRecord->budget->policy;
                                    }

                                    $PortfolioDataArray['inBudget'] = $singleResponseRecord->inBudget;
                                    $PortfolioDataArray['state'] = $singleResponseRecord->state;
                                    $PortfolioDataArray['created_at'] = date('Y-m-d H:i:s');
                                    $PortfolioDataArray['updated_at'] = date('Y-m-d H:i:s');
                                    array_push($PortfolioDataInsert, $PortfolioDataArray);
                                } // End Foreach Loop for making insertion data of portfolis

                                if (!empty($PortfolioDataArray)){
                                    Portfolios::insertPortfolioList($PortfolioDataInsert);
                                    unset($PortfolioDataInsert);
                                    unset($PortfolioDataArray);
                                    Log::info('Portfolio inserted against Profile Id : ' . $single->profileId);
                                }

                            } else {
                                // Portfolioss status
                                Log::error('report name : Get Portfolios Against' . ' profile id: ' . $single->profileId . 'not record found . portfolio details');
                                AMSModel::insertTrackRecord('report name : Get Portfolios Against' . ' profile id: ' . $single->profileId, 'not record found');
                            }
                        } catch (\Exception $ex) {
                            if ($ex->getCode() == 401) {
                                Log::error('Refresh Access token. In file filePath:Commands\Ams\Portfolio\getPortfolioDetailData');
                                $authCommandArray = array();
                                $authCommandArray['fkConfigId'] = $fkConfigId;
                                \Artisan::call('getaccesstoken:amsauth', $authCommandArray);
                                $obaccess_token = new AMSModel();
                                $getAMSTokenById = $obaccess_token->getAMSTokenById($fkConfigId);
                                $accessToken = $getAMSTokenById->access_token;
                                goto b;
                            } else if ($ex->getCode() == 429) { //https://advertising.amazon.com/API/docs/v2/guides/developer_notes#Rate-limiting
                                sleep(Config::get('constants.sleepTime') + 2);
                                goto b;
                            } else if ($ex->getCode() == 502) {
                                sleep(Config::get('constants.sleepTime') + 2);
                                goto b;
                            }
                            // store report status
                            AMSModel::insertTrackRecord('Profile List Id : Get Portfolios', 'fail');
                            Log::error($ex->getMessage());
                        }// end catch

                    } // End Foreach Loop
                } else {
                    Log::info("Profile List not found.");
                }

        Log::info("filePath:Commands\Ams\Portfolio. End Cron.");
    }
}
