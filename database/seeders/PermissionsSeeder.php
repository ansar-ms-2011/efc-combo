<?php

namespace Database\Seeders;

use App\Models\Module;
use Illuminate\Support\Str;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;

class PermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $modules = [
            'dashboard',
            'departments',
            'centers',
            'services',
            'applications',
            'demographies',
            'users',
            'roles',
            'types',
            'service-instructions',
            'services-centers',
            'backups',
            'api-tokens',
            'templates',
        ];

        foreach ($modules as $item) {

            $module = Module::create([
                'name' => $item,
                'slug' => Str::slug($item)
            ]);

            foreach (['view', 'create', 'edit', 'delete'] as $action) {
                Permission::firstOrCreate([
                    'name' => $module->slug . '.' . $action,
                    'module_id' => $module->id,
                    'guard_name' => 'web'
                ]);
            }

            if ($module->slug === 'backups') {
                Permission::firstOrCreate([
                    'name' => 'backups.download',
                    'module_id' => $module->id,
                    'guard_name' => 'web',
                ]);
            }
        }
    }
}
