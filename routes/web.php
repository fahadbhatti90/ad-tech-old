<?php


/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
 */

// This route is used to remove application cache

Route::get('/test', function () {
});

Route::get('/clear-cache', function () {
    Artisan::call('cache:clear');
    return "Cache is cleared";
});
Route::get('/customlogin', 'AdminController@login')->name('login');
Route::get('/',  function () {
    return view('reactUI.index');
  });
Route::get("/getEvents","ClientAuth\ClientController@getDailyEvents");

Route::post('/login', 'AdminController@authenticate')->name("admin.login");
Route::get('/logout', 'AdminController@logout')->name("helotek.logout");
Route::prefix("client")->group(function () {
    Route::get("/login", "ClientAuth\ClientLoginController@ShowLoginForm")->name("client.login");
    Route::post("/login", "ClientAuth\ClientLoginController@login")->name("client.login.submit");
    Route::get('/logout', 'ClientAuth\ClientLoginController@logout')->name("client.logout");

    Route::get('products', 'ClientAuth\ClientController@products');
    Route::get('/productList', 'ClientAuth\ClientController@productList');
    Route::get('/productListForDemo', 'ClientAuth\ClientController@productListForDemo');

    Route::get('/events', "ClientAuth\NotesController@events")->name('clients.notes'); //for loading Product Preview Manager view
    Route::get('/eventsData', "ClientAuth\NotesController@eventsData")->name('clients.notes'); //for loading Product Preview Manager view
    Route::get('/events/logs/{id?}', "ClientAuth\NotesController@eventsLogs")->name('clients.notes'); //for loading Product Preview Manager view
    Route::get('/events/delete/{id}',  'ClientAuth\NotesController@deleteEventLog');//For Deleting Schedule
    Route::post('/addEventLogs', "ClientAuth\NotesController@manageNotes")->name("manage.notes"); //for add,Update
    Route::get('/getRequiredData', 'ClientAuth\NotesController@getRequiredData'); //For loading data in dropdowns

    Route::get('/productTable', "ClientAuth\ProductTableController@productTable")->name('clients.productTable'); //for loading Product Preview Manager view
    // Route::delete('/{account}/deleteProductPreview',  'NotesController@deleteProductPreview');//For Deleting Schedule
    Route::post('/manageproductTable', "ClientAuth\ProductTableController@manageproductTable")->name("manage.productTable"); //for add,Update
    Route::post('/productTable/getRequiredData', 'ClientAuth\ProductTableController@getRequiredData'); //For loading data in dropdowns

    Route::get("/dashboard", "ClientAuth\ClientController@dashboard")->name("client.dashboard");

    Route::get("/getSubCategoryByCategory", "ClientAuth\ClientController@getSubCategoryByCategory")->name("client.category");
    Route::get('/getAsinBySubCategory', "ClientAuth\ClientController@getAsinBySubCategory");
    Route::get("/getDataByAsin", "ClientAuth\ClientController@getDataByAsin")->name("client.asin");
    Route::get('/getDataByDateFilter', "ClientAuth\ClientController@getDataByDateFilter");
    Route::get('/getAvailableMonths', "ClientAuth\ClientController@getAvailableMonths");
    Route::get('/getDailyCompCards', "ClientAuth\ClientController@getDailyCompCards");

    Route::get('/getNotifications', "ClientAuth\NotesController@getNotificaitons");
    Route::get('/notifications/{notification}/preview/', "ClientAuth\NotesController@previewNotificaiton")->name("notificationPreview");
    Route::get('/notifications/{notification}/download', "ClientAuth\NotesController@DownloadNotificationDetailsImprovised")->name("client.notificationDownload");
    Route::post('/notifications/readAll', "ClientAuth\NotesController@UpdateNotificationsStatus");

    Route::get('/getNavigationData', "ClientAuth\NotesController@getNavigationData");

    Route::get('products', 'ClientAuth\ClientController@products');
    Route::post('/productList', 'ClientAuth\ClientController@productList');

    Route::group(['prefix' => 'tags'], function () {
        Route::get('/', "ClientAuth\ProductTableTagsController@getAllTags");
        Route::get('/filter', "ClientAuth\ProductTableTagsController@getAllTagsForFilter");
        Route::get('/getAllTagsToDelete', "ClientAuth\ProductTableTagsController@getAllTagsToDelete");
        Route::post('/singleUnAssign', "ClientAuth\ProductTableTagsController@unAssignSingleTag")->name("productTable.tag.single.unassign");
        Route::post('/add', "ClientAuth\ProductTableTagsController@addTag");
        Route::get('/{tag}/edit', "ClientAuth\ProductTableTagsController@editTag");
        Route::post('/asign', "ClientAuth\ProductTableTagsController@asignTag");
        Route::get('/{tag}/delete', "ClientAuth\ProductTableTagsController@deleteTag");
    });

    Route::group(['prefix' => 'campaign'], function () {
        Route::get('/tags', 'ClientAuth\CompaignTaggingController@compaignStrategyType')->name("campaign.tags");
        Route::get('/getCampaignList', 'ClientAuth\CompaignTaggingController@compaignList')->name("capaign.strategyType.getAllCampaigns");
        Route::get('/productType', 'ClientAuth\CompaignTaggingController@compaignProductType')->name("campaign.productType");
        Route::post('/productType', 'ClientAuth\CompaignTaggingController@asinList')->name("capaign.productType.getAllCampaigns");;

        Route::get('/strategy/tags', "ClientAuth\CompaignTaggingController@getAllTags")->name("campaign.strategyType.getAllTags");
        Route::get('/strategy/tags/getAllTagsToDelete', "ClientAuth\CompaignTaggingController@getAllTagsToDelete")->name("campaign.strategyType.unAssignedTags");
        Route::post('/strategy/tags/singleUnAssign', "ClientAuth\CompaignTaggingController@unAssignSingleTag")->name("campaign.tag.single.unassign");
        Route::post('/strategy/tags/add', "ClientAuth\CompaignTaggingController@addTag")->name("campaign.strategyType.addTag");
        Route::get('/strategy/{tag}/edit', "ClientAuth\CompaignTaggingController@editTag")->name("campaign.strategyType.editTag");
        Route::post('/strategy/tags/asign', "ClientAuth\CompaignTaggingController@asignTag")->name("campaign.strategyType.assignTag");
        Route::get('/strategy/tags/{tag}/delete', "ClientAuth\CompaignTaggingController@deleteTag")->name("campaign.strategyType.deleteTag");

    });
}); //end route group
Route::get('getGraphData/', "ClientAuth\ClientController@getDataByCategories");
/**
 *
 * Add all routes related to Super Admin in this middleware
 *
 */
