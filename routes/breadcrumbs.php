<?php
// Admin Dashboard
Breadcrumbs::for('admin_dashboard', function ($trail) {
    $trail->push('Dashboard', route('adminDashboard'));
});
/**
 * --------------------------------
 * AMS Breadcrumb Section
 * --------------------------------
 */
// Admin Dashboard > AMS Dashboard
Breadcrumbs::for('ams_dashboard', function ($trail) {
    $trail->parent('admin_dashboard');
//    $trail->push('AMS Dashboard', route('amsDashboard'));
});
Breadcrumbs::for('Advertising_Visuals', function ($trail) {
    $trail->parent('admin_dashboard');
    $trail->push('Advertising Visuals', route('vissuals.view'));
});
// Admin Dashboard > AMS > Api Config
Breadcrumbs::for('ams_api_config', function ($trail) {
//    $trail->parent('ams_dashboard');
    $trail->push('API Config', route('amsApiConfig'));
});
// Admin Dashboard > AMS > Scheduling
Breadcrumbs::for('ams_scheduling', function ($trail) {
//    $trail->parent('ams_dashboard');
    $trail->push('AMS Scheduling', route('amsScheduling'));
});
Breadcrumbs::for('ams_history', function ($trail) {
    //$trail->parent('ams_dashboard');
    $trail->push('Export CSV', route('amsHistoryForm'));
});
// Admin Dashboard > Bidding Rule
Breadcrumbs::for('ams_bidding_rule', function ($trail) {
    $trail->parent('admin_dashboard');
//    $trail->push('AMS Dashboard', route('amsDashboard'));
});
/**
 * ------------------
 * Day Parting
 * ------------------
 * */
// Admin Dashboard > Day Parting > Schedule
Breadcrumbs::for('day_partying_schedule', function ($trail) {
    $trail->push('Schedule', route('dayPartingSchedule'));
});

// Admin Dashboard > Day Parting > Schedule
Breadcrumbs::for('day_partying_history', function ($trail) {
    $trail->push('History', route('dayPartingHistory'));
});

/**
 * ------------------
 * Vendor Central
 * ------------------
 * */

// Admin Dashboard > VC Dashboard
Breadcrumbs::for('vc_dashboard', function ($trail) {
    //$trail->parent('admin_dashboard');
    $trail->push('VC Dashboard', route('vcDashboard'));
});

// Admin Dashboard > VC Dashboard > Daily Sales
Breadcrumbs::for('upload_daily_sales', function ($trail) {
    //$trail->parent('vc_dashboard');
    $trail->push('Daily Sales', route('dailySalesView'));
});

// Admin Dashboard > VC Dashboard > Purchase Order
Breadcrumbs::for('purchase_order', function ($trail) {
    //$trail->parent('vc_dashboard');
    $trail->push('Purchase Order', route('purchaseOrder'));
});

// Admin Dashboard > VC Dashboard > Daily Inventory
Breadcrumbs::for('daily_inventory', function ($trail) {
    // $trail->parent('vc_dashboard');
    $trail->push('Daily Inventory', route('dailyInventory'));
});

// Admin Dashboard > VC Dashboard > Daily Inventory
Breadcrumbs::for('traffic', function ($trail) {
    // $trail->parent('vc_dashboard');
    $trail->push('Traffic', route('traffic'));
});

// Admin Dashboard > VC Dashboard > forecast
Breadcrumbs::for('forecast', function ($trail) {
    //$trail->parent('vc_dashboard');
    $trail->push('Forecast', route('forecast'));
});

// Admin Dashboard > VC Dashboard > Product Catalog
Breadcrumbs::for('product_catalog', function ($trail) {
    //$trail->parent('vc_dashboard');
    $trail->push('Product Catalog', route('catalog'));
});

// Admin Dashboard > VC Dashboard > Vendor
Breadcrumbs::for('vendor_add', function ($trail) {
    //$trail->parent('vc_dashboard');
    $trail->push('Add Vendor', route('vendorsAdd'));
});

// Admin Dashboard > VC Dashboard > Historical Data
Breadcrumbs::for('historical_data', function ($trail) {
    //$trail->parent('vc_dashboard');
    $trail->push('Export CSV', route('HistoryForm'));
});

// Admin Dashboard > VC Dashboard > Scrap Product Catalog
Breadcrumbs::for('scrap_catalog', function ($trail) {
    //$trail->parent('vc_dashboard');
    $trail->push('Scrap Product Catalog', route('scrapCatalog'));
});

/*MWS Breadcrumbs Section*/
Breadcrumbs::for('mws_dashboard', function ($trail) {
    $trail->parent('admin_dashboard');
    // $trail->push('MWS Dashboard', route('mwsDashboard'));
});
Breadcrumbs::for('mws_api_config', function ($trail) {
    $trail->parent('mws_dashboard');
    $trail->push('API Config', route('mwsApiConfig'));
});
Breadcrumbs::for('mws_scheduling', function ($trail) {
    $trail->parent('mws_dashboard');
    $trail->push('Scheduling', route('mwsScheduling'));
});

/**
 * ------------------
 * Scraping
 * ------------------
 * */
