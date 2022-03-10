<?php

namespace App\Http\Controllers\Api\V1;

use App\Models\Plan;
use App\Paypal\PayPal;
use App\Models\Subscription;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\Api\V1\SubscriptionResource;

class SubscriptionController extends Controller
{
    public function store(Request $request)
    {
        $validated_data = $request->validate([
            'plan_id' => 'required|exists:plans,id',
            'payment_id' => 'required|string',
        ]);

        if (! $profile = auth()->user()->profile) {
            return $this->validationError('message', 'Profile not found');
        }

        if (! $plan = Plan::find($validated_data['plan_id'])) {
            return $this->validationError('message', 'Plan not found');
        }

        $data = [
            'profile_id' => $profile->id,
            'plan_id' => $plan->id,
            'price' => $plan->price,
            'duration' => $plan->duration,
            'payment_id' => $validated_data['payment_id'],
            'payment_status' => Subscription::PAYMENT_STATUS_CREATED,
            'status' => Subscription::STATUS_INACTIVE,
            'start_date' => now()->startOfDay(),
            'end_date' => now()->addDays($plan->duration)->endOfDay(),
        ];

        $subscription = $profile->subscriptions()->create($data);

        $payment_status = $this->get_payment_status($validated_data['payment_id']);
        $subscription->update(['payment_status' => $payment_status]);

        if ($payment_status === Subscription::PAYMENT_STATUS_APPROVED) {
            $subscription->update(['status' => Subscription::STATUS_ACTIVE]);
        }

        return response()->json(SubscriptionResource::make($subscription->refresh()));
    }

    public function show()
    {
        if (! $profile = auth()->user()->profile) {
            return $this->validationError('message', 'Profile not found');
        }

        return response()->json(SubscriptionResource::collection($profile->subscriptions()->paginate())->response()->getData());
    }

    private function get_payment_status($payment_id)
    {
        $paypal = new PayPal;
        $paypal->getAccessToken();

        $response = $paypal->getPaymentDetails($payment_id);

        return $response['state'] ?? Subscription::PAYMENT_STATUS_CREATED;
    }
}