Route::group(['middleware' => ['auth.super_admin']], function () {
// Main Admin Page
    /*super admin dashboard route*/
    Route::get('/dashboard', 'SuperAdminController@dashboard')->name('adminDashboard');
    /*Amazon Marketing Services Routes starts*/
    Route::get('/ams-scheduling', 'AMSController@scheduling')->name('amsScheduling');
    Route::post('/ams-cronCall', 'AMSController@CronCall');
    /*Amazon Marketing Services Routes ends*/
    /*seller central routes starts*/
    Route::get('/mws-scheduling', 'MWSController@scheduling')->name('mwsScheduling');
    Route::post('/mws-addCron', 'MWSController@addCron');
    Route::post('/mws-editCron', 'MWSController@editCron');
    Route::get('/mws-testCron', 'MWSController@testCron');
    Route::post('/mws-changeCronStatus', 'MWSController@changeCronStatus');
    Route::get('/mws-deleteCron/{id}', 'MWSController@deleteCron');
    Route::get('/mws-runCron', 'MWSController@runCron');
    /*seller central routes ends*/
});

Route::get('superadmin/getNotifications', "SuperAdminNotificationController@getNotificaitons");
Route::get('superadmin/notifications/{notification}/preview/', "SuperAdminNotificationController@previewNotificaiton");
Route::get('superadmin/notifications/{notification}/download', "SuperAdminNotificationController@DownloadNotificationDetailsImprovised");
Route::post('superadmin/notifications/readAll', "SuperAdminNotificationController@UpdateNotificationsStatus");

Route::get('superadmin/getNavigationData', "SuperAdminNotificationController@getNavigationData");
/**
 *
 * Add all routes related to Admin in this middleware
 *
 */
