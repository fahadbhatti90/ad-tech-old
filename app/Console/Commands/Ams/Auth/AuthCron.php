<?php

namespace App\Console\Commands\Ams\Auth;

use Artisan;
use DB;
use App\Models\AMSModel;
use GuzzleHttp\Client;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Log;

class AuthCron extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'getaccesstoken:amsauth {fkConfigId*}';
    //protected $signature = 'getaccesstoken:amsauth';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'This command is used to get and update Auth Access Token';

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
        Log::info("filePath:Commands\Ams\Auth\AuthCron. Start Cron.");
        Log::info($this->description);
        $fkConfigId = $this->argument('fkConfigId');
        //$fkConfigId = 4;
        $APIParametr = new AMSModel();
        $addAMSToken = new AMSModel();
        $data = array();
        $data = $APIParametr->getParameterById($fkConfigId);
        // check AMS client ID and Client secret key Founded or not
        if (isset($data) && !empty($data)) {
            $client = new Client();
            $body = array();
            //Create a client with a base URI
            $url = Config::get('constants.amsAuthUrl');
            $post_data = ['grant_type' => $data->grant_type,
                'refresh_token' => $data->refresh_token,
                'client_id' => $data->client_id,
                'client_secret' => $data->client_secret];
            try {
                // Get Response CURL call
                $response = $client->request('POST', $url, [
                    'headers' => ['Content-Type' => 'application/x-www-form-urlencoded'],
                    'form_params' => $post_data,
                    'delay' => Config::get('constants.delayTimeInApi'),
                    'connect_timeout' => Config::get('constants.connectTimeOutInApi'),
                    'timeout' => Config::get('constants.timeoutInApi'),
                ]);
                $body = json_decode($response->getBody()->getContents());
                if (!empty($body)) {
                    // store track data
                    AMSModel::insertTrackRecord('Authentication Data', 'record found');
                    Log::info("start insertion query filePath:Commands\Ams\Auth\AuthCron.");
                    $storeData = array();
                    $storeData['fkConfigId'] = $fkConfigId;
                    $storeData['client_id'] = $data->client_id;
                    $storeData['access_token'] = $body->access_token;
                    $storeData['refresh_token'] = $body->refresh_token;
                    $storeData['token_type'] = $body->token_type;
                    $storeData['expires_in'] = $body->expires_in;
                    $addAMSToken->addAMSToken($storeData, $fkConfigId);
                    Log::info("end insertion query back filePath:Commands\Ams\Auth\AuthCron.");
                } else {
                    // store track data
                    AMSModel::insertTrackRecord('Authentication Data', 'not record found');
                    Log::info("Empty Response body.");
                }
                Log::info("AMS Auth Token Updated!");
                Log::info("AMS Auth Cron Command Run successfully!");
            } catch (\Exception $ex) {
                // store report status
                AMSModel::insertTrackRecord('AuthCron Token', 'fail');
                Log::error($ex->getMessage());
            }
        } else {
            Log::info("AMS client id and secret key not found.");
        }
        Log::info("filePath:Commands\Ams\Auth\AuthCron. End Cron.");
    }
}
