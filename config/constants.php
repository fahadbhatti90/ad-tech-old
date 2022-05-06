<?php
return [
    'delayTimeInApi' => 3000, // The number of milliseconds to delay, URL::http://docs.guzzlephp.org/en/stable/request-options.html#delay
    'connectTimeOutInApi' => 30, // Timeout if the client fails to connect to the server, URL::http://docs.guzzlephp.org/en/stable/request-options.html#connect-timeout
    'timeoutInApi' => 60, // Timeout if a server does not return a response . http://docs.guzzlephp.org/en/stable/request-options.html#timeout
    'sleepTime' => 3,
    'apiVersion' => 'v2',
    'nextDateTimeFormat' => date('Y-m-d H:i:s', strtotime('+1 day', time())),
    'lastDateTimeFormat' => date('Y-m-d H:i:s', strtotime('-1 day', time())),
    'dateTimeFormat' => date('Y-m-d H:i:s'),
    'refresh_token' => 'Atzr|IwEBINe7Jvf1FrgMlQypnICjRSqpueThtDgQqoTYLXLLiYqgFC0CdWJUsDuP2o4wbZalyVTH0KqqFl6NmBqTJtajX7LYpcIXup4tl_fzvtQ3zDrt92Bqly3r5X4ffbRU8mHsdZfDeZSQjLaiviCtx9J_7ANJq-XewQIZ0H_lQWD1yoBt0CueeGhAIgUtJPfONrjQlGy006TiTsilnX9fIP98BLaNq4ZYHKOQZyxL0vABxuu0TRBU_kNJjGRiuRVdPiCQt6SWy0_BDzC0GhZA68C5eRf2NWoxHd2TDF3hQvFTC-QlLVygOAQoQP5j__rivIjbXR8FJgjdcI-0iJOEi0o-06FOie48MFFKQOtqQB48B0vaMSOhSIApw6cNoaHnEVOMmgSNjpWsLRdelIe-fKwRHHnInQZZpsiT5-_PyfRCIQq5EyLol7r6Uzf8N9ZgIrh35YoCsPKqdTLSmKw9TIO6Mh8uAnppMHJhkqf3mbPko3cQxolvg5i0UFyBvpdiI4e2AkZgPr6C9obpU9j25EOLxApt09PKwV7bkWRt_--2-14SKtcgTfi6Bgf8ADROJo-hK2Z-WPBjBPSnQeymQATOnv-ZMhxbE7xqGw2c9EpKjWVoLsviVqFd3syrAojgsCQDFUYZJ8Bv-dqaNSSfGcvdKQwd',
    'ReportDate' => date_format(date_sub(date_create(date("Ymd")), date_interval_create_from_date_string("1 day")), "Ymd"),
    'dayFormat' => date('Y-m-d'),
    'amsApiUrl' => 'https://advertising-api.amazon.com',
    'testingAmsApiUrl' => 'https://advertising-api-test.amazon.com',
    'amsAuthUrl' => 'https://api.amazon.com/auth/o2/token',
    'amsProfileUrl' => 'profiles',
    'amshsaCampaignUrl' => 'hsa/campaigns',
    'SPCampaignReport' => 'sp/campaigns/report',
    'SDCampaignReport' => 'sd/campaigns/report',
    'SDproductAdsReport' => 'sd/productAds/report',
    'SDadGroupsReport' => 'sd/adGroups/report',
    'HSACampaignReport' => 'hsa/campaigns/report',
    'HSAKeywordReport' => 'hsa/keywords/report',
    'spKeywordList' => 'sp/keywords/extended',
    'spKeywordUpdateBid' => 'sp/keywords',
    'sbKeywordList' => 'sb/keywords',
    'sdTargetsList' => 'sd/targets',
    'spKeywordReport' => 'sp/keywords/report',
    'productAdsReport' => 'sp/productAds/report',
    'targetsReport' => 'sp/targets/report',
    'targetsReportSb' => 'hsa/targets/report',
    'adGroupsReport' => 'sp/adGroups/report',
    'adGroupsReportSb' => 'hsa/adGroups/report',
    'ASINsReport' => 'asins/report',
    'downloadReport' => 'reports',
    'amsPortfolioUrl' => 'portfolios',
    'spCampaignUrl' => 'sp/campaigns',
    'sdCampaignUrl' => 'sd/campaigns',
    'sbCampaignUrl' => 'sb/campaigns',
    'portfolioSponsoredBrand' => 'sponsoredBrand',
    'portfolioSponsoredDisplay' => 'sponsoredDisplay',
    'portfolioSponsoredProduct' => 'sponsoredProducts',
    // Sponsored Products Campaign Metrics List
    'spCampaignMetrics' => 'bidPlus,campaignName,campaignId,campaignStatus,campaignBudget,impressions,clicks,cost,portfolioId,portfolioName,attributedConversions1d,attributedConversions7d,attributedConversions14d,attributedConversions30d,attributedConversions1dSameSKU,attributedConversions7dSameSKU,attributedConversions14dSameSKU,attributedConversions30dSameSKU,attributedUnitsOrdered1d,attributedUnitsOrdered7d,attributedUnitsOrdered14d,attributedUnitsOrdered30d,attributedSales1d,attributedSales7d,attributedSales14d,attributedSales30d,attributedSales1dSameSKU,attributedSales7dSameSKU,attributedSales14dSameSKU,attributedSales30dSameSKU,attributedUnitsOrdered1dSameSKU,attributedUnitsOrdered7dSameSKU,attributedUnitsOrdered14dSameSKU,attributedUnitsOrdered30dSameSKU',
    // Sponsored Products AdGroup Metrics List
    'spAdGroupMetrics' => 'campaignName,campaignId,adGroupName,adGroupId,impressions,clicks,cost,attributedConversions1d,attributedConversions7d,attributedConversions14d,attributedConversions30d,attributedConversions1dSameSKU,attributedConversions7dSameSKU,attributedConversions14dSameSKU,attributedConversions30dSameSKU,attributedUnitsOrdered1d,attributedUnitsOrdered7d,attributedUnitsOrdered14d,attributedUnitsOrdered30d,attributedSales1d,attributedSales7d,attributedSales14d,attributedSales30d,attributedSales1dSameSKU,attributedSales7dSameSKU,attributedSales14dSameSKU,attributedSales30dSameSKU',
    // Sponsored Brands Keyword Metrics List
    'sbKeywordMetrics' => 'campaignName,campaignId,campaignStatus,campaignBudget,campaignBudgetType,adGroupName,adGroupId,keywordId,keywordText,matchType,impressions,clicks,cost,attributedSales14d,attributedSales14dSameSKU,attributedConversions14d,attributedConversions14dSameSKU,attributedOrdersNewToBrand14d,attributedOrdersNewToBrandPercentage14d,attributedOrderRateNewToBrand14d,attributedSalesNewToBrand14d,attributedSalesNewToBrandPercentage14d,attributedUnitsOrderedNewToBrand14d,attributedUnitsOrderedNewToBrandPercentage14d,keywordBid,keywordStatus,targetId,targetingExpression,targetingText,targetingType,attributedDetailPageViewsClicks14d,unitsSold14d,dpv14d',
    // Sponsored Products Product Targeting Metrics List
    'productTargetingMetrics' => 'campaignName,campaignId,targetId,targetingExpression,targetingText,targetingType,impressions,clicks,cost,attributedConversions1d,attributedConversions7d,attributedConversions14d,attributedConversions30d,attributedConversions1dSameSKU,attributedConversions7dSameSKU,attributedConversions14dSameSKU,attributedConversions30dSameSKU,attributedUnitsOrdered1d,attributedUnitsOrdered7d,attributedUnitsOrdered14d,attributedUnitsOrdered30d,attributedSales1d,attributedSales7d,attributedSales14d,attributedSales30d,attributedSales1dSameSKU,attributedSales7dSameSKU,attributedSales14dSameSKU,attributedSales30dSameSKU',
    // Sponsored Products Products Ads Metrics List
    'productAdsMetrics' => 'campaignName,campaignId,adGroupName,adGroupId,impressions,clicks,cost,currency,asin,attributedConversions1d,attributedConversions7d,attributedConversions14d,attributedConversions30d,attributedConversions1dSameSKU,attributedConversions7dSameSKU,attributedConversions14dSameSKU,attributedConversions30dSameSKU,attributedUnitsOrdered1d,attributedUnitsOrdered7d,attributedUnitsOrdered14d,attributedUnitsOrdered30d,attributedSales1d,attributedSales7d,attributedSales14d,attributedSales30d,attributedSales1dSameSKU,attributedSales7dSameSKU,attributedSales14dSameSKU,attributedSales30dSameSKU,attributedUnitsOrdered1dSameSKU,attributedUnitsOrdered7dSameSKU,attributedUnitsOrdered14dSameSKU,attributedUnitsOrdered30dSameSKU',
    /****************************************************************
     * Sponsored Products Products Ads Metrics List With SKU Field
     * **************************************************************/
    'productAdsMetricsSKU' => 'campaignName,campaignId,adGroupName,adGroupId,impressions,clicks,cost,currency,asin,sku,attributedConversions1d,attributedConversions7d,attributedConversions14d,attributedConversions30d,attributedConversions1dSameSKU,attributedConversions7dSameSKU,attributedConversions14dSameSKU,attributedConversions30dSameSKU,attributedUnitsOrdered1d,attributedUnitsOrdered7d,attributedUnitsOrdered14d,attributedUnitsOrdered30d,attributedSales1d,attributedSales7d,attributedSales14d,attributedSales30d,attributedSales1dSameSKU,attributedSales7dSameSKU,attributedSales14dSameSKU,attributedSales30dSameSKU,attributedUnitsOrdered1dSameSKU,attributedUnitsOrdered7dSameSKU,attributedUnitsOrdered14dSameSKU,attributedUnitsOrdered30dSameSKU',
    // Sponsored Products Keyword Metrics List
    'spKeywordMetrics' => 'campaignName,campaignId,adGroupName,adGroupId,keywordId,keywordText,matchType,impressions,clicks,cost,attributedConversions1d,attributedConversions7d,attributedConversions14d,attributedConversions30d,attributedConversions1dSameSKU,attributedConversions7dSameSKU,attributedConversions14dSameSKU,attributedConversions30dSameSKU,attributedUnitsOrdered1d,attributedUnitsOrdered7d,attributedUnitsOrdered14d,attributedUnitsOrdered30d,attributedSales1d,attributedSales7d,attributedSales14d,attributedSales30d,attributedSales1dSameSKU,attributedSales7dSameSKU,attributedSales14dSameSKU,attributedSales30dSameSKU',
    // Sponsored Products ASIN Reports
    'asinsReportsMetrics' => 'campaignName,campaignId,adGroupName,adGroupId,keywordId,keywordText,asin,otherAsin,currency,matchType,attributedUnitsOrdered1d,attributedUnitsOrdered7d,attributedUnitsOrdered14d,attributedUnitsOrdered30d,attributedUnitsOrdered1dOtherSKU,attributedUnitsOrdered7dOtherSKU,attributedUnitsOrdered14dOtherSKU,attributedUnitsOrdered30dOtherSKU,attributedSales1dOtherSKU,attributedSales7dOtherSKU,attributedSales14dOtherSKU,attributedSales30dOtherSKU',
    /****************************************************************
     * Sponsored Products ASIN Reports Metrics List With SKU Field
     * **************************************************************/
    'asinsReportsMetricsSKU' => 'campaignName,campaignId,adGroupName,adGroupId,keywordId,keywordText,asin,otherAsin,sku,currency,matchType,attributedUnitsOrdered1d,attributedUnitsOrdered7d,attributedUnitsOrdered14d,attributedUnitsOrdered30d,attributedUnitsOrdered1dOtherSKU,attributedUnitsOrdered7dOtherSKU,attributedUnitsOrdered14dOtherSKU,attributedUnitsOrdered30dOtherSKU,attributedSales1dOtherSKU,attributedSales7dOtherSKU,attributedSales14dOtherSKU,attributedSales30dOtherSKU',
    /****************************************************************
     * Sponsored Brand Campaign Reports Metrics List
     * **************************************************************/
    'sbCampaignMetrics' => 'campaignName,campaignId,campaignStatus,campaignBudget,campaignBudgetType,impressions,clicks,cost,attributedDetailPageViewsClicks14d,attributedSales14d,attributedSales14dSameSKU,attributedConversions14d,attributedConversions14dSameSKU,attributedOrdersNewToBrand14d,attributedOrdersNewToBrandPercentage14d,attributedOrderRateNewToBrand14d,attributedSalesNewToBrand14d,attributedSalesNewToBrandPercentage14d,attributedUnitsOrderedNewToBrand14d,attributedUnitsOrderedNewToBrandPercentage14d,unitsSold14d,dpv14d',
    /****************************************************************
     * Sponsored Display Campaign Reports Metrics List
     * **************************************************************/
    'sdCampaignMetrics' => 'campaignName,campaignId,campaignStatus,currency,impressions,clicks,cost,attributedDPV14d,attributedUnitsSold14d,attributedSales14d',
    /****************************************************************
    * Sponsored Display productAds Reports Metrics List
     * **************************************************************/
    'sdProductAdsMetrics' => 'adGroupName,adGroupId,asin,sku,campaignName,campaignId,impressions,clicks,cost,currency,attributedConversions1d,attributedConversions7d,attributedConversions14d,attributedConversions30d,attributedConversions1dSameSKU,attributedConversions7dSameSKU,attributedConversions14dSameSKU,attributedConversions30dSameSKU,attributedUnitsOrdered1d,attributedUnitsOrdered7d,attributedUnitsOrdered14d,attributedUnitsOrdered30d,attributedSales1d,attributedSales7d,attributedSales14d,attributedSales30d,attributedSales1dSameSKU,attributedSales7dSameSKU,attributedSales14dSameSKU,attributedSales30dSameSKU',
    /****************************************************************
    * Sponsored Display AdGroup Reports Metrics List
    * **************************************************************/
    'sdAdGroupMetrics' => 'adGroupName,adGroupId,campaignName,campaignId,impressions,clicks,cost,currency,attributedConversions1d,attributedConversions7d,attributedConversions14d,attributedConversions30d,attributedConversions1dSameSKU,attributedConversions7dSameSKU,attributedConversions14dSameSKU,attributedConversions30dSameSKU,attributedUnitsOrdered1d,attributedUnitsOrdered7d,attributedUnitsOrdered14d,attributedUnitsOrdered30d,attributedSales1d,attributedSales7d,attributedSales14d,attributedSales30d,attributedSales1dSameSKU,attributedSales7dSameSKU,attributedSales14dSameSKU,attributedSales30dSameSKU',
    /****************************************************************
     * Sponsored Brand AdGroup Reports Metrics List
     * **************************************************************/
    'sbAdGroupMetrics' => 'campaignId,campaignName,campaignBudget,campaignBudgetType,campaignStatus,adGroupName,adGroupId,impressions,clicks,cost,attributedDetailPageViewsClicks14d,attributedSales14d,attributedSales14dSameSKU,attributedConversions14d,attributedConversions14dSameSKU,attributedOrdersNewToBrand14d,attributedOrdersNewToBrandPercentage14d,attributedOrderRateNewToBrand14d,attributedSalesNewToBrand14d,attributedSalesNewToBrandPercentage14d,attributedUnitsOrderedNewToBrand14d,attributedUnitsOrderedNewToBrandPercentage14d,unitsSold14d,dpv14d',
    /****************************************************************
     * Sponsored Brand Targeting Reports Metrics List
     * **************************************************************/
    'sbTargetingMetrics' => 'campaignId,campaignName,adGroupId,adGroupName,campaignBudgetType,campaignStatus,targetId,targetingExpression,targetingType,targetingText,impressions,clicks,cost,attributedDetailPageViewsClicks14d,attributedSales14d,attributedSales14dSameSKU,attributedConversions14d,attributedConversions14dSameSKU,attributedOrdersNewToBrand14d,attributedOrdersNewToBrandPercentage14d,attributedOrderRateNewToBrand14d,attributedSalesNewToBrand14d,attributedSalesNewToBrandPercentage14d,attributedUnitsOrderedNewToBrand14d,attributedUnitsOrderedNewToBrandPercentage14d,unitsSold14d,dpv14d',
    //------------------------------------------------------------------------------------
];