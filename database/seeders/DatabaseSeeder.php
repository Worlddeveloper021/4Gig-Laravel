<?php

namespace Database\Seeders;

use DB;
use App\Models\User;
use App\Models\Order;
use App\Models\Skill;
use App\Models\Review;
use App\Models\Package;
use App\Models\Profile;
use App\Models\Category;
use App\Models\Customer;
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
            'fcm_key' => 'ew_6ScGCQAuGBpZQjbIXRb:APA91bH_WzA3vIzFrMtvCjdZND2zgdgwiv1JULQqMVG209PE4ehGIkFPOxtkGDXUYfFfzcMytd0aVMCyVAZ-lKK642oWYJZgw9s2XK0NrXAKAhV_3DSxlqsZVRkBNHkoviyqsqcxa5Tg',
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

        $profile = Profile::factory()
            ->has(Skill::factory()->count(4))
            ->has(SpokenLanguage::factory()->count(4), 'spoken_languages')
            ->has(Package::factory()->count(5), 'packages')
            ->create([
                'user_id' => $user->id,
                'category_id' => $categories[0]->id,
                'sub_category_id' => $categories[0]->children->first()->id,
            ]);

        $customers = Customer::factory()->count(5)->create();

        Review::factory()->count(10)->create([
            'customer_id' => $customers->first()->id,
            'profile_id' => $profile->id,
        ]);

        Profile::factory()
            ->has(Skill::factory()->count(4))
            ->has(SpokenLanguage::factory()->count(4), 'spoken_languages')
            ->has(Review::factory()->count(5), 'reviews')
            ->count(20)
            ->sequence(function ($sequence) use ($categories) {
                return [
                    'user_id' => User::factory()->create()->id,
                    'category_id' => $categories[$sequence->index % 6]->id,
                    'sub_category_id' => $categories[$sequence->index % 6]->children->first()->id,
                ];
            })->create();

        User::find(2)->update([
            'email' => 'customer@test.com',
            'fcm_key' => 'euTx0FAbRYq9qhQ1XcIYvX:APA91bGwau_KOvueoFf4aTTqutyE4guozDd5Zu0UZOTmSVWXLW3harlPrTxr3EOsvU5KoMbfoaVRkcwIjLSmNDyNtjcKy5C1j7G0KEhlTllEu2b5SHyyBPzoeXKWKzDhWR4ml1Vu1N3L',
        ]);

        Order::factory()->create();
    }
}
