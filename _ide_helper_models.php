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
 * App\Models\Category
 *
 * @property int $id
 * @property string $name
 * @property int|null $parent_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection|Category[] $children
 * @property-read int|null $children_count
 * @property-read Category|null $parent
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Profile[] $profiles
 * @property-read int|null $profiles_count
 * @method static \Database\Factories\CategoryFactory factory(...$parameters)
 * @method static \Illuminate\Database\Eloquent\Builder|Category newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Category newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Category query()
 * @method static \Illuminate\Database\Eloquent\Builder|Category root()
 * @method static \Illuminate\Database\Eloquent\Builder|Category whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Category whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Category whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Category whereParentId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Category whereUpdatedAt($value)
 */
	class Category extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\Profile
 *
 * @property int $id
 * @property int $user_id
 * @property string $first_name
 * @property string $last_name
 * @property string $nationality
 * @property string $birth_date
 * @property int $gender
 * @property bool $availability_on_demand
 * @property int $per_hour
 * @property string|null $description
 * @property int|null $category_id
 * @property int|null $sub_category_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Category|null $category
 * @property-read mixed $gender_name
 * @property-read \Spatie\MediaLibrary\MediaCollections\Models\Collections\MediaCollection|\Spatie\MediaLibrary\MediaCollections\Models\Media[] $media
 * @property-read int|null $media_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Skill[] $skills
 * @property-read int|null $skills_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\SpokenLanguage[] $spoken_languages
 * @property-read int|null $spoken_languages_count
 * @property-read \App\Models\Category|null $sub_category
 * @property-read \App\Models\User $user
 * @method static \Database\Factories\ProfileFactory factory(...$parameters)
 * @method static \Illuminate\Database\Eloquent\Builder|Profile newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Profile newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Profile query()
 * @method static \Illuminate\Database\Eloquent\Builder|Profile whereAvailabilityOnDemand($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Profile whereBirthDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Profile whereCategoryId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Profile whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Profile whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Profile whereFirstName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Profile whereGender($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Profile whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Profile whereLastName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Profile whereNationality($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Profile wherePerHour($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Profile whereSubCategoryId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Profile whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Profile whereUserId($value)
 */
	class Profile extends \Eloquent implements \Spatie\MediaLibrary\HasMedia {}
}

namespace App\Models{
/**
 * App\Models\Skill
 *
 * @property int $id
 * @property int $profile_id
 * @property string $name
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Database\Factories\SkillFactory factory(...$parameters)
 * @method static \Illuminate\Database\Eloquent\Builder|Skill newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Skill newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Skill query()
 * @method static \Illuminate\Database\Eloquent\Builder|Skill whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Skill whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Skill whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Skill whereProfileId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Skill whereUpdatedAt($value)
 */
	class Skill extends \Eloquent {}
}

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
 * App\Models\SpokenLanguage
 *
 * @property int $id
 * @property int $profile_id
 * @property string $name
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Database\Factories\SpokenLanguageFactory factory(...$parameters)
 * @method static \Illuminate\Database\Eloquent\Builder|SpokenLanguage newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|SpokenLanguage newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|SpokenLanguage query()
 * @method static \Illuminate\Database\Eloquent\Builder|SpokenLanguage whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SpokenLanguage whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SpokenLanguage whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SpokenLanguage whereProfileId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SpokenLanguage whereUpdatedAt($value)
 */
	class SpokenLanguage extends \Eloquent {}
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
 * @property-read \Spatie\MediaLibrary\MediaCollections\Models\Collections\MediaCollection|\Spatie\MediaLibrary\MediaCollections\Models\Media[] $media
 * @property-read int|null $media_count
 * @property-read \Illuminate\Notifications\DatabaseNotificationCollection|\Illuminate\Notifications\DatabaseNotification[] $notifications
 * @property-read int|null $notifications_count
 * @property-read \App\Models\Profile|null $profile
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
	class User extends \Eloquent implements \Illuminate\Contracts\Auth\MustVerifyEmail, \Spatie\MediaLibrary\HasMedia {}
}

