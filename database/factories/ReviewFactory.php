<?php

namespace Database\Factories;

use App\Models\Profile;
use App\Models\Customer;
use Illuminate\Database\Eloquent\Factories\Factory;

class ReviewFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'profile_id' => Profile::factory()->forUser(),
            'customer_id' => Customer::factory()->forUser(),
            'review' => $this->faker->text(),
            'rate' => $this->faker->numberBetween(1, 5),
        ];
    }
}
