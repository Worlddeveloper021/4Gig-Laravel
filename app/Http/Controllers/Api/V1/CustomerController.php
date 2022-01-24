<?php

namespace App\Http\Controllers\Api\V1;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class CustomerController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'mobile' => 'required|unique:users,mobile',
        ]);

        $user = User::create([
            'mobile' => $request->mobile,
            'verify_code' => rand(100000, 1000000 - 1),
        ]);

        $user->customer()->create([
            'name' => $request->name,
        ]);

        $user->notify(new \App\Notifications\VerifyCustomerNotification($user));

        return response()->json([
            'message' => 'SMS successfully sent',
        ]);
    }

    public function verify(Request $request)
    {
        $request->validate([
            'mobile' => 'required',
            'verify_code' => 'required',
        ]);

        $user = User::where('mobile', $request->mobile)->first();

        if ($user->is_valid_verify_code($request->verify_code)) {
            $user->mobile_verified_at = now();
            $user->save();

            $token = $user->createToken('test-token')->plainTextToken;

            return response()->json([
                'token' => $token,
                'message' => 'Mobile number verified successfully',
            ]);
        }

        return $this->validationError('verify_code', 'Invalid verification code');
    }
}
