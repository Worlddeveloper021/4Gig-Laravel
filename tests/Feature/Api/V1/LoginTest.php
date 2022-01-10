<?php

namespace Tests\Feature\Api\V1;

use Tests\TestCase;
use App\Models\User;
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
    public function user_can_login()
    {
        $this->assertDatabaseCount('users', 1);
        $this->assertDatabaseCount('personal_access_tokens', 0);

        $response = $this->json('post', 'api/v1/login', [
            'email' => $this->user->email,
            'password' => 'password',
        ]);

        $response->assertOk()
                 ->assertJsonStructure(['token']);

        $this->assertDatabaseCount('users', 1);
        $this->assertDatabaseCount('personal_access_tokens', 1);
    }

    /** @test */
    public function user_can_not_login_with_incorrect_email()
    {
        $this->assertDatabaseCount('users', 1);
        $this->assertDatabaseCount('personal_access_tokens', 0);

        $response = $this->json('post', 'api/v1/login', [
            'email' => 'email@test.com',
            'password' => 'password',
        ]);

        $response->assertJsonValidationErrors(['email']);

        $this->assertDatabaseCount('users', 1);
        $this->assertDatabaseCount('personal_access_tokens', 0);
    }

    /** @test */
    public function user_can_not_login_with_incorrect_password()
    {
        $this->assertDatabaseCount('users', 1);
        $this->assertDatabaseCount('personal_access_tokens', 0);

        $response = $this->json('post', 'api/v1/login', [
            'email' => $this->user->email,
            'password' => '12345678',
        ]);

        $response->assertJsonValidationErrors(['email']);

        $this->assertDatabaseCount('users', 1);
        $this->assertDatabaseCount('personal_access_tokens', 0);
    }
}
