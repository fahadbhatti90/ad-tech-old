<?php

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
         $this->call(RolesTableSeeder::class);
         $this->call(AccountTypeSeeder::class);
         $this->call(UserTableSeeder::class);
         $this->call(SettingsTableSeeder::class);
         $this->call(ProxyTableSeeder::class);
         $this->call(SearchRankDepartmentsSeeder::class);
         $this->call(EventsSeeder::class);
         $this->call(ActionSeeder::class);
         $this->call(ProductPreviewTypeSeeder::class);
         $this->call(AttributeTypeSeeder::class);
         /**** Email schedule reports seeders starts *****/
         $this->call(AmsSponsordTypes::class);
         $this->call(AmsSponsordReportTypes::class);
         $this->call(AmsParameterTypesSeeder::class);
         $this->call(AmsReportsMetricsSeeder::class);
         /**** Email schedule reports seeders ends *****/
        // bidding rule preset seeder
        $this->call(BiddingRulePreset::class);
        /**** Product Segments seeders starts *****/
        $this->call(ProductSegmentGroupsSeeder::class);
        $this->call(ProductSegmentsSeeder::class);
        /**** Product Segments seeders ends *****/
    }
}
