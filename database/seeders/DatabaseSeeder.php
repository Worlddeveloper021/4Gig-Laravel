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

        $category = Category::factory()
            ->has(Category::factory()->count(3), 'children')
            ->create();

        Profile::factory()
            ->has(Skill::factory()->count(4))
            ->has(SpokenLanguage::factory()->count(4))
            ->create([
                'user_id' => $user->id,
                'category_id' => $category->id,
                'sub_category_id' => $category->children->first()->id,
            ]);
    }
}
