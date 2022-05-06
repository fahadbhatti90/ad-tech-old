<?php

use Illuminate\Database\Seeder;
use App\Models\ams\scheduleEmail\amsReportsMetrics;

class AmsReportsMetricsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        amsReportsMetrics::truncate();
        /*******************************campaign metric starts******************************************/
    $sdCompaignMetricsData = $arrayName = array(

   /* [
                                   'metricName'=>'Campaign Id',
                                   'tblColumnName'=>'campaign_id',
                                   'fkParameterType'=>1,
                                   'isActive'=>0
    ],*/
    [
                                    'metricName'=>'Campaign Name',
                                    'tblColumnName'=>'campaign_name',
                                    'fkParameterType'=>1,
                                    'isActive'=>0
    ],
    [
                                    'metricName'=>'Campaign Type',
                                    'tblColumnName'=>'campaign_type',
                                    'fkParameterType'=>1,
                                    'isActive'=>0
    ],
    [
                                    'metricName'=>'Campaign Budget',
                                    'tblColumnName'=>'campaign_budget',
                                    'fkParameterType'=>1,
                                    'isActive'=>0
    ],
    [
                                    'metricName'=>'Impressions',
                                    'tblColumnName'=>'impressions',
                                    'fkParameterType'=>1,
                                    'isActive'=>0
    ],
    [
                                    'metricName'=>'Clicks',
                                    'tblColumnName'=>'clicks',
                                    'fkParameterType'=>1,
                                    'isActive'=>0
    ],
    [
                                    'metricName'=>'Cost',
                                    'tblColumnName'=>'cost',
                                    'fkParameterType'=>1,
                                    'isActive'=>0
    ],
    [
                                    'metricName'=>'Revenue',
                                    'tblColumnName'=>'revenue',
                                    'fkParameterType'=>1,
                                    'isActive'=>0
    ],
    [
                                    'metricName'=>'Order Conversion',
                                    'tblColumnName'=>'order_conversion',
                                    'fkParameterType'=>1,
                                    'isActive'=>0
    ],
    [
                                    'metricName'=>'ACOS',
                                    'tblColumnName'=>'acos',
                                    'fkParameterType'=>1,
                                    'isActive'=>0
    ],
    [
                                    'metricName'=>'CPC',
                                    'tblColumnName'=>'cpc',
                                    'fkParameterType'=>1,
                                    'isActive'=>0
    ],
    [
                                    'metricName'=>'CTR',
                                    'tblColumnName'=>'ctr',
                                    'fkParameterType'=>1,
                                    'isActive'=>0
    ],
    [
                                    'metricName'=>'CPA',
                                    'tblColumnName'=>'cpa',
                                    'fkParameterType'=>1,
                                    'isActive'=>0
    ],
    [
                                    'metricName'=>'ROAS',
                                    'tblColumnName'=>'roas',
                                    'fkParameterType'=>1,
                                    'isActive'=>0
    ]
    );
       $cmpaignMetrics=  amsReportsMetrics::insert($sdCompaignMetricsData);
       /*******************************campaign metric ends******************************************/

        /*******************************Keyword metric starts******************************************/
        $keywordMetricsData = $arrayName = array(
           /* [
                'metricName'=>'Campaign Id',
                'tblColumnName'=>'campaign_id',
                'fkParameterType'=>4,
                'isActive'=>0
            ],*/
            [
                'metricName'=>'Campaign Name',
                'tblColumnName'=>'campaign_name',
                'fkParameterType'=>4,
                'isActive'=>0
            ],
            [
                'metricName'=>'Campaign Type',
                'tblColumnName'=>'campaign_type',
                'fkParameterType'=>4,
                'isActive'=>0
            ],
            [
                'metricName'=>'Ad Group Id',
                'tblColumnName'=>'adGroupId',
                'fkParameterType'=>4,
                'isActive'=>0
            ],
            [
                'metricName'=>'Ad Group Name',
                'tblColumnName'=>'adGroupName',
                'fkParameterType'=>4,
                'isActive'=>0
            ],
            [
                'metricName'=>'Keyword Id',
                'tblColumnName'=>'keywordId',
                'fkParameterType'=>4,
                'isActive'=>0
            ],
            [
                'metricName'=>'Keyword Text',
                'tblColumnName'=>'keywordText',
                'fkParameterType'=>4,
                'isActive'=>0
            ],
            [
                'metricName'=>'Impressions',
                'tblColumnName'=>'impressions',
                'fkParameterType'=>4,
                'isActive'=>0
            ],
            [
                'metricName'=>'Clicks',
                'tblColumnName'=>'clicks',
                'fkParameterType'=>4,
                'isActive'=>0
            ],
            [
                'metricName'=>'Cost',
                'tblColumnName'=>'cost',
                'fkParameterType'=>4,
                'isActive'=>0
            ],
            [
                'metricName'=>'Revenue',
                'tblColumnName'=>'revenue',
                'fkParameterType'=>4,
                'isActive'=>0
            ],
            [
                'metricName'=>'Order Conversion',
                'tblColumnName'=>'order_conversion',
                'fkParameterType'=>4,
                'isActive'=>0
            ],
            [
                'metricName'=>'ACOS',
                'tblColumnName'=>'acos',
                'fkParameterType'=>4,
                'isActive'=>0
            ],
            [
                'metricName'=>'CPC',
                'tblColumnName'=>'cpc',
                'fkParameterType'=>4,
                'isActive'=>0
            ],
            [
                'metricName'=>'CTR',
                'tblColumnName'=>'ctr',
                'fkParameterType'=>4,
                'isActive'=>0
            ],
            [
                'metricName'=>'CPA',
                'tblColumnName'=>'cpa',
                'fkParameterType'=>4,
                'isActive'=>0
            ],
            [
                'metricName'=>'ROAS',
                'tblColumnName'=>'roas',
                'fkParameterType'=>4,
                'isActive'=>0
            ]
        );
        $keywordMetrics = amsReportsMetrics::insert($keywordMetricsData);
        /*******************************Keyword metric starts******************************************/
        /*******************************Ad group metric starts******************************************/
        $adGroupMetricsData = $arrayName = array(
            /* [
                 'metricName'=>'Campaign Id',
                 'tblColumnName'=>'campaign_id',
                 'fkParameterType'=>2,
                 'isActive'=>0
             ],*/
            [
                'metricName'=>'Campaign Name',
                'tblColumnName'=>'campaign_name',
                'fkParameterType'=>2,
                'isActive'=>0
            ],
            [
                'metricName'=>'Campaign Type',
                'tblColumnName'=>'campaign_type',
                'fkParameterType'=>2,
                'isActive'=>0
            ],
            [
                'metricName'=>'Ad Group Id',
                'tblColumnName'=>'adGroupId',
                'fkParameterType'=>2,
                'isActive'=>0
            ],
            [
                'metricName'=>'Ad Group Name',
                'tblColumnName'=>'adGroupName',
                'fkParameterType'=>2,
                'isActive'=>0
            ],
            [
                'metricName'=>'Impressions',
                'tblColumnName'=>'impressions',
                'fkParameterType'=>2,
                'isActive'=>0
            ],
            [
                'metricName'=>'Clicks',
                'tblColumnName'=>'clicks',
                'fkParameterType'=>2,
                'isActive'=>0
            ],
            [
                'metricName'=>'Cost',
                'tblColumnName'=>'cost',
                'fkParameterType'=>2,
                'isActive'=>0
            ],
            [
                'metricName'=>'Revenue',
                'tblColumnName'=>'revenue',
                'fkParameterType'=>2,
                'isActive'=>0
            ],
            [
                'metricName'=>'Order Conversion',
                'tblColumnName'=>'order_conversion',
                'fkParameterType'=>2,
                'isActive'=>0
            ],
            [
                'metricName'=>'ACOS',
                'tblColumnName'=>'acos',
                'fkParameterType'=>2,
                'isActive'=>0
            ],
            [
                'metricName'=>'CPC',
                'tblColumnName'=>'cpc',
                'fkParameterType'=>2,
                'isActive'=>0
            ],
            [
                'metricName'=>'CTR',
                'tblColumnName'=>'ctr',
                'fkParameterType'=>2,
                'isActive'=>0
            ],
            [
                'metricName'=>'CPA',
                'tblColumnName'=>'cpa',
                'fkParameterType'=>2,
                'isActive'=>0
            ],
            [
                'metricName'=>'ROAS',
                'tblColumnName'=>'roas',
                'fkParameterType'=>2,
                'isActive'=>0
            ]
        );
        $adGroupMetrics = amsReportsMetrics::insert($adGroupMetricsData);
        /*******************************Ad group metric starts******************************************/
        /*******************************Ad Product Ads starts******************************************/
        $productAdsMetricsData = $arrayName = array(
            /* [
                 'metricName'=>'Campaign Id',
                 'tblColumnName'=>'campaign_id',
                 'fkParameterType'=>3,
                 'isActive'=>0
             ],*/
            [
                'metricName'=>'Campaign Name',
                'tblColumnName'=>'campaign_name',
                'fkParameterType'=>3,
                'isActive'=>0
            ],
            [
                'metricName'=>'Campaign Type',
                'tblColumnName'=>'campaign_type',
                'fkParameterType'=>3,
                'isActive'=>0
            ],
            [
                'metricName'=>'adGroupId',
                'tblColumnName'=>'adGroupId',
                'fkParameterType'=>3,
                'isActive'=>0
            ],
            [
                'metricName'=>'Ad Group Name',
                'tblColumnName'=>'adGroupName',
                'fkParameterType'=>3,
                'isActive'=>0
            ],
            [
                'metricName'=>'Ad Id',
                'tblColumnName'=>'ad_id',
                'fkParameterType'=>3,
                'isActive'=>0
            ],
            [
                'metricName'=>'Asin',
                'tblColumnName'=>'asin',
                'fkParameterType'=>3,
                'isActive'=>0
            ],
            [
                'metricName'=>'Sku',
                'tblColumnName'=>'sku',
                'fkParameterType'=>3,
                'isActive'=>0
            ],
            [
                'metricName'=>'Impressions',
                'tblColumnName'=>'impressions',
                'fkParameterType'=>3,
                'isActive'=>0
            ],
            [
                'metricName'=>'Clicks',
                'tblColumnName'=>'clicks',
                'fkParameterType'=>3,
                'isActive'=>0
            ],
            [
                'metricName'=>'Cost',
                'tblColumnName'=>'cost',
                'fkParameterType'=>3,
                'isActive'=>0
            ],
            [
                'metricName'=>'Revenue',
                'tblColumnName'=>'revenue',
                'fkParameterType'=>3,
                'isActive'=>0
            ],
            [
                'metricName'=>'Order Conversion',
                'tblColumnName'=>'order_conversion',
                'fkParameterType'=>3,
                'isActive'=>0
            ],
            [
                'metricName'=>'ACOS',
                'tblColumnName'=>'acos',
                'fkParameterType'=>3,
                'isActive'=>0
            ],
            [
                'metricName'=>'CPC',
                'tblColumnName'=>'cpc',
                'fkParameterType'=>3,
                'isActive'=>0
            ],
            [
                'metricName'=>'CTR',
                'tblColumnName'=>'ctr',
                'fkParameterType'=>3,
                'isActive'=>0
            ],
            [
                'metricName'=>'CPA',
                'tblColumnName'=>'cpa',
                'fkParameterType'=>3,
                'isActive'=>0
            ],
            [
                'metricName'=>'ROAS',
                'tblColumnName'=>'roas',
                'fkParameterType'=>3,
                'isActive'=>0
            ]
        );
        $productAdsMetrics = amsReportsMetrics::insert($productAdsMetricsData);
        /*******************************Product Ads metric starts******************************************/
        /*******************************ASIN Ads starts******************************************/
        $asinMetricsData = $arrayName = array(
            [
                'metricName'=>'Campaign Name',
                'tblColumnName'=>'campaign_name',
                'fkParameterType'=>5,
                'isActive'=>0
            ],
            [
                'metricName'=>'Campaign Type',
                'tblColumnName'=>'campaign_type',
                'fkParameterType'=>5,
                'isActive'=>0
            ],
            [
                'metricName'=>'adGroupId',
                'tblColumnName'=>'adGroupId',
                'fkParameterType'=>5,
                'isActive'=>0
            ],
            [
                'metricName'=>'Ad Group Name',
                'tblColumnName'=>'adGroupName',
                'fkParameterType'=>5,
                'isActive'=>0
            ],
            [
                'metricName'=>'Keyword Id',
                'tblColumnName'=>'KeywordId',
                'fkParameterType'=>5,
                'isActive'=>0
            ],
            [
                'metricName'=>'Keyword Text',
                'tblColumnName'=>'KeywordText',
                'fkParameterType'=>5,
                'isActive'=>0
            ],
            [
                'metricName'=>'Asin',
                'tblColumnName'=>'asin',
                'fkParameterType'=>5,
                'isActive'=>0
            ],
            [
                'metricName'=>'Other Asin',
                'tblColumnName'=>'other_asin',
                'fkParameterType'=>5,
                'isActive'=>0
            ],
            [
                'metricName'=>'Sku',
                'tblColumnName'=>'sku',
                'fkParameterType'=>5,
                'isActive'=>0
            ],
            [
                'metricName'=>'Currency',
                'tblColumnName'=>'currency',
                'fkParameterType'=>5,
                'isActive'=>0
            ],
            [
                'metricName'=>'Match Type',
                'tblColumnName'=>'match_type',
                'fkParameterType'=>5,
                'isActive'=>0
            ],
            [
                'metricName'=>'Attributed Units Ordered',
                'tblColumnName'=>'attributed_units_ordered',
                'fkParameterType'=>5,
                'isActive'=>0
            ],
            [
                'metricName'=>'Sales Other Sku',
                'tblColumnName'=>'sales_other_sku',
                'fkParameterType'=>5,
                'isActive'=>0
            ],
            [
                'metricName'=>'Units Ordered Other Sku',
                'tblColumnName'=>'units_ordered_other_sku',
                'fkParameterType'=>5,
                'isActive'=>0
            ]
        );
        $asinMetrics = amsReportsMetrics::insert($asinMetricsData);
        /*******************************ASIN metric starts******************************************/

    }
}
