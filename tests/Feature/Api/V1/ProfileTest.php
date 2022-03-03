<?php

namespace Tests\Feature\Api\V1;

use Tests\TestCase;
use App\Models\User;
use App\Models\Order;
use App\Models\Skill;
use App\Models\Review;
use App\Models\Package;
use App\Models\Profile;
use App\Models\Category;
use Laravel\Sanctum\Sanctum;
use App\Models\SpokenLanguage;
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

        $username = 'new_username';

        $skills = [
            'skill_1', 'skill_2', 'skill_3', 'skill_4',
        ];

        $spoken_languages = [
            'language_1', 'language_2', 'language_3', 'language_4',
        ];

        $request_data = array_merge($fake_data, [
            'category_id' => $category_id = Category::factory()->create()->id,
            'sub_category_id' => Category::factory()->create(['parent_id' => $category_id])->id,
            'skills' => $skills,
            'spoken_languages' => $spoken_languages,
            'username' => $username,
            'is_active' => 1,
        ]);

        $resoponse = $this->json('post', route('v1.profile.store'), $request_data);
        $resoponse->assertOk()
            ->assertJsonStructure($this->expected_structure());

        $this->assertDatabaseCount('profiles', 1);
        $this->assertDatabaseHas('profiles', array_merge($fake_data, [
            'user_id' => $this->user->id,
            'is_active' => 1,
        ]));

        $profile = Profile::first();

        $this->assertDatabaseHas('users', [
            'id' => $this->user->id,
            'username' => $username,
        ]);

        $this->assertDatabaseCount('skills', 4);
        foreach ($skills as $skill) {
            $this->assertDatabaseHas('skills', [
                'profile_id' => $profile->id,
                'name' => $skill,
            ]);
        }

        $this->assertDatabaseCount('spoken_languages', 4);
        foreach ($spoken_languages as $language) {
            $this->assertDatabaseHas('spoken_languages', [
                'profile_id' => $profile->id,
                'name' => $language,
            ]);
        }
    }

    /** @test */
    public function user_can_update_profile()
    {
        Sanctum::actingAs($this->user);

        $profile = Profile::factory()
            ->has(Skill::factory()->count(4))
            ->has(SpokenLanguage::factory()->count(4), 'spoken_languages')
            ->create(['user_id' => $this->user->id]);

        $this->assertDatabaseCount('profiles', 1);
        $this->assertDatabaseCount('skills', 4);
        $this->assertDatabaseCount('spoken_languages', 4);

        $fake_data = Profile::factory()->definition();

        $username = 'new_username';

        $skills = [
            'skill_1', 'skill_2', 'skill_3', 'skill_4',
        ];

        $spoken_languages = [
            'language_1', 'language_2', 'language_3', 'language_4',
        ];

        $request_data = array_merge($fake_data, [
            'category_id' => $category_id = Category::factory()->create()->id,
            'sub_category_id' => Category::factory()->create(['parent_id' => $category_id])->id,
            'skills' => $skills,
            'spoken_languages' => $spoken_languages,
            'username' => $username,
        ]);

        $resoponse = $this->json('put', route('v1.profile.store'), $request_data);
        $resoponse->assertOk()
            ->assertJsonStructure($this->expected_structure());

        $this->assertDatabaseCount('profiles', 1);
        $this->assertDatabaseHas('profiles', array_merge($fake_data, [
            'user_id' => $this->user->id,
            'id' => $profile->id,
        ]));

        $this->assertDatabaseHas('users', [
            'id' => $this->user->id,
            'username' => $username,
        ]);

        $this->assertDatabaseCount('skills', 4);
        foreach ($skills as $skill) {
            $this->assertDatabaseHas('skills', [
                'profile_id' => $profile->id,
                'name' => $skill,
            ]);
        }

        $this->assertDatabaseCount('spoken_languages', 4);
        foreach ($spoken_languages as $language) {
            $this->assertDatabaseHas('spoken_languages', [
                'profile_id' => $profile->id,
                'name' => $language,
            ]);
        }
    }

    /** @test */
    public function user_can_get_profile()
    {
        Sanctum::actingAs($this->user);

        Profile::factory()
            ->has(Skill::factory()->count(4))
            ->has(SpokenLanguage::factory()->count(4), 'spoken_languages')
            ->for(Category::factory(), 'category')
            ->for(Category::factory(), 'sub_category')
            ->create(['user_id' => $this->user->id]);

        $resoponse = $this->json('get', route('v1.profile.show'));

        $resoponse->assertOk()
            ->assertJsonStructure($this->expected_structure(true));
    }

    /** @test */
    public function user_can_see_profile_by_id()
    {
        $profile = Profile::factory()
            ->has(Skill::factory()->count(4))
            ->has(SpokenLanguage::factory()->count(4), 'spoken_languages')
            ->for(Category::factory(), 'category')
            ->for(Category::factory(), 'sub_category')
            ->create(['user_id' => $this->user->id]);

        $resoponse = $this->json('get', route('v1.profile.show_by_id', $profile->id));

        $resoponse->assertOk()
            ->assertJsonStructure($this->expected_structure(true));
    }

    /** @test */
    public function user_can_complete_profile_step_2()
    {
        Sanctum::actingAs($this->user);

        Profile::factory()->create(['user_id' => $this->user->id]);
        Category::factory()
            ->has(Category::factory()->count(2), 'children')
            ->create();

        $request_data = [
            'description' => 'this is a berif description',
            'category_id' => 1,
            'sub_category_id' => 2,
        ];

        $resoponse = $this->json('put', route('v1.profile.store.step_2'), $request_data);

        $resoponse->assertOk()
            ->assertJsonStructure($this->expected_structure(true));

        $profile = Profile::first();

        $this->assertDatabaseHas('profiles', [
            'id' => $profile->id,
            'user_id' => $this->user->id,
            'description' => 'this is a berif description',
            'category_id' => 1,
            'sub_category_id' => 2,
        ]);
    }

    /** @test */
    public function user_can_upload_avatar()
    {
        Sanctum::actingAs($this->user);

        $response = $this->json('post', route('v1.profile.upload_file'), [
            'avatar' => UploadedFile::fake()->image('avatar.jpg'),
        ]);

        $response->assertOk();

        $this->assertDatabaseHas('media', [
            'model_type' => User::class,
            'model_id' => User::first()->id,
            'collection_name' => User::AVATAR_COLLECTION_NAME,
            'name' => 'avatar',
        ]);
    }

    /** @test */
    public function user_can_upload_video_presentation()
    {
        Sanctum::actingAs($this->user);
        $profile = Profile::factory()->create(['user_id' => $this->user->id]);

        $response = $this->json('post', route('v1.profile.upload_file'), [
            'video_presentation' => UploadedFile::fake()->create('video.mp4'),
        ]);

        $response->assertOk();

        $this->assertDatabaseHas('media', [
            'model_type' => Profile::class,
            'model_id' => $profile->id,
            'collection_name' => Profile::PRESENTATION_COLLECTION_NAME,
            'name' => 'video',
        ]);
    }

    /** @test */
    public function user_can_upload_portfolio()
    {
        Sanctum::actingAs($this->user);
        $profile = Profile::factory()->create(['user_id' => $this->user->id]);

        $response = $this->json('post', route('v1.profile.upload_file'), [
            'portfolio' => UploadedFile::fake()->create('portfolio.pdf'),
        ]);

        $response->assertOk();

        $this->assertDatabaseHas('media', [
            'model_type' => Profile::class,
            'model_id' => $profile->id,
            'collection_name' => Profile::PORTFOLIO_COLLECTION_NAME,
            'name' => 'portfolio',
        ]);
    }

    /** @test */
    public function user_can_update_is_active_by_profile_id()
    {
        $profile = Profile::factory()
            ->has(Skill::factory()->count(4))
            ->has(SpokenLanguage::factory()->count(4), 'spoken_languages')
            ->for(Category::factory(), 'category')
            ->for(Category::factory(), 'sub_category')
            ->create(['user_id' => $this->user->id]);

        Sanctum::actingAs($this->user);

        $resoponse = $this->json('put', route('v1.profile.update.is_active', $profile->id), [
            'is_active' => 0,
        ]);

        $this->assertDatabaseHas('profiles', [
            'id' => $profile->id,
            'is_active' => 0,
        ]);

        $resoponse->assertOk()
            ->assertJsonStructure($this->expected_structure(true));
    }

    /** @test */
    public function calculate_min_and_max_price_of_per_hours()
    {
        $category = Category::factory()
            ->has(Category::factory(), 'children')
            ->create();

        Profile::factory()
            ->has(Skill::factory()->count(4))
            ->has(SpokenLanguage::factory()->count(4), 'spoken_languages')
            ->has(Review::factory()->count(5), 'reviews')
            ->for(User::factory())
            ->count(10)
            ->create([
                'category_id' => $category->id,
                'sub_category_id' => $category->children->first()->id,
            ]);

        $response = $this->json('get', route('v1.profile.min_max_price'));

        $response->assertOk();
        $response->assertJsonStructure([
            'min_price',
            'max_price',
            'max_review',
        ]);
    }

    /** @test */
    public function filter_profiles_by_price()
    {
        Sanctum::actingAs($this->user);

        Profile::factory(['is_active' => 1])
            ->has(Skill::factory()->count(4))
            ->has(SpokenLanguage::factory()->count(4), 'spoken_languages')
            ->has(Package::factory()->count(2), 'packages')
            ->has(Review::factory()->count(rand(2, 10)), 'reviews')
            ->for(Category::factory(), 'category')
            ->for(Category::factory(), 'sub_category')
            ->for(User::factory(), 'user')
            ->count(10)
            ->sequence(function ($sequence) {
                return [
                    'per_hour' => ($sequence->index + 1) * 10,
                ];
            })->create();

        $response = $this->json('get', route('v1.profile.filter', ['category' => 1]), [
            'min_price' => 10,
            'max_price' => 50,
            // 'min_rates' => 3,
            // 'max_rates' => 4,
            // 'min_reviews' => 2,
            // 'max_reviews' => 5,
        ]);

        $response->assertOk();
        $response->assertJsonStructure(
            [
                'data' => [
                    '*' => $this->expected_structure(true),
                ],
            ],
        );
    }

    /** @test */
    public function search_profiles_by_name()
    {
        Sanctum::actingAs($this->user);

        Profile::factory(['is_active' => 1])
            ->has(Skill::factory()->count(4))
            ->has(SpokenLanguage::factory()->count(4), 'spoken_languages')
            ->has(Package::factory()->count(2), 'packages')
            ->has(Review::factory()->count(rand(2, 10)), 'reviews')
            ->for(Category::factory(), 'category')
            ->for(Category::factory(), 'sub_category')
            ->for(User::factory(), 'user')
            ->count(10)
            ->sequence(function ($sequence) {
                return [
                    'per_hour' => ($sequence->index + 1) * 10,
                ];
            })->create();

        $response = $this->json('get', route('v1.profile.search', ['category' => 1]), [
            'name' => 'ja',
        ]);

        $response->assertOk();
        $response->assertJsonStructure(
            [
                'data' => [
                    '*' => $this->expected_structure(true),
                ],
            ],
        );
    }

    /** @test */
    public function get_seller_orders()
    {
        Sanctum::actingAs($this->user);

        Order::factory()
            ->for(Profile::factory(['user_id' => $this->user->id]))
            ->count(5)
            ->create();

        $response = $this->json('get', route('v1.profile.orders'));

        $response->assertOk();
        $response->assertJsonCount(5, 'data');
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

        return array_merge([
            'first_name',
            'last_name',
            'nationality',
            'birth_date',
            'gender',
            'availability_on_demand',
            'is_active',
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
        ], $categories);
    }
}
