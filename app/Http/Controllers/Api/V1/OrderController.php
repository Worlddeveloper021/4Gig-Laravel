<?php

namespace App\Http\Controllers\Api\V1;

use Str;
use App\Models\Order;
use App\Models\Profile;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Http;
use App\Http\Resources\Api\V1\OrderResource;
use Illuminate\Http\Exceptions\HttpResponseException;

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
        if (true) { // this is for check payment status in later
            $channel_name = $this->create_channel_name();

            $order->update(['channel_name' => $channel_name]);

            $this->send_firebase_push_notification($order);
        }

        return response()->json(new OrderResource($order), 200);
    }

    private function create_channel_name()
    {
        $channel_name = Str::random();

        if (Order::where('channel_name', $channel_name)->exists()) {
            $channel_name = $this->create_channel_name();
        }

        return $channel_name;
    }

    private function send_firebase_push_notification(Order $order)
    {
        $target_user = $order->profile->user;
        if (is_null($target_user->fcm_key)) {
            return $this->validationError('fcm_key', "the target user doesn't have a fcm_key");
        }

        $firebaseToken = [$target_user->fcm_key];
        $body = json_encode(['order_id' => $order->id]);

        $data = [
            'registration_ids' => $firebaseToken,
            'notification' => [
                'title' => 'This is Order Notification',
                'body' => $body,
                'content_available' => true,
                'priority' => 'high',
            ],
        ];

        $response = Http::withToken(config('services.firebase.server_key'), 'key=')
            ->contentType('application/json')
            ->post('https://fcm.googleapis.com/fcm/send', $data);

        if ($response->status() !== 200 || $response->json()['failure'] > 0) {
            throw new HttpResponseException(
                response()->json(['message' => 'Failed to send push notification', 'body' => $response->json()], $response->status())
            );
        }

        return true;
    }
}
