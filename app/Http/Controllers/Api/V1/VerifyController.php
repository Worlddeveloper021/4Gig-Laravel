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

        $this->check_verify_code_is_correct($user, $validated_data['verify_code']);

        $user->markEmailAsVerified();

        return response()->json(['success' => true]);
    }

    private function check_verify_code_is_correct($user, $verify_code)
    {
        if (! $user->is_valid_verify_code($verify_code)) {
            $this->validationError('verify_code', 'Verify Code Is Incorrect');
        }
    }
}
