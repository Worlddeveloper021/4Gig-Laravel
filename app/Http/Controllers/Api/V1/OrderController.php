<?php

namespace App\Http\Controllers\Api\V1;

use App\Models\Order;
use App\Models\Profile;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class OrderController extends Controller
{
    public function store(Request $request, Profile $profile)
    {
        $validated_data = $request->validate([
            'package_id' => 'required|exists:packages,id',
            'payment_id' => 'required',
        ]);

        $package = $profile->packages()->find($validated_data['package_id']);

        if (! $package) {
            return $this->validationError('package_id', 'The selected package is not belongs to this profile.');
        }

        if (! $customer = auth()->user()->customer) {
            return $this->validationError('user', 'The logged in user is not a customer.');
        }

        $order = Order::create([
            'customer_id' => $customer->id,
            'profile_id' => $profile->id,
            'package_id' => $package->id,
            'duration' => $package->duration,
            'price' => $package->price,
            'payment_id' => $validated_data['payment_id'],
            'status' => Order::STATUS_PENDING,
        ]);

        // TODO: check payment status

        //TODO: if payment status is done, change order to create channel name

        // TODO: return stored order
    }
}
