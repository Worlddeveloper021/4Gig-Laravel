<?php

namespace App\Http\Controllers\Api\V1;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Laravel\Socialite\Facades\Socialite;
use App\Http\Resources\Api\V1\UserResource;

class SocialAccountController extends Controller
{
    public function store(Request $request, $provider)
    {
        $request->validate([
            'provider_id' => 'required',
            'token' => 'required',
            'refresh_token' => 'required',
            'expires_in' => 'required',
        ]);

        $social_user = Socialite::driver($provider)->userFromToken($request->token);

        $user = User::whereEmail($social_user->getEmail())->first();

        if (! $user) {
            $user = User::create([
                'email' => $social_user->getEmail(),
                'email_verified_at' => now(),
            ]);

            $user->social_accounts()->create([
                'provider' => $provider,
                'provider_id' => $request->provider_id,
                'token' => $request->token,
                'refresh_token' => $request->refresh_token,
                'expires_in' => $request->expires_in,
            ]);
        } else {
            $user->social_accounts()->update([
                'provider' => $provider,
                'provider_id' => $request->provider_id,
                'token' => $request->token,
                'refresh_token' => $request->refresh_token,
                'expires_in' => $request->expires_in,
            ]);
        }

        return response()->json(UserResource::make($user), 200);
    }
}
