<?php

namespace App\Console\Commands\Mws\copyProductSalesRank;

use App\Models\MWSModel;
use DateTime;
use Illuminate\Console\Command;
use Artisan;
use Illuminate\Support\Facades\Log;

class copyProductSalesRank extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'copyProductSalesRank:cron';

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
        //do not run this cron at 00:00
        $requestReportTime=date('H:i');
        $requestReportTimecheck = DateTime::createFromFormat('H:i', $requestReportTime);
        $day_end_check_from=DateTime::createFromFormat('H:i', '00:05');
        $day_end_check_to=DateTime::createFromFormat('H:i', '23:55');
        if ($requestReportTimecheck >= $day_end_check_from && $requestReportTimecheck <= $day_end_check_to) {
            Log::info("filePath:app\Console\Commands\Mws\copyProductSalesRank\copyProductSalesRank.API : GetMatchingProductForId. Start Cron.");
            MWSModel::insert_mws_Activity('Start Cron.', 'Cron : copyProductSalesRank)', 'app\Console\Commands\Mws\scGetProductDetails\copyProductSalesRank.php');
            //Increase execution time
            scSetMemoryLimitAndExeTime();
            //get all asins to get sales rank
            $product_ids = MWSModel::getScAsinToCopy();
            if ($product_ids) {
                if (count($product_ids) > 0) {
                    foreach ($product_ids as $value) {
                        $fkProductTblId = $value->id;
                        $productTblAsin = $value->asin;
                        $accountId = $value->fkAccountId;
                        $fkSellerConfigId = $value->fkSellerConfigId;
                        $batchId = $value->fkBatchId;
                        $source = $value->source;
                        //If sales rank already exist and active then do not enter value in db
                        $scCheckSalesRankExist = MWSModel::scCheckSalesRankExist($productTblAsin, $accountId);
                        if ($scCheckSalesRankExist == 0) {
                        //In Case of  SC get maximum tree and in case of vc get minimum tree from categories table
                        switch ($source) {
                            case "SC":
                                $categoryTreeNo = MWSModel::getScTreeNo($productTblAsin, $accountId);
                                break;
                            case "VC":
                                $categoryTreeNo = MWSModel::getVcTreeNo($productTblAsin, $accountId);
                                break;
                            default:
                                $categoryTreeNo = MWSModel::getVcTreeNo($productTblAsin, $accountId);
                        }//end switch
                        if (empty(trim($categoryTreeNo))) {
                            $categoryTreeNo = 1;
                        }//end if

                        //GET Ctegory and SubCategory Details
                        $getScAsinCategoryId = MWSModel::getScAsinCategoryId($productTblAsin, $accountId, $categoryTreeNo);
                        if (!empty($getScAsinCategoryId)) {
                            if (count($getScAsinCategoryId) > 0) {
                                $catSubCatArray = array();
                                /*Make Single Array For Category and Sub Category Values From Category Tree */
                                foreach ($getScAsinCategoryId as $categoryValues) {
                                    $categoryTreeSequence = $categoryValues->categoryTreeSequence;
                                    $categoryTreeCatId = $categoryValues->productCategoryId;
                                    $categoryTreeCatName = $categoryValues->productCategoryName;
                                    if ($categoryTreeSequence == 1) {
                                        $catSubCatArray['productSubCategoryId'] = trim($categoryTreeCatId);
                                        $catSubCatArray['productSubCategoryName'] = trim($categoryTreeCatName);
                                    } else {
                                        $catSubCatArray['productCategoryId'] = trim($categoryTreeCatId);
                                        $catSubCatArray['productCategoryName'] = trim($categoryTreeCatName);
                                    }//end if
                                }//end foreach

                                /*Product Category and Sub Category Values From Category Tree */
                                isset($catSubCatArray['productSubCategoryId']) ? $productSubCategoryId = $catSubCatArray['productSubCategoryId'] : $productSubCategoryId = 0;
                                isset($catSubCatArray['productSubCategoryName']) ? $productSubCategoryName = $catSubCatArray['productSubCategoryName'] : $productSubCategoryName = 'NA';
                                isset($catSubCatArray['productCategoryId']) ? $productCategoryId = $catSubCatArray['productCategoryId'] : $productCategoryId = $productSubCategoryId;
                                isset($catSubCatArray['productCategoryName']) ? $productCategoryName = $catSubCatArray['productCategoryName'] : $productCategoryName = $productSubCategoryName;
                                //get sales rank value for sub category
                                $ScCopySalesRankData = MWSModel::getScSalesRankDataToCopy($productSubCategoryId, $productTblAsin, $accountId);

                                if (isset($ScCopySalesRankData) && count($ScCopySalesRankData) > 0) {
                                    $productSalesRank = $ScCopySalesRankData[0]->salesRank;
                                } else {
                                    $productSalesRank = -1;
                                }//end if
                                $salesRankData = array();
                                $salesRankData['fkProductTblId'] = $fkProductTblId;
                                $salesRankData['fkAccountId'] = $accountId;
                                $salesRankData['fkBatchId'] = $batchId;
                                $salesRankData['source'] = $source;
                                $salesRankData['isActive'] = 1;
                                $salesRankData['fkSellerConfigId'] = $fkSellerConfigId;
                                $salesRankData['asin'] = $productTblAsin;
                                $salesRankData['productCategoryId'] = $productCategoryId;
                                $salesRankData['productCategoryName'] = $productCategoryName;
                                $salesRankData['productSubCategoryId'] = $productSubCategoryId;
                                $salesRankData['productSubCategoryName'] = $productSubCategoryName;
                                $salesRankData['salesRank'] = $productSalesRank;
                                $salesRankData['createdAt'] = date('Y-m-d H:i:s');
                                $scInsertSalesRankData = MWSModel::scInsertSalesRankData($salesRankData);
                                if ($scInsertSalesRankData) {
                                    $updateSalesRankCopystatus = array();
                                    $updateSalesRankCopystatus['productSalesRankCoppied'] = 1;
                                    MWSModel::updateSalesRankCopystatus($updateSalesRankCopystatus, $fkProductTblId);
                                }//end if
                            } else {
                                $updateSalesRankCopystatus = array();
                                $updateSalesRankCopystatus['productSalesRankCoppied'] = 2;
                                MWSModel::updateSalesRankCopystatus($updateSalesRankCopystatus, $fkProductTblId);
                            }//end if
                        } else {
                            $updateSalesRankCopystatus = array();
                            $updateSalesRankCopystatus['productSalesRankCoppied'] = 2;
                            MWSModel::updateSalesRankCopystatus($updateSalesRankCopystatus, $fkProductTblId);
                        }//end if
                    }
                    }//end foreach

                }//end if
            }//end if
        }
    }
}
