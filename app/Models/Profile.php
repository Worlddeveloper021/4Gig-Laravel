<?php

namespace App\Models;

use Spatie\MediaLibrary\HasMedia;
use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\InteractsWithMedia;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Profile extends Model implements HasMedia
{
    use HasFactory, InteractsWithMedia;

    protected $fillable = [
        'user_id',
        'first_name',
        'last_name',
        'gender',
        'nationality',
        'profile_type',
        'availability_on_demand',
        'per_hour',
    ];

    protected $casts = [
        'availability_on_demand' => 'boolean',
    ];

    const COLLECTION_NAME = 'avatar';

    const SELLER = 0;

    const BUYER = 1;

    const FEMALE = 0;

    const MALE = 1;

    const GENDERS = [
        self::FEMALE,
        self::MALE,
    ];

    const TYPES = [
        self::SELLER,
        self::BUYER,
    ];

    const GENDER_NAMES = [
        self::FEMALE => 'Female',
        self::MALE => 'Male',
    ];

    const TYPE_NAMES = [
        self::SELLER => 'Seller',
        self::BUYER => 'Buyer',
    ];

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection(self::COLLECTION_NAME)
            ->singleFile();
    }

    public function getTypeNameAttribute()
    {
        return self::TYPE_NAMES[$this->profile_type];
    }

    public function getGenderNameAttribute()
    {
        return self::GENDER_NAMES[$this->gender];
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
