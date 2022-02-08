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
            'email' => 'required',
            'password' => 'required|min:6',
            'device_name' => 'nullable',
            'fcm_key' => 'nullable',
        ]);

        $user = User::whereEmail($validated_data['email'])->orWhere('mobile', $validated_data['email'])->first();

        $this->check_user_exists($user);

        if ($request->has('fcm_key')) {
            $user->update(
                $request->only('fcm_key')
            );
        }

        $this->check_password_is_correct($user, $validated_data['password']);

        $token = $user->createToken($request->get('device_name', 'test-token'))->plainTextToken;

        return response()->json(['token' => $token]);
    }

    private function check_user_exists($user)
    {
        if (! $user) {
            $this->validationError('email', 'Email or Password Is Incorrect');
        }
    }

    private function check_password_is_correct($user, $password)
    {
        if (! Hash::check($password, $user->password)) {
            $this->validationError('email', 'Email or Password Is Incorrect');
        }
    }
}
