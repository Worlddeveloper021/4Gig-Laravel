<?php

namespace Tests\Feature\Api\V1;

use Tests\TestCase;
use App\Models\Package;
use App\Models\Profile;
use App\Models\Customer;
use Laravel\Sanctum\Sanctum;
use Illuminate\Foundation\Testing\RefreshDatabase;

class OrderTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function customer_can_create_an_order()
    {
        $profile = Profile::factory()->has(Package::factory())->create();
        $customer = Customer::factory()->create();

        Sanctum::actingAs($customer->user);

        $response = $this->json('post', route('v1.orders.store', $profile), [
            'package_id' => $profile->packages->first()->id,
            'payment_id' => 1,
        ]);

        $response->assertOk();

        $this->assertDatabaseHas('orders', [
            'customer_id' => $customer->id,
            'profile_id' => $profile->id,
            'package_id' => $profile->packages->first()->id,
            'duration' => $profile->packages->first()->duration,
            'price' => $profile->packages->first()->price,
            'payment_id' => 1,
        ]);
    }

    /** @test */
    public function customer_can_not_create_an_order_with_invalid_package_id()
    {
        $profile = Profile::factory()->has(Package::factory())->create();
        $customer = Customer::factory()->create();

        Sanctum::actingAs($customer->user);

        $response = $this->json('post', route('v1.orders.store', $profile), [
            'package_id' => $profile->packages->first()->id + 1,
            'payment_id' => 1,
        ]);

        $response->assertJsonValidationErrorFor('package_id');

        $this->assertDatabaseMissing('orders', [
            'customer_id' => $customer->id,
            'profile_id' => $profile->id,
            'package_id' => $profile->packages->first()->id + 1,
            'duration' => $profile->packages->first()->duration,
            'price' => $profile->packages->first()->price,
            'payment_id' => 1,
        ]);
    }

    /** @test */
    public function customer_can_not_create_an_order_with_invalid_profile_id()
    {
        $profile = Profile::factory()->has(Package::factory())->create();
        $customer = Customer::factory()->create();

        Sanctum::actingAs($customer->user);

        $response = $this->json('post', route('v1.orders.store', $profile->id + 2), [
            'package_id' => 1,
            'payment_id' => 1,
        ]);

        $response->assertNotFound();

        $this->assertDatabaseMissing('orders', [
            'customer_id' => $customer->id,
            'profile_id' => $profile->id,
            'package_id' => $profile->packages->first()->id,
            'duration' => $profile->packages->first()->duration,
            'price' => $profile->packages->first()->price,
            'payment_id' => 1,
        ]);
    }

    /** @test */
    public function customer_can_not_create_an_order_with_invalid_customer_id()
    {
        $profile = Profile::factory()->has(Package::factory())->create();

        Sanctum::actingAs($profile->user);

        $response = $this->json('post', route('v1.orders.store', $profile), [
            'package_id' => 1,
            'payment_id' => 1,
        ]);

        $response->assertJsonValidationErrors(['user']);

        $this->assertDatabaseMissing('orders', [
            'customer_id' => 1,
            'profile_id' => $profile->id,
            'package_id' => $profile->packages->first()->id,
            'duration' => $profile->packages->first()->duration,
            'price' => $profile->packages->first()->price,
            'payment_id' => 1,
        ]);
    }
}
