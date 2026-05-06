<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CenterWorkingDaysSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $centers = DB::table('centers')->get();

        $workingDayIds = DB::table('types')->where('parent_id', 24)->pluck('id')->toArray();

        foreach ($centers as $center) {
            foreach ($workingDayIds as $dayId) {
                DB::table('center_working_days')->insert([
                    'center_id'      => $center->id,
                    'working_day_id' => $dayId,
                    'created_at'     => now(),
                    'updated_at'     => now(),
                ]);
            }
        }
    }
}