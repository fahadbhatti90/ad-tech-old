<?php

namespace App\Console\Commands\ProductSegments;

use Illuminate\Console\Command;
use App\Models\ProductSegments\AsinSegments;
use App\Models\ProductSegments\ProductSegments;
use App\Models\ProductPreviewModels\GraphDataModels\AsinDailyModels;
use App\Models\ProductPreviewModels\GraphDataModels\ViewProductSegmentModel;

class PopulateSegmentViewCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'refreshProductTableView:refresh';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'This Command will execute the query and populate the view porduct segment';

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
        ViewProductSegmentModel::truncate();
        $ViewProductSegmentTN = ViewProductSegmentModel::getCompleteTableName();
        $vpsmTN = ViewProductSegmentModel::getCompleteTableName();
        $tas = AsinSegments::getCompleteTableName();
        $ps = ProductSegments::getCompleteTablename();
        $masterTN = AsinDailyModels::getCompleteTableName();
        \DB::select("
        INSERT INTO $ViewProductSegmentTN 
            SELECT tas.ASIN AS segmentASIN, 
                GROUP_CONCAT(ps.id) AS segmentId, 
                GROUP_CONCAT(ps.segmentName) AS segmentName
            FROM $tas tas
            LEFT JOIN $ps ps
                ON tas.fkSegmentId = ps.id
            GROUP BY tas.ASIN
        ");
    }
}
