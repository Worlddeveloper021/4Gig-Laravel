<?php

namespace Database\Factories;

use App\Models\Category;
use Illuminate\Database\Eloquent\Factories\Factory;

class CategoryFactory extends Factory
{

    protected $model = Category::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'name' => $this->faker->word(),
        ];
    }

    public function parent($id = null)
    {
        return $this->state([
            'parent_id' => $id ?? Category::factory()->create()->id,
        ]);
    }
}
