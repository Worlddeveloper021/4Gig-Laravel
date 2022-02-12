<?php

namespace Database\Factories;

use App\Models\Package;
use App\Models\Profile;
use App\Models\Customer;
use Illuminate\Database\Eloquent\Factories\Factory;

class OrderFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $profile = Profile::factory()->for(Package::factory())->create();
        $package = $profile->packges->first();

        return [
            'profile_id' => $profile->id,
            'customer_id' => Customer::factory()->create()->id,
            'package_id' => $package->id,
            'duration' => $package->duration,
            'price' => $package->price,
            'status' => $this->faker->numberBetween(0, 2),
            'payment_status' => $this->faker->randomElement(['pending', 'done', 'canceled']),
            'payment_id' => $this->faker->uuid,
            'channel_name' => $this->faker->unique()->word,
        ];
    }
}
