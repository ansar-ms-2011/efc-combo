<?php

namespace Database\Seeders;

use App\Models\Service;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ServiceSeeder extends Seeder
{
    public function run(): void
    {
        $departments = DB::table('departments')->pluck('id')->toArray();

        Service::insert([
            [
                'id' => 1,
                'name' => 'Birth Certificate',
                'dept_id' => $departments[0],
                'no_of_days' => '3',
                'service_icon' => 'fa fa-heart',
                'service_description' => 'To Apply for Birth Certificate',
                'file' => '',
                'price' => 750,                
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 2,
                'name' => 'Death Certificate',
                'dept_id' => $departments[0],
                'no_of_days' => '3',
                'service_icon' => 'fas fa-ambulance',
                'service_description' => 'Death certificate',
                'file' => '',
                'price' => 1000,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 3,
                'name' => 'Arms License',
                'dept_id' => $departments[1],
                'no_of_days' => '7',
                'service_icon' => 'fas fa-anchor',
                'service_description' => 'Acquiring Arms License in AJK Only.',
                'file' => '',
                'price' => 10000,               
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 4,
                'name' => 'State Subject Certificate',
                'dept_id' => $departments[1],
                'no_of_days' => '7',
                'service_icon' => 'fab fa-accusoft',
                'service_description' => 'To get AJK Residential Status',
                'file' => '',
                'price' => 5000,              
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 5,
                'name' => 'Domicile',
                'dept_id' => $departments[1],
                'no_of_days' => '7',
                'service_icon' => 'fas fa-certificate',
                'service_description' => 'To get Domicile of AJ&K',
                'file' => '',
                'price' => 500,                
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
