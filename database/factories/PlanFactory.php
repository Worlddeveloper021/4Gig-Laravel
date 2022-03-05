<?php

namespace Database\Factories;

use App\Models\Plan;
use Illuminate\Database\Eloquent\Factories\Factory;

class PlanFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'name' => $this->faker->name,
            'description' => $this->faker->sentence,
            'price' => $this->faker->randomFloat(2, 50, 200),
            'duration' => $this->faker->numberBetween(1, 30),
            'status' => $this->faker->randomElement(Plan::STATUSES),
        ];
    }
}
