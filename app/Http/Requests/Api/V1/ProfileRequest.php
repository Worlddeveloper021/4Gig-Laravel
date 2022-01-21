<?php

namespace App\Http\Requests\Api\V1;

use App\Models\Profile;
use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;

class ProfileRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'first_name' => 'required',
            'last_name' => 'required',
            'username' => ['nullable', Rule::unique('users', 'username')->ignore($this->user()->id)],
            'nationality' => 'required',
            'birth_date' => 'required',
            'gender' => ['required', Rule::in(Profile::GENDERS)],
            'availability_on_demand' => 'required',
            'per_hour' => 'required',
            'avatar' => 'nullable | image',
            'skills' => 'nullable | array',
            'spoken_languages' => 'nullable | array',
        ];
    }
}
