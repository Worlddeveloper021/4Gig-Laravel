<?php

namespace App\Http\Controllers\Api\V1;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;

class LoginController extends Controller
{
    public function __invoke(Request $request)
    {
        $validated_data = $request->validate([
            'email' => 'required|email',
            'password' => 'required|min:6',
        ]);

        $user = User::whereEmail($validated_data['email'])->first();

        if (! $user) {
            return response()->json(['errors' => ['email' => 'Email or Password Is Incorrect']], 422);
        }

        if (! Hash::check($validated_data['password'], $user->password)) {
            return response()->json(['errors' => ['email' => 'Email or Password Is Incorrect']], 422);
        }

        $token = $user->createToken('test-token')->plainTextToken;

        return response()->json(['token' => $token]);
    }
}
