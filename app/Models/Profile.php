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
        'nationality',
        'birth_date',
        'gender',
        'availability_on_demand',
        'per_hour',
    ];

    protected $casts = [
        'availability_on_demand' => 'boolean',
    ];

    const COLLECTION_NAME = 'avatar';

    const FEMALE = 0;

    const MALE = 1;

    const GENDERS = [
        self::FEMALE,
        self::MALE,
    ];

    const GENDER_NAMES = [
        self::FEMALE => 'Female',
        self::MALE => 'Male',
    ];

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection(self::COLLECTION_NAME)
            ->singleFile();
    }

    public function getGenderNameAttribute()
    {
        return self::GENDER_NAMES[$this->gender];
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function skills()
    {
        return $this->hasMany(Skill::class);
    }

    public function spokenLanguages()
    {
        return $this->hasMany(SpokenLanguage::class);
    }
}
