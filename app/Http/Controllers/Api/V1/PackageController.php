<?php

namespace App\Http\Controllers\Api\V1;

use App\Models\Package;
use App\Models\Profile;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
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

        $profile->packages()->create(array_merge($validated_data, ['status' => Package::STATUS_ACTIVE]));

        return response()->json([
            'message' => 'Package created successfully',
        ]);
    }

    public function update(Request $request, Package $package)
    {
        $validated_data = $request->validate([
            'price' => 'sometimes|numeric',
            'duration' => 'sometimes|numeric',
            'description' => 'sometimes|string',
            'on_demand' => 'sometimes|string',
            'status' => ['sometimes', Rule::in(Package::STATUSES)],
        ]);

        $package->update($validated_data);

        return response()->json(PackageResource::make($package));
    }
}
