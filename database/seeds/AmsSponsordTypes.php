<?php

use Illuminate\Database\Seeder;
use App\Models\ams\scheduleEmail\sponsordTypes;

class AmsSponsordTypes extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        sponsordTypes::truncate();
                              /*Sponsord Display Metrics starts*/
    $sponsordTypesData = array(
    [
                                   'sponsordTypenName'=>'Sponsored Display'
                                   ],
    [
                                   'sponsordTypenName'=>'Sponsored Products'
    ],
    [
                                   'sponsordTypenName'=>'Sponsored Brands'
    ]
    );
       $sponsordTypeInsert = sponsordTypes::insert($sponsordTypesData);
    }
}
