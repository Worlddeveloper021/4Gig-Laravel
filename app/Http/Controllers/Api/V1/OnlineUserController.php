<?php

namespace App\Http\Controllers\Api\V1;

use App\Models\User;
use App\Http\Controllers\Controller;
use App\Http\Resources\Api\V1\UserResource;

class OnlineUserController extends Controller
{
    public function index()
    {
        $users = User::get()->filter(function ($user) {
            return $user->is_online();
        });

        return response()->json(UserResource::collection($users));
    }
}
