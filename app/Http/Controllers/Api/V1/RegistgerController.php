<?php

namespace App\Http\Controllers\Api\V1;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class RegistgerController extends Controller
{
    public function __invoke(Request $request)
    {
        $request->validate([
            'email' => 'required|email|unique:users',
            'password' => 'required|confirmed|min:6',
            'device_name' => 'nullable',
        ]);

        $user = User::create([
            'email' => $request->email,
            'password' => bcrypt($request->password),
        ]);

        $user->sendEmailVerificationNotification();

        $token = $user->createToken($request->get('device_name', 'test-token'))->plainTextToken;

        return response()->json(['token' => $token]);
    }
}
