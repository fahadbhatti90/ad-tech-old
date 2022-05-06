<?php

use Illuminate\Database\Seeder;
use App\Models\ProductSegments\ProductSegmentGroupsModel;
class ProductSegmentGroupsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        ProductSegmentGroupsModel::truncate();
        $data = array(
            array(
                'groupName' =>"Amz Tags"
            )
        );
        ProductSegmentGroupsModel::insert($data);
    }
}
