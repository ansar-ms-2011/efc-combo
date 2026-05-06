<?php

namespace Database\Factories;

use App\Models\Applicant;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Applicant>
 */
class ApplicantFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'uuid' => Str::uuid(),

            'full_name' => $this->faker->name(),
            'identity_number' => rand(10000,99999).'-'.rand(1000000,9999999).'-'.rand(1,9),
            'identity_type' => 'CNIC',
            'identity_symbol' => 'Mole on face',
            'guardian_type_id' => 40,
            'religion_id' => 43,
            'gender_id' => 48,
            'marital_status_id' => 54,

            'dob' => $this->faker->date(),
            'pob' => $this->faker->city(),

            'father_name' => $this->faker->name(),
            'father_identity_number' => rand(10000,99999).'-'.rand(1000000,9999999).'-'.rand(1,9),

            'email' => $this->faker->safeEmail(),
            'phone' => '03'.rand(100000000,999999999),

            'occupation' => $this->faker->jobTitle(),

            'address' => $this->faker->address(),

            'status' => 1,
        ];
    }
}
