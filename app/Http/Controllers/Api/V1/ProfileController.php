<?php

namespace App\Http\Controllers\Api\V1;

use App\Models\Profile;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\ProfileRequest;
use App\Http\Resources\Api\V1\ProfileResource;

class ProfileController extends Controller
{
    public function store(ProfileRequest $request)
    {
        $profile = Profile::updateOrCreate(['user_id' => auth()->id()], $request->validated());

        if ($request->has('username')) {
            auth()->user()->update(['username' => $request->username]);
        }

        if ($request->has('avatar')) {
            $profile->addMediaFromRequest('avatar')->toMediaCollection(Profile::COLLECTION_NAME);
        }

        if ($request->has('skills')) {
            $profile->skills()->delete();
            foreach ($request->get('skills') as $skill) {
                $profile->skills()->create(['name' => $skill]);
            }
        }

        if ($request->has('spoken_languages')) {
            $profile->spokenLanguages()->delete();
            foreach ($request->get('spoken_languages') as $language) {
                $profile->spokenLanguages()->create(['name' => $language]);
            }
        }

        return response()->json(new ProfileResource($profile), 200);
    }
}
