<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;


class ApplicationFactory extends Factory
{
    // public function definition(): array
    // {
    //     return [
    //         'uuid' => Str::uuid(),
    //         'current_status' => 'pending',
    //         'certificate_type' => $this->faker->randomElement(['domicile','state','both']),
    //         'token' => Str::upper(Str::random(10)) . time() . rand(100,999),
    //         'qmatic_token' => rand(1000,9999),

    //         'missalno' => rand(10000,99999),

    //         'first_name' => $this->faker->firstName(),
    //         'cnic' => rand(10000,99999).'-'.rand(1000000,9999999).'-'.rand(1,9),
    //         'dob' => $this->faker->date(),
    //         'pob' => $this->faker->city(),
    //         'identity_symbol' => 'Mole',

    //         'father_name' => $this->faker->name(),
    //         'father_cnic' => rand(10000,99999).'-'.rand(1000000,9999999).'-'.rand(1,9),

    //         'email' => $this->faker->unique()->safeEmail(),
    //         'phone' => '03'.rand(100000000,999999999),

    //         'occupation' => $this->faker->jobTitle(),
    //         'address' => $this->faker->address(),

    //         'entry_date' => now(),
    //         'entry_time' => now()->year,
    //         'entry_month' => now()->format('m'),

    //         'amount' => rand(100,500),

    //         'appointment_date' => now()->format('Y-m-d'),
    //         'appointment_time' => now()->format('H:i:s'),

    //         'status' => 0,
    //         'ondesk' => 'DEO'
    //     ];
    // }

    public function definition(): array
    {
        return [
            'uuid' => Str::uuid(),
            'current_status' => 'pending',

            'missal_no' => rand(10000, 99999),

            'application_type_id' => collect([1,2,3])->random(), // Assuming these are the IDs for 'domicile' and 'state'

            'entry_datetime' => now(),
            'region_id' => 95, 
            'district_id' => 98,
            'tehsil_id' => 108,


            'amount' => rand(100, 500),
            'certificate_type' => $this->faker->randomElement(['domicile', 'state', 'both']),

            'on_desk' => 'DEO',
        ];
    }
}
