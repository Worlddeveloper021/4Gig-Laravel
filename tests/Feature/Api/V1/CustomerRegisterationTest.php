<?php

namespace Tests\Feature\Api\V1;

use Notification;
use Tests\TestCase;
use App\Models\User;
use App\Models\Customer;
use Illuminate\Foundation\Testing\RefreshDatabase;

class CustomerRegisterationTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_can_register_a_customer_step_1()
    {
        Notification::fake();

        $response = $this->json('post', route('v1.customers.store'), [
            'name' => 'John Doe',
            'mobile' => '0123456789',
        ]);

        $response->assertOk();

        $this->assertDatabaseHas('users', [
            'mobile' => '0123456789',
        ]);

        $user = User::first();

        $this->assertDatabaseHas('customers', [
            'name' => 'John Doe',
            'user_id' => $user->id,
        ]);

        $this->assertNotNull($user->verify_code);

        Notification::assertSentTo($user, \App\Notifications\VerifyCustomerNotification::class);
    }

    /** @test */
    public function it_can_verify_a_customer_step_2()
    {
        $user = User::factory(['mobile' => '0123456789', 'verify_code' => '123456'])->create();
        $customer = Customer::factory(['user_id' => $user->id])->create();

        $response = $this->json('post', route('v1.customers.verify'), [
            'mobile' => '0123456789',
            'verify_code' => '123456',
        ]);

        $response->assertOk()
            ->assertJsonStructure(['message', 'token']);

        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'mobile_verified_at' => now(),
        ]);
    }
}
