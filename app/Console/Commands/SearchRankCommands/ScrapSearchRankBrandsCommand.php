<?php

namespace App\Console\Commands\SearchRankCommands;

use Illuminate\Console\Command;
use App\Http\Controllers\SearchRankScrapingController;

class ScrapSearchRankBrandsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'ScrapSearchRankBrandsCommand:brands {st_id}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Scraps the Missing Brands of Each Search Term';

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
        $arguments = $this->argument("st_id");
        $st_id =  $arguments;
        $srController = new SearchRankScrapingController();
        $srController->getBrands($st_id);
    }
}
