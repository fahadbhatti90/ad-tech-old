<?php

namespace App\Console\Commands\RTL;

use Illuminate\Console\Command;

class AmsKeywordReport extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'keywordRTL:reportData';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'This command is used to populate keyword data in RTL table at layer 0 using SProc.';

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
        $Date= date('Ymd', strtotime('-1 day', time()));
        \DB::connection($DB1)->statement('CALL spRTLAMSKeyword(?)', array($Date));
        \DB::disconnect($DB1); // end connection
    }
}
