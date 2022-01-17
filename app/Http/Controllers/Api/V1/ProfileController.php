<?php

namespace App\Http\Controllers\Api\V1;

use App\Models\Profile;
use Orion\Http\Requests\Request;
use Orion\Http\Controllers\Controller;
use Orion\Concerns\DisableAuthorization;
use App\Http\Requests\Api\V1\ProfileRequest;
use App\Http\Resources\Api\V1\ProfileResource;

class ProfileController extends Controller
{
    use DisableAuthorization;

    protected $model = Profile::class;

    protected $request = ProfileRequest::class;

    protected $resource = ProfileResource::class;

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
     * @param Request $request
     * @param Profile $profile
     */
    protected function beforeSave(Request $request, $profile)
    {
        $profile->user()->associate(auth()->user());
    }

    /**
     * @param Request $request
     * @param Profile $profile
     */
    protected function afterSave(Request $request, $profile)
    {
        if ($request->has('avatar')) {
            $profile->addMedia($request->avatar)->toMediaCollection(Profile::COLLECTION_NAME);
        }
    }
}
