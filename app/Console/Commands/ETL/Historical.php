<?php

namespace App\Console\Commands\ETL;

use Illuminate\Console\Command;

class Historical extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'historical:etl {startDate} {endDate} {sec}';

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
        $DB1 = 'mysql'; // layer 0 database
        $DB2 = 'mysqlDb2'; // layer 1 BI database
        $differenceFormat = '%R%a';
        $startDate = date("Y-m-d", strtotime($this->argument('startDate')));
        $endDate = date("Y-m-d", strtotime($this->argument('endDate')));
        $delay = $this->argument('sec');
        echo $startDate . PHP_EOL . $endDate;
        $date1 = date_create($startDate);
        $date2 = date_create($endDate);
        $diff = date_diff($date1, $date2);
        $numberOFDays = $diff->format($differenceFormat);
        if ($numberOFDays <= 0) {
            echo 'value is less the 0 and equal to 0';
            echo PHP_EOL;
            echo $numberOFDays;
        } else {
            echo PHP_EOL;
            echo 'value is greater 1';
            for ($i = 0; $i <= $numberOFDays; $i++) {
                $singleDate = date('Ymd', strtotime($startDate . $i . ' day'));
                echo PHP_EOL;
                echo $i;
                echo PHP_EOL;
                echo $singleDate;
                \DB::connection($DB1)->statement('CALL spMasterRTL(?)', array($singleDate));
                \DB::connection($DB2)->statement('CALL spMasterETL(?)', array($singleDate));
                \DB::connection($DB2)->statement('CALL spMasterPresentationLayer(?)', array($singleDate));
                \DB::connection($DB2)->statement('CALL spMasterDataValidation(?)', array($singleDate));
                \DB::disconnect($DB1); // end connection
                \DB::disconnect($DB2); // end connection
                sleep($delay);
            }
        }
    }
}
