<?php

namespace Database\Factories;

use App\Models\SpokenLanguage;
use Illuminate\Database\Eloquent\Factories\Factory;

class SpokenLanguageFactory extends Factory
{
    protected $model = SpokenLanguage::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'name' => $this->faker->word,
        ];
    }
}
