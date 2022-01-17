<?php

// @formatter:off
/**
 * A helper file for your Eloquent Models
 * Copy the phpDocs from this file to the correct Model,
 * And remove them from this file, to prevent double declarations.
 *
 * @author Barry vd. Heuvel <barryvdh@gmail.com>
 */


namespace App\Models{
/**
 * App\Models\SocialAccount
 *
 * @property int $id
 * @property int $user_id
 * @property string $provider
 * @property string $provider_id
 * @property string $token
 * @property string $refresh_token
 * @property string $expires_in
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\User $user
 * @method static \Illuminate\Database\Eloquent\Builder|SocialAccount newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|SocialAccount newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|SocialAccount query()
 * @method static \Illuminate\Database\Eloquent\Builder|SocialAccount whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SocialAccount whereExpiresIn($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SocialAccount whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SocialAccount whereProvider($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SocialAccount whereProviderId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SocialAccount whereRefreshToken($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SocialAccount whereToken($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SocialAccount whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SocialAccount whereUserId($value)
 */
	class SocialAccount extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\User
 *
 * @property int $id
 * @property string|null $username
 * @property string $email
 * @property string|null $password
 * @property string|null $verify_code
 * @property \Illuminate\Support\Carbon|null $email_verified_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Notifications\DatabaseNotificationCollection|\Illuminate\Notifications\DatabaseNotification[] $notifications
 * @property-read int|null $notifications_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\Laravel\Sanctum\PersonalAccessToken[] $tokens
 * @property-read int|null $tokens_count
 * @method static \Database\Factories\UserFactory factory(...$parameters)
 * @method static \Illuminate\Database\Eloquent\Builder|User newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|User newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|User query()
 * @method static \Illuminate\Database\Eloquent\Builder|User whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereEmailVerifiedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User wherePassword($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereUsername($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereVerifyCode($value)
 */
	class User extends \Eloquent implements \Illuminate\Contracts\Auth\MustVerifyEmail {}
}

