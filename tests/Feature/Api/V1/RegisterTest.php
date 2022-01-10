<?php

namespace Tests\Feature\Api\V1;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class RegisterTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function user_can_register()
    {
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
    }

    /** @test */
    public function email_is_required()
    {
        $response = $this->json('post','/api/v1/register', [
            'email' => '',
            'password' => 'secret',
            'password_confirmation' => 'secret',
        ]);

        $response->assertJsonValidationErrors(['email']);
    }

    /** @test */
    public function email_must_be_valid()
    {
        $response = $this->json('post','/api/v1/register', [
            'email' => 'emailtest.com',
            'password' => 'secret',
            'password_confirmation' => 'secret',
        ]);

        $response->assertJsonValidationErrors(['email']);
    }
    
    /** @test */
    public function password_is_required()
    {
        $response = $this->json('post','/api/v1/register', [
            'email' => 'test@test.com',
            'password' => '',
            'password_confirmation' => 'secret',
        ]);

        $response->assertJsonValidationErrors(['password']);
    }

    /** @test */
    public function password_must_be_more_than_or_equal_to_6()
    {
        $response = $this->json('post','/api/v1/register', [
            'email' => 'test@test.com',
            'password' => '12345',
            'password_confirmation' => '12345',
        ]);

        $response->assertJsonValidationErrors(['password']);
    }

    /** @test */
    public function password_confirmation_must_be_more_than_or_equal_to_6()
    {
        $response = $this->json('post','/api/v1/register', [
            'email' => 'test@test.com',
            'password' => '123456',
            'password_confirmation' => '12345',
        ]);

        $response->assertJsonValidationErrors(['password']);
    }

    /** @test */
    public function password_confirmation_is_required()
    {
        $response = $this->json('post','/api/v1/register', [
            'email' => 'test@test.com',
            'password' => '123456',
            'password_confirmation' => '',
        ]);

        $response->assertJsonValidationErrors(['password']);
    }

    /** @test */
    public function password_and_password_confirmation_muse_be_equal()
    {
        $response = $this->json('post','/api/v1/register', [
            'email' => 'test@test.com',
            'password' => '123456',
            'password_confirmation' => 'secret',
        ]);

        $response->assertJsonValidationErrors(['password']);
    }
}
