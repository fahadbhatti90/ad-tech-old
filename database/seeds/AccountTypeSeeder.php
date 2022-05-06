<?php

use App\Models\AccountModels\AccountTypeModel;
use Illuminate\Database\Seeder;

class AccountTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        AccountTypeModel::truncate();
        $data = array(
            array(
                'name' =>"AMS",
            ),
            array(
                'name' =>"MWS",
            ),
            array(
                'name' =>"VC",
            ),
        );//end main array
        AccountTypeModel::insert($data);
    }
}
