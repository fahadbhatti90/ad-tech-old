<?php

use App\Models\ProductPreviewModels\UserActionsModel;
use Illuminate\Database\Seeder;

class ActionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        UserActionsModel::truncate();
        $data = array(
            array(
                'actionName' =>"Brand Price Change",
            ),
            array(
                'actionName' =>"Cost Change",
            ),
            array(
                'actionName' =>"Promotion",
            ),
            array(
                'actionName' =>"Content Change",
            ),
            array(
                'actionName' =>"Ad Changes",
            ),
            array(
                'actionName' =>"New Platform",
            ),
        );
        UserActionsModel::insert($data);
    }
}
