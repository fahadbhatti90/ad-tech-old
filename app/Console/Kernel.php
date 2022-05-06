<?php

namespace App\Console;


use App\Console\Commands\AMSAuthCron;
use App\Console\Commands\Ams\AmsAdvertisingEmailSchedule\runAmsAdvertisingEmailSchedule;
use App\Console\Commands\Mws\catalogReportsRequest\catalogReportsRequest;
use App\Console\Commands\Mws\inventoryReportsRequest\inventoryReportsRequest;
use App\Console\Commands\Mws\runGetAsinsFromReportsCron\runGetAsinsFromReportsCron;
use App\Console\Commands\Mws\runGetProductCategoriesDetailsCron\runGetProductCategoriesDetailsCron;
use App\Console\Commands\Mws\runGetProductDetailsCron\runGetProductDetailsCron;
use App\Console\Commands\Mws\runGetReportCron\runGetReportCron;
use App\Console\Commands\Mws\runReportListCron\runReportListCron;
use App\Console\Commands\Mws\runRequestReportCron\runRequestReportCron;
use App\Console\Commands\Mws\salesReportsRequest\salesReportsRequest;
use App\Console\Commands\Mws\ScCronRun\mwsCronRun;
use App\Console\Commands\Mws\scGetProductCategoryDetails\scGetProductCategoryDetails;
use App\Console\Commands\Mws\scGetProductDetails\scGetProductDetails;
use App\Console\Commands\Mws\ScGetProductIds\scGetCatalogProductsIds;
use App\Console\Commands\Mws\ScGetProductIds\scGetInventoryProductsIds;
use App\Console\Commands\Mws\ScGetProductIds\scGetSalesProductsIds;
use App\Console\Commands\Mws\screports\ScCatActive;
use App\Console\Commands\Mws\screports\ScFbaHealth;
use App\Console\Commands\Mws\screports\ScOrdersUpdt;
use App\Console\Commands\Mws\copyProductSalesRank\copyProductSalesRank;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use Artisan;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        // AMS Job List Commands
        Commands\AmsCronJobList::class,
        // AMS Auth Commands
        Commands\Ams\Auth\AuthCron::class,
        // Profile Commands
        Commands\Ams\Profile\ProfileCron::class,
        // Campaign Commands Sponsored Products
        Commands\Ams\Campaign\SP\getReportIdCron::class,
        Commands\Ams\Campaign\SP\getReportLinkCron::class,
        Commands\Ams\Campaign\SP\getReportLinkDataCron::class,
        // Keyword Commands Sponsored Brand
        Commands\Ams\Keyword\SB\getReportIdCron::class,
        Commands\Ams\Keyword\SB\getReportLinkCron::class,
        Commands\Ams\Keyword\SB\getReportLinkDataCron::class,
        // Keyword Commands Sponsored Products
        Commands\Ams\Keyword\SP\getReportIdCron::class,
        Commands\Ams\Keyword\SP\getReportLinkCron::class,
        Commands\Ams\Keyword\SP\getReportLinkDataCron::class,
        // AdGroup Commands
        Commands\Ams\AdGroup\SP\getReportIdCron::class,
        Commands\Ams\AdGroup\SP\getReportLinkCron::class,
        Commands\Ams\AdGroup\SP\getReportLinkDataCron::class,
        // Targets Commands
        Commands\Ams\Target\getReportIdCron::class,
        Commands\Ams\Target\getReportLinkCron::class,
        Commands\Ams\Target\getReportLinkDataCron::class,
        // Products Ads Sponsored Products Command
        Commands\Ams\ProductsAds\getReportIdCron::class,
        Commands\Ams\ProductsAds\getReportLinkCron::class,
        Commands\Ams\ProductsAds\getReportLinkDataCron::class,
        // ASIN Report Sponsored Products Command
        Commands\Ams\ASIN\ASINReportsIdCron::class,
        Commands\Ams\ASIN\getReportLinkCron::class,
        Commands\Ams\ASIN\getReportLinkDataCron::class,
        //Commands\MWSRequestReport::class,
        Commands\Mws\ScRequestReport\MWSRequestReport::class,
        Commands\Mws\ScRequestReportList\MWSGetReportRequestList::class,
        Commands\Mws\ScReportList\MWSGetReportList::class,
        Commands\Mws\screports\ScCatActive::class,
        Commands\Mws\screports\CatalogScCatActivereport::class,
        Commands\Mws\screports\ScCatInactive::class,
        Commands\Mws\screports\ScFbaHealth::class,
        Commands\Mws\screports\CatalogScFbaHealth::class,
        Commands\Mws\screports\ScFbaReceipt::class,
        Commands\Mws\screports\ScFbaRestock::class,
        Commands\Mws\screports\ScFbaReturns::class,
        Commands\Mws\screports\ScMfnReturns::class,
        Commands\Mws\screports\ScOrders::class,
        Commands\Mws\screports\ScOrdersUpdt::class,
        Commands\Mws\ScCronRun\mwsCronRun::class,
        Commands\Mws\runRequestReportCron\runRequestReportCron::class,
        Commands\Mws\runReportRequestListCron\runReportRequestListCron::class,
        Commands\Mws\runReportListCron\runReportListCron::class,
        Commands\Mws\runGetReportCron\runGetReportCron::class,
        Commands\Mws\catalogReportsRequest\catalogReportsRequest::class,
        Commands\Mws\inventoryReportsRequest\inventoryReportsRequest::class,
        Commands\Mws\salesReportsRequest\salesReportsRequest::class,
        /**
         * 
         * Scrapper Command
         * 
         */
        Commands\ScrapperCommands\ScheduleCronCommand::class,
        Commands\ScrapperCommands\ManageASINScrapingCommand::class,
        /**
         * 
         * Search Rank Scrapper Command
         * 
         */
        Commands\SearchRankCommands\RunSearchRankScrapingCommand::class,
        Commands\SearchRankCommands\ScrapSearchRankBrandsCommand::class,
        Commands\Mws\runGetAsinsFromReportsCron\runGetAsinsFromReportsCron::class,
        Commands\Mws\runGetProductDetailsCron\runGetProductDetailsCron::class,
        Commands\Mws\runGetProductCategoriesDetailsCron\runGetProductCategoriesDetailsCron::class,
        Commands\Mws\ScGetProductIds\scGetCatalogProductsIds::class,
        Commands\Mws\ScGetProductIds\scGetInventoryProductsIds::class,
        Commands\Mws\ScGetProductIds\scGetSalesProductsIds::class,
        Commands\Mws\scGetProductDetails\scGetProductDetails::class,
        Commands\Mws\scGetProductCategoryDetails\scGetProductCategoryDetails::class,
        Commands\Mws\copyProductSalesRank\copyProductSalesRank::class,
        Commands\Ams\AmsAdvertisingEmailSchedule\runAmsAdvertisingEmailSchedule::class,
    ];

    /**
     * Define the application's command schedule.
     *
     * @param \Illuminate\Console\Scheduling\Schedule $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
//        $schedule->command('getkeywordreportlinkdata:sbkeyword')
        /*$schedule->command('ScCatActivereport:cron')
            ->everyMinute();*/


         // $schedule->command('mwsrequestreport:cron')->everyMinute();

     /* mws crons starts */


        /*$schedule->command('runRequestReportCron:cron')->everyMinute();
        $schedule->command('runReportRequestListCron:cron')->everyMinute();
        $schedule->command('runReportListCron:cron')->everyMinute();
        $schedule->command('runGetReportCron:cron')->everyMinute();*/


        //$schedule->command('runGetReportCron')->withoutOverlapping(10);
     /* mws crons ends */


        /* mws crons starts */

        $schedule->command('mwsCronRun:cron')->everyMinute();

        /* mws crons ends */

         //********************************************************************
        /**
         *                   Scrapper Command Section
         */
        //********************************************************************
         
        $schedule->command('ScrapperCommandManager:manage')->everyMinute();
        //
        //********************************************************************
        /**
         *                 Search Rank Scrapper Command Section
         */
        //********************************************************************
        $schedule->command('ManageSearchRankScrapingCommand:sr')->everyMinute();


        /*$schedule->command('runRequestReportCron')->everyMinute();
            $schedule->command('runGetReportCron')->everyMinute();*/

//        $schedule->command('amsprofile:cron')
//            ->everyMinute()->before(function () {
//                Artisan::call('amsauth:cron');
//            });
    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__ . '/Commands');

        require base_path('routes/console.php');
    }
}
