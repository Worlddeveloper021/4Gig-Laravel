<?php

namespace Database\Seeders;

use DB;
use App\Models\User;
use App\Models\Skill;
use App\Models\Profile;
use App\Models\Category;
use App\Models\SpokenLanguage;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $user = User::factory([
            'email' => 'test@test.com',
            'verify_code' => '123456',
        ])->create();

        $user->createToken('test-token');

        DB::table('password_resets')->insert([
            'email' => $user->email,
            'token' => '123456',
            'created_at' => now(),
        ]);

        $category_names = [
            'Lawyer',
            'Doctor',
            'Psicologist',
            'Mechanic',
            'Veterinarian',
            'Home Repair',
        ];

        foreach ($category_names as $category_name) {
            $category = Category::create([
                'name' => $category_name,
            ]);

            Category::factory()->count(5)->create([
                'parent_id' => $category->id,
            ]);
        }

        $categories = Category::root()->with('children')->get();

        Profile::factory()
            ->has(Skill::factory()->count(4))
            ->has(SpokenLanguage::factory()->count(4), 'spoken_languages')
            ->count(20)
            ->sequence(fn ($sequence) => [
                'user_id' => User::factory()->create()->id,
                'category_id' => $categories[$sequence->index % 6]->id,
                'sub_category_id' => $categories[$sequence->index % 6]->children->first()->id,
            ])->create();

        Profile::factory()
            ->has(Skill::factory()->count(4))
            ->has(SpokenLanguage::factory()->count(4), 'spoken_languages')
            ->create([
                'user_id' => $user->id,
                'category_id' => $categories[0]->id,
                'sub_category_id' => $categories[0]->children->first()->id,
            ]);
    }
}
