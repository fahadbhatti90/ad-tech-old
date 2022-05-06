<?php

namespace App\Console\Commands\Ams\Historical;

use App\Models\ams\ProfileModel;
use App\Models\AMSModel;
use App\Models\ScrapingModels\SettingsModel;
use Graze\ParallelProcess\PriorityPool;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Symfony\Component\Process\Process;

class CheckProfile extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'checkprofile:historical';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'This command is used to get report id of all profile last 60 days';

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
        $ams60Reports = SettingsModel::where("name", "amsSixtyDaysReportsIdCount")->first();
        if (!empty($ams60Reports)) {
            $hitValues = (int)$ams60Reports->value;
            if ($hitValues != 0 && $hitValues > 0) {
                SettingsModel::where("id", $ams60Reports->id)
                    ->update(['value' => $hitValues - 1]);
                $AllProfileIdObject = new AMSModel();
                $AllProfileID = $AllProfileIdObject->getAllHistoricalProfiles();
                if (!empty($AllProfileID)) {
                    foreach ($AllProfileID as $single) {
                        $updateProfileValue = ProfileModel::where("id", $single->id)->first();
                        if ($updateProfileValue->adGroupSpSixtyDays == 0) {
                            Artisan::call('getadgrouphistoricalreportid:spadgroup ' . $updateProfileValue->id . ' ' . $updateProfileValue->profileId);
                        }
                        if ($updateProfileValue->aSINsSixtyDays == 0) {
                            Artisan::call('getASINhistoricalreport:asinreport ' . $updateProfileValue->id . ' ' . $updateProfileValue->profileId);
                        }
                        if ($updateProfileValue->campaignSpSixtyDays == 0) {
                            Artisan::call('getcampaignhistoricalreportid:spcampaign ' . $updateProfileValue->id . ' ' . $updateProfileValue->profileId);
                        }
                        if ($updateProfileValue->keywordSbSixtyDays == 0) {
                            Artisan::call('getkeywordhistoricalreportid:sbkeyword ' . $updateProfileValue->id . ' ' . $updateProfileValue->profileId);
                        }
                        if ($updateProfileValue->keywordSpSixtyDays == 0) {
                            Artisan::call('getkeywordhistoricalreportid:spkeyword ' . $updateProfileValue->id . ' ' . $updateProfileValue->profileId);

                        }
                        if ($updateProfileValue->productAdsSixtyDays == 0) {
                            Artisan::call('getproductsadshistoricalreportid:productsads ' . $updateProfileValue->id . ' ' . $updateProfileValue->profileId);
                        }
                        if ($updateProfileValue->productTargetingSixtyDays == 0) {
                            Artisan::call('gettargethistoricalreportid:targets ' . $updateProfileValue->id . ' ' . $updateProfileValue->profileId);

                        }
                        if ($updateProfileValue->SponsoredBrandCampaignsSixtyDays == 0) {
                            Artisan::call('getcampaignhistoricalreportid:sbcampaign ' . $updateProfileValue->id . ' ' . $updateProfileValue->profileId);
                        }
                        if ($updateProfileValue->SponsoredDisplayCampaignsSixtyDays == 0) {
                            Artisan::call('getcampaignhistoricalreportid:sdcampaign ' . $updateProfileValue->id . ' ' . $updateProfileValue->profileId);
                        }
                        if ($updateProfileValue->SponsoredDisplayAdgroupSixtyDays == 0) {
                            Artisan::call('getsdadgrouphistoricalreportid:sdadgroup ' . $updateProfileValue->id . ' ' . $updateProfileValue->profileId);
                        }
                        if ($updateProfileValue->SponsoredBrandAdgroupSixtyDays == 0) {
                            Artisan::call('getsbadgrouphistoricalreportid:sbadgroup ' . $updateProfileValue->id . ' ' . $updateProfileValue->profileId);
                        }
                        if ($updateProfileValue->SponsoredBrandTargetingSixtyDays == 0) {
                            Artisan::call('gettargethistoricalreportid:sbtargets ' . $updateProfileValue->id . ' ' . $updateProfileValue->profileId);
                        }
                        if ($updateProfileValue->SponsoredDisplayProductAdsSixtyDays == 0) {
                            Artisan::call('getsdproductsadshistoricalreportid:sdproductsads ' . $updateProfileValue->id . ' ' . $updateProfileValue->profileId);
                        }
                    }
                }
            } else {
                if (date('H:i') == '00:00') {
                    SettingsModel::where("id", $ams60Reports->id)
                        ->update(['value' => 20]);
                }
            }
        }
    }
}
