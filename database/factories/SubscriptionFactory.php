<?php

namespace Database\Factories;

use App\Models\Plan;
use App\Models\Subscription;
use Illuminate\Database\Eloquent\Factories\Factory;

class SubscriptionFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'price' => function (array $attributes) {
                return Plan::find($attributes['profile_id'])->price;
            },
            'duration' => function (array $attributes) {
                return Plan::find($attributes['profile_id'])->duration;
            },
            'payment_id' => 'PAYID-MIEEYEQ9AT89072W84764009',
            'payment_status' => $this->faker->randomElement(Subscription::PAYMENT_STATUSES),
            'status' => $this->faker->randomElement(Subscription::STATUSES),
            'start_date' => now()->startOfDay(),
            'end_date' => function (array $attributes) {
                return now()->addDays(Plan::find($attributes['profile_id'])->duration)->endOfDay();
            },
        // 'end_date' => now()->addDays($plan->duration)->endOfDay(),
        ];
    }
}
