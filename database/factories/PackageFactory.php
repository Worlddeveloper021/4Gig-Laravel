<?php

namespace Database\Factories;

use App\Models\Package;
use App\Models\Profile;
use Illuminate\Database\Eloquent\Factories\Factory;

class PackageFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'profile_id' => Profile::factory(),
            'price' => $this->faker->randomFloat(2, 0, 100),
            'duration' => $this->faker->randomElement([30, 60]),
            'description' => $this->faker->sentence(),
            'on_demand' => $this->faker->randomElement(Package::ON_DEMAND_VALUES),
        ];
    }
}
