<?php

use Illuminate\Database\Seeder;
use App\Models\ams\scheduleEmail\sponsordReports;

class AmsSponsordReportTypes extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
         sponsordReports::truncate();
                              /*Sponsord Display Metrics starts*/
    $sponsordReportsData=$arrayName = array(
    [
                                    'reportName'=>'Campaign(SD)',
                                    'fkSponsordTypeId'=>1,
                                    'fkParameterType'=>1,
                                    'isActive'=>0
    ],
    [
                                   'reportName'=>'Ad Group(SD)',
                                    'fkSponsordTypeId'=>1,
                                    'fkParameterType'=>2,
                                    'isActive'=>0
    ],
    [
                                   'reportName'=>'Product Ads(SD)',
                                    'fkSponsordTypeId'=>1,
                                    'fkParameterType'=>3,
                                    'isActive'=>0
    ],
    [
                                   'reportName'=>'Campaign(SP)',
                                    'fkSponsordTypeId'=>2,
                                    'fkParameterType'=>1,
                                    'isActive'=>0
    ],
    [
                                   'reportName'=>'Ad Group(SP)',
                                    'fkSponsordTypeId'=>2,
                                    'fkParameterType'=>2,
                                    'isActive'=>0
    ],
    [
                                   'reportName'=>'Product Ads(SP)',
                                    'fkSponsordTypeId'=>2,
                                    'fkParameterType'=>3,
                                    'isActive'=>0
    ],
    [
                                   'reportName'=>'Keyword(SP)',
                                    'fkSponsordTypeId'=>2,
                                    'fkParameterType'=>4,
                                    'isActive'=>0
    ],
    [
                                   'reportName'=>'Asins(SP)',
                                    'fkSponsordTypeId'=>2,
                                    'fkParameterType'=>5,
                                    'isActive'=>0
    ],
    [
                                   'reportName'=>'Campaign(SB)',
                                    'fkSponsordTypeId'=>3,
                                    'fkParameterType'=>1,
                                    'isActive'=>0
    ],
    [
                                   'reportName'=>'Ad Group(SB)',
                                    'fkSponsordTypeId'=>3,
                                    'fkParameterType'=>2,
                                    'isActive'=>0
    ],
    [
                                   'reportName'=>'Keyword(SB)',
                                    'fkSponsordTypeId'=>3,
                                    'fkParameterType'=>4,
                                    'isActive'=>0
    ]
    );
       $sponsordReportsInsert=  sponsordReports::insert($sponsordReportsData);
    }
}
