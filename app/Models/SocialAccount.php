<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class SocialAccount extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'provider',
        'provider_id',
        'token',
        'refresh_token',
        'expires_in',
    ];

    const GOOGLE = 'google';

    const LINKEDIN = 'linkedin';

    const FACEBOOK = 'facebook';

    const PROVIDERS = [
        self::GOOGLE,
        self::LINKEDIN,
        self::FACEBOOK,
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
