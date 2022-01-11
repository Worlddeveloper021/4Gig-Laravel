<?php

namespace Tests\Feature\Api\V1;

use Tests\TestCase;
use App\Models\User;
use Laravel\Sanctum\Sanctum;
use Illuminate\Foundation\Testing\RefreshDatabase;

class VerificationTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory(['email' => 'test@test.com', 'verify_code' => '123456'])->unverified()->create();
    }

    /** @test */
    public function user_can_enter_verify_code()
    {
        Sanctum::actingAs($this->user);

        $response = $this->json('post', '/api/v1/verify', [
            'verify_code' => '123456',
        ]);

        $response->assertOk()->assertJson(['success' => true]);

        $this->assertDatabaseHas('users', [
            'id' => $this->user->id,
            'verify_code' => '123456',
            'email_verified_at' => now(),
        ]);
    }

    /** @test */
    public function veify_code_must_be_correct()
    {
        Sanctum::actingAs($this->user);

        $response = $this->json('post', '/api/v1/verify', [
            'verify_code' => '654321',
        ]);

        $response->assertJsonValidationErrors(['verify_code'])
                ->assertUnprocessable();

        $this->assertDatabaseHas('users', [
            'id' => $this->user->id,
            'verify_code' => '123456',
            'email_verified_at' => null,
        ]);
    }
}