Route::group(['middleware' => ['auth.admin']], function () {
    /**
     * Vendor Central Routes
     */
    //  Dashboard Route
    Route::get('/vc/dashboard', 'VCController@dashboard')->name('vcDashboard');

    // Sales
    Route::get('/vc/dailysales', 'VCController@dailySalesView')->name('dailySalesView');
    //   Route::post('/vc/dailysales', 'VCController@dailySalesStoreRecords')->name('dailySalesStore');

    // Purchase Order PO
    Route::get('/vc/purchaseorder', 'VCController@purchaseOrderView')->name('purchaseOrder');
    //    Route::post('/vc/purchaseorder', 'VCController@purchaseOrderStoreRecords');

    // Inventory
    Route::get('/vc/dailyinventory', 'VCController@dailyInventoryView')->name('dailyInventory');
    //Route::post('/vc/dailyinventory', 'VCController@dailyInventoryStoreRecords');

    // Traffic
    Route::get('/vc/traffic', 'VCController@trafficView')->name('traffic');
    //Route::post('/vc/traffic', 'VCController@trafficStoreRecords');

    // forecast
    Route::get('/vc/forecast', 'VCController@forecastView')->name('forecast');
    Route::post('/vc/forecast', 'VCController@forecastStoreRecords');

    // Product Catalog
    Route::get('/vc/catalog', 'VCController@productCatalogView')->name('catalog');
    //  Route::post('/vc/catalog', 'VCController@productCatalogStoreRecords');

        // Vendor
    Route::get('/vc/vendors', 'VCController@vendorsView')->name('vendorsAdd');
   // Route::post('/vc/vendors', 'VCController@vendorsAdd');

    // Historical Data
    Route::get('/vc/history', 'VCController@showHistoryForm')->name('HistoryForm');
    //  Route::post('/vc/history', 'VCController@historicalDataDownload');
    // Delete Specific Data
    Route::get('/vc/delete', 'VCController@deleteView')->name('verifyFrom');
    //    Route::post('/vc/delete', 'VCController@deleteStoreRecords');
    //    Route::post('/vc/verify', 'VCController@verifyStoredRecords');
    //    Route::post('/vc/move', 'VCController@moveStoredRecords');

    // Historical Data Download
    Route::get('/vc-download/{reportType}/{startDate}/{endDate}', "VCController@historicalDataDownloadCSV")->name("vcDownload");

    /* Scrap Catalog */
    //Route::get('/vc/scrapcatalog','VCScrapController@scrapCatalogView')->name('scrapCatalog');
    //Route::post('/vc/scrapcatalog','VCScrapController@scrapCatalogStore')->name('scrap_catalog');

    /**
     * Amazon Marketing Services Routes
     */
    Route::get('/ams-runApi', 'AMSController@runApi')->name('runApi');
    Route::get('/ams-dashboard', 'AMSController@dashboard')->name('amsDashboard');
    Route::get('/ams-apiconfig', 'AMSController@apiConfig')->name('amsApiConfig');
    Route::get('/ams-handle_login', 'AMSController@AccountSetup');
    Route::get('/ams-export-csv', "AMSController@showHistoryFrom")->name("amsHistoryForm");
    Route::get('/ams-download/{reportType}/{startDate}/{endDate}', "AMSController@downloadCSV")->name("amsDownload");
    // POST METHOD FOR AMS REQUEST
    Route::post('/ams-addConfig', 'AMSController@addConfig');
    Route::post('/ams-deleteConfig', 'AMSController@deleteConfig');
    Route::post('/ams-editConfig', 'AMSController@editConfig');
    Route::post('/ams-checkHistory', "AMSController@checkHistory");


    /*Amazon Marketing Services Routes Ends*/
    /*Seller Central Routes Starts*/

    Route::get('/mws/dashboard', 'MWSController@dashboard')->name('mwsDashboard');
    Route::get('/mws/apiconfig', 'MWSController@apiconfig')->name('mwsApiConfig');
    Route::post('/mws/addConfig', 'MWSController@addConfig');
    Route::post('/mws/editConfig', 'MWSController@editConfig');
    Route::POST('/mws/deleteApiConfig', 'MWSController@deleteApiConfig')->name('deleteApiConfig');

    Route::get('/mws/history', "MWSController@scHistoryForm")->name("scHistoryForm");
    Route::post('/mws/checkScHistory', "MWSController@checkScHistory")->name("checkScHistory");
    Route::get('/mws/download/{report_type}/{startDate}/{endDate}', "MWSController@scDownloadCsv")->name("scDownloadCsv");
    Route::get('/mws/set_config', 'MWSController@set_config');
    /*MWS API ROUTS*/
    Route::get('/mws/MWSReportRequest', 'MWSController@MWSReportRequest')->name('MWSReportRequest');
    Route::get('/mws/MWSGetReportRequestList', 'MWSController@MWSGetReportRequestList')->name('MWSGetReportRequestList');
    Route::get('/mws/MWSGetReportList', 'MWSController@MWSGetReportList')->name('MWSGetReportList');
    Route::get('/mws/MWSGetReport', 'MWSController@MWSGetReport')->name('MWSGetReport');
    Route::get('/mws/acknowledgeReports', 'MWSController@acknowledgeReports')->name('acknowledgeReports');
    /*Seller Central Routes Ends*/
    
});
/***************************************Label Override**************************************/
Route::group(['prefix' => 'admin/labelOverride'], function () {
    Route::get("/","Admin\LabelOverrideController@attributes")->name("label.override.attributes");
    Route::get('/data', 'Admin\LabelOverrideController@getAttributesData')->name("label.override.data");
    Route::get('/download/{type}/attribute', 'Admin\LabelOverrideController@downloadAttribute')->name("download.attribute.data");
    Route::post('/addAlias', 'Admin\LabelOverrideController@addLabel')->name("label.override.post.data");
    Route::post('/uploadAlias', 'Admin\LabelOverrideController@AliasBulkInsertion')->name("label.override.upload.alias");
});
/***************************************Label Override**************************************/
/* Scrap Catalog
this module is stopped by client
 */
