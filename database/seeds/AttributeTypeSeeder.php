<?php

use App\Models\ProductPreviewModels\AttributeTypeModel;
use Illuminate\Database\Seeder;

class AttributeTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        AttributeTypeModel::truncate();
        $data = array(
            array(
                'attributeTypeName' =>"Price",
            ),
            array(
                'attributeTypeName' =>"Sales",
            ),
            array(
                'attributeTypeName' =>"Sales Rank",
            )
        );
        AttributeTypeModel::insert($data);
    }
}
