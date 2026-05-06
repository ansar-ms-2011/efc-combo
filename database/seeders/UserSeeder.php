<?php

namespace Database\Seeders;

use App\Models\Center;
use App\Models\Demography;
use App\Models\Service;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {

        $regionId = Demography::where('type', 'REGION')
            ->where('name', 'Muzaffarabad')
            ->value('id');

        $districtId = Demography::where('type', 'DISTRICT')
            ->where('name', 'Muzaffarabad')
            ->value('id');

        $tehsilId = Demography::where('type', 'TEHSIL')
            ->where('parent_id', $districtId)
            ->where('name', 'Muzaffarabad')
            ->value('id');

        $center = Center::where('name', 'Muzaffarabad')->first();
        $centerId = $center?->id;

        DB::beginTransaction();

        // Super Admin
        $super_admin = User::create([
            'first_name' => 'Super',
            'last_name' => 'Admin',
            'prefix' => 'Mr.',
            'email' => 'superadmin@gmail.com',
            'password' => Hash::make('cdigital99'),
        ]);

        $super_admin->assignRole('Super Admin');

        // Commissioner Muzaffarabad
        $commissioner = User::create([
            'region_id' => $regionId,
            'first_name' => 'Commissioner',
            'last_name' => 'Muzaffarabad',
            'email' => 'commissioner@gmail.com',
            'password' => Hash::make('cdigital99'),
        ]);

        $commissioner->assignRole('Commissioner');

        // DC Muzaffarabad
        $dc = User::create([
            'first_name' => 'DC',
            'last_name' => 'Muzaffarabad',
            'email' => 'dc.muzaffarabad@gmail.com',
            'password' => Hash::make('cdigital99'),
            'region_id' => $regionId,
            'district_id' => $districtId,
        ]);

        $dc->assignRole('DC');

        // AC Muzaffarabad
        $ac = User::create([
            'first_name' => 'AC',
            'last_name' => 'Muzaffarabad',
            'email' => 'ac.muzaffarabad@gmail.com',
            'password' => Hash::make('cdigital99'),
            'district_id' => $districtId,
            'tehsil_id' => $tehsilId,
        ]);

        $ac->assignRole('AC');


        // ACR Muzaffarabad
        $acr = User::create([
            'first_name' => 'ACR',
            'last_name' => 'Muzaffarabad',
            'email' => 'acr.muzaffarabad@gmail.com',
            'password' => Hash::make('cdigital99'),
            'district_id' => $districtId,
            'tehsil_id' => $tehsilId,
        ]);

        $acr->assignRole('ACR');

        // DEO
        $deo = User::create([
            'first_name' => 'DEO',
            'last_name' => 'Muzaffarabad',
            'email' => 'deo.muzaffarabad@gmail.com',
            'password' => Hash::make('cdigital99'),
            'center_id' => $centerId,
            'district_id' => $districtId,
            'tehsil_id' => $tehsilId,
        ]);

        $deo->assignRole('DEO');

        $center = Center::whereName('Muzaffarabad')->first();
        $serviceIds = Service::all()->pluck('id')->toArray();
        if ($center) {
            $center->services()->sync($serviceIds);
            $deo->serviceCenters()->sync($center->services->pluck('id')->toArray());
        }

        // Center In-charge
        $ci = User::create([
            'first_name' => 'Center',
            'last_name' => 'In-charge',
            'email' => 'ca.muzaffarabad@gmail.com',
            'password' => Hash::make('cdigital99'),
            'center_id' => $centerId,
            'district_id' => $districtId,
            'tehsil_id' => $tehsilId,
        ]);

        $ci->assignRole('Center In-charge');
        if($center){
            $ci->serviceCenters()->sync($center->services->pluck('id')->toArray());
        }

        DB::commit();
    }
}
