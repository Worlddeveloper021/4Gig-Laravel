<?php

namespace Tests\Feature\Api\V1;

use Tests\TestCase;
use Illuminate\Support\Facades\Notification;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ExpirationTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function ensure_sanctum_expiration_works()
    {
        Notification::fake();

        $expiration_duration = config('sanctum.expiration', 0);

        $response = $this->json('post', route('v1.register'), [
            'email' => 'test@test.com',
            'password' => 'secret',
            'password_confirmation' => 'secret',
        ]);

        $response->assertSessionDoesntHaveErrors()
                ->assertOk()
                ->assertJsonStructure(['token']);

        $this->travelTo(now()->addMinutes($expiration_duration + 1));

        $this->json('get', 'api/user', [], ['Authorization' => 'Bearer '.$response->json('token')])
             ->assertUnauthorized();
    }
}
