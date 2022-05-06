<?php

namespace App\Console\Commands\ETL;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class Layer extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'layer:etl';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'This command is used update data in DWH daily base. Current Day - 2 days';

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
        Log::info('Start ETL here');
        $DB1 = 'mysql'; // layer 0 database
        $DB2 = 'mysqlDb2'; // layer 1 BI database
        // $ETL_date = '20191216'; // e.g 20201501
        $ETL_date = date('Ymd', strtotime('-2 day', time()));
        Log::info('Master layer ETL date:' . $ETL_date);
        \DB::connection($DB1)->statement('CALL spMasterRTL(?)', array($ETL_date));
        \DB::connection($DB2)->statement('CALL spMasterETL(?)', array($ETL_date));
        \DB::connection($DB2)->statement('CALL spMasterPresentationLayer(?)', array($ETL_date));
        \DB::connection($DB2)->statement('CALL spMasterDataValidation(?)', array($ETL_date));
        Log::info('End Master layer ETL here');
        \DB::disconnect($DB1); // end connection
        \DB::disconnect($DB2); // end connection
    }
}
