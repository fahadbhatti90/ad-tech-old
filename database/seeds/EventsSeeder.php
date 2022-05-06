<?php

use App\Models\ProductPreviewModels\EventsModel;
use Illuminate\Database\Seeder;

class EventsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        
        // [0,'Reviews', "linear-gradient(180deg, #00000000 10%, #b3b3b3b3 100%)","#b3b3b3"],
        // [1,'Content', "linear-gradient(180deg, #00000000 10%, #3f51b5b3 100%)","#323f8a"],
        // [2,'Page not found',"linear-gradient(180deg, #00000000 10%, #ff00f4b3 100%)","#ff00f4"],
        // [3,'Andon Cord',"linear-gradient(180deg, #00000000 10%, #f44336b3 100%)","#f44336"],
        // [4,'Crap', "linear-gradient(180deg, #00000000 10%, #224abeb3 100%)","#224abe"],
        // [5,'Out of Stock', "linear-gradient(180deg, #00000000 10%, #03ffc5b3 100%)","#03ffc5"],
        // [6,'Seller change',"linear-gradient(180deg, #00000000 10%, #795548b3 100%)","#795548"],
        // [7,'Price change', "linear-gradient(180deg, #00000000 10%, #9c27b0b3 100%)","#9c27b0"],
        // [8,'Advertising', "linear-gradient(180deg, #00000000 10%, #4caf50b3 100%)","#4CAF50"],
        EventsModel::truncate();
        $data = array(
            array(
                'eventName' =>"Reviews",
                'isEventAuto' => 1,
                'eventColor' => "#b3b3b3"
            ),
            array(
                'eventName' => "Content Performance",
                'isEventAuto' => 1,
                'eventColor' => "#323f8a"
            ),
            array(
                'eventName' =>"Product not found",
                'isEventAuto' => 1,
                'eventColor' => "#ff00f4"
            ),
            array(
                'eventName' =>"Andon",
                'isEventAuto' => 0,
                'eventColor' => "#f44336"
            ),
            array(
                'eventName' =>"Crap",
                'isEventAuto' => 0,
                'eventColor' => "#2196F3"
            ),
            array(
                'eventName' =>"Out of Stock SC",
                'isEventAuto' => 0,
                'eventColor' => "#03ffc5"
            ),
            array(
                'eventName' =>"Price change",
                'isEventAuto' => 1,
                'eventColor' => "#795548"
            ),
            array(
                'eventName' =>"Seller change",
                'isEventAuto' => 1,
                'eventColor' => "#9c27b0"
            ),
            array(
                'eventName' =>"Content Change",
                'isEventAuto' => 1,
                'eventColor' => "#4CAF50"
            ) ,
            array(
                'eventName' =>"Out of Stock VC",
                'isEventAuto' => 0,
                'eventColor' => "#ff9800"
            ),
            array(
                'eventName' =>"Offer not found",
                'isEventAuto' => 1,
                'eventColor' => "#fbff03"
            )
        );  
        EventsModel::insert($data);
    }
}
