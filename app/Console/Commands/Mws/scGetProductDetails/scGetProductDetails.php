<?php

namespace App\Console\Commands\Mws\scGetProductDetails;

use App\Libraries\mws\AmazonProductList;
use App\Models\MWSModel;
use Config;
use DateTime;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class scGetProductDetails extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'scGetProductDetails:cron';

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
        $day_end_check_from=DateTime::createFromFormat('H:i', '02:45');
        $day_end_check_to=DateTime::createFromFormat('H:i', '22:45');
        if ($requestReportTimecheck >= $day_end_check_from && $requestReportTimecheck <= $day_end_check_to) {
            Log::info("filePath:app\Console\Commands\Mws\scGetProductDetails\scGetProductDetails.API : GetMatchingProductForId. Start Cron.");
            MWSModel::insert_mws_Activity('Start Cron.', 'API : GetMatchingProductForId)', 'app\Console\Commands\Mws\scGetProductDetails\scGetProductDetails.php');
            scSetMemoryLimitAndExeTime();
            $product_ids = MWSModel::get_sc_product_ids();
            MWSModel::updateProductDetailsInQueue();
            foreach ($product_ids as $values) {
                $sellerConfigId = $values->fkSellerConfigId;
                $fkAccountId = $values->fkAccountId;
                $fkBatchId = $values->fkBatchId;
                $source = $values->source;
                $asinValue = $values->asin;
                $getScProductDetailsDuplicateCount = MWSModel::getScProductDetailsDuplicateCount($fkAccountId, $asinValue, $source);
                if ($getScProductDetailsDuplicateCount == 0) {
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
                    $amz = new AmazonProductList("store1");
                    if ($values->idType == 'ASIN') {
                        $product_id = $values->asin;
                        $amz->setIdType('ASIN');
                        $amz->setProductIds($values->asin);
                    }//end if
                    $products_list = $amz->fetchProductList();
                    $products_details = $amz->getProduct();
                    if (isset($products_details[0]->data) && !empty($products_details)) {
                        foreach ($products_details as $products_details_values) {
                            $product_data = array();
                            $product_data['fkProductTblId'] = $values->id;
                            $product_data['fkAccountId'] = $fkAccountId;
                            $product_data['fkBatchId'] = $fkBatchId;
                            $product_data['source'] = $source;
                            $product_data['isActive'] = 1;
                            $product_data['fkSellerConfigId'] = $values->fkSellerConfigId;
                            if (isset($products_details_values->data['Identifiers']['MarketplaceASIN']['MarketplaceId']) && !is_array($products_details_values->data['Identifiers']['MarketplaceASIN']['MarketplaceId'])) {
                                $product_data['marketplaceId'] = trim($products_details_values->data['Identifiers']['MarketplaceASIN']['MarketplaceId']) != '' ? $products_details_values->data['Identifiers']['MarketplaceASIN']['MarketplaceId'] : 'NA';
                            }
                            if (isset($products_details_values->data['Identifiers']['MarketplaceASIN']['ASIN']) && !is_array($products_details_values->data['Identifiers']['MarketplaceASIN']['ASIN'])) {
                                $product_data['asin'] = trim($products_details_values->data['Identifiers']['MarketplaceASIN']['ASIN']) != '' ? $products_details_values->data['Identifiers']['MarketplaceASIN']['ASIN'] : 'NA';
                            }
                            if (isset($products_details_values->data['AttributeSets'][0]['Binding']) && !is_array($products_details_values->data['AttributeSets'][0]['Binding'])) {
                                $product_data['binding'] = trim($products_details_values->data['AttributeSets'][0]['Binding']) != '' ? $products_details_values->data['AttributeSets'][0]['Binding'] : 'NA';
                            }
                            if (isset($products_details_values->data['AttributeSets'][0]['Brand']) && !is_array($products_details_values->data['AttributeSets'][0]['Brand'])) {
                                $product_data['brand'] = trim($products_details_values->data['AttributeSets'][0]['Brand']) != '' ? $products_details_values->data['AttributeSets'][0]['Brand'] : 'NA';
                            }
                            if (isset($products_details_values->data['AttributeSets'][0]['Color']) && !is_array($products_details_values->data['AttributeSets'][0]['Color'])) {

                                $color = $products_details_values->data['AttributeSets'][0]['Color'];
                                $color = sc_clean_product_attributes_strings($color);
                                $product_data['color'] = trim($color) != '' ? $color : 'NA';
                            }
                            if (isset($products_details_values->data['AttributeSets'][0]['Department']) && !is_array($products_details_values->data['AttributeSets'][0]['Department'])) {
                                $product_data['department'] = trim($products_details_values->data['AttributeSets'][0]['Department']) != '' ? $products_details_values->data['AttributeSets'][0]['Department'] : 'NA';
                            }
                            if (isset($products_details_values->data['AttributeSets'][0]['ItemDimensions']['Height']) && !is_array($products_details_values->data['AttributeSets'][0]['ItemDimensions']['Height'])) {
                                $product_data['itemHeight'] = trim($products_details_values->data['AttributeSets'][0]['ItemDimensions']['Height']) != '' ? get_sc_decimel_value($products_details_values->data['AttributeSets'][0]['ItemDimensions']['Height']) : 0.00;
                            }
                            if (isset($products_details_values->data['AttributeSets'][0]['ItemDimensions']['Length']) && !is_array($products_details_values->data['AttributeSets'][0]['ItemDimensions']['Length'])) {
                                $product_data['itemLength'] = trim($products_details_values->data['AttributeSets'][0]['ItemDimensions']['Length']) != '' ? get_sc_decimel_value($products_details_values->data['AttributeSets'][0]['ItemDimensions']['Length']) : 0.00;
                            }
                            if (isset($products_details_values->data['AttributeSets'][0]['ItemDimensions']['Width']) && !is_array($products_details_values->data['AttributeSets'][0]['ItemDimensions']['Width'])) {
                                $product_data['itemWidth'] = trim($products_details_values->data['AttributeSets'][0]['ItemDimensions']['Width']) != '' ? get_sc_decimel_value($products_details_values->data['AttributeSets'][0]['ItemDimensions']['Width']) : 0.00;
                            }
                            if (isset($products_details_values->data['AttributeSets'][0]['ItemDimensions']['Weight']) && !is_array($products_details_values->data['AttributeSets'][0]['ItemDimensions']['Weight'])) {
                                $product_data['itemWeight'] = trim($products_details_values->data['AttributeSets'][0]['ItemDimensions']['Weight']) != '' ? get_sc_decimel_value($products_details_values->data['AttributeSets'][0]['ItemDimensions']['Weight']) : 0.00;
                            }
                            if (isset($products_details_values->data['AttributeSets'][0]['Label']) && !is_array($products_details_values->data['AttributeSets'][0]['Label'])) {

                                $itemLabel = sc_clean_product_attributes_strings($products_details_values->data['AttributeSets'][0]['Label']);
                                $product_data['itemLabel'] = $itemLabel != '' ? $itemLabel : 'NA';
                            }
                            if (isset($products_details_values->data['AttributeSets'][0]['ListPrice']['Amount']) && !is_array($products_details_values->data['AttributeSets'][0]['ListPrice']['Amount'])) {
                                $product_data['itemAmount'] = trim($products_details_values->data['AttributeSets'][0]['ListPrice']['Amount']) != '' ? $products_details_values->data['AttributeSets'][0]['ListPrice']['Amount'] : 0.00;
                            }
                            if (isset($products_details_values->data['AttributeSets'][0]['ListPrice']['CurrencyCode']) && !is_array($products_details_values->data['AttributeSets'][0]['ListPrice']['CurrencyCode'])) {
                                $product_data['currencyCode'] = trim($products_details_values->data['AttributeSets'][0]['ListPrice']['CurrencyCode']) != '' ? $products_details_values->data['AttributeSets'][0]['ListPrice']['CurrencyCode'] : 'NA';
                            }
                            if (isset($products_details_values->data['AttributeSets'][0]['Manufacturer']) && !is_array($products_details_values->data['AttributeSets'][0]['Manufacturer'])) {
                                $attributeSets = sc_clean_product_attributes_strings($products_details_values->data['AttributeSets'][0]['Manufacturer']);
                                $product_data['manufacturer'] = $attributeSets != '' ? $attributeSets : 'NA';
                            }
                            if (isset($products_details_values->data['AttributeSets'][0]['MaterialType']) && !is_array($products_details_values->data['AttributeSets'][0]['MaterialType'])) {
                                $materialType = sc_clean_product_attributes_strings($products_details_values->data['AttributeSets'][0]['MaterialType']);
                                $product_data['materialType'] = $materialType != '' ? $materialType : 'NA';
                            }
                            if (isset($products_details_values->data['AttributeSets'][0]['Model']) && !is_array($products_details_values->data['AttributeSets'][0]['Model'])) {
                                $model = sc_clean_product_attributes_strings($products_details_values->data['AttributeSets'][0]['Model']);
                                $product_data['model'] = $model && $model != '' ? $model : 'NA';
                            }
                            if (isset($products_details_values->data['AttributeSets'][0]['NumberOfItems']) && !is_array($products_details_values->data['AttributeSets'][0]['NumberOfItems'])) {
                                $product_data['numberOfItems'] = trim($products_details_values->data['AttributeSets'][0]['NumberOfItems']) != '' ? $products_details_values->data['AttributeSets'][0]['NumberOfItems'] : 0;
                            }
                            if (isset($products_details_values->data['AttributeSets'][0]['PackageDimensions']['Height']) && !is_array($products_details_values->data['AttributeSets'][0]['PackageDimensions']['Height'])) {
                                $product_data['packageHeight'] = trim($products_details_values->data['AttributeSets'][0]['PackageDimensions']['Height']) != '' ? $products_details_values->data['AttributeSets'][0]['PackageDimensions']['Height'] : 0.00;
                            }
                            if (isset($products_details_values->data['AttributeSets'][0]['PackageDimensions']['Length']) && !is_array($products_details_values->data['AttributeSets'][0]['PackageDimensions']['Length'])) {
                                $product_data['packageLength'] = trim($products_details_values->data['AttributeSets'][0]['PackageDimensions']['Length']) != '' ? $products_details_values->data['AttributeSets'][0]['PackageDimensions']['Length'] : 0.00;
                            }
                            if (isset($products_details_values->data['AttributeSets'][0]['PackageDimensions']['Width']) && !is_array($products_details_values->data['AttributeSets'][0]['PackageDimensions']['Width'])) {
                                $product_data['packageWidth'] = trim($products_details_values->data['AttributeSets'][0]['PackageDimensions']['Width']) != '' ? $products_details_values->data['AttributeSets'][0]['PackageDimensions']['Width'] : 0.00;
                            }
                            if (isset($products_details_values->data['AttributeSets'][0]['PackageDimensions']['Weight']) && !is_array($products_details_values->data['AttributeSets'][0]['PackageDimensions']['Weight'])) {
                                $product_data['packageWeight'] = trim($products_details_values->data['AttributeSets'][0]['PackageDimensions']['Weight']) != '' ? $products_details_values->data['AttributeSets'][0]['PackageDimensions']['Weight'] : 0.00;
                            }
                            if (isset($products_details_values->data['AttributeSets'][0]['PackageQuantity']) && !is_array($products_details_values->data['AttributeSets'][0]['PackageQuantity'])) {
                                $product_data['packageQuantity'] = trim($products_details_values->data['AttributeSets'][0]['PackageQuantity']) != '' ? $products_details_values->data['AttributeSets'][0]['PackageQuantity'] : 0;
                            }
                            if (isset($products_details_values->data['AttributeSets'][0]['PartNumber']) && !is_array($products_details_values->data['AttributeSets'][0]['PartNumber'])) {
                                $partNumber = sc_clean_product_attributes_strings($products_details_values->data['AttributeSets'][0]['PartNumber']);
                                $product_data['partNumber'] = $partNumber != '' ? $partNumber : 'NA';
                            }
                            if (isset($products_details_values->data['AttributeSets'][0]['ProductGroup']) && !is_array($products_details_values->data['AttributeSets'][0]['ProductGroup'])) {
                                $product_data['productGroup'] = trim($products_details_values->data['AttributeSets'][0]['ProductGroup']) != '' ? $products_details_values->data['AttributeSets'][0]['ProductGroup'] : 'NA';
                            }
                            if (isset($products_details_values->data['AttributeSets'][0]['ProductTypeName']) && !is_array($products_details_values->data['AttributeSets'][0]['ProductTypeName'])) {
                                $product_data['productTypeName'] = trim($products_details_values->data['AttributeSets'][0]['ProductTypeName']) != '' ? $products_details_values->data['AttributeSets'][0]['ProductTypeName'] : 'NA';
                            }
                            if (isset($products_details_values->data['AttributeSets'][0]['Publisher']) && !is_array($products_details_values->data['AttributeSets'][0]['Publisher'])) {
                                $publisher = sc_clean_product_attributes_strings($products_details_values->data['AttributeSets'][0]['Publisher']);
                                $product_data['publisher'] = $publisher != '' ? $publisher : 'NA';
                            }

                            if (isset($products_details_values->data['AttributeSets'][0]['ReleaseDate']) && !is_array($products_details_values->data['AttributeSets'][0]['ReleaseDate'])) {
                                $product_data['releaseDate'] = trim($products_details_values->data['AttributeSets'][0]['ReleaseDate']) != '' ? $products_details_values->data['AttributeSets'][0]['ReleaseDate'] : '0000-00-00';
                            }
                            if (isset($products_details_values->data['AttributeSets'][0]['Size']) && !is_array($products_details_values->data['AttributeSets'][0]['Size'])) {
                                $size = sc_clean_product_attributes_strings($products_details_values->data['AttributeSets'][0]['Size']);
                                $product_data['size'] = $size != '' ? $size : 'NA';
                            }
                            if (isset($products_details_values->data['AttributeSets'][0]['SmallImage']['URL']) && !is_array($products_details_values->data['AttributeSets'][0]['SmallImage']['URL'])) {
                                $product_data['smallImageURL'] = trim($products_details_values->data['AttributeSets'][0]['SmallImage']['URL']) != '' ? $products_details_values->data['AttributeSets'][0]['SmallImage']['URL'] : 'NA';
                            }
                            if (isset($products_details_values->data['AttributeSets'][0]['SmallImage']['Height']) && !is_array($products_details_values->data['AttributeSets'][0]['SmallImage']['Height'])) {
                                $product_data['smallImageHeight'] = trim($products_details_values->data['AttributeSets'][0]['SmallImage']['Height']) != '' ? $products_details_values->data['AttributeSets'][0]['SmallImage']['Height'] : 0.00;
                            }
                            if (isset($products_details_values->data['AttributeSets'][0]['SmallImage']['Width']) && !is_array($products_details_values->data['AttributeSets'][0]['SmallImage']['Width'])) {
                                $product_data['smallImageWidth'] = trim($products_details_values->data['AttributeSets'][0]['SmallImage']['Width']) != '' ? $products_details_values->data['AttributeSets'][0]['SmallImage']['Width'] : 0.00;
                            }
                            if (isset($products_details_values->data['AttributeSets'][0]['Studio']) && !is_array($products_details_values->data['AttributeSets'][0]['Studio'])) {
                                $studio = sc_clean_product_attributes_strings($products_details_values->data['AttributeSets'][0]['Studio']);
                                $product_data['studio'] = $studio != '' ? $studio : 'NA';
                            }
                            if (isset($products_details_values->data['AttributeSets'][0]['Title']) && !is_array($products_details_values->data['AttributeSets'][0]['Title'])) {
                                $product_data['title'] = trim($products_details_values->data['AttributeSets'][0]['Title']) != '' ? $products_details_values->data['AttributeSets'][0]['Title'] : 'NA';
                            } else {
                                $product_data['title'] = 'NA';
                            }
                            if (isset($products_details_values->data['AttributeSets'][0]['Warranty']) && !is_array($products_details_values->data['AttributeSets'][0]['Warranty'])) {
                                $warranty = sc_clean_product_attributes_strings($products_details_values->data['AttributeSets'][0]['Warranty']);
                                $product_data['warranty'] = $warranty != '' ? $warranty : 'NA';
                            } else {
                                $product_data['warranty'] = 'NA';
                            }
                            if (isset($products_details_values->data['Relationships']['VariationParent']['Identifiers']['MarketplaceASIN']['MarketplaceId']) && !is_array($products_details_values->data['Relationships']['VariationParent']['Identifiers']['MarketplaceASIN']['MarketplaceId'])) {
                                $product_data['parentAsinMarketplaceId'] = trim($products_details_values->data['Relationships']['VariationParent']['Identifiers']['MarketplaceASIN']['MarketplaceId']) != '' ? $products_details_values->data['Relationships']['VariationParent']['Identifiers']['MarketplaceASIN']['MarketplaceId'] : 'NA';

                            }
                            if (isset($products_details_values->data['Relationships']['VariationParent']['Identifiers']['MarketplaceASIN']['ASIN']) && !is_array($products_details_values->data['Relationships']['VariationParent']['Identifiers']['MarketplaceASIN']['ASIN'])) {
                                $product_data['parentAsin'] = trim($products_details_values->data['Relationships']['VariationParent']['Identifiers']['MarketplaceASIN']['ASIN']) != '' ? $products_details_values->data['Relationships']['VariationParent']['Identifiers']['MarketplaceASIN']['ASIN'] : 'NA';

                            }
                            if (isset($products_details_values->data['AttributeSets'][0]['IsAdultProduct']) && !is_array($products_details_values->data['AttributeSets'][0]['IsAdultProduct'])) {
                                $product_data['isAdultProduct'] = trim($products_details_values->data['AttributeSets'][0]['IsAdultProduct']) != '' ? $products_details_values->data['AttributeSets'][0]['IsAdultProduct'] : 'NA';

                            }
                            if (isset($products_details_values->data['AttributeSets'][0]['IsAutographed']) && !is_array($products_details_values->data['AttributeSets'][0]['IsAutographed'])) {
                                $product_data['isAutographed'] = trim($products_details_values->data['AttributeSets'][0]['IsAutographed']) != '' ? $products_details_values->data['AttributeSets'][0]['IsAutographed'] : 'NA';
                            }
                            if (isset($products_details_values->data['AttributeSets'][0]['IsMemorabilia']) && !is_array($products_details_values->data['AttributeSets'][0]['IsMemorabilia'])) {
                                $product_data['isMemorabilia'] = trim($products_details_values->data['AttributeSets'][0]['IsMemorabilia']) != '' ? $products_details_values->data['AttributeSets'][0]['IsMemorabilia'] : 'NA';
                            }
                            if (isset($products_details_values->data['AttributeSets'][0]['Platform']) && !is_array($products_details_values->data['AttributeSets'][0]['Platform'])) {
                                $product_data['platform'] = trim($products_details_values->data['AttributeSets'][0]['Platform']) != '' ? $products_details_values->data['AttributeSets'][0]['Platform'] : 'NA';
                            }
                            if (isset($products_details_values->data['AttributeSets'][0]['PublicationDate']) && !is_array($products_details_values->data['AttributeSets'][0]['PublicationDate'])) {
                                $product_data['publicationDate'] = trim($products_details_values->data['AttributeSets'][0]['PublicationDate']) != '' ? $products_details_values->data['AttributeSets'][0]['PublicationDate'] : 'NA';
                            }
                            if (isset($products_details_values->data['AttributeSets'][0]['ManufacturerMaximumAge']) && !is_array($products_details_values->data['AttributeSets'][0]['ManufacturerMaximumAge'])) {
                                $product_data['manufacturerMaximumAge'] = trim($products_details_values->data['AttributeSets'][0]['ManufacturerMaximumAge']) != '' ? $products_details_values->data['AttributeSets'][0]['ManufacturerMaximumAge'] : '0.00';
                            }
                            if (isset($products_details_values->data['AttributeSets'][0]['ManufacturerMinimumAge']) && !is_array($products_details_values->data['AttributeSets'][0]['ManufacturerMinimumAge'])) {
                                $products_details_values->data['AttributeSets'][0]['ManufacturerMinimumAge'];
                                $product_data['manufacturerMinimumAge'] = trim($products_details_values->data['AttributeSets'][0]['ManufacturerMinimumAge']) != '' ? $products_details_values->data['AttributeSets'][0]['ManufacturerMinimumAge'] : '0.00';
                            }
                            $product_data['createdAt'] = date('Y-m-d H:i:s');
                            $result = MWSModel::insert_product_details($product_data);
                            unset($product_data);
                            if ($result) {
                                //product downloaded successfully
                                $storeArray['productDetailsInQueue'] = 2;
                                $storeArray['productDetailsDownloaded'] = 1;
                                $result_downloded_status = MWSModel::update_product_download_status($storeArray, $values->id);
                                if ($result_downloded_status) {
                                    if (isset($products_details_values->data['SalesRankings']) && !empty($products_details_values->data['SalesRankings'])) {
                                        $sales_rank_array = $products_details_values->data['SalesRankings']->SalesRank;
                                        $sales_rank_data = array();
                                        $sales_rank_count = 1;
                                        foreach ($sales_rank_array as $sales_rank_values) {
                                            $sales_rank_array = array();
                                            $sales_rank_array['fkProductTblId'] = $values->id;
                                            $sales_rank_array['fkAccountId'] = $fkAccountId;
                                            $sales_rank_array['fkBatchId'] = $fkBatchId;
                                            $sales_rank_array['source'] = $source;
                                            $sales_rank_array['isActive'] = 1;
                                            $sales_rank_array['fkSellerConfigId'] = $values->fkSellerConfigId;
                                            if (isset($products_details_values->data['Identifiers']['MarketplaceASIN']['ASIN']) && !is_array($products_details_values->data['Identifiers']['MarketplaceASIN']['ASIN'])) {
                                                $sales_rank_array['asin'] = trim($products_details_values->data['Identifiers']['MarketplaceASIN']['ASIN']) != '' ? $products_details_values->data['Identifiers']['MarketplaceASIN']['ASIN'] : 'NA';
                                            }
                                            if (isset($sales_rank_values->ProductCategoryId) && !empty($sales_rank_values->ProductCategoryId)) {
                                                $sales_rank_array['productCategoryId'] = trim($sales_rank_values->ProductCategoryId) != '' ? $sales_rank_values->ProductCategoryId : 'NA';
                                            }
                                            if (isset($sales_rank_values->Rank) && !empty($sales_rank_values->Rank)) {
                                                $sales_rank_array['salesRank'] = trim($sales_rank_values->Rank) != '' ? $sales_rank_values->Rank : 'NA';
                                            }
                                            $sales_rank_array['salesRankCount'] = $sales_rank_count;
                                            $sales_rank_array['createdAt'] = date('Y-m-d H:i:s');
                                            $sales_rank_data[] = $sales_rank_array;
                                            $sales_rank_count++;
                                            unset($sales_rank_array);
                                        }//end foreach
                                        if (!empty($sales_rank_data)) {
                                            $sales_rank_result = MWSModel::insert_product_sales_rank($sales_rank_data);
                                        }//end if
                                        unset($sales_rank_data);
                                    }//end if
                                }//end if


                            }//end if
                        }//end foreach
                    } else {
                        //no data found against product
                        $storeArray['productDetailsInQueue'] = 2;
                        $storeArray['productDetailsDownloaded'] = 2;
                        MWSModel::update_product_download_status($storeArray, $values->id);
                    }//end if
                } else {
                    $storeArray['productDetailsInQueue'] = 2;
                    $storeArray['productDetailsDownloaded'] = 1;
                    $result_downloded_status = MWSModel::update_product_download_status($storeArray, $values->id);
                }
                //end if
            }//end if
            Log::info("filePath:app\Console\Commands\Mws\scGetProductDetails\scGetProductDetails.API : GetMatchingProductForId. Ends Cron.");
            MWSModel::insert_mws_Activity('End Cron.', 'API : GetMatchingProductForId)', 'app\Console\Commands\Mws\scGetProductDetails\scGetProductDetails.php');
        }
    }
}
