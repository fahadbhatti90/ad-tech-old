<?php

use Illuminate\Database\Seeder;
use App\Models\ScrapingModels\SettingsModel;

class SettingsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        SettingsModel::truncate();
        $data = array(
            array(
                'name' => "asin_threads",
                'value' => 25,
                'description' => "Total number of threads ALLOWED to create",
                'created_at' => date('Y-m-d'),
            ),
            array(
                'name' => "sr_threads",
                'value' => 5,
                'description' => "Total Number of threads ALLOWED for search rank scraping",
                'created_at' => date('Y-m-d'),
            ),
            array(
                'name' => "reset_limit",
                'value' => 15,
                'description' => "How many times an asin can be reseted for scraping again after that limit it will be removed from asin temp table to faild asin table with fail type code",
                'created_at' => date('Y-m-d'),
            ),
            array(
                'name' => "scheduleTime",
                'value' => "14:00",
                'description' => "At this time the cron commands will run daily. Default Time will be 14:00 (02:00PM)",
                'created_at' => date('Y-m-d'),
            ),
            array(
                'name' => "SrScheduleTime",
                'value' => "10:00",
                'description' => "At this time the cron commands for search rank will run daily. Default Time will be 10:00 (10:00AM)",
                'created_at' => date('Y-m-d'),
            ),
            array(
                'name' => "SrSleepTime",
                'value' => "3",
                'description' => "Time the search rank code sleep or waits before resetting the temp table urls",
                'created_at' => date('Y-m-d'),
            ),
            array(
                'name' => "Host",
                'value' => url('/'),
                'description' => "Host url for identification of notification source",
                'created_at' => date('Y-m-d'),
            ),
            array(
                'name' => "amsSixtyDaysReportsIdCount",
                'value' => 20,
                'description' => "Ams reports ID hits count for sixty days data",
                'created_at' => date('Y-m-d'),
            ),
            array(
                'name' => "amsSixtyDaysReportsLinkCount",
                'value' => 10,
                'description' => "Ams reports link hits count for sixty days data",
                'created_at' => date('Y-m-d'),
            ),
            array(
                'name' => "amsSixtyDaysReportsDataCount",
                'value' => 10,
                'description' => "Ams reports Data hits count for sixty days data",
                'created_at' => date('Y-m-d'),
            ),
        );
        SettingsModel::insert($data);
    }
}
