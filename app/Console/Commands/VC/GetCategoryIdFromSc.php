<?php

namespace App\Console\Commands\VC;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use App\Models\VCModel;
use Illuminate\Support\Facades\Log;

class GetCategoryIdFromSc extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'get:categoryIdFromSc';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Get Category Id From Seller Central Table';

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
        $getAllCronToRun = VCModel::getAllCron();
        if (!empty($getAllCronToRun)) {
            foreach ($getAllCronToRun as $singleRun) {
                $cronJobList = [];
                switch ($singleRun->moduleName) {
                    case "daily_sales":
                        Log::info('Start Daily Sales Cron');
                        echo 'Get Distinct Category' . PHP_EOL;
                        $distinctCategory = VCModel::getDistinctDailySalesCategory();
                        if (!empty($distinctCategory)) {
                            echo 'Distinct Category Found' . PHP_EOL;
                            foreach ($distinctCategory as $cat) {
                                $getCatId = VCModel::getCategoryId($cat->strCategory);
                                if (!empty($getCatId)) {
                                    echo 'Category Id Found = ' . $getCatId;
                                    $updateRecord = VCModel::updateDailySalesCategoryId($getCatId, $cat->strCategory);
                                    echo 'Record Updated';
                                    if (!empty($updateRecord)) {
                                        $cronJobList = $this->successCronJobData($singleRun->moduleName);

                                    } else {
                                        $cronJobList = $this->failedCronJobData($singleRun->moduleName);
                                    }
                                    VCModel::cronInsert($cronJobList);
                                } else {
                                    echo 'Category Id Not Found' . PHP_EOL;
                                }

                            }
                        } else {
                            echo 'Distinct Category Not Found' . PHP_EOL;
                            Log::info('Distinct Category Not Found');
                        }

                        Log::info('End Daily Sales Cron ');
                        break;
                    case "daily_inventory":
                        Log::info('Start Daily Inventory Cron');
                        $distinctCategory = VCModel::getDistinctDailyInventoryCategory();
                        if (!empty($distinctCategory)) {
                            echo 'If Not Empty Category' . PHP_EOL;
                            foreach ($distinctCategory as $cat) {
                                $getCatId = VCModel::getCategoryId($cat->strCategory);
                                if (!empty($getCatId)) {
                                    echo 'Category Id Found = ' . $getCatId;
                                    $updateRecord = VCModel::updateDailyInventoryCategoryId($getCatId, $cat->strCategory);
                                    if (!empty($updateRecord)) {
                                        $cronJobList = $this->successCronJobData($singleRun->moduleName);

                                    } else {
                                        $cronJobList = $this->failedCronJobData($singleRun->moduleName);
                                    }
                                    VCModel::cronInsert($cronJobList);
                                    echo 'Record Updated';
                                } else {
                                    echo 'Category Id Not Found' . PHP_EOL;
                                }

                            }
                        } else {
                            echo 'Distinct Category Not Found' . PHP_EOL;
                            Log::info('Distinct Category Not Found');
                        }

                        Log::info('End Daily Inventory Cron ');
                        break;
                    case "daily_forecast":
                        Log::info('Start Daily Forecast Cron');
                        $distinctCategory = VCModel::getDistinctDailyForecastCategory();
                        if (!empty($distinctCategory)) {
                            echo 'If Not Empty Category' . PHP_EOL;
                            foreach ($distinctCategory as $cat) {
                                $getCatId = VCModel::getCategoryId($cat->strCategory);
                                if (!empty($getCatId)) {
                                    echo 'Category Id Found = ' . $getCatId;
                                    $updateRecord = VCModel::updateDailyForecastCategoryId($getCatId, $cat->strCategory);
                                    echo 'Record Updated';
                                    if (!empty($updateRecord)) {
                                        $cronJobList = $this->successCronJobData($singleRun->moduleName);

                                    } else {
                                        $cronJobList = $this->failedCronJobData($singleRun->moduleName);
                                    }

                                    VCModel::cronInsert($cronJobList);
                                } else {
                                    echo 'Category Id Not Found' . PHP_EOL;
                                }

                            }
                        } else {
                            echo 'Distinct Category Not Found' . PHP_EOL;
                            Log::info('Distinct Category Not Found');
                        }
                        Log::info('End Daily Forecast Cron ');
                        break;
                    default:
                        Log::info('Cron Not Run VC');
                }
            }
        } else {
            echo 'No Cron Aavailable To Run VC' . PHP_EOL;
            Log::info('No Cron Aavailable To Run VC');
        }
    }

    private function successCronJobData($moduleName)
    {
        $cronJobList['moduleName'] = $moduleName;
        $cronJobList['isDoneModuleData'] = 1;
        $cronJobList['isRunned'] = 1;
        $cronJobList['isFailed'] = 0;
        $cronJobList['isSuccess'] = 1;
        $cronJobList['updatedAt'] = date('Y-m-d H:i:s');

        return $cronJobList;
    }

    private function failedCronJobData($moduleName)
    {
        $cronJobList['moduleName'] = $moduleName;
        $cronJobList['isDoneModuleData'] = 0;
        $cronJobList['isRunned'] = 1;
        $cronJobList['isFailed'] = 1;
        $cronJobList['isSuccess'] = 0;
        $cronJobList['updatedAt'] = date('Y-m-d H:i:s');

        return $cronJobList;
    }
}
