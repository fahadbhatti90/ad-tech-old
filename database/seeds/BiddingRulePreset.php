<?php

use Illuminate\Database\Seeder;
use App\Models\BiddingRule;

class BiddingRulePreset extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        BiddingRule::truncatePreset();
        $dataArray = array(
            'presetName' => 'Bid Up',
            'metric' => 'impression',
            'condition' => 'less',
            'integerValues' => '0',
            'thenClause' => 'raise',
            'bidBy' => '0',
            'andOr' => 'NA',
            'frequency' => 'once_per_day',
            'lookBackPeriod' => '7d',
            'lookBackPeriodDays' => '7',
            'createdAt' => date('Y-m-d H:i:s'),
            'updatedAt' => date('Y-m-d H:i:s')
        ); // end else
        BiddingRule::storePresetRule($dataArray);
    }
}
