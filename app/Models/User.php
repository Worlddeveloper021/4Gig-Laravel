<?php

namespace App\Models;

use DB;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Notifications\ResetPasswordNotification;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable implements MustVerifyEmail
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'verify_code',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

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

    public function profile()
    {
        return $this->hasOne(Profile::class);
    }
}
