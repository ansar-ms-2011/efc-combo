<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ServiceCenter;
use App\Models\Service;
use App\Models\Center;

use function Symfony\Component\Clock\now;

class ServiceCenterSeeder extends Seeder
{
    
      public function run(): void
    {
        $services = Service::orderBy('id')->get();
        $centers  = Center::orderBy('id')->get();

        if ($services->isEmpty() || $centers->isEmpty()) {
            $this->command->info('Services or Centers not found.');
            return;
        }

        $centerIndex = 0;

        foreach ($services as $service) {

            $center = $centers[$centerIndex];

            ServiceCenter::firstOrCreate([
                'service_id' => $service->id,
                'center_id'  => $center->id,
            ]);

            // next center (loop)
            $centerIndex++;
            if ($centerIndex >= $centers->count()) {
                $centerIndex = 0;
            }
        }

    }
}