Breadcrumbs::for('scraping_dashboard', function ($trail) {
    $trail->parent('admin_dashboard');
    $trail->push('Scraping Dashboard', route('Scrapboard'));
});
Breadcrumbs::for('asin_upload', function ($trail) {
    $trail->parent('admin_dashboard');
    $trail->push('Upload ASINs', route('uploadASIN'));
});
Breadcrumbs::for('asin_scheduling', function ($trail) {
    $trail->parent('admin_dashboard');
    $trail->push('Schedule ASINS Scraping', route('ScraperScheduling'));
});

Breadcrumbs::for('label_override', function ($trail) {
    $trail->parent('admin_dashboard');
    $trail->push('Label Override', route('label.override.attributes'));
});
/**
 * ------------------
 * Search Rank
 * ------------------
 * */
Breadcrumbs::for('add_department', function ($trail) {
    $trail->parent('admin_dashboard');
    $trail->push('Search Rank Department', route('showAddDepartmentForm'));
});
Breadcrumbs::for('search_rank_scheduling', function ($trail) {
    $trail->parent('admin_dashboard');
    $trail->push('Search Rank', route('showSearchRankCrawlerForm'));
});
/**
 * ------------------
 * Export CSV
 * ------------------
 * */
Breadcrumbs::for('history', function ($trail) {
    $trail->parent('admin_dashboard');
    $trail->push('Export CSV', route('showHistoryForm'));
});
Breadcrumbs::for('schistory', function ($trail) {
    $trail->parent('mws_dashboard');
    $trail->push('Export CSV', route('scHistoryForm'));
});
Breadcrumbs::for('buybox_dashboard', function ($trail) {
    $trail->parent('admin_dashboard');
    $trail->push('BuyBox Dashboard', route('buyboxDashboard'));
});
Breadcrumbs::for('buybox_scheduling', function ($trail) {
    $trail->parent('admin_dashboard');
    $trail->push('BuyBox Scheduling', route('buyboxScheduling'));
});
/**
 * ------------------
 * Notification Preview
 * ------------------
 * */
Breadcrumbs::for('notification', function ($trail, $noti_id) {
    $trail->parent('admin_dashboard');
    $trail->push('Notification #' . $noti_id, route('notificationPreview', $noti_id));
});
/**
 * ------------------
 * Client Super Admin Module
 * ------------------
 * */
Breadcrumbs::for('superAdminClient', function ($trail) {
    $trail->parent('agency_dashboard');
    $trail->push('Brands', route('addBrands'));
});
/**
 * ------------------
 * Client Super Admin Module Product Preview
 * ------------------
 * */
Breadcrumbs::for('clientProductPreview', function ($trail) {
    $trail->parent('admin_dashboard');
    $trail->push('Product Notes', route('clients.notes'));
});
/**
 * ------------------
 * Client Super Admin Module Strategy type
 * ------------------
 * */
Breadcrumbs::for('CampaignTagging', function ($trail) {
    $trail->parent('admin_dashboard');
    $trail->push('Campaign Tagging', route('campaign.tags'));
});
/**
 * ------------------
 * Client Super Admin Module Product Type
 * ------------------
 * */
Breadcrumbs::for('CampaignProductType', function ($trail) {
    $trail->parent('admin_dashboard');
    $trail->push('Product Type', route('campaign.productType'));
});
/**
 * ------------------
 * Account Super Admin Module
 * ------------------
 * */
Breadcrumbs::for('superAdminAccountModule', function ($trail) {
    $trail->parent('agency_dashboard');
    $trail->push('Accounts', route('addBrands'));
});

/**
 * ------------------
 *Super Admin Add Admin Module
 * ------------------
 * */
Breadcrumbs::for('superAdminAddAdmin', function ($trail) {
    $trail->parent('agency_dashboard');
    $trail->push('Manage Agencies', route('super.admin.addAdmins'));
});

/**
 * ------------------
 *Super Admin Add Managers Module
 * ------------------
 * */
Breadcrumbs::for('AdminAddManager', function ($trail) {
    $trail->parent('agency_dashboard');
    $trail->push('Manage Managers', route('admin.managers'));
});

// Agency Dashboard
Breadcrumbs::for('agency_dashboard', function ($trail) {
    $trail->push('Dashboard', route('admin.dashboard'));
});
/**
 * ------------------
 * Account Super Admin Module
 * ------------------
 * */
Breadcrumbs::for('adminBrandAssociate', function ($trail) {
    $trail->parent('agency_dashboard');
    $trail->push('Associate Brands', route('brandAssociate'));
});
/**
 * ------------------
 * Schedule Advertising Reports
 * ------------------
 * */
Breadcrumbs::for('advertisingReportsEmailView', function ($trail) {
    $trail->parent('admin_dashboard');
    $trail->push('Advertising Reports', route('advertisingReportsEmailView'));
});

Breadcrumbs::for('advertisingReportsEmailaddSchedule', function ($trail) {
    $trail->parent('admin_dashboard');
    $trail->push('Advertising Reports', route('advertisingReportsEmailaddSchedule'));
});