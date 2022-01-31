<?php

namespace App\Http\Controllers\Api\V1;

use App\Models\Profile;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\Api\V1\ReviewResource;

class ReviewController extends Controller
{
    public function store(Request $request, Profile $profile)
    {
        $request->validate([
            'review' => 'required',
            'rate' => 'required|between:1,5',
        ]);

        if (! $customer = auth()->user()->customer) {
            return $this->validationError('customer', 'Customer not found');
        }

        $customer->reviews()->create([
            'profile_id' => $profile->id,
            'review' => $request->review,
            'rate' => $request->rate,
        ]);

        return response()->json([
            'message' => 'Review successfully stored',
        ]);
    }

    public function show(Profile $profile)
    {
        $reviews = $profile->reviews;

        return response()->json(ReviewResource::collection($reviews->load('customer')));
    }
}
