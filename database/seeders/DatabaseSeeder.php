<?php

namespace Database\Seeders;

use Artisan;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Log;


class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            //Role and Permissions Seeders
            RoleSeeder::class,
            PermissionsSeeder::class,
            AdditionPermissionSeeder::class,
            AssignPermissionSeeder::class,


            //Dropdowns Seeders
            
            TypeSeeder::class,
            CenterWorkingDaysSeeder::class,
            DemographySeeder::class,
            CenterSeeder::class,
            DepartmentSeeder::class,
            ServiceSeeder::class,
            ServiceInstructionSeeder::class,
            DuplicateReasonSeeder::class,
            RequiredDocumentSeeder::class,
            //----Placed here so that Districts and Tehsils are created before users----//
            UserSeeder::class,
            TemplateSeeder::class,
        ]);

        // Clear cache
        try {
            Artisan::call('optimize:clear');
        } catch (\Exception $e) {
            Log::error('optimize:clear failed. ' . $e->getMessage());
        }
    }
}
