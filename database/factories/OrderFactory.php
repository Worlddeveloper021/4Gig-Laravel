<?php

namespace Database\Factories;

use App\Models\Order;
use App\Models\Package;
use App\Models\Profile;
use App\Models\Category;
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
        return [
            'profile_id' => Profile::factory([
                'category_id' => function () {
                    return Category::first()->id;
                },
                'sub_category_id' => function () {
                    return Category::first()->children->first()->id;
                },
            ]),
            'customer_id' => Customer::factory(),
            'package_id' => function (array $attributes) {
                return Package::factory(['profile_id' => $attributes['profile_id']]);
            },
            'duration' => function (array $attributes) {
                return Package::find($attributes['package_id'])->duration;
            },
            'price' => function (array $attributes) {
                return Package::find($attributes['package_id'])->price;
            },
            'status' => $this->faker->randomElement(Order::STATUSES),
            'payment_status' => $this->faker->randomElement(['pending', 'done', 'canceled']),
            'payment_id' => $this->faker->uuid,
            'channel_name' => $this->faker->unique()->word,
            'call_type' => $this->faker->randomElement(Order::CALL_TYPES),
            'access_token' => $this->faker->uuid,
        ];
    }
}
