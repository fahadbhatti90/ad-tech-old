<?php

namespace App\Console\Commands;

use App\Models\BuyBoxModel;
use Illuminate\Console\Command;
use Symfony\Component\Console\Output\ConsoleOutput;
use Symfony\Component\Process\Process;

class BuyBoxThreads extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'buyboxthread:buybox {argument*}';

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
        $ArrayArgument = $this->argument('argument');
        $collectionName = $ArrayArgument[0];
        $object = new BuyBoxModel();
        $AsinCollection = $object->getAsinBatch();
        $numberThreads = 25;
        $offset = 0;
        $pool = new \Graze\ParallelProcess\PriorityPool();
        if (!$AsinCollection->isEmpty()) {
            $totalASIN = count($AsinCollection);
            if ($totalASIN > $numberThreads) {
                $ASINPerThread = round($totalASIN / $numberThreads);
                $limit = $ASINPerThread;
                for ($i = 0; $i < $numberThreads; $i++) {
                    $pool->add(new Process(sprintf('php artisan asinscraper:buybox ' . $collectionName . ' ' . $offset . ' ' . $limit)));
                    $offset = $offset + $limit;
                }
            } else {
                $limit = 1;
                for ($i = 0; $i < $totalASIN; $i++) {
                    $pool->add(new Process(sprintf('php artisan asinscraper:buybox ' . $collectionName . ' ' . $offset . ' ' . $limit)));
                    $offset = $offset + $limit;
                }
            }
            $output = new \Symfony\Component\Console\Output\ConsoleOutput();
            $lines = new \Graze\ParallelProcess\Display\Lines($output, $pool);
            $lines->run();
        } else {
            echo 'it empty' . PHP_EOL;
        }
    }
}
