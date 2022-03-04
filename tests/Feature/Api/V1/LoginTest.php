<?php

namespace Tests\Feature\Api\V1;

use Tests\TestCase;
use App\Models\User;
use App\Models\Profile;
use App\Models\Customer;
use Illuminate\Foundation\Testing\RefreshDatabase;

class LoginTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory(['email' => 'test@test.com'])->unverified()->create();
    }

    /** @test */
    public function user_can_login_email()
    {
        $this->assertDatabaseCount('users', 1);
        $this->assertDatabaseCount('personal_access_tokens', 0);

        $response = $this->json('post', route('v1.login'), [
            'email' => $this->user->email,
            'password' => 'password',
            'fcm_key' => '::fcm_key::',
        ]);

        $response->assertOk()
                 ->assertJsonStructure(['token']);

        $this->assertDatabaseCount('users', 1);
        $this->assertDatabaseHas('users', [
            'email' => $this->user->email,
            'fcm_key' => '::fcm_key::',
        ]);

        $this->assertDatabaseCount('personal_access_tokens', 1);

        $this->json('get', route('v1.user.current'), [], ['Authorization' => 'Bearer '.$response->json('token')])
             ->assertOk()
             ->assertJsonStructure(['id', 'username', 'email', 'mobile', 'email_verified_at', 'mobile_verified_at', 'is_online']);
    }

    /** @test */
    public function user_can_login_mobile()
    {
        $this->assertDatabaseCount('users', 1);
        $this->assertDatabaseCount('personal_access_tokens', 0);

        $response = $this->json('post', route('v1.login'), [
            'email' => $this->user->mobile,
            'password' => 'password',
            'fcm_key' => '::fcm_key::',
        ]);

        $response->assertOk()
                 ->assertJsonStructure(['token']);

        $this->assertDatabaseCount('users', 1);
        $this->assertDatabaseHas('users', [
            'mobile' => $this->user->mobile,
            'fcm_key' => '::fcm_key::',
        ]);

        $this->assertDatabaseCount('personal_access_tokens', 1);

        $this->json('get', route('v1.user.current'), [], ['Authorization' => 'Bearer '.$response->json('token')])
             ->assertOk()
             ->assertJsonStructure(['id', 'username', 'email', 'mobile', 'email_verified_at', 'mobile_verified_at', 'is_online']);
    }

    /** @test */
    public function user_can_not_login_with_incorrect_email()
    {
        $this->assertDatabaseCount('users', 1);
        $this->assertDatabaseCount('personal_access_tokens', 0);

        $response = $this->json('post', route('v1.login'), [
            'email' => 'email@test.com',
            'password' => 'password',
        ]);

        $response->assertUnprocessable();

        $this->assertDatabaseCount('users', 1);
        $this->assertDatabaseCount('personal_access_tokens', 0);
    }

    /** @test */
    public function user_can_not_login_with_incorrect_password()
    {
        $this->assertDatabaseCount('users', 1);
        $this->assertDatabaseCount('personal_access_tokens', 0);

        $response = $this->json('post', route('v1.login'), [
            'email' => $this->user->email,
            'password' => '12345678',
        ]);

        $response->assertUnprocessable();

        $this->assertDatabaseCount('users', 1);
        $this->assertDatabaseCount('personal_access_tokens', 0);
    }

    /** @test */
    public function seller_can_login_email()
    {
        Profile::factory(['user_id' => $this->user->id])->create();

        $this->assertDatabaseCount('users', 1);
        $this->assertDatabaseCount('personal_access_tokens', 0);

        $response = $this->json('post', route('v1.login'), [
            'email' => $this->user->email,
            'password' => 'password',
            'fcm_key' => '::fcm_key::',
        ]);

        $response->assertOk()
                 ->assertJsonStructure(['token', 'user_type']);

        $this->assertDatabaseCount('users', 1);
        $this->assertDatabaseHas('users', [
            'email' => $this->user->email,
            'fcm_key' => '::fcm_key::',
        ]);

        $this->assertDatabaseCount('personal_access_tokens', 1);

        $this->json('get', route('v1.user.current'), [], ['Authorization' => 'Bearer '.$response->json('token')])
             ->assertOk()
             ->assertJsonStructure(['id', 'username', 'email', 'mobile', 'email_verified_at', 'mobile_verified_at', 'is_online']);
    }

    /** @test */
    public function buyer_can_login_email()
    {
        Customer::factory(['user_id' => $this->user->id])->create();

        $this->assertDatabaseCount('users', 1);
        $this->assertDatabaseCount('personal_access_tokens', 0);

        $response = $this->json('post', route('v1.login'), [
            'email' => $this->user->email,
            'password' => 'password',
            'fcm_key' => '::fcm_key::',
        ]);

        $response->assertOk()
                 ->assertJsonStructure(['token', 'user_type']);

        $this->assertDatabaseCount('users', 1);
        $this->assertDatabaseHas('users', [
            'email' => $this->user->email,
            'fcm_key' => '::fcm_key::',
        ]);

        $this->assertDatabaseCount('personal_access_tokens', 1);

        $this->json('get', route('v1.user.current'), [], ['Authorization' => 'Bearer '.$response->json('token')])
             ->assertOk()
             ->assertJsonStructure(['id', 'username', 'email', 'mobile', 'email_verified_at', 'mobile_verified_at', 'is_online']);
    }
}
