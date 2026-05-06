<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $roles = [
            ['name' => 'Super Admin', 'guard_name' => 'web', 'sort_order' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Commissioner', 'guard_name' => 'web', 'sort_order' => 2, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'DC', 'guard_name' => 'web', 'sort_order' => 3, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'AC', 'guard_name' => 'web', 'sort_order' => 4, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'ACR', 'guard_name' => 'web', 'sort_order' => 5, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Center In-charge', 'guard_name' => 'web', 'sort_order' => 6, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'DEO', 'guard_name' => 'web', 'sort_order' => 7, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Patwari', 'guard_name' => 'web', 'sort_order' => 7, 'created_at' => now(), 'updated_at' => now()],
            
        ];

        Role::insert($roles);
    }
}
