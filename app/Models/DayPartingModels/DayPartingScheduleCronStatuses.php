<?php

namespace App\models\DayPartingModels;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;

class DayPartingScheduleCronStatuses extends Model
{
    public $table = 'tbl_ams_day_parting_schedule_cron_statuses';
    public $timestamps = false;

    public static function insertScheduleStatuses($statusData)
    {
        foreach ($statusData as $data){
            $existData = DayPartingScheduleCronStatuses::select('*')
                ->where('scheduleDate', $data['scheduleDate'])
                ->where('fkScheduleId', $data['fkScheduleId'])
                ->exists();

            if (!$existData) {
                try {
                    DayPartingScheduleCronStatuses::insert($data);
                    Log::info('Schedule Cron Statuses Insertion');
                } catch (\Illuminate\Database\QueryException $ex) {
                    Log::error($ex->getMessage());
                }
            } else {
                DayPartingScheduleCronStatuses::
                where('fkScheduleId', $data['fkScheduleId'])
                    ->where('scheduleDate', $data['scheduleDate'])
                    ->update($data);
            }
        }

    }


    public static function updateScheduleStatuses($data){
        $existData = DayPartingScheduleCronStatuses::select('*')
            ->where('scheduleDate', $data['scheduleDate'])
            ->where('fkScheduleId', $data['fkScheduleId'])
            ->exists();

        if (!$existData) {
            try {
                DayPartingScheduleCronStatuses::insert($data);
                Log::info('Schedule Cron Statuses Insertion');
            } catch (\Illuminate\Database\QueryException $ex) {
                Log::error($ex->getMessage());
            }
        } else {
            DayPartingScheduleCronStatuses::
            where('fkScheduleId', $data['fkScheduleId'])
                ->where('scheduleDate', $data['scheduleDate'])
                ->update($data);
        }
    }
}
