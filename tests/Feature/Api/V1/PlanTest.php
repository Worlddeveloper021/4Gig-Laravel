<?php

namespace Tests\Feature\Api\V1;

use Tests\TestCase;
use App\Models\Plan;
use Illuminate\Foundation\Testing\RefreshDatabase;

class PlanTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function user_can_create_plan()
    {
        $plan_data = [
            'name' => 'Plan 1',
            'description' => 'Plan 1 description',
            'price' => '100',
            'duration' => '30', // in days
            'status' => 1, // 1 = active, 0 = inactive
        ];

        $response = $this->post(route('v1.plans.store'), $plan_data);

        $response->assertOk();
        $response->assertJsonStructure([
            'id',
            'name',
            'description',
            'price',
            'duration',
            'status',
        ]);

        $this->assertDatabaseHas('plans', $plan_data);
    }

    /** @test */
    public function user_can_update_plan()
    {
        $plan = Plan::factory()->create();

        $plan_data = [
            'name' => 'Plan 1',
            'description' => 'Plan 1 description',
            'price' => '100',
            'duration' => '30', // in days
            'status' => 1, // 1 = active, 0 = inactive
        ];

        $response = $this->put(route('v1.plans.update', $plan->id), $plan_data);

        $response->assertOk();
        $response->assertJsonStructure([
            'id',
            'name',
            'description',
            'price',
            'duration',
            'status',
        ]);

        $this->assertDatabaseHas('plans', $plan_data);
    }

    /** @test */
    public function user_can_get_plans()
    {
        Plan::factory()->count(5)->create();

        $response = $this->get(route('v1.plans.index'));

        $response->assertOk();
        $response->assertJsonStructure([
            'data' => [
                [
                    'id',
                    'name',
                    'description',
                    'price',
                    'duration',
                    'status',
                ],
            ],
        ]);
    }
    
    /** @test */
    public function user_can_get_active_plans()
    {
        Plan::factory(['status' => Plan::STATUS_INACTIVE])->count(5)->create();
        Plan::factory(['status' => Plan::STATUS_ACTIVE])->count(5)->create();

        $response = $this->get(route('v1.plans.index', ['get_actives' => 1]));

        $response->assertOk();
        $response->assertJsonStructure([
            'data' => [
                [
                    'id',
                    'name',
                    'description',
                    'price',
                    'duration',
                    'status',
                ],
            ],
        ]);
    }
}
