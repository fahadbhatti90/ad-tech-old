<?php

use Illuminate\Database\Seeder;
use App\Models\ams\scheduleEmail\amsParameterTypes;

class AmsParameterTypesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
                 amsParameterTypes::truncate();
                              /*Sponsord Display Metrics starts*/
    $amsParameterTypesData=$arrayName = array(
    [
                                    'parameterName'=>'Campaign',
                                    'isSd'=>0,
                                    'isSp'=>0,
                                    'isSb'=>0
    ],
    [
                                    'parameterName'=>'Ad Groups',
                                    'isSd'=>0,
                                    'isSp'=>0,
                                    'isSb'=>0
    ],
    [
                                    'parameterName'=>'Product Ads',
                                    'isSd'=>0,
                                    'isSp'=>0,
                                    'isSb'=>1
    ],
    [
                                    'parameterName'=>'Keyword',
                                    'isSd'=>1,
                                    'isSp'=>0,
                                    'isSb'=>0
    ],
    [
                                    'parameterName'=>'ASINS',
                                    'isSd'=>1,
                                    'isSp'=>0,
                                    'isSb'=>1
    ]
    );
       $amsParameterTypesInsert=  amsParameterTypes::insert($amsParameterTypesData);
    }
}
