<?php

namespace App\Models;

use DB;
use Laravel\Sanctum\HasApiTokens;
use Spatie\MediaLibrary\HasMedia;
use Illuminate\Support\Facades\Cache;
use Illuminate\Notifications\Notifiable;
use Spatie\MediaLibrary\InteractsWithMedia;
use App\Notifications\ResetPasswordNotification;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable implements HasMedia
{
    use HasApiTokens, HasFactory, Notifiable, InteractsWithMedia;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'email',
        'mobile',
        'username',
        'password',
        'verify_code',
        'fcm_key',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'verify_code',
        'fcm_key',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    const AVATAR_COLLECTION_NAME = 'avatar';

    const ONLINE_CACHE_KEY = 'user_is_online_';

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection(self::AVATAR_COLLECTION_NAME)
            ->singleFile();
    }

    public function sendEmailVerificationNotification()
    {
        $token = rand(100000, 1000000 - 1);

        $this->verify_code = $token;
        $this->save();

        $this->notify(new \App\Notifications\VerifyEmailNotification($token));
    }

    /**
     * Send the password reset notification.
     *
     * @param  string  $token
     * @return void
     */
    public function sendPasswordResetNotification($token)
    {
        $this->notify(new ResetPasswordNotification($token));
    }

    public function requestResetPassword()
    {
        $token = rand(100000, 1000000 - 1);

        DB::table('password_resets')->insert([
            'email' => $this->email,
            'token' => $token,
            'created_at' => now(),
        ]);

        $this->sendPasswordResetNotification($token);
    }

    public function is_valid_verify_code(string $verify_code)
    {
        return $this->verify_code === $verify_code;
    }

    public function record_last_activity()
    {
        Cache::set(self::ONLINE_CACHE_KEY.$this->id, true, now()->addMinutes(2));
    }

    public function is_online()
    {
        return Cache::has(self::ONLINE_CACHE_KEY.$this->id);
    }

    public function getIsOnlineAttribute()
    {
        return $this->is_online();
    }

    public function profile()
    {
        return $this->hasOne(Profile::class);
    }

    public function customer()
    {
        return $this->hasOne(Customer::class);
    }

    public function social_accounts()
    {
        return $this->hasOne(SocialAccount::class);
    }
}
