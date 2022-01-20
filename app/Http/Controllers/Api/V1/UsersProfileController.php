<?php

namespace App\Http\Controllers\Api\V1;

use App\Models\User;
use App\Models\Profile;
use Orion\Http\Requests\Request;
use Illuminate\Database\Eloquent\Model;
use Orion\Concerns\DisableAuthorization;
use Orion\Http\Controllers\RelationController;
use App\Http\Requests\Api\V1\UsersProfileRequest;
use App\Http\Resources\Api\V1\UsersProfileResource;

class UsersProfileController extends RelationController
{
    use DisableAuthorization;

    protected $model = User::class;

    protected $relation = 'profile';

    protected $request = UsersProfileRequest::class;

    protected $resource = UsersProfileResource::class;

    /**
     * The relations that are loaded by default together with a resource.
     *
     * @return array
     */
    public function alwaysIncludes() : array
    {
        return ['user', 'media'];
    }

    /**
     * The attributes that are used for filtering.
     *
     * @return array
     */
    public function filterableBy() : array
    {
        return [
            'user_id',
            'first_name',
            'last_name',
            'gender',
            'nationality',
            'profile_type',
            'availability_on_demand',
            'per_hour',
            'created_at',
        ];
    }

    /**
     * The hook is executed before creating or updating a relation resource.
     *
     * @param Request $request
     * @param User $user
     * @param Profile $profile
     * @return mixed
     */
    protected function beforeStore(Request $request, Model $user, Model $profile)
    {
        if ($user->profile) {
            return response()->json(['message' => 'Profile already exists.'], 422);
        }
    }

    /**
     * The hook is executed after creating or updating a relation resource.
     *
     * @param Request $request
     * @param User $user
     * @param Profile $profile
     * @return mixed
     */
    protected function afterSave(Request $request, Model $user, Model $profile)
    {
        if ($request->has('avatar')) {
            $profile->addMedia($request->avatar)->toMediaCollection(Profile::COLLECTION_NAME);
        }

        if ($request->has('username')) {
            $user->update($request->only('username'));
        }
    }
}
