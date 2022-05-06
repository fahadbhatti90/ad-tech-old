<?php


use App\Models\EventTracking\CronEvent;
use Illuminate\Database\Seeder;

class EventTrackingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        CronEvent::truncate();
        $eventData = [
            [
                'cronEventName' => 'price-change',
                'created_at' => date('Y-m-d h:i:s'),
                'updated_at' => date('Y-m-d h:i:s')
            ],
            [
                'cronEventName' => 'seller-change',
                'created_at' => date('Y-m-d h:i:s'),
                'updated_at' => date('Y-m-d h:i:s')
            ],
            [
                'cronEventName' => 'review-change',
                'created_at' => date('Y-m-d h:i:s'),
                'updated_at' => date('Y-m-d h:i:s')
            ]
        ];
        CronEvent::insert($eventData);
    }
}
