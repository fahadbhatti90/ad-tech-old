<?php

namespace App\Console\Commands\VCScrapCommands;

use App\Http\Controllers\VCScrapController;
use Illuminate\Console\Command;

class ScrapCatalog extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'ScrapCatalog:scrap';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'This command is use to scrap vendor central product catalog table';

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
        $productCatalog = new VCScrapController();
        $productCatalog->scrapCatalogStore();
    }
}
