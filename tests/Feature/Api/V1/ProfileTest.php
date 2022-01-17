<?php

namespace Tests\Feature\Api\V1;

use Tests\TestCase;
use App\Models\User;
use App\Models\Profile;
use Laravel\Sanctum\Sanctum;
use Illuminate\Http\UploadedFile;
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

        $resoponse = $this->json('post', route('v1.profiles.store'), array_merge($fake_data, [
            'avatar' => UploadedFile::fake()->image('avatar.jpg'),
        ]));
        $resoponse->assertCreated()
            ->assertJsonStructure($this->expected_structure());

        $this->assertDatabaseCount('profiles', 1);
        $this->assertDatabaseHas('profiles', $fake_data + ['user_id' => $this->user->id]);

        $this->assertDatabaseCount('media', 1);
        $this->assertDatabaseHas('media', [
            'model_type' => Profile::class,
            'model_id' => Profile::first()->id,
            'collection_name' => Profile::COLLECTION_NAME,
            'name' => 'avatar',
        ]);
    }

    /** @test */
    public function user_can_update_profile()
    {
        Sanctum::actingAs($this->user);

        $profile = Profile::factory()->create(['user_id' => $this->user->id]);

        $fake_data = Profile::factory()->definition();

        $resoponse = $this->json('put', route('v1.profiles.update', $profile), array_merge($fake_data, [
            'avatar' => UploadedFile::fake()->image('avatar.jpg'),
        ]));
        $resoponse->assertOk()
            ->assertJsonStructure($this->expected_structure());

        $this->assertDatabaseCount('profiles', 1);
        $this->assertDatabaseHas('profiles', $fake_data + ['user_id' => $this->user->id]);

        $this->assertDatabaseCount('media', 1);
        $this->assertDatabaseHas('media', [
            'model_type' => Profile::class,
            'model_id' => Profile::first()->id,
            'collection_name' => Profile::COLLECTION_NAME,
            'name' => 'avatar',
        ]);
    }

    /** @test */
    public function user_can_delete_profile()
    {
        Sanctum::actingAs($this->user);

        $profile = Profile::factory()->create(['user_id' => $this->user->id]);

        $resoponse = $this->json('delete', route('v1.profiles.destroy', $profile));
        $resoponse->assertOk()
            ->assertJsonStructure($this->expected_structure());

        $this->assertDatabaseCount('profiles', 0);
        $this->assertDatabaseMissing('profiles', $profile->getAttributes());

        $this->assertDatabaseCount('media', 0);
        $this->assertDatabaseMissing('media', [
            'model_type' => Profile::class,
            'model_id' => $profile->id,
            'collection_name' => Profile::COLLECTION_NAME,
            'name' => 'avatar',
        ]);
    }

    protected function expected_structure()
    {
        return [
            'data' => [
                'first_name',
                'last_name',
                'gender',
                'nationality',
                'profile_type',
                'availability_on_demand',
                'per_hour',
                'avatar',
                'user' => [
                    'id',
                    'username',
                    'email',
                    'email_verified_at',
                    'created_at',
                    'updated_at',
                ],
            ],
        ];
    }
}
