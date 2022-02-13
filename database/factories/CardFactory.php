<?php

namespace Database\Factories;

use App\Models\Customer;
use Illuminate\Database\Eloquent\Factories\Factory;

class CardFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'name' => $this->faker->name(),
            'card_number' => $this->faker->creditCardNumber(),
            'expiry_date' => $this->faker->date(),
            'cvc' => $this->faker->numberBetween(100, 999),
            'customer_id' => Customer::factory(),
        ];
    }

    public function customer($id = null)
    {
        return $this->state([
            'customer_id' => $id ?? Customer::factory(),
        ]);
    }
}
