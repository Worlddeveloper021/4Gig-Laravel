<?php

namespace Tests\Feature\Api\V1;

use Tests\TestCase;
use App\Models\User;
use App\Models\Profile;
use Laravel\Sanctum\Sanctum;
use Illuminate\Foundation\Testing\RefreshDatabase;

class OnlineUserTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function check_user_is_online()
    {
        $user = User::factory()->create();
        $this->assertFalse($user->is_online());

        $user->record_last_activity();
        $this->assertTrue($user->is_online());

        // cache not expired yet
        $this->travelTo(now()->addMinutes(1));
        $this->assertTrue($user->is_online());

        // cache expired
        $this->travelTo(now()->addMinutes(3));
        $this->assertFalse($user->is_online());
    }

    /** @test */
    public function check_user_online_with_middleware()
    {
        $user = User::factory()->create();
        $this->assertFalse($user->is_online());

        Sanctum::actingAs($user);

        $this->json('get', route('v1.user.current'))->assertOk();

        $this->assertTrue($user->is_online());

        $this->travelTo(now()->addMinutes(1));
        $this->assertTrue($user->is_online());

        $this->travelTo(now()->addMinutes(2));
        $this->assertFalse($user->is_online());

        $this->json('get', route('v1.user.current'))->assertOk();
        $this->assertTrue($user->is_online());
    }

    /** @test */
    public function get_online_users()
    {
        User::factory()
            ->count(5)
            ->has(Profile::factory())
            ->create();

        User::factory()
            ->has(Profile::factory())
            ->count(10)
            ->create()
            ->each(function ($user) {
                $user->record_last_activity();
            });

        $response = $this->json('get', route('v1.users.online'));
        $response->assertOk();

        $response->assertJsonCount(10);
    }
}
