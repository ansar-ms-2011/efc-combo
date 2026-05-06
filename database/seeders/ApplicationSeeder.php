<?php

namespace Database\Seeders;

use App\Models\Applicant;
use App\Models\Application;
use App\Models\ApplicationDocument;
use App\Models\Appointment;
use App\Models\Center;
use App\Models\Demography;
use App\Models\Type;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Arr;

class ApplicationSeeder extends Seeder
{
    // public function run(): void
    // {
    //     $total = 100;
    //     $chunkSize = 100;

    //     // preload all foreign ids ONCE
    //     $centerIds = Center::pluck('id')->toArray();
    //     $typeIds = Type::pluck('id')->toArray();
    //     $userIds = User::pluck('id')->toArray();
    //     $demographyIds = [98, 108];

    //     for ($i = 0; $i < $total; $i += $chunkSize)
    //     {
    //         $applications = Application::factory()
    //             ->count($chunkSize)
    //             ->make()
    //             ->map(function ($app) use (
    //                 $centerIds,
    //                 $typeIds,
    //                 $userIds,
    //                 $demographyIds
    //             ) {

    //                 $app->center_id = $centerIds[0];

    //                 $app->application_type_id = $typeIds[array_rand($typeIds)];
    //                 $app->application_for_id = $typeIds[array_rand($typeIds)];
    //                 $app->citizen_type_id = $typeIds[array_rand($typeIds)];
    //                 $app->guardian_type_id = $typeIds[array_rand($typeIds)];
    //                 $app->religion_id = $typeIds[array_rand($typeIds)];
    //                 $app->gender_id = $typeIds[array_rand($typeIds)];
    //                 $app->marital_status_id = $typeIds[array_rand($typeIds)];
    //                 $app->current_status = 'pending';
    //                 $app->district_id = $demographyIds[0];
    //                 $app->tehsil_id = $demographyIds[1];
    //                 $app->created_by = $userIds[array_rand($userIds)];
    //                 $app->updated_by = $userIds[array_rand($userIds)];

    //                 return $app->toArray();
    //             })
    //             ->toArray();

    //         Application::insert($applications);

    //         echo "Inserted: " . ($i + $chunkSize) . "\n";
    //     }
    // }


    public function run(): void
    {
        $total = 1000;

        $centerIds = Center::pluck('id')->toArray();
        $userIds = User::pluck('id')->toArray();
       


        for ($i = 0; $i < $total; $i++) {
            // ✅ 1. Applicant
            $applicant = Applicant::factory()->create([
                'created_by' => $userIds[array_rand($userIds)] ?? null,
            ]);

            // ✅ 2. Application
            $application = Application::factory()->create([
                'applicant_id' => $applicant->id,

                'center_id' => $centerIds[0] ?? null,
                // 'region_id' => $demographyIds[95],
                

                'created_by' => $userIds[array_rand($userIds)] ?? null,
                'updated_by' => $userIds[array_rand($userIds)] ?? null,
            ]);
            // ✅ ADD THIS (IMPORTANT)
            Appointment::create([
                'application_id' => $application->id,
                'qmatic_token' => rand(1000, 9999),

                'appointment_date' => now()->addDays(rand(1, 5)),
                'appointment_time' => now()->format('H:i:s'),
            ]);

            // ✅ 3. Documents (2 per application)
            ApplicationDocument::factory()
                ->count(2)
                ->create([
                    'application_id' => $application->id,
                    'created_by' => $userIds[array_rand($userIds)] ?? null,
                ]);
        }

        echo "✅ Done: {$total} applications with applicants & documents created\n";
    }
}
