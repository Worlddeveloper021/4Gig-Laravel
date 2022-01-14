<?php

namespace Tests\Feature\Api\V1;

use DB;
use Tests\TestCase;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ForgotPasswordTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory(['email' => 'test@test.com'])->unverified()->create();
    }

    /** @test */
    public function user_can_request_reset_password()
    {
        Notification::fake();

        $this->assertDatabaseCount('password_resets', 0);

        $response = $this->json('post', route('v1.forgot_password.request'), [
            'email' => $this->user->email,
        ]);

        $response->assertOk()
            ->assertJsonStructure(['message', 'success']);

        $this->assertDatabaseCount('password_resets', 1);
        $this->assertDatabaseHas('password_resets', [
            'email' => $this->user->email,
        ]);

        Notification::assertSentTo($this->user, \App\Notifications\ResetPasswordNotification::class);
    }

    /** @test */
    public function user_can_not_request_reset_password_with_invalid_email()
    {
        Notification::fake();

        $this->assertDatabaseCount('password_resets', 0);

        $response = $this->json('post', route('v1.forgot_password.request'), [
            'email' => 'wrong@email.com',
        ]);

        $response->assertUnprocessable()
            ->assertJsonStructure(['message', 'errors']);

        $this->assertDatabaseCount('password_resets', 0);
        $this->assertDatabaseMissing('password_resets', [
            'email' => $this->user->email,
        ]);

        Notification::assertNothingSent();
    }

    /** @test */
    public function user_can_enter_token()
    {
        DB::table('password_resets')->insert([
            'email' => $this->user->email,
            'token' => '123456',
            'created_at' => now(),
        ]);

        $response = $this->json('post', route('v1.forgot_password.verify'), [
            'email' => $this->user->email,
            'token' => '123456',
        ]);

        $response->assertOk()
                ->assertJsonStructure(['message', 'success']);
    }

    /** @test */
    public function token_should_be_same()
    {
        DB::table('password_resets')->insert([
            'email' => $this->user->email,
            'token' => '123456',
            'created_at' => now(),
        ]);

        $response = $this->json('post', route('v1.forgot_password.verify'), [
            'email' => $this->user->email,
            'token' => '654321',
        ]);

        $response->assertUnprocessable()
                ->assertJsonStructure(['message', 'errors' => ['token']]);
    }

    /** @test */
    public function user_can_reset_password()
    {
        $this->assertTrue(Hash::check('password', $this->user->password));

        $response = $this->json('post', route('v1.forgot_password.reset'), [
            'email' => $this->user->email,
            'password' => 'new-password',
            'password_confirmation' => 'new-password',
        ]);

        $response->assertOk()
            ->assertJsonStructure(['message', 'success']);

        $this->assertFalse(Hash::check('password', $this->user->fresh()->password));
        $this->assertTrue(Hash::check('new-password', $this->user->fresh()->password));
    }
}