//Route::get('/vc/scrapcatalog','VCScrapController@scrapCatalogView')->name('scrapCatalog');
//Route::post('/vc/scrapcatalog','VCScrapController@scrapCatalogStore')->name('scrap_catalog');

/**
 *
 * Add all routes related to manager in this middleware
 *
 */

Route::group(['middleware' => ['auth.manager']], function () {
    // ------------ Day Parting ----------------- //
    Route::prefix("dayParting")->group(function () {
        Route::post('/getCampaignPortfolioData', 'DayParting@getCampaignPortfolioData')->name("getCmPfData");
        Route::get("/schedule", "DayParting@showScheduleForm")->name("dayPartingSchedule");
        Route::post("/schedule", "DayParting@storeScheduleForm")->name("dayPartingSchedule.submit");
        Route::get('/scheduleList', 'DayParting@getScheduleList')->name('getScheduleList');
        Route::post("/editSchedule", "DayParting@showEditScheduleForm")->name("dayPartingSchedule.edit");
        Route::post("/editScheduleSubmit", "DayParting@editScheduleForm")->name("dayPartingSchedule.submit");
        Route::post("/deleteSchedule", "DayParting@deleteSchedule")->name("deleteSchedule");
        Route::post("/stopSchedule", "DayParting@stopSchedule");
        Route::post("/startSchedule", "DayParting@startSchedule");
        Route::get("/history", "DayParting@showHistoryForm")->name("dayPartingHistory");
        Route::get('/day-parting-profile', 'DayParting@getProfileList')->name("day-parting-profile");
        //Route::get("/getHistorySchedule", "DayParting@getHistoryScheduleData")->name("dayPartingHistoryScheduleCalender");
        Route::post("/getHistorySchedule", "DayParting@getHistoryScheduleData")->name("dayPartingHistoryScheduleCalender");
    });//end route Day Parting
    // ------------ Biding Rule ----------------- //
    Route::prefix("bidding-rule")->group(function () {
        Route::get('/dashboard', 'BiddingController@index')->name("bidding-rule");
        Route::post('/store-rules', 'BiddingController@storeRules')->name("store-rule");
        Route::get('/bidding-profile/{id?}', 'BiddingController@getProfileList')->name("bidding-profile");
        Route::post('/change-brand', 'BiddingController@changeBrand')->name("change-brand");
        Route::post('/only-store-rules', 'BiddingController@onlyStoreRules')->name("only-store-rule");
        Route::get('/get-bidding-rule-list', 'BiddingController@getbiddingRuleList')->name("get-bidding-rule-list");
        Route::post('/preset-rule-list', 'BiddingController@presetRuleList')->name("presetRuleList");
        Route::get('/preset-rule', 'BiddingController@presetRule')->name("presetRule");
        Route::get('/campaign-portfolio-list', 'BiddingController@getCampaignPortfolioList')->name("campaignPortfolioList");
        Route::get('/cron-code', 'BiddingController@croncode');
    });//end route Biding Rule
    /* Advertising Reports TO Eamil Routes Starts */
    Route::get('/advertisingReports/getPopUpData', "advertisingReportsEmail@getPopUpData")->name('getPopUpData');
    Route::get('/advertisingReports/emailSchedule', "advertisingReportsEmail@view")->name('advertisingReportsEmailView');
    Route::post('/manageEmailSchedule', "advertisingReportsEmail@manageEmailSchedule")->name("manageEmailSchedule"); //for add,Update
    Route::post('/advertisingReports/getReportTypes', 'advertisingReportsEmail@getReportTypes'); //For loading data in dropdowns
    Route::post('/advertisingReports/getReportMetrics', 'advertisingReportsEmail@getReportMetrics'); //For metrics
    Route::delete('/advertisingReports/{scheduleId}/deleteSchedule', 'advertisingReportsEmail@deleteSchedule'); //For Deleting Schedule
    /* Advertising Reports TO Eamil Routes Starts */
    /* Advertising Reports TO Eamil Routes Starts demo */
    Route::get('/advertisingReports/schedule', "advertisingReportsEmail@addSchedule")->name('advertisingReportsEmailaddSchedule');
    /* Advertising Reports TO Eamil Routes Starts demo */
    Route::post('/advertisingReports/getEditFormData', 'advertisingReportsEmail@getEditFormData'); //For loading data in dropdowns
    /* Advertising Reports TO Eamil generate csv */
    Route::get('/advertisingReports/compaign/{scheduleId}', "generateAmsCompaignReportExcel@downloadCompaignReport")->name("compaignDownload");
    //Route::get('/advertisingReports/compaign/{scheduleId}', "generateAmsCompaignReportExcel@downloadCompaignReport")->name("compaignDownload");
    Route::post('/advertisingReports/getMetricsPopupData', 'advertisingReportsEmail@getMetricsPopupData'); //For loading data in dropdowns
});
Route::get('/asindetbatch', 'ASINDetailController@index');
Route::post('/uploadFile', 'ASINDetailController@uploadFile');
Route::get('/ScrapData', 'ASINDetailController@ScrapData');
Route::get('/Scraper', 'ASINDetailController@Scraper');
Route::get('/scheduling', 'ASINDetailController@edit');
Route::post('/scheduling', 'ASINDetailController@AddCron');
/**
 *
 *
 * Scraper Routes
 * s
 */
