<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Log;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class AssignPermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $super_admin_role = Role::where('name', 'Super Admin')->first();
        $ac_role = Role::where('name', 'AC')->first();
        $dc_role = Role::where('name', 'DC')->first();
        $deo_role = Role::where('name', 'DEO')->first();

        $saPermissions = Permission::whereNotIn('name', [
            'applications.create',
            'applications.edit',
            'applications.delete',
        ])->get();

        $dcPermissions = Permission::whereIn('name', [
            'dashboard.view',
            'applications.view',
            'applications-for-approval.view'
        ])->get();

        // Assign permissions to DC
        $dc_role->givePermissionTo($dcPermissions);

        // Assign all permissions to Super Admin
        $super_admin_role->givePermissionTo($saPermissions);

        $acPermissions = Permission::whereIn('name', [
            'dashboard.view',
            'applications.view',
            'applications-for-verification.view'
        ])->get();

        // Assign permissions to AC
        $ac_role->givePermissionTo($acPermissions);

        $deoPermissions = Permission::whereIn('name', [
            'dashboard.view',
            'applications.create',
            'applications.edit',
            'applications.delete',
            'applications.view',
            'applications-all.view',
            'applications-pending.view',
            'applications-objected.view',
            'applications-for-printing.view',
            'applications-for-delivery.view',
        ])->get();

        // Assign all permissions to DEO
        $deo_role->givePermissionTo($deoPermissions);
    }
}
