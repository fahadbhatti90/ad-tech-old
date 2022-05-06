<?php


namespace App\Console\Commands\Mws\runGetReportCron;
use App\Models\MWSModel;
use DateTime;
use Illuminate\Console\Command;
use Artisan;

class runGetReportCron extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'runGetReportCron:cron';

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



        $current_date=date('Y-m-d');
        $current_time=date("H:i", time());
        $to_time=date("H:i", strtotime("+2 minutes"));
        $from_tim=date("H:i", strtotime("-2 minutes"));
        $Crons = new MWSModel();
        $active_crons = $Crons->get_crons_to_run();
        if ($active_crons) {
            foreach ($active_crons as $value) {
                if ($current_date != $value->getReportLastRun){
                    $cron_time = DateTime::createFromFormat('H:i', $value->getReportTime);
                    $start = DateTime::createFromFormat('H:i', $from_tim);
                    $end = DateTime::createFromFormat('H:i', $to_time);
                    if ($cron_time >= $start && $cron_time <= $end)
                    {
                        echo 'run';
                        if ($value->report_type=='Catalog'){

                            $data['getReportCompletedTime']=date('Y-m-d H:i:s');
                            $data['getReportLastRun']=$current_date;
                            $data['isCronRunning']=0;
                            $updateCronLastRunDate = MWSModel::updateCronLastRunDate($data,$value->task_id);

                            Artisan::call('CatalogScCatActivereport:cron');
                            Artisan::call('ScCatInactivereport:cron');
                            Artisan::call('CatalogScFbaHealth:cron');


                            $count_active_crons = MWSModel::count_active_crons();
                            $count_crons_run_today = MWSModel::count_crons_run_today();
                            $count_total_crons = MWSModel::count_total_crons();
                            if ($count_active_crons==$count_crons_run_today && $count_total_crons>0){
                            //$updateCustomerHistoricalDataStatus = MWSModel::update_customer_historical_data_status();
                               $updateCustomerHistoricalDataStatus =  scUpdateHistoricalDataStatus();
                            }
                            /*get product ids(asins) from reports*/
                            Artisan::call('scGetCatalogProductsIds:cron');
                            if ($count_active_crons==$count_crons_run_today && $count_total_crons>0){

                                Artisan::call('scGetProductDetails:cron');
                                Artisan::call('scGetProductCategoryDetails:cron');
                            }

                        }elseif($value->report_type=='Inventory'){

                            $data['getReportCompletedTime']=date('Y-m-d H:i:s');
                            $data['getReportLastRun']=$current_date;
                            $data['isCronRunning']=0;
                            $updateCronLastRunDate = MWSModel::updateCronLastRunDate($data,$value->task_id);

                            Artisan::call('ScFbaReceipt:cron');
                            Artisan::call('ScCatActivereport:cron');
                            Artisan::call('ScFbaHealth:cron');

                            $count_active_crons = MWSModel::count_active_crons();
                            $count_crons_run_today = MWSModel::count_crons_run_today();
                            $count_total_crons = MWSModel::count_total_crons();
                            if ($count_active_crons==$count_crons_run_today && $count_total_crons>0){
                                //$updateCustomerHistoricalDataStatus = MWSModel::update_customer_historical_data_status();
                                $updateCustomerHistoricalDataStatus =  scUpdateHistoricalDataStatus();
                            }
                            Artisan::call('scGetInventoryProductsIds:cron');
                            if ($count_active_crons==$count_crons_run_today && $count_total_crons>0){

                                Artisan::call('scGetProductDetails:cron');
                                Artisan::call('scGetProductCategoryDetails:cron');
                            }
                            // after insertion track and add log event on inventory report of seller central
                            \Artisan::call('eventTrackingCron:OOS sc');
                        }elseif($value->report_type=='Sales'){

                            $data['getReportCompletedTime']=date('Y-m-d H:i:s');
                            $data['getReportLastRun']=$current_date;
                            $data['isCronRunning']=0;
                            $updateCronLastRunDate = MWSModel::updateCronLastRunDate($data,$value->task_id);

                            Artisan::call('ScFbaReturns:cron');
                            Artisan::call('ScMfnReturns:cron');
                            Artisan::call('ScOrdersUpdt:cron');
                            Artisan::call('ScOrders:cron');

                            $count_active_crons = MWSModel::count_active_crons();
                            $count_crons_run_today = MWSModel::count_crons_run_today();
                            $count_total_crons = MWSModel::count_total_crons();
                            if ($count_active_crons==$count_crons_run_today && $count_total_crons>0){
                                //$updateCustomerHistoricalDataStatus = MWSModel::update_customer_historical_data_status();
                                $updateCustomerHistoricalDataStatus =  scUpdateHistoricalDataStatus();
                            }
                            Artisan::call('scGetSalesProductsIds:cron');
                            if ($count_active_crons==$count_crons_run_today && $count_total_crons>0){
                                Artisan::call('scGetProductDetails:cron');
                                Artisan::call('scGetProductCategoryDetails:cron');
                            }
                        }

                    }else{
                        echo 'not run';
                        echo '<br>';
                    }
                }else{

                    echo 'already run';

                    echo '<br>';
                }
                echo '<br>';

            }
        }
    }
}
