<?php

namespace Tests\Feature\Api\V1;

use Tests\TestCase;
use App\Models\Plan;
use App\Models\User;
use App\Models\Skill;
use App\Models\Profile;
use App\Models\Category;
use App\Models\Subscription;
use Laravel\Sanctum\Sanctum;
use App\Models\SpokenLanguage;
use Illuminate\Foundation\Testing\RefreshDatabase;

class SubscriptionTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_can_create_a_subscription()
    {
        $user = User::factory()->create();
        $profile = Profile::factory()
            ->has(Skill::factory()->count(4))
            ->has(SpokenLanguage::factory()->count(4), 'spoken_languages')
            ->for(Category::factory(), 'category')
            ->for(Category::factory(), 'sub_category')
            ->create(['user_id' => $user->id]);

        Sanctum::actingAs($user);

        $plan = Plan::factory(['duration' => 30])->create();

        $response = $this->json('post', route('v1.subscriptions.store'), [
            'plan_id' => $plan->id,
            'payment_id' => '::PAYID::',
        ]);

        $response->assertOk()
            ->assertJsonStructure([
                'id',
                'price',
                'duration',
                'start_date',
                'end_date',
                'payment_id',
                'payment_status',
                'status',
                'plan',
            ]);

        $this->assertDatabaseHas('subscriptions', [
            'profile_id' => $profile->id,
            'plan_id' => $plan->id,
            'price' => $plan->price,
            'duration' => $plan->duration,
            'payment_id' => '::PAYID::',
            'payment_status' => Subscription::PAYMENT_STATUS_CREATED,
            'status' => Subscription::STATUS_INACTIVE,
            'start_date' => now()->startOfDay(),
            'end_date' => now()->addDays($plan->duration)->endOfDay(),
        ]);

        $this->assertDatabaseHas('profiles', [
            'id' => $profile->id,
            'order' => Profile::ORDER_INACTIVE,
        ]);
    }

    /** @test */
    public function it_can_create_a_subscription_paid()
    {
        $user = User::factory()->create();
        $profile = Profile::factory(['user_id' => $user->id])
            ->has(Skill::factory()->count(4))
            ->has(SpokenLanguage::factory()->count(4), 'spoken_languages')
            ->for(Category::factory(), 'category')
            ->for(Category::factory(), 'sub_category')
            ->create();

        $plan = Plan::factory(['duration' => 30])->create();

        Subscription::factory(['profile_id', $profile])
            ->for(Plan::factory());

        Sanctum::actingAs($user);

        $response = $this->json('post', route('v1.subscriptions.store'), [
            'plan_id' => $plan->id,
            'payment_id' => 'PAYID-MIEEYEQ9AT89072W84764009',
        ]);

        $response->assertOk()
            ->assertJsonStructure([
                'id',
                'price',
                'duration',
                'start_date',
                'end_date',
                'payment_id',
                'payment_status',
                'status',
                'plan',
            ]);

        $this->assertDatabaseHas('subscriptions', [
            'profile_id' => $profile->id,
            'plan_id' => $plan->id,
            'price' => $plan->price,
            'duration' => $plan->duration,
            'payment_id' => 'PAYID-MIEEYEQ9AT89072W84764009',
            'payment_status' => Subscription::PAYMENT_STATUS_APPROVED,
            'status' => Subscription::STATUS_ACTIVE,
            'start_date' => now()->startOfDay(),
            'end_date' => now()->addDays($plan->duration)->endOfDay(),
        ]);

        $this->assertDatabaseHas('profiles', [
            'id' => $profile->id,
            'order' => Profile::ORDER_ACTIVE,
        ]);
    }

    /** @test */
    public function it_can_get_all_stored_subscriptions()
    {
        $user = User::factory()->create();
        $profile = Profile::factory(['user_id' => $user->id])
            ->has(Skill::factory()->count(4))
            ->has(SpokenLanguage::factory()->count(4), 'spoken_languages')
            ->for(Category::factory(), 'category')
            ->for(Category::factory(), 'sub_category')
            ->create();

        $plan = Plan::factory()->create();

        Subscription::factory(['profile_id' => $profile->id, 'plan_id' => $plan->id])
            ->count(5)
            ->create();

        Sanctum::actingAs($user);

        $response = $this->json('get', route('v1.subscriptions.show'));

        $response->assertOk();
        $response->assertJsonStructure([
            'data' => [
                '*' => [
                    'id',
                    'price',
                    'duration',
                    'start_date',
                    'end_date',
                    'payment_id',
                    'payment_status',
                    'status',
                    'plan',
                ],
            ],
        ]);
    }
}
