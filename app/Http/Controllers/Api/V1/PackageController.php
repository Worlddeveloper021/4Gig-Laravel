<?php

namespace App\Http\Controllers\Api\V1;

use App\Models\Package;
use App\Models\Profile;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\Api\V1\PackageResource;

class PackageController extends Controller
{
    public function show(Profile $profile)
    {
        $packages = $profile->packages;

        return response()->json(PackageResource::collection($packages));
    }

    public function store(Request $request)
    {
        $validated_data = $request->validate([
            'price' => 'required|numeric',
            'duration' => 'required|numeric',
            'description' => 'required|string',
            'on_demand' => 'required|string',
        ]);

        if (! $profile = $request->user()->profile) {
            return $this->validationError('profile', 'Profile not found');
        }

        $profile->packages()->create($validated_data);

        return response()->json([
            'message' => 'Package created successfully',
        ]);
    }

    public function min_max_price()
    {
        return response()->json([
            'min_price' => Package::min('price'),
            'max_price' => Package::max('price'),
        ]);
    }
}
