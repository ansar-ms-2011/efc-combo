<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class ArchivePermissionSeeder extends Seeder 
{
public function run(): void
{
    $allPermissions = [

        'archived-dashboard.view',
        'archived-scanner.view',
        'archived-scanner.create',
        'archived-scanner.edit',
        'archived-scanner.delete',
        'archived-verification.view',
        // 'archived-verification.create', 
        'archived-verification.edit',
        'archived-verification.delete', 
    ];

    foreach ($allPermissions as $permission) {
    Permission::firstOrCreate(['name' => $permission, 'guard_name' => 'web']);
         

    }

    // Roles
    $scannerRole = Role::firstOrCreate(['name' => 'Scanner', 'guard_name' => 'web']);
    $supervisorRole = Role::firstOrCreate(['name' => 'Supervisor', 'guard_name' => 'web']);
    $superAdminRole = Role::where('name', 'Super Admin')->first();

    //sacnner  permissions
    $scannerRole->syncPermissions([
        'archived-dashboard.view',
        'archived-scanner.view',
        'archived-scanner.create',
        'archived-verification.view'
    ]);

        $supervisorRole->syncPermissions($allPermissions);
        if ($superAdminRole) {
            $superAdminRole->givePermissionTo($allPermissions);
        }
}
}