Route::get('asin/uploadASIN', 'ASINDetailController@getAsinCollections')->name("uploadASIN"); //Upload ASIN'S Form Display

Route::post('asin/upload', 'ASINDetailController@uploadFile')->name('uploadFile'); //For Uploading ASIN File

Route::get('proxyForm', 'ASINDetailController@showProxyForm')->name("showAddProxyForm"); //Upload proxy Form Display
Route::get('deleteAllProxies', 'ASINDetailController@deleteAllProxies')->name("deleteAllProxies"); //Upload proxy Form Display

Route::post('scrap/uploadProxy', 'ASINDetailController@uploadProxy')->name('uploadProxy'); //For Uploading ASIN File

Route::post('settings/SchedulerTime', 'SettingsController@SetSchedulingTime')->name('SetSchedulingTime'); //For Uploading ASIN File

Route::get('sr/departmentForm', 'SearchRankScrapingController@showDepartmentForm')->name("showAddDepartmentForm"); //Upload proxy Form Display

Route::post('addDepartment', 'SearchRankScrapingController@addDepartment')->name('addDepartment'); //For Uploading ASIN File
Route::get('sr/schedules', 'SearchRankScrapingController@showSearchRankCrawlerForm')->name("showSearchRankCrawlerForm"); //Upload proxy Form Display

Route::post('sr/addSearchRankCrawler', 'SearchRankScrapingController@addSearchRankCrawler')->name('addSearchRankCrawler'); //For Uploading ASIN File

Route::delete('sr/deleteSearchRankCrawler/{id}', 'SearchRankScrapingController@deleteSearchRankCrawler'); //For Deleting Schedule
Route::get('scrap/ScrapData/', 'ASINDetailController@ScrapData'); //For Instant Scraping Data Call

Route::get('asin/scheduling', 'ASINDetailController@edit')->name("ScraperScheduling"); //Add Schedule Form Display

Route::get('asin/scrapboard', 'ASINDetailController@Scrapboard')->name("Scrapboard"); //Scraping Dashboad Named as Scrap Board

Route::post('asin/addScheduling', 'ASINDetailController@AddCron')->name("AddSchedual"); //For Adding Schedule

Route::delete('scrap/DeleteSchedual/{id}', 'ASINDetailController@deleteSchedual'); //For Deleting Schedule

/**
 * ASIN's Scraping Historical Data Retrival Download Routes
 */
Route::get('checkHistory', "HistoricalDataPreviewController@checkHistory")->name("checkHistory");

Route::get('download/{startDate}/{endDate}', "HistoricalDataPreviewController@downloadCSV")->name("download");

Route::get('asin/history', "HistoricalDataPreviewController@showHistoryFrom")->name("showHistoryForm");

/**
 * Search Rank Historical Data Retrival Download Routes
 */
Route::get('sr/checkHistory', "HistoricalDataPreviewController@checkSearchRankHistory")->name("checkSearchRankHistory");

Route::get('sr/download/{startDate}/{endDate}', "HistoricalDataPreviewController@downloadSearchRankCSV")->name("searchRankDownload");

Route::get('sr/history', "HistoricalDataPreviewController@showSearchRankHistoryFrom")->name("showSearchRankHistoryForm");
Route::get('vcLogin', "VCScrapController@vendorCredentialsAdd")->name("loginSellercentral");

