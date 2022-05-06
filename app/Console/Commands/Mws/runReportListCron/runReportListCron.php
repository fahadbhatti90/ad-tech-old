<?php


namespace App\Console\Commands\Mws\runReportListCron;

use App\Models\MWSModel;
use DateTime;
use Illuminate\Console\Command;
use Artisan;

class runReportListCron extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'runReportListCron:cron';

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
                if ($current_date != $value->reportListLastRun){
                    $cron_time = DateTime::createFromFormat('H:i', $value->reportLISTTime);
                    $start = DateTime::createFromFormat('H:i', $from_tim);
                    $end = DateTime::createFromFormat('H:i', $to_time);
                    if ($cron_time >= $start && $cron_time <= $end)
                    {
                        echo 'run';
                        if ($value->report_type=='Catalog'){
                            Artisan::call('mwsreportlist:cron');
                            $data['reportListLastRun']=$current_date;
                            $data['reportLISTRunTime'] = date('Y-m-d H:i:s');
                            $updateCronLastRunDate = MWSModel::updateCronLastRunDate($data,$value->task_id);
                        }elseif($value->report_type=='Inventory'){
                            Artisan::call('mwsreportlist:cron');
                            $data['reportLISTRunTime'] = date('Y-m-d H:i:s');
                            $data['reportListLastRun']=$current_date;
                            $updateCronLastRunDate = MWSModel::updateCronLastRunDate($data,$value->task_id);
                        }elseif($value->report_type=='Sales'){
                            Artisan::call('mwsreportlist:cron');
                            $data['reportLISTRunTime'] = date('Y-m-d H:i:s');
                            $data['reportListLastRun']=$current_date;
                            $updateCronLastRunDate = MWSModel::updateCronLastRunDate($data,$value->task_id);
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
