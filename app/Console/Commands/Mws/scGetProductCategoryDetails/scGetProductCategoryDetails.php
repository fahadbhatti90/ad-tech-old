<?php

namespace App\Console\Commands\Mws\scGetProductCategoryDetails;

use App\Libraries\mws\AmazonProductInfo;
use App\Models\MWSModel;
use Config;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Artisan;

class scGetProductCategoryDetails extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'scGetProductCategoryDetails:cron';

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
        Log::info("filePath:app\Console\Commands\Mws\\scGetProductCategoryDetails\scGetProductCategoryDetails.php .API : GetProductCategoriesForASIN. Start Cron.");
        MWSModel::insert_mws_Activity('Start Cron.', 'API : GetProductCategoriesForASIN)', 'app\Console\Commands\Mws\\scGetProductCategoryDetails\scGetProductCategoryDetails.php');
        scSetMemoryLimitAndExeTime();
        //get asin for request
        $product_ids = MWSModel::get_sc_product_ids_for_categories();
        //update categories in queue
        MWSModel::updateCategoriesInQueue();
        foreach ($product_ids as $values) {
            Log::info("filePath:app\Console\Commands\Mws\\scGetProductCategoryDetails\scGetProductCategoryDetails.php .API : GetProductCategoriesForASIN. Sleeping for 5 seconds before every category request request start.".date("Y-m-d H:i:s"));
            sleep(5);
            Log::info("filePath:app\Console\Commands\Mws\\scGetProductCategoryDetails\scGetProductCategoryDetails.php .API : GetProductCategoriesForASIN. Sleeping for 5 seconds before every category request request end.".date("Y-m-d H:i:s"));
            $sellerConfigId = $values->fkSellerConfigId;
            $fkAccountId = $values->fkAccountId;
            $fkBatchId = $values->fkBatchId;
        //if source not defined make it VC
            if (!empty($source = $values->source)){
                $source = $values->source;
            }else{
                $source = 'VC';
            }
                                                            /*source of asin*/
            switch ($source) {
                case "SC":
                    $getSellerDetailsById = MWSModel::getSellerDetailsById($sellerConfigId);
                    $sellerId = $getSellerDetailsById[0]->seller_id;
                    $mwsAccessKeyId = $getSellerDetailsById[0]->mws_access_key_id;
                    $mwsSecretKey = $getSellerDetailsById[0]->mws_secret_key;
                    $mwsAuthtoken = $getSellerDetailsById[0]->mws_authtoken;
                    Config::set('amazon-mws.store.store1.merchantId', trim($sellerId));
                    Config::set('amazon-mws.store.store1.marketplaceId', 'ATVPDKIKX0DER');
                    Config::set('amazon-mws.store.store1.keyId', trim($mwsAccessKeyId));
                    Config::set('amazon-mws.store.store1.secretKey', trim($mwsSecretKey));
                    Config::set('amazon-mws.store.store1.authToken', trim($mwsAuthtoken));
                    break;
                default:
                    Config::set('amazon-mws.store.store1.merchantId', 'ALHQO889BDX6E');
                    Config::set('amazon-mws.store.store1.marketplaceId', 'ATVPDKIKX0DER');
                    Config::set('amazon-mws.store.store1.keyId', 'AKIAI4VW7BAXJPB72M5A');
                    Config::set('amazon-mws.store.store1.secretKey', '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');
                    Config::set('amazon-mws.store.store1.authToken', 'amzn.mws.36b7be47-e88d-9ecd-be34-613607c4da69');
            }
            $amz = new AmazonProductInfo("store1");
            if ($values->idType == 'ASIN') {
                if ($values->idType == 'ASIN') {
                    $amz->setASINs($values->asin);
                }
                if ($values->idType == 'SellerSKU') {
                    $amz->setASINs($values->sku);
                }
                $amz->fetchCategories();
                $products_category_details = $amz->getProduct();
                if (isset($products_category_details[0]->data['Categories']) && is_array($products_category_details[0]->data['Categories']) && !empty(is_array($products_category_details[0]->data['Categories']))) {
                    $categoryTreeArray = $products_category_details[0]->data['Categories'];
                    $lastcategoryTreeArray = end($products_category_details[0]->data['Categories']);
                    if (isset($categoryTreeArray) && is_array($categoryTreeArray)) {
                        $categoryTreeNumber=1;
                        foreach ($categoryTreeArray as $categoryTreeArrayValues) {
                            $categoryValues = array();
                            $currentCategoryTreeArray = array();
                            $currentCategoryTreeArray = $categoryTreeArrayValues;
                            $parentCatValue = 1;
                            doagainCategoryTree:
                            $tempCatTree = array(
                                "fkProductTblId" => $values->id,
                                "fkSellerConfigId" => $values->fkSellerConfigId,
                                "fkAccountId" => $fkAccountId,
                                "fkBatchId" => $fkBatchId,
                                "source" => $source,
                                "isActive" => 1,
                                "asin" => $values->asin,
                                "productCategoryId" => $currentCategoryTreeArray["ProductCategoryId"],
                                "productCategoryName" => $currentCategoryTreeArray["ProductCategoryName"],
                                "categoryTreeSequence" => $parentCatValue,
                                "categoryTreeNumber" => $categoryTreeNumber,
                                "createdAt" => date('Y-m-d H:i:s')
                            );
                            array_push($categoryValues, $tempCatTree);
                            $parentCatValue++;
                            if (isset($currentCategoryTreeArray["Parent"])) {
                                $currentCategoryTreeArray = $currentCategoryTreeArray["Parent"];
                                goto doagainCategoryTree;
                            }
                            if (isset($categoryValues)) {
                                foreach ($categoryValues as $singleCategoryValues) {
                                    $productCategoryId = $singleCategoryValues['productCategoryId'];
                                    $singleCatAsin = $singleCategoryValues['asin'];
                                    $singleCatCategoryTreeSequence = $singleCategoryValues['categoryTreeSequence'];
                                    $singleCatCategoryTreeNumber = $singleCategoryValues['categoryTreeNumber'];
                                    $get_categories_duplicate_count = MWSModel::get_categories_duplicate_count($productCategoryId,$singleCatAsin,$singleCatCategoryTreeSequence,$singleCatCategoryTreeNumber,$fkAccountId);
                                         MWSModel::insert_product_category_details($singleCategoryValues);
                                }
                            }
                            $categoryTreeNumber++;
                        }
                    }
                    $storeArray['productCategoryDetailsInQueue'] = 2;
                    $storeArray['productCategoryDetailsDownloaded'] = 1;
                    MWSModel::update_product_download_status($storeArray, $values->id);
                } else {
                    /*no recrod found*/
                    $storeArray['productCategoryDetailsInQueue'] = 2;
                    $storeArray['productCategoryDetailsDownloaded'] = 2;
                    MWSModel::update_product_download_status($storeArray, $values->id);
                }
            }
        }//end foreach
        //echo 'start';
        Artisan::call('copyProductSalesRank:cron');
        Log::info("filePath:app\Console\Commands\Mws\\scGetProductCategoryDetails\scGetProductCategoryDetails.php .API : GetProductCategoriesForASIN. Ends Cron.");
        MWSModel::insert_mws_Activity('End Cron.', 'API : GetProductCategoriesForASIN)', 'app\Console\Commands\Mws\\scGetProductCategoryDetails\scGetProductCategoryDetails.php');

    }
}
