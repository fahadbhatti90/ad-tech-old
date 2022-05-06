<?php

namespace App\Console\Commands\Ams;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class retain60DaysDataTracker extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'retain:amsTracker';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'This command is used to delete more then 60 dyas data from database.';

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
        Log::info('Retain Data only 60 days into Database. Ams tracker');
        $date = date('Y-m-d', strtotime('-60 day', time()));
        Log::info('Delete Date:' . $date);
        $data = \DB::connection($DB1)
            ->table('tbl_ams_tracker')
            ->where('dated', 'like', $date . ' %')
            ->get();
        if ($data->isNotEmpty()) {
            Log::info('Data Found and date: '.$date);
            \DB::connection($DB1)
                ->table('tbl_ams_tracker')
                ->where('dated', 'like', $date . ' %')
                ->delete();
        }else{
            Log::info('Not Found Data and date: '.$date);
        }
        \DB::disconnect($DB1); // end connection
        Log::info('End Delete Command');
    }
}
