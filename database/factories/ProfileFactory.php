<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProfileFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'first_name' => $this->faker->firstName(),
            'last_name' => $this->faker->lastName(),
            'nationality' => $this->faker->country(),
            'birth_date' => $this->faker->date(),
            'gender' => rand(0, 1),
            'availability_on_demand' => rand(0, 1),
            'per_hour' => rand(10, 500),
        ];
    }

    public function has_user()
    {
        return $this->state(function () {
            return [
                'user_id' => User::factory()->create()->id,
            ];
        });
    }
}
