<?php

namespace App\Console\Commands\BuyBox;

use Illuminate\Console\Command;
use App\Models\BuyBoxModels\BuyBoxTempUrlsModel;
use App\Models\BuyBoxModels\BuyBoxActivityTrackerModel;

class BuyBox5MinutCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'updateBuyBoxStatus:update';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Updates the -5 or 503 errors of buy box';

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
        $asinUpdated = BuyBoxTempUrlsModel::where("scrapStatus","-5")
        ->update(["scrapStatus"=>"0"]);
        BuyBoxActivityTrackerModel::setActivity( 
            "BuyBox -5 updated => ".json_encode($asinUpdated),
            "Commands",
            "BuyBox5MinutCommand:rest",
            " App\Console\Commands\Buybox",
            date('Y-m-d H:i:s')
        );

    }
}
