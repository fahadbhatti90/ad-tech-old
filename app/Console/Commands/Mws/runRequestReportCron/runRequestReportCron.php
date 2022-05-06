<?php

namespace App\Console\Commands\Mws\runRequestReportCron;

use DateTime;
use Illuminate\Console\Command;
use App\Models\MWSModel;
use Artisan;

class runRequestReportCron extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'runRequestReportCron:cron';

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
        $active_crons = $Crons->get_active_crons();

        if ($active_crons) {

            foreach ($active_crons as $value) {

                if ($current_date != $value->requestReportLastRun){

                    $cron_time = DateTime::createFromFormat('H:i', $value->requestReportTime);
                    $start = DateTime::createFromFormat('H:i', $from_tim);
                    $end = DateTime::createFromFormat('H:i', $to_time);
                    if ($cron_time >= $start && $cron_time <= $end)
                    {
                        echo 'run';
                        if ($value->report_type=='Catalog'){
                            $catalogCrondata=array();
                            $catalogCrondata['isCronRunning']=1;
                            $catalogCrondata['requestReportLastRun']=$current_date;
                            $updateCatalogCronLastRunDate = MWSModel::updateCronLastRunDate($catalogCrondata,$value->task_id);
                            //catalog cron caommand
                            Artisan::call('catalogReportsRequest:cron');

                            $CatalogCronCompletedTime=array();
                            $CatalogCronCompletedTime['requestReportCompletedTime'] = date('Y-m-d H:i:s');
                            $updateCatalogCronCompletedTime = MWSModel::updateCronLastRunDate($CatalogCronCompletedTime,$value->task_id);

                        }elseif($value->report_type=='Inventory'){

                            $inventoryCrondata=array();
                            $inventoryCrondata['isCronRunning']=1;
                            $inventoryCrondata['requestReportLastRun']=$current_date;
                            //catalog cron caommand
                            $updateInventoryCronLastRunDate = MWSModel::updateCronLastRunDate($inventoryCrondata,$value->task_id);

                            Artisan::call('inventoryReportsRequest:cron');

                            $inventoryCronCompletedTime=array();
                            $inventoryCronCompletedTime['requestReportCompletedTime'] = date('Y-m-d H:i:s');
                            $updateInventoryCronCompletedTime = MWSModel::updateCronLastRunDate($inventoryCronCompletedTime,$value->task_id);
                        }elseif($value->report_type=='Sales'){

                            $salesCrondata=array();
                            $salesCrondata['isCronRunning']=1;
                            $salesCrondata['requestReportLastRun']=$current_date;

                            $updateSalesCronLastRunDate = MWSModel::updateCronLastRunDate($salesCrondata,$value->task_id);

                            Artisan::call('salesReportsRequest:cron');

                            $salesCronCompletedTime=array();
                            $salesCronCompletedTime['requestReportCompletedTime'] = date('Y-m-d H:i:s');
                            $salesCronCompletedTime['requestReportCompletedTime'] = date('Y-m-d H:i:s');
                            $updateCronLastRunDate = MWSModel::updateCronLastRunDate($salesCronCompletedTime,$value->task_id);
                        }
                    }else{
                        echo 'not run';
                        echo '<br>';
                    }
                }else{
                    echo 'already run';
                }


            }
        }

    }
}
