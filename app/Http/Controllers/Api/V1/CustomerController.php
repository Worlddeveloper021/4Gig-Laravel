<?php

namespace App\Http\Controllers\Api\V1;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;

class CustomerController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'first_name' => 'required',
            'last_name' => 'required',
            'email' => 'nullable|email|unique:users',
            'mobile' => 'required|unique:users,mobile',
            'password' => 'required | min:6',
        ]);

        $user = User::create([
            'mobile' => $request->mobile,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'verify_code' => rand(100000, 1000000 - 1),
        ]);

        $user->customer()->create(
            $request->only(['first_name', 'last_name'])
        );

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

        if (! $user) {
            return response()->json([
                'message' => 'User not found',
            ], 404);
        }

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

    public function store_card(Request $request)
    {
        $validated_data = $request->validate([
            'name' => 'required',
            'card_number' => 'required',
            'expiry_date' => 'required',
            'cvc' => 'required',
        ]);

        if (! $customer = auth()->user()->customer) {
            return $this->validationError('customer', 'Customer not found');
        }

        if ($customer->card()->exists()) {
            return $this->validationError('card', 'Card already exists');
        }

        $customer->card()->create($validated_data);

        return response()->json([
            'message' => 'Card successfully stored',
        ]);
    }

    public function login(Request $request)
    {
        $request->validate([
            'mobile' => 'required',
            'password' => 'required',
            'fcm_key' => 'nullable',
        ]);

        $user = User::where('mobile', $request->mobile)->first();

        if (! $user) {
            return $this->validationError('mobile', 'Mobile or Password is Incorrect');
        }

        if ($request->has('fcm_key')) {
            $user->update(
                $request->only('fcm_key')
            );
        }

        if (! Hash::check($request->password, $user->password)) {
            return $this->validationError('mobile', 'Mobile or Password is Incorrect');
        }

        if (! $user->mobile_verified_at) {
            return $this->validationError('mobile', 'Mobile number not verified');
        }

        $token = $user->createToken(
            $request->get('device_name', 'test-token')
        )->plainTextToken;

        return response()->json([
            'token' => $token,
        ]);
    }
}
