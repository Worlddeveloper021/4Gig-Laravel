<?php

namespace App\Http\Controllers\Api\V1;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class VerifyController extends Controller
{
    public function __invoke(Request $request)
    {
        $validated_data = $request->validate([
            'verify_code' => 'required | string | size:6',
        ]);

        /** @var \App\Models\User $user */
        $user = auth()->user();

        if ($user->is_valid_verify_code($validated_data['verify_code'])) {
            $user->markEmailAsVerified();

            return response()->json(['success' => true]);
        }

        return response()->json(['errors' => ['verify_code' => 'The verify code is invalid.']], 422);
    }
}
