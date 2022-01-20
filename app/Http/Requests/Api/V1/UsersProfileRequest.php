<?php

namespace App\Http\Requests\Api\V1;

use App\Models\Profile;
use Illuminate\Validation\Rule;
use Orion\Http\Requests\Request;

class UsersProfileRequest extends Request
{
    public function commonRules(): array
    {
        return [
            'first_name' => 'required',
            'last_name' => 'required',
            'gender' => ['required', Rule::in(Profile::GENDERS)],
            'nationality' => 'required',
            'profile_type' => ['required', Rule::in(Profile::TYPES)],
            'availability_on_demand' => 'required_if:profile_type,'.Profile::SELLER,
            'per_hour' => 'required_if:profile_type,'.Profile::SELLER,
        ];
    }

    public function storeRules(): array
    {
        return [
            'avatar' => 'required | image',
        ];
    }

    public function updateRules(): array
    {
        return [
            'avatar' => 'nullable | image',
        ];
    }
}
