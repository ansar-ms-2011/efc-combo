<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Department;

use function Symfony\Component\Clock\now;

class DepartmentSeeder extends Seeder
{
    public function run(): void
    {
        Department::insert([
            [
                'name' => 'Local Govt and Rural Development AJK',
                'description' => null,
                'updated_by' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Board of Revenue AJK',
                'description' => null,
                'updated_by' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
