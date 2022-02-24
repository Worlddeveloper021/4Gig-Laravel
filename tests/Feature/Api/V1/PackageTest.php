<?php

namespace Tests\Feature\Api\V1;

use Tests\TestCase;
use App\Models\User;
use App\Models\Package;
use App\Models\Profile;
use Laravel\Sanctum\Sanctum;
use Illuminate\Foundation\Testing\RefreshDatabase;

class PackageTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_can_store_package_for_profile()
    {
        $user = User::factory()->create();
        $profile = Profile::factory(['user_id' => $user->id])->create();

        Sanctum::actingAs($user);

        $response = $this->json('post', route('v1.profile.package.store'), [
            'price' => '100',
            'duration' => '30',
            'description' => 'Package description',
            'on_demand' => 'available',
        ]);

        $response->assertOk();

        $this->assertDatabaseHas('packages', [
            'profile_id' => $profile->id,
            'price' => '100',
            'duration' => '30',
            'description' => 'Package description',
            'on_demand' => 'available',
        ]);
    }

    /** @test */
    public function user_should_be_logged_in()
    {
        $user = User::factory()->create();
        $profile = Profile::factory(['user_id' => $user->id])->create();

        $response = $this->json('post', route('v1.profile.package.store'), [
            'price' => '100',
            'duration' => '30',
            'description' => 'Package description',
            'on_demand' => 'available',
        ]);

        $response->assertStatus(401);

        $this->assertDatabaseMissing('packages', [
            'profile_id' => $profile->id,
            'price' => '100',
            'duration' => '30',
            'description' => 'Package description',
            'on_demand' => 'available',
        ]);
    }

    /** @test */
    public function user_should_have_profile()
    {
        $user = User::factory()->create();

        Sanctum::actingAs($user);

        $response = $this->json('post', route('v1.profile.package.store'), [
            'price' => '100',
            'duration' => '30',
            'description' => 'Package description',
            'on_demand' => 'available',
        ]);

        $response->assertStatus(422);

        $this->assertDatabaseMissing('packages', [
            'price' => '100',
            'duration' => '30',
            'description' => 'Package description',
            'on_demand' => 'available',
        ]);
    }

    /** @test */
    public function it_can_show_packages_of_a_profile()
    {
        $user = User::factory()->create();
        $profile = Profile::factory(['user_id' => $user->id])->create();
        Package::factory()->count(5)->create(['profile_id' => $profile->id]);

        Sanctum::actingAs($user);

        $response = $this->json('get', route('v1.profile.package.show', $profile->id));

        $response->assertOk();

        $response->assertJsonStructure([
            '*' => [
                'id',
                'price',
                'duration',
                'description',
                'on_demand',
            ],
        ]);
    }
}
