<?php

namespace App\Http\Controllers\Api\V1;

use App\Models\Profile;
use App\Http\Controllers\Controller;
use App\Http\Resources\Api\V1\ProfileResource;

class OnlineUserController extends Controller
{
    public function index()
    {
        $profiles = Profile::get()->filter(function ($profile) {
            return $profile->user->is_online();
        });

        return response()->json(ProfileResource::collection($profiles));
    }
}
