<?php

namespace Tests\Feature\Api\V1;

use Tests\TestCase;
use App\Models\Category;
use Illuminate\Foundation\Testing\RefreshDatabase;

class CategoryTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function user_can_get_root_categories()
    {
        Category::factory()
            ->has(Category::factory()->count(2), 'children')
            ->count(5)->create();

        $response = $this->json('get', route('v1.categories.index'));

        $response->assertOk()
            ->assertJsonStructure([
                '*' => [
                    'id',
                    'name',
                ],
            ]);

        $expected_data = Category::root()->get()->map(function ($category) {
            return [
                'id' => $category->id,
                'name' => $category->name,
            ];
        });

        $response->assertJson($expected_data->toArray());
    }

    /** @test */
    public function user_can_get_root_categories_with_children()
    {
        Category::factory()
            ->has(Category::factory()->count(2), 'children')
            ->count(5)->create();

        $response = $this->json('get', route('v1.categories.index', ['with_children' => true]));

        $response->assertOk()
            ->assertJsonStructure([
                '*' => [
                    'id',
                    'name',
                    'children' => [
                        '*' => [
                            'id',
                            'name',
                        ],
                    ],
                ],
            ]);

        $expected_data = Category::root()->get()->map(function ($category) {
            return [
                'id' => $category->id,
                'name' => $category->name,
                'children' => $category->children->map(function ($child) {
                    return [
                        'id' => $child->id,
                        'name' => $child->name,
                    ];
                })->toArray(),
            ];
        });

        $response->assertJson($expected_data->toArray());
    }
}
