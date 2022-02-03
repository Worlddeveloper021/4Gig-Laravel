<?php

namespace Tests\Feature\Api\V1;

use Notification;
use Tests\TestCase;
use App\Models\User;
use App\Models\Customer;
use Illuminate\Support\Facades\Hash;
use Illuminate\Foundation\Testing\RefreshDatabase;

class CustomerTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_can_register_a_customer()
    {
        Notification::fake();

        $response = $this->json('post', route('v1.customers.store'), [
            'first_name' => 'John',
            'last_name' => 'Doe',
            'mobile' => '0123456789',
            'password' => 'password',
        ]);

        $response->assertOk();

        $this->assertDatabaseHas('users', [
            'mobile' => '0123456789',
        ]);

        $user = User::first();

        $this->assertTrue(Hash::check('password', $user->password));
        $this->assertNotNull($user->verify_code);
        $this->assertNull($user->mobile_verified_at);

        $this->assertDatabaseHas('customers', [
            'first_name' => 'John',
            'last_name' => 'Doe',
            'user_id' => $user->id,
        ]);

        Notification::assertSentTo($user, \App\Notifications\VerifyCustomerNotification::class);
    }

    /** @test */
    public function it_can_verify_a_customer()
    {
        $user = User::factory(['mobile' => '0123456789', 'verify_code' => '123456'])->create();
        Customer::factory(['user_id' => $user->id])->create();

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

    /** @test */
    public function customer_can_login()
    {
        $user = User::factory(['mobile' => '0912345678'])->create();
        Customer::factory(['user_id' => $user->id])->create();

        $response = $this->json('post', route('v1.customers.login'), [
            'mobile' => $user->mobile,
            'password' => 'password',
        ]);

        $response->assertOk()
            ->assertJsonStructure(['token']);
    }
}
