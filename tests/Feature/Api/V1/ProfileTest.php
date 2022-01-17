<?php

namespace Tests\Feature\Api\V1;

use Tests\TestCase;
use App\Models\User;
use App\Models\Profile;
use Laravel\Sanctum\Sanctum;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ProfileTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;

    public function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create();
    }

    /** @test */
    public function user_can_create_profile()
    {
        Sanctum::actingAs($this->user);

        $fake_data = Profile::factory()->definition();

        $resoponse = $this->json('post', route('v1.profiles.store'), $fake_data);
        $resoponse->assertCreated();

        $this->assertDatabaseCount('profiles', 1);
        $this->assertDatabaseHas('profiles', $fake_data + ['user_id' => $this->user->id]);
    }

    /** @test */
    public function user_can_update_profile()
    {
        Sanctum::actingAs($this->user);

        $profile = Profile::factory()->create(['user_id' => $this->user->id]);

        $fake_data = Profile::factory()->definition();

        $resoponse = $this->json('put', route('v1.profiles.update', $profile), $fake_data);
        $resoponse->assertOk();

        $this->assertDatabaseCount('profiles', 1);
        $this->assertDatabaseHas('profiles', $fake_data + ['user_id' => $this->user->id]);
    }

    /** @test */
    public function user_can_delete_profile()
    {
        Sanctum::actingAs($this->user);

        $profile = Profile::factory()->create(['user_id' => $this->user->id]);

        $resoponse = $this->json('delete', route('v1.profiles.destroy', $profile));
        $resoponse->assertOk();

        $this->assertDatabaseCount('profiles', 0);
        $this->assertDatabaseMissing('profiles', $profile->getAttributes());
    }
}
