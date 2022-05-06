<?php

//namespace App\Console\Commands;
namespace App\Libraries;

//namespace App\Console\Commands\Ams\AdGroup\SP;

use Config;
use Exception;
use Illuminate\Console\Command;
use App\Models\MWSModel;
use App\Libraries\mws\AmazonReport as AmazonReport;
use App\Libraries\mws\AmazonReportsCore;


class ReportHandler extends AmazonReport
{
    protected $reportdata;
    public function __construct($s, $id = null, $mock = false, $m = null)
    {
        parent::__construct($s, $mock, $m);
    }

    public function fetchReport()
    {
    }
    public function saveReport($path)
    {
    }
}

?>