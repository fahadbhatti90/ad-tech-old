<?php

use App\Models\ProductPreviewModels\ProductPreviewTypeModel;
use Illuminate\Database\Seeder;

class ProductPreviewTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        
        ProductPreviewTypeModel::truncate();
        $data = array(
            array(
                'typeTitle' =>"User Actions",
            ),
            array(
                'typeTitle' =>"Events",
            )
        );
        ProductPreviewTypeModel::insert($data);
    }
}
