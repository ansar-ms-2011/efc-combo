<?php

namespace Database\Seeders;

use App\Models\Module;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class AdditionPermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $super_admin = Role::where('name', 'Super Admin')->first();
        $permissions = [
            'applications-all.view',
            'applications-pending.view',
            'applications-for-verification.view',
            'applications-for-approval.view',
            'applications-for-printing.view',
            'applications-for-delivery.view',
            'applications-delivered.view',
            'applications-objected.view',
            'applications.view-document',
            'required-documents.view',
            'schedule-jobs.view',
            'online-applications.view',
        ];
        foreach ($permissions as $item) {
            Permission::firstOrCreate([
                'name' => $item,
                'module_id' => null,
                'guard_name' => 'web'
            ]);
        }
        $list = Permission::whereIn('name', $permissions)->get();

        // Assign all permissions to Super Admin
        $super_admin->givePermissionTo($list);
    }
}
