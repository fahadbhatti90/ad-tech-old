<?php

//namespace App\Console\Commands;
namespace App\Console\Commands\Mws\ScCronRun;

use DateTime;
use Illuminate\Console\Command;

use App\Models\MWSModel;
use Artisan;


class mwsCronRun extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'mwsCronRun:cron';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

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
        Artisan::call('runRequestReportCron:cron');
        Artisan::call('runReportRequestListCron:cron');
        //Artisan::call('runReportListCron:cron');
        Artisan::call('runGetReportCron:cron');
        /*Artisan::call('runGetAsinsFromReportsCron:cron');
        Artisan::call('runGetProductDetailsCron:cron');
        Artisan::call('runGetProductCategoriesDetailsCron:cron');*/
    }
}
