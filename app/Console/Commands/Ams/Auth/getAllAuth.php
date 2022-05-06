<?php

namespace App\Console\Commands\Ams\Auth;

use App\Models\AMSModel;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class getAllAuth extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'getallaccesstoken:amsauth';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'This command is used to get and update Auth Access Token For All Api Credentials';

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
        Log::info("filePath:Commands\Ams\Auth\getAllAuth. Start Cron.");
        Log::info($this->description);
        $obaccess_token = new AMSModel();
        Log::info("AMS Auth token get from DB Start!");
        $getAllAmsApiCreds = $obaccess_token->getAllAmsParameters();
        if ($getAllAmsApiCreds != FALSE) {
            foreach ($getAllAmsApiCreds as $single) {
                $fkConfigId = $single->id;
                $authCommandArray = array();
                $authCommandArray['fkConfigId'] = $fkConfigId;
                \Artisan::call('getaccesstoken:amsauth', $authCommandArray);
            }
        }
        else {
        Log::info("AMS client id and secret key not found.");
        }
        Log::info("filePath:Commands\Ams\Auth\getAllAuth. End Cron.");
    }
}