Route::post('settings/SerachRankSchedulingTime', 'SettingsController@SetSerachRankSchedulingTime')->name('SetSerachRankSchedulingTime'); //For Uploading ASIN File
//--------------------------------------------------------------------
/**
 * BuyBox Alert Scraping Controller
 */
Route::get('/buybox/dashboard', 'Buybox@dashboard')->name('buyboxDashboard');
Route::get('/buybox/run', 'Buybox@attachment_email');
Route::get('/buybox/scheduling', 'Buybox@scheduling')->name('buyboxScheduling');
Route::post('/buybox/addbatch', 'Buybox@addbatch');
Route::delete('/buybox/deletebatch/{collection}', 'Buybox@deletebatch');

Route::get('getNotifications', "NotificationController@getNotificaitons");
Route::get('notifications/{notification}/preview/', "NotificationController@previewNotificaiton")->name("notificationPreview");
Route::get('notifications/{notification}/download', "NotificationController@DownloadNotificationDetailsImprovised")->name("notificationDownload");
Route::post('notifications/readAll', "NotificationController@UpdateNotificationsStatus");

Route::get('getNavigationData', "NotificationController@getNavigationData");
/**
 * admin dashboard routs
 */

/********************  Admin Module Routes***********************/
Route::prefix("admin")->middleware(['auth.admin'])->group(function () {
    Route::get("/dashboard", "Admin\AdminRoleController@dashboard")->name("admin.dashboard");
}); //end route group

Route::prefix("accounts")->middleware(['auth.admin'])->group(function () {
    Route::get('/getAccounts', "SuperAdminController@accounts")->name('account');
    Route::get('/{account}/deleteAccount', 'SuperAdminController@deleteAccount'); //For Deleting Schedule

    Route::post('/manageaccount', "SuperAdminController@manageAccount")->name("manage.account");
}); //end route group
Route::get('getGraphData/{g}', "Client\ProductPreviewController@getData");


Route::prefix("ht")->group(function () {

    Route::get('/brands', "SuperAdminController@brands")->name("addBrands")->middleware('auth.admin');
    Route::get('/brandsAddPopupData', "SuperAdminController@brandsAddPopupData")->name("brandsAddPopupData")->middleware('auth.admin');
    Route::post('/brandsEditPopupData', "SuperAdminController@brandsEditPopupData")->name("brandsEditPopupData")->middleware('auth.admin');
    Route::post('/getBrandAssignedUsers', "SuperAdminController@getBrandAssignedUsers")->name("getBrandAssignedUsers")->middleware('auth.admin');

    // Routes updated for vendor central by Umer 15-sept-2k20
    Route::post('/vendors', 'VCController@vendorsAdd')->middleware('auth.admin');
    Route::get('/getAllVendors', 'VCController@getAllVendors')->middleware('auth.admin');
    Route::post('/dailySales', 'VCController@dailySalesStoreRecords');
    Route::post('/purchaseOrder', 'VCController@purchaseOrderStoreRecords');
    Route::post('/dailyInventory', 'VCController@dailyInventoryStoreRecords');
    Route::post('/forecast', 'VCController@forecastStoreRecords');
    Route::post('/catalog', 'VCController@productCatalogStoreRecords');
    Route::post('/traffic', 'VCController@trafficStoreRecords');
    Route::post('/history', 'VCController@historicalDataDownload');
    Route::post('/vc/delete', 'VCController@deleteStoreRecords');
    Route::post('/vc/verify', 'VCController@verifyStoredRecords');
    Route::post('/vc/move', 'VCController@moveStoredRecords');

    Route::delete('/{client}/deleteClient', 'SuperAdminController@deleteClient')->middleware('auth.admin'); //For Deleting Schedule
    Route::post('/manageClient', "SuperAdminController@manageClient")->name("super.clients.submit")->middleware('auth.admin');

    /******************** Admin Managers Module Routes Starts***********************/

    Route::get('/managers', "addManagersController@viewManagers")->name("admin.managers")->middleware('auth.admin');
    Route::post('/getEditManagerData', 'addManagersController@getEditManagerData')->middleware('auth.admin'); //For edit user api
    Route::post('/managerOperations', "addManagersController@managerOperations")->name("admin.manager.submit")->middleware('auth.admin');
    Route::post('/checkUserBrands', 'addManagersController@checkUserBrands');
    Route::post('/addBrandManagers', "addManagersController@addBrandManagers")->name("addBrandManagers")->middleware('auth.admin');
    Route::post('/getUsersByType', 'addManagersController@getUsersByType');
    Route::delete('/{client}/deleteManager', 'addManagersController@deleteClient')->middleware('auth.admin'); //For Deleting Schedule
    Route::delete('/{client}/deleteAdmin', 'addAdminController@deleteAdmin')->middleware('auth.super_admin');
    //Route::delete('/{client}/deleteClient',  'SuperAdminController@deleteClient')->middleware('auth.admin');
    /******************** Admin Managers Module Routes Ends***********************/

    /******************** Super Admin Add Admin Module Routes Starts***********************/

    Route::get('/admins', "addAdminController@viewAdmins")->name("super.admin.addAdmins")->middleware('auth.super_admin');

    Route::post('/adminOperations', "addAdminController@adminOperations")->name("super.admin.submit")->middleware('auth.super_admin');

    /******************** Super Admin Add Admin Module Routes Ends***********************/
}); //end route group

