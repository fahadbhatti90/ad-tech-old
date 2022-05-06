<?php

use Illuminate\Database\Seeder;
use App\Models\ScrapingModels\DepartmentModel;

class SearchRankDepartmentsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DepartmentModel::truncate();
        $data = array(
            array(
            "d_name"=> "Audible Books & Originals",
            "d_alias"=> "audible",
            "created_at"=>date('Y-m-d H:i:s')
            ),
            array(
            "d_name"=> "Alexa Skills",
            "d_alias"=> "alexa-skills",
            "created_at"=>date('Y-m-d H:i:s')
            ),
            array(
            "d_name"=> "Amazon Devices",
            "d_alias"=> "amazon-devices",
            "created_at"=>date('Y-m-d H:i:s')
            ),
            array(
            "d_name"=> "Amazon Fresh",
            "d_alias"=> "amazonfresh",
            "created_at"=>date('Y-m-d H:i:s')
            ),
            array(
            "d_name"=> "Amazon Warehouse",
            "d_alias"=> "warehouse-deals",
            "created_at"=>date('Y-m-d H:i:s')
            ),
            array(
            "d_name"=> "Appliances",
            "d_alias"=> "appliances",
            "created_at"=>date('Y-m-d H:i:s')
            ),
            array(
            "d_name"=> "Apps & Games",
            "d_alias"=> "mobile-apps",
            "created_at"=>date('Y-m-d H:i:s')
            ),
            array(
            "d_name"=> "Arts, Crafts & Sewing ",
            "d_alias"=> "arts-crafts",
            "created_at"=>date('Y-m-d H:i:s')
            ),
            array(
            "d_name"=> "Automative Parts & Accessories",
            "d_alias"=> "automotive",
            "created_at"=>date('Y-m-d H:i:s')
            ),
            array(
            "d_name"=> "Baby",
            "d_alias"=> "baby-products",
            "created_at"=>date('Y-m-d H:i:s')
            ),
            array(
            "d_name"=> "Beauty & Personal Care",
            "d_alias"=> "beauty",
            "created_at"=>date('Y-m-d H:i:s')
            ),
            array(
            "d_name"=> "Books",
            "d_alias"=> "stripbooks",
            "created_at"=>date('Y-m-d H:i:s')
            ),
            array(
            "d_name"=> "CDs & Vinyl",
            "d_alias"=> "popular",
            "created_at"=>date('Y-m-d H:i:s')
            ),
            array(
            "d_name"=> "Cell Phones & Accessories",
            "d_alias"=> "mobile",
            "created_at"=>date('Y-m-d H:i:s')
            ),
            array(
            "d_name"=> "Clothing, Shoes & Jewelery ",
            "d_alias"=> "fashion",
            "created_at"=>date('Y-m-d H:i:s')
            ),
            array(
            "d_name"=> "Women",
            "d_alias"=> "fashion-womens",
            "created_at"=>date('Y-m-d H:i:s')
            ),
            array(
            "d_name"=> "Men",
            "d_alias"=> "fashion-mens",
            "created_at"=>date('Y-m-d H:i:s')
            ),
            array(
            "d_name"=> "Girls",
            "d_alias"=> "fashion-girls",
            "created_at"=>date('Y-m-d H:i:s')
            ),
            array(
            "d_name"=> "Boys",
            "d_alias"=> "fashion-boys",
            "created_at"=>date('Y-m-d H:i:s')
            ),
            array(
            "d_name"=> "Fashion-Baby",
            "d_alias"=> "fashion-baby",
            "created_at"=>date('Y-m-d H:i:s')
            ),
            array(
            "d_name"=> "Collectibles & Fine Art",
            "d_alias"=> "collectibles",
            "created_at"=>date('Y-m-d H:i:s')
            ),
            array(
            "d_name"=> "Computers",
            "d_alias"=> "computers",
            "created_at"=>date('Y-m-d H:i:s')
            ),
            array(
            "d_name"=> "Courses",
            "d_alias"=> "courses",
            "created_at"=>date('Y-m-d H:i:s')
            ),
            array(
            "d_name"=> "Credit and Payments Cards",
            "d_alias"=> "financial",
            "created_at"=>date('Y-m-d H:i:s')
            ),
            array(
            "d_name"=> "Digital Music",
            "d_alias"=> "digital-music",
            "created_at"=>date('Y-m-d H:i:s')
            ),
            array(
            "d_name"=> "Electronics",
            "d_alias"=> "computers",
            "created_at"=>date('Y-m-d H:i:s')
            ),
            array(
            "d_name"=> "Garden & Outdoor",
            "d_alias"=> "lawngarden",
            "created_at"=>date('Y-m-d H:i:s')
            ),
          
        );
        
        DepartmentModel::insert($data);
    }
}
