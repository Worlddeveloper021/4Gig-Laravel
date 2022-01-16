<?php

namespace App\Http\Controllers\Api\V1;

use DB;
use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;

class ForgotPasswordController extends Controller
{
    public function request(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:users,email',
        ]);

        $user = User::where('email', $request->email)->first();

        $user->requestResetPassword();

        return response()->json([
            'message' => 'We Sent You An Email',
            'success' => true,
        ]);
    }

    public function verify(Request $request)
    {
        $request->validate([
           'email' => 'required | email | exists:password_resets,email',
           'token' => 'required | string | size:6',
        ]);

        $password_reset = DB::table('password_resets')->where('email', $request->email)->first();

        $this->token_is_correct($password_reset, $request->token);

        return response()->json([
            'message' => 'Token Verified',
            'success' => true,
        ]);
    }

    public function reset(Request $request)
    {
        $request->validate([
            'email' => 'required | email | exists:users,email',
            'password' => 'required | string | min:6 | confirmed',
        ]);

        User::where('email', $request->email)->update([
            'password' => Hash::make($request->password),
        ]);

        return response()->json([
            'message' => 'Password Reset Successfully',
            'success' => true,
        ]);
    }

    private function token_is_correct($password_reset, $token)
    {
        if ($password_reset->token != $token) {
            $this->validationError('token', 'Token Is Incorrect');
        }
    }
}
