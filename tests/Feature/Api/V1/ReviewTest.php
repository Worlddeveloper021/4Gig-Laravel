<?php

namespace Tests\Feature\Api\V1;

use Tests\TestCase;
use App\Models\User;
use App\Models\Review;
use App\Models\Profile;
use App\Models\Customer;
use Laravel\Sanctum\Sanctum;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ReviewTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_can_create_a_customer_review()
    {
        $profile = Profile::factory()->create();

        $user = User::factory()->create();
        $customer = Customer::factory(['user_id' => $user->id])->create();

        Sanctum::actingAs($user);

        $response = $this->json('post', route('v1.reviews.store', $profile), [
            'review' => 'This is a review',
            'rate' => 4,
        ]);

        $response->assertStatus(200);

        $this->assertDatabaseHas('reviews', [
            'profile_id' => $profile->id,
            'customer_id' => $customer->id,
            'review' => 'This is a review',
            'rate' => 4,
        ]);
    }

    /** @test */
    public function it_can_show_reviews_for_a_profile()
    {
        $profile = Profile::factory()->create();

        Review::factory()->count(10)->create([
            'profile_id' => $profile->id,
        ]);

        Review::factory()->count(10)->create();

        $response = $this->json('get', route('v1.reviews.show', $profile));

        $response->assertStatus(200);

        $response->assertJsonCount(10);
        $response->assertJsonStructure([
            '*' => [
                'id',
                'review',
                'rate',
                'created_at',
                'customer' => [
                    'name',
                ],
            ],
        ]);
    }
}
