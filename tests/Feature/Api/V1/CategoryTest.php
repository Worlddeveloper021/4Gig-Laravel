<?php

namespace Tests\Feature\Api\V1;

use Tests\TestCase;
use App\Models\User;
use App\Models\Skill;
use App\Models\Profile;
use App\Models\Category;
use App\Models\SpokenLanguage;
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

    /** @test */
    public function user_can_get_profiles_by_given_category()
    {
        $categories = Category::factory()
            ->has(Category::factory()->count(2), 'children')
            ->count(5)->create();

        $category = $categories->random();

        Profile::factory()
            ->has(Skill::factory()->count(4))
            ->has(SpokenLanguage::factory()->count(4), 'spoken_languages')
            ->for(User::factory())
            ->count(10)
            ->create([
                'category_id' => $category->id,
                'sub_category_id' => $category->children->first()->id,
            ]);

        $response = $this->json('get', route('v1.categories.profiles.index', ['category' => $category]));

        $response->assertOk()
            ->assertJsonStructure($this->expected_structure(true));
    }

    protected function expected_structure($has_category = false)
    {
        $categories = (! $has_category) ? [] : [
            'category' => [
                'id',
                'name',
            ],
            'sub_category' => [
                'id',
                'name',
            ],
        ];

        return [
            'data' => [
                '*' => [
                    'first_name',
                    'last_name',
                    'nationality',
                    'birth_date',
                    'gender',
                    'availability_on_demand',
                    'per_hour',
                    'avatar',
                    'user' => [
                        'id',
                        'username',
                        'email',
                        'email_verified_at',
                    ],
                    'skills' => [
                        '*' => [
                            'id',
                            'name',
                        ],
                    ],
                    'spoken_languages' => [
                        '*' => [
                            'id',
                            'name',
                        ],
                    ],
                    'description',
                    'video_presentation',
                    'portfolio',
                    'rate',
                ] + $categories,
            ],
            'links' => [
                'first',
                'last',
                'prev',
                'next',
            ],
            'meta' => [
                'current_page',
                'last_page',
                'from',
                'to',
                'path',
                'per_page',
                'total',
                'links',
            ],
        ];
    }
}
