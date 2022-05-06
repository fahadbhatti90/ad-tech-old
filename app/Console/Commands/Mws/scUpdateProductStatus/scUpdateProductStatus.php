<?php

//namespace App\Console\Commands;

//use Illuminate\Console\Command;
namespace App\Console\Commands\Mws\scUpdateProductStatus;

use App\Models\MWSModel;
use DateTime;
use Illuminate\Console\Command;
use Artisan;
use Illuminate\Support\Facades\Log;

class scUpdateProductStatus extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'scUpdateProductStatus:cron';

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

        $requestReportTime = date('H:i');
        $requestReportTimecheck = DateTime::createFromFormat('H:i', $requestReportTime);
        //$day_end_check_from = DateTime::createFromFormat('H:i', '00:02');
        $day_end_check_from = DateTime::createFromFormat('H:i', '00:02');
        
        if ($requestReportTimecheck == $day_end_check_from) {
        //$day_end_check_to=DateTime::createFromFormat('H:i', '22:45');
        Log::info("filePath:app\Console\Commands\Mws\scUpdateProductStatus\scUpdateProductStatus.Cron : scUpdateProductStatus. Start Cron.");
        MWSModel::insert_mws_Activity('Start Cron.', 'Cron : scUpdateProductStatus', 'app\Console\Commands\Mws\scUpdateProductStatus\scUpdateProductStatus.php');
        scSetMemoryLimitAndExeTime();
        //reset all products details downloaded status to 0 in table "tbl_sc_product_ids"
        MWSModel::resetAllProductDetailsDownloadedStatus();
        //reset all products details in Queue status to 0 in table "tbl_sc_product_ids"
        MWSModel::resetAllProductDetailsInQueueStatus();
        //reset all Product Sales Rank Coppied status to 0 in table "tbl_sc_product_ids"
        MWSModel::resetAllProductSalesRankCoppiedStatus();
        //reset all Product Details "isActive" column status to 0 in table "tbl_sc_product_details"
        MWSModel::resetAllProductDetailsIsActiveStatus();
        //reset all Product Sales Rank "isActive" column status to 0 in table "tbl_sc_sales_rank"
        MWSModel::resetAllProductSalesRankIsActiveStatus();
        //reset all Product Processed Sales Rank "isActive" column status to 0 in table "tbl_sc_processed_sales_rank"
        MWSModel::resetAllProductProcessedSalesRankIsActiveStatus();

        Log::info("filePath:app\Console\Commands\Mws\scUpdateProductStatus\scUpdateProductStatus.Cron : scUpdateProductStatus. End Cron.");
        MWSModel::insert_mws_Activity('End Cron.', 'Cron : scUpdateProductStatus', 'app\Console\Commands\Mws\scUpdateProductStatus\scUpdateProductStatus.php');
    }

    }
}
