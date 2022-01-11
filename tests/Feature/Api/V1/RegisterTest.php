<?php

namespace Tests\Feature\Api\V1;

use Tests\TestCase;
use App\Models\User;
use Illuminate\Support\Facades\Notification;
use Illuminate\Foundation\Testing\RefreshDatabase;

class RegisterTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function user_can_register()
    {
        Notification::fake();

        $response = $this->json('post', '/api/v1/register', [
            'email' => 'test@test.com',
            'password' => 'secret',
            'password_confirmation' => 'secret',
        ]);

        $response->assertSessionDoesntHaveErrors()
                ->assertOk()
                ->assertJsonStructure(['token']);

        $response_data = $response->json();

        [$id] = explode('|', $response_data['token'], 2);

        $this->assertDatabaseCount('users', 1);
        $this->assertDatabaseHas('users', [
            'email' => 'test@test.com',
            'email_verified_at' => null,
        ]);

        $this->assertDatabaseCount('personal_access_tokens', 1);
        $this->assertDatabaseHas('personal_access_tokens', [
            'tokenable_id' => $id,
            'name' => 'test-token',
        ]);

        $this->json('get', 'api/user', [], ['Authorization' => 'Bearer '.$response->json('token')])
             ->assertOk()
             ->assertJsonStructure(['id', 'email', 'created_at', 'updated_at']);

        Notification::assertSentTo(User::first(), \App\Notifications\VerifyEmail::class);
    }

    /** @test */
    public function email_is_required()
    {
        Notification::fake();

        $response = $this->json('post', '/api/v1/register', [
            'email' => '',
            'password' => 'secret',
            'password_confirmation' => 'secret',
        ]);

        $response->assertJsonValidationErrors(['email']);
        Notification::assertNothingSent();
    }

    /** @test */
    public function email_must_be_valid()
    {
        Notification::fake();

        $response = $this->json('post', '/api/v1/register', [
            'email' => 'emailtest.com',
            'password' => 'secret',
            'password_confirmation' => 'secret',
        ]);

        $response->assertJsonValidationErrors(['email']);

        Notification::assertNothingSent();
    }

    /** @test */
    public function password_is_required()
    {
        Notification::fake();

        $response = $this->json('post', '/api/v1/register', [
            'email' => 'test@test.com',
            'password' => '',
            'password_confirmation' => 'secret',
        ]);

        $response->assertJsonValidationErrors(['password']);

        Notification::assertNothingSent();
    }

    /** @test */
    public function password_must_be_more_than_or_equal_to_6()
    {
        Notification::fake();

        $response = $this->json('post', '/api/v1/register', [
            'email' => 'test@test.com',
            'password' => '12345',
            'password_confirmation' => '12345',
        ]);

        $response->assertJsonValidationErrors(['password']);

        Notification::assertNothingSent();
    }

    /** @test */
    public function password_confirmation_must_be_more_than_or_equal_to_6()
    {
        Notification::fake();

        $response = $this->json('post', '/api/v1/register', [
            'email' => 'test@test.com',
            'password' => '123456',
            'password_confirmation' => '12345',
        ]);

        $response->assertJsonValidationErrors(['password']);

        Notification::assertNothingSent();
    }

    /** @test */
    public function password_confirmation_is_required()
    {
        Notification::fake();

        $response = $this->json('post', '/api/v1/register', [
            'email' => 'test@test.com',
            'password' => '123456',
            'password_confirmation' => '',
        ]);

        $response->assertJsonValidationErrors(['password']);

        Notification::assertNothingSent();
    }

    /** @test */
    public function password_and_password_confirmation_muse_be_equal()
    {
        Notification::fake();

        $response = $this->json('post', '/api/v1/register', [
            'email' => 'test@test.com',
            'password' => '123456',
            'password_confirmation' => 'secret',
        ]);

        $response->assertJsonValidationErrors(['password']);

        Notification::assertNothingSent();
    }
}
