<?php

namespace Database\Factories;

use App\Models\User;
use App\Models\Profile;
use App\Models\Category;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProfileFactory extends Factory
{
    /**
     * Configure the model factory.
     *
     * @return $this
     */
    public function configure()
    {
        return $this->afterCreating(function (Profile $profile) {
            $file = storage_path('app/assets/avatar.jpg');

            $profile
                ->user
                ->copyMedia($file)
                ->toMediaCollection(User::AVATAR_COLLECTION_NAME);
        });
    }

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
            'user_id' => User::factory(),
            'description' => $this->faker->paragraph(),
            // 'category_id' => $category_id = Category::factory()->create()->id,
            // 'sub_category_id' => Category::factory()->create(['parent_id' => $category_id])->id,
        ];
    }
}
