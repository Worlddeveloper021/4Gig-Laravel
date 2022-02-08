<?php

namespace Tests\Feature\Api\V1;

use Tests\TestCase;
use App\Models\User;
use Laravel\Sanctum\Sanctum;
use Illuminate\Foundation\Testing\RefreshDatabase;

class SocialAccountTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function send_push_notification_using_firebase_cloud_messaging()
    {
        $user = User::factory(['fcm_key' => '::fcm_key::'])->create();

        $response = $this->json('post', route('v1.firebase.push_notifications.send', $user->id));

        $response->assertOk();
    }

    /** @test */
    public function create_agora_access_token()
    {
        $user = User::factory()->create();

        Sanctum::actingAs($user);

        $response = $this->json('post', route('v1.agora.access_token.create'));

        $response->assertOk();
    }

    /** @test */
    // public function store_social_account_tokens()
    // {
    // $response = $this->json('post', route('v1.social-accounts.store', 'google'), [
    //     'provider_id' => '123456789',
    //     'token' => '123456789',
    //     'refresh_token' => '123456789',
    //     'expires_in' => '123456789',
    // ]);

    // $response->assertStatus(200);

    // $this->assertDatabaseHas('social_accounts', [
    //     'provider' => 'google',
    //     'provider_id' => '123456789',
    //     'token' => '123456789',
    //     'refresh_token' => '123456789',
    //     'expires_in' => '123456789',
    // ]);
    // }
}
