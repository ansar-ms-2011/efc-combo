<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ServiceCenterUser;
use App\Models\ServiceCenter;
use App\Models\User;
use function Symfony\Component\Clock\now;

class ServiceCenterUserSeeder extends Seeder
{
   

    public function run(): void
    {
        $serviceCenters = ServiceCenter::orderBy('id')->get();
        $users = User::orderBy('id')->get();

        if ($serviceCenters->isEmpty() || $users->isEmpty()) {
            $this->command->info('ServiceCenters or Users not found.');
            return;
        }

        $userIndex = 0;

        foreach ($serviceCenters as $sc) {

            $user = $users[$userIndex];

            ServiceCenterUser::firstOrCreate([
                'service_center_id' => $sc->id,
                'user_id' => $user->id,
            ]);

            // next user (loop)
            $userIndex++;
            if ($userIndex >= $users->count()) {
                $userIndex = 0;
            }
        }

    }
}
