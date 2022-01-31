<?php

namespace Tests\Feature\Api\V1;

use Tests\TestCase;
use App\Models\User;
use App\Models\Customer;
use Laravel\Sanctum\Sanctum;
use Illuminate\Foundation\Testing\RefreshDatabase;

class CustomerCardTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_can_create_a_customer_card()
    {
        $user = User::factory()->create();
        $customer = Customer::factory(['user_id' => $user])->create();

        Sanctum::actingAs($user);

        $response = $this->json('post', route('v1.customers.card.store'), [
            'name' => 'Visa',
            'card_number' => '1234567890123456',
            'expiry_date' => '12/22',
            'cvc' => '123',
        ]);

        $response->assertStatus(200);

        $this->assertDatabaseHas('cards', [
            'customer_id' => $customer->id,
            'name' => 'Visa',
            'card_number' => '1234567890123456',
            'expiry_date' => '12/22',
            'cvc' => '123',
        ]);
    }

    /** @test */
    public function it_should_have_customer_to_create_card()
    {
        $user = User::factory()->create();

        Sanctum::actingAs($user);

        $response = $this->json('post', route('v1.customers.card.store'), [
            'name' => 'Visa',
            'card_number' => '1234567890123456',
            'expiry_date' => '12/22',
            'cvc' => '123',
        ]);

        $response->assertStatus(422);

        $this->assertDatabaseMissing('cards', [
            'name' => 'Visa',
            'card_number' => '1234567890123456',
            'expiry_date' => '12/22',
            'cvc' => '123',
        ]);
    }

    /** @test */
    public function it_should_store_card_once()
    {
        $user = User::factory()->create();
        $customer = Customer::factory(['user_id' => $user])->create();

        Sanctum::actingAs($user);

        $response = $this->json('post', route('v1.customers.card.store'), [
            'name' => 'Visa',
            'card_number' => '1234567890123456',
            'expiry_date' => '12/22',
            'cvc' => '123',
        ]);

        $response->assertStatus(200);

        $this->assertDatabaseHas('cards', [
            'customer_id' => $customer->id,
            'name' => 'Visa',
            'card_number' => '1234567890123456',
            'expiry_date' => '12/22',
            'cvc' => '123',
        ]);

        $response = $this->json('post', route('v1.customers.card.store'), [
            'name' => 'Test Test',
            'card_number' => '6543211234567890',
            'expiry_date' => '18/22',
            'cvc' => '321',
        ]);

        $response->assertStatus(422);

        $this->assertDatabaseHas('cards', [
            'customer_id' => $customer->id,
            'name' => 'Visa',
            'card_number' => '1234567890123456',
            'expiry_date' => '12/22',
            'cvc' => '123',
        ]);

        $this->assertDatabaseMissing('cards', [
            'customer_id' => $customer->id,
            'name' => 'Test Test',
            'card_number' => '6543211234567890',
            'expiry_date' => '18/22',
            'cvc' => '321',
        ]);
    }
}
