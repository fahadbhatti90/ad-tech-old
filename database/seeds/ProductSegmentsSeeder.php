<?php

use Illuminate\Database\Seeder;
use App\Models\ProductSegments\ProductSegments;
class ProductSegmentsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        ProductSegments::truncate();
        $data = array(
            array(
                'segmentName' =>"Amz Choice tag",
                'fkGroupId'   =>1
            ),
            array(
                'segmentName' => "Prime Tag",
                'fkGroupId'   =>1
            ),
            array(
                'segmentName' => "Bestseller Tag",
                'fkGroupId'   =>1
            ),
            array(
                'segmentName' => "Buybox Status",
                'fkGroupId'   =>NULL
            )
        );
        ProductSegments::insert($data);
    }
}
