<?php

namespace Database\Factories;

use App\Models\User;
use App\Models\Profile;
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
        $profile_type = rand(0, 1);

        return [
            'first_name' => $this->faker->firstName(),
            'last_name' => $this->faker->lastName(),
            'gender' => rand(0, 1),
            'nationality' => $this->faker->countryCode(),
            'profile_type' => $profile_type,
            'availability_on_demand' => ($profile_type === Profile::SELLER) ? rand(0, 1) : null,
            'per_hour' => ($profile_type === Profile::SELLER) ? rand(10, 100) : null,
        ];
    }

    public function hasUser()
    {
        return $this->state(function () {
            return [
                'user_id' => User::factory()->create()->id,
            ];
        });
    }
}