/********************  Admin Clients Module Routes***********************/
/**
 * admin dashboard routs
 */
Route::prefix("manager")->group(function () {
    Route::get("/dashboard", "Manager\ManagerRoleController@dashboard")->name("manager.dashboard");
    Route::get("/brands","Manager\GlobalBrandSwitcherController@getManagerBrands")->name("manager.brands");
    Route::get("/brands/{brandId?}","Manager\GlobalBrandSwitcherController@switchActiveBrand")->name("manager.brand.switch");
    Route::get('/asinvisuals', "Vissuals\VissualsController@AsinPerformanceVisuals")->name('vissuals.asinPerformanceVisuals');

Route::group(['prefix' => 'visuals'], function () {
    Route::get('/asinLevelSpData', "Vissuals\VissualsController@asinlevelspdata")->name('vissuals.asinlevelspdata');
    
    Route::get('/getAsinPerformanceVisualsCampaigns', "Vissuals\VissualsController@getAsinPerformanceVisualsCampaigns")->name('vissuals.getAsinPerformanceVisualsCampaigns');
    Route::get('/getAsinPerformanceVisualsAsins', "Vissuals\VissualsController@getAsinPerformanceVisualsAsins")->name('vissuals.getAsinPerformanceVisualsAsins');


    Route::get('/', "Vissuals\VissualsController@loadVissuals")->name('vissuals.view');
    Route::get('/getCampaigns', "Vissuals\VissualsController@getCampaigns")->name('vissuals.campaigns');
    Route::get('/getTagCampaigns', "Vissuals\VissualsController@getTagCampaigns")->name('vissuals.tagCampaigns');

    Route::get('/spPopulateCampaignPerformance', "Vissuals\VissualsController@spPopulateCampaignPerformance")->name('vissuals.spPopulateCampaignPerformance');
    Route::get('/spPopulateCampaignEfficiency', "Vissuals\VissualsController@spPopulateCampaignEfficiency")->name('vissuals.spPopulateCampaignEfficiency');
    Route::get('/spPopulateCampaignAwareness', "Vissuals\VissualsController@spPopulateCampaignAwareness")->name('vissuals.spPopulateCampaignAwareness');
    Route::get('/spPopulateCampaignMTD', "Vissuals\VissualsController@spPopulateCampaignMTD")->name('vissuals.spPopulateCampaignMTD');
    Route::get('/spPopulatePresentationWowTable', "Vissuals\VissualsController@spPopulatePresentationWowTable")->name('vissuals.spPopulatePresentationWowTable');
    Route::get('/spPopulatePresentationDODTable', "Vissuals\VissualsController@spPopulatePresentationDODTable")->name('vissuals.spPopulatePresentationDODTable');
    Route::get('/spPopulatePresentationAdType', "Vissuals\VissualsController@spPopulatePresentationAdType")->name('vissuals.spPopulatePresentationAdType');
    Route::get('/spCalculateCustomCampTagingVisual', "Vissuals\VissualsController@spCalculateCustomCampTagingVisual")->name('vissuals.spCalculateCustomCampTagingVisual');
    Route::get('/spCalculateStragTypeCampTagingVisual', "Vissuals\VissualsController@spCalculateStragTypeCampTagingVisual")->name('vissuals.spCalculateStragTypeCampTagingVisual');
    Route::get('/spCalculateProdTypeCampTagingVisual', "Vissuals\VissualsController@spCalculateProdTypeCampTagingVisual")->name('vissuals.spCalculateProdTypeCampTagingVisual');
    Route::get('/spPerformancePre30Day', "Vissuals\VissualsController@spPerformancePre30Day")->name('vissuals.spPerformancePre30Day');
    Route::get('/spPerformanceytd', "Vissuals\VissualsController@spPerformanceytd")->name('vissuals.spPerformanceytd');
    Route::get('/spPopulatePresentationTopCampiagnTable', "Vissuals\VissualsController@spPopulatePresentationTopCampiagnTable")->name('vissuals.spPopulatePresentationTopCampiagnTable');
    Route::get('/spCalculatePreformancePrecentages', "Vissuals\VissualsController@spCalculatePreformancePrecentages")->name('vissuals.spCalculatePreformancePrecentages');
    Route::get('/spCalculateCampaignPerformanceGrandTotal', "Vissuals\VissualsController@spCalculateCampaignPerformanceGrandTotal")->name('vissuals.spCalculateCampaignPerformanceGrandTotal');
    Route::get('/spCalculateEfficiencyPrecentages', "Vissuals\VissualsController@spCalculateEfficiencyPrecentages")->name('vissuals.spCalculateEfficiencyPrecentages');
    Route::get('/spCalculateCampaignEfficiencyGrandTotal', "Vissuals\VissualsController@spCalculateCampaignEfficiencyGrandTotal")->name('vissuals.spCalculateCampaignEfficiencyGrandTotal');
    Route::get('/spCalculateAwarenessPrecentages', "Vissuals\VissualsController@spCalculateAwarenessPrecentages")->name('vissuals.spCalculateAwarenessPrecentages');
    Route::get('/spCalculateCampaignAwarenessGrandTotal', "Vissuals\VissualsController@spCalculateCampaignAwarenessGrandTotal")->name('vissuals.spCalculateCampaignAwarenessGrandTotal');
    Route::get('/spCalculateMTDPercentages', "Vissuals\VissualsController@spCalculateMTDPercentages")->name('vissuals.spCalculateMTDPercentages');
    Route::get('/spCalculateWowPercentages', "Vissuals\VissualsController@spCalculateWowPercentages")->name('vissuals.spCalculateWowPercentages');
    Route::get('/spCalculateDODPrecentages', "Vissuals\VissualsController@spCalculateDODPrecentages")->name('vissuals.spCalculateDODPrecentages');
    Route::get('/spCalculateYTDPercentages', "Vissuals\VissualsController@spCalculateYTDPercentages")->name('vissuals.spCalculateYTDPercentages');
    Route::get('/spCalculateWTDPercentages', "Vissuals\VissualsController@spCalculateWTDPercentages")->name('vissuals.spCalculateWTDPercentages');
    Route::get('/spPopulatePresentationCpgYTDTable', "Vissuals\VissualsController@spPopulatePresentationCpgYTDTable")->name('vissuals.spPopulatePresentationCpgYTDTable');
    Route::get('/spPopulatePresentationWTDTable', "Vissuals\VissualsController@spPopulatePresentationWTDTable")->name('vissuals.spPopulatePresentationWTDTable');
    Route::get('/spCalculateAMSScoreCards', "Vissuals\VissualsController@spCalculateAMSScoreCards")->name('vissuals.spCalculateAMSScoreCards');

    Route::get('/spCalculateStragTypeCampTagingVisual', "Vissuals\VissualsController@spCalculateStragTypeCampTagingVisual")->name('vissuals.spCalculateStragTypeCampTagingVisual');
    Route::get('/spCalculateStragTypeCampTagingVisualGrandTotal', "Vissuals\VissualsController@spCalculateStragTypeCampTagingVisualGrandTotal")->name('vissuals.spCalculateStragTypeCampTagingVisualGrandTotal');
    Route::get('/spCalculateProdTypeCampTagingVisual', "Vissuals\VissualsController@spCalculateProdTypeCampTagingVisual")->name('vissuals.spCalculateProdTypeCampTagingVisual');
    Route::get('/spCalculateProdTypeCampTagingVisualGrandTotal', "Vissuals\VissualsController@spCalculateProdTypeCampTagingVisualGrandTotal")->name('vissuals.spCalculateProdTypeCampTagingVisualGrandTotal');
});
}); //end route group

Route::post('/userSettingsOperations', "UserSettingsController@userSettingsOperations")->name("user.settings.update");

/********************  Admin Brand Routes***********************/
Route::prefix("ht")->middleware(['auth.admin'])->group(function () {
    Route::get('/associateBrands', "BrandsController@brandAssociate")->name('brandAssociate');
    Route::delete('/{brand}/deleteBrands', 'BrandsController@deleteBrand'); //For Deleting Schedule
    Route::post('/managebrandAssociation', "BrandsController@manageBrandAssociation")->name("manage.brandAssociation");
    /*Route::delete('/{brandAssociation}/deleteBrandAssociation', 'BrandsController@deleteBrandAssociation');*/
    Route::delete('/{client}/deleteBrandAssociation/', 'BrandsController@deleteBrandAssociation')->middleware('auth.admin');//For Deleting Schedule
});//end route grou
