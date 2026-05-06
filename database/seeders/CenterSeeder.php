<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\Demography;

class CenterSeeder extends Seeder
{
    public function run(): void
    {
        // =========================
        // GET TEHSILS AS ID MAP
        // =========================
        $tehsils = Demography::where('type', 'TEHSIL')
            ->get()
            ->keyBy('name');

        // =========================
        // CENTER DATA (NO STRINGS FOR RELATION)
        // =========================
        $centers = [
            ['name' => 'Muzaffarabad', 'tehsil' => 'Muzaffarabad', 'address' => 'Muzaffarabad center'],
            ['name' => 'Bagh', 'tehsil' => 'Bagh', 'address' => 'Main Road , Bagh City'],
            ['name' => 'Mirpur', 'tehsil' => 'Mirpur', 'address' => 'Main Road, Mirpur'],
            ['name' => 'Rawlakot', 'tehsil' => 'Rawalakot', 'address' => 'Rawlakot city'],
            ['name' => 'Bhimber', 'tehsil' => 'Bhimber', 'address' => 'LRMIS Center Bhimber'],
            ['name' => 'Trar Khal', 'tehsil' => 'Tarar Khal', 'address' => 'LRMIS Center Trar Khal'],
            ['name' => 'Khui Ratta', 'tehsil' => 'Khuiratta', 'address' => 'LRMIS Center Khui Ratta'],
            ['name' => 'Authmuqam', 'tehsil' => 'Athmuqam', 'address' => 'District complex Authmuqam'],
            ['name' => 'Sharda', 'tehsil' => 'Sharda', 'address' => 'Tehsil HQ Sharda'],
            ['name' => 'Pattika', 'tehsil' => 'Pattika', 'address' => 'Tehsil HQ Pattika Naseerabad'],
            ['name' => 'Hattian', 'tehsil' => 'Hattian Bala', 'address' => 'AC office Hattian'],
            ['name' => 'Chikar', 'tehsil' => 'Chikar', 'address' => 'AC office Chikar Jehlum Valley'],
            ['name' => 'Leepa', 'tehsil' => 'Leepa', 'address' => 'AC office Leepa'],
            ['name' => 'Palandari', 'tehsil' => 'Pallandri', 'address' => 'Palandari Sudhnoti'],
            ['name' => 'Haveli', 'tehsil' => 'Forward Kahuta', 'address' => 'AC Office Haveli'],
            ['name' => 'Kotli', 'tehsil' => 'Kotli', 'address' => 'AC Office Kotli'],
            ['name' => 'Dadyal', 'tehsil' => 'Dadyal', 'address' => 'AC Office Dadyal'],
            ['name' => 'Dhirkot', 'tehsil' => 'Dhirkot', 'address' => 'ACF Office Dhirkot'],
        ];

        // =========================
        // INSERT CENTERS
        // =========================
        foreach ($centers as $center) {

            $tehsil = $tehsils[$center['tehsil']] ?? null;

            if (!$tehsil) {
                throw new \Exception("TEHSIL NOT FOUND: " . $center['tehsil']);
            }

            DB::table('centers')->insert([
                'name' => $center['name'],
                'tehsil_id' => $tehsil->id,        // 🔥 MAIN RELATION
                'district_id' => $tehsil->parent_id,
                'address' => $center['address'],
                'timing' => '08:30-15:00',
                'number_of_counters' => 2,
                'lunch_break' => '13:00-13:30',
                'jumma_break' => '13:00-14:00',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}