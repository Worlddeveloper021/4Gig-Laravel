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
        'category_id',
        'sub_category_id',
        'description',
        'video_presentation',
        'portfolio',
    ];

    protected $casts = [
        'availability_on_demand' => 'boolean',
    ];

    const PRESENTATION_COLLECTION_NAME = 'presentation';

    const PORTFOLIO_COLLECTION_NAME = 'portfolio';

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
        $this->addMediaCollection(self::PRESENTATION_COLLECTION_NAME)
            ->singleFile();

        $this->addMediaCollection(self::PORTFOLIO_COLLECTION_NAME)
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

    public function spoken_languages()
    {
        return $this->hasMany(SpokenLanguage::class);
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function sub_category()
    {
        return $this->belongsTo(Category::class, 'sub_category_id');
    }

    public function reviews()
    {
        return $this->hasMany(Review::class);
    }
}
