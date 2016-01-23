<?php

use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CalendarSeeder extends Seeder
{
    public function run()
    {
        DB::connection()->disableQueryLog();
        $data = collect([]);

        $begin = Carbon::create(2010, 01, 01, 0, 0, 0);
        $end = Carbon::create(2030, 12, 31, 23, 59, 59);

        $interval = new DateInterval('PT1H');
        $daterange = new DatePeriod($begin, $interval, $end->addHour());

        foreach ($daterange as $date) {
            $format = $date->format('Y-m-d H:i:s');
            $data->push([
                'calendar_datetime' => $format
            ]);
        }

        $dateChunks = $data->chunk(20000);

        foreach ($dateChunks->toArray() as $key => $chunk) {
            DB::table('bfacp_calendar')->insert($chunk);
        }
    }
}
