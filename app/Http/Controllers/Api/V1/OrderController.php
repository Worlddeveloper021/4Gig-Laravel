<?php

namespace App\Http\Controllers\Api\V1;

use DB;
use Str;
use App\Models\Order;
use App\Paypal\PayPal;
use App\Models\Profile;
use Illuminate\Http\Request;
use App\Agora\RtcTokenBuilder;
use Illuminate\Validation\Rule;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Http;
use App\Http\Resources\Api\V1\OrderResource;
use Illuminate\Http\Exceptions\HttpResponseException;

class OrderController extends Controller
{
    public function show(Order $order)
    {
        return response()->json(OrderResource::make($order));
    }

    public function store(Request $request, Profile $profile)
    {
        $validated_data = $request->validate([
            'package_id' => 'required|exists:packages,id',
            'payment_id' => 'required',
            'call_type' => ['required', Rule::in(Order::CALL_TYPES)],
        ]);

        $package = $profile->packages()->find($validated_data['package_id']);

        if (! $package) {
            return $this->validationError('package_id', 'The selected package is not belongs to this profile.');
        }

        if (! $customer = auth()->user()->customer) {
            return $this->validationError('user', 'The logged in user is not a customer.');
        }

        if ($profile->user->fcm_key == $customer->user->fcm_key) {
            return $this->validationError('customer', 'You can not order for yourself.');
        }

        try {
            DB::beginTransaction();

            $order = Order::create([
                'customer_id' => $customer->id,
                'profile_id' => $profile->id,
                'package_id' => $package->id,
                'duration' => $package->duration,
                'price' => $package->price,
                'payment_id' => $validated_data['payment_id'],
                'status' => Order::STATUS_PENDING,
                'call_type' => $validated_data['call_type'],
            ]);

            $payment_status = $this->get_payment_status($order->payment_id);
            $order->update(['payment_status' => $payment_status]);

            if ($payment_status === Order::PAYMENT_STATUS_APPROVED) {
                $channel_name = "order_channel_{$order->id}";
                // $channel_name = $this->create_channel_name();

                $access_token = $this->create_access_token($channel_name, 86400); // TODO: one day in seconds for testing
                // $access_token = $this->create_access_token($channel_name, $order->duration * 60); // duration in seconds

                $order->update([
                    'channel_name' => $channel_name,
                    'access_token' => $access_token,
                ]);

                $this->send_firebase_push_notification($order);
            }
            DB::commit();

            return response()->json(new OrderResource($order), 200);
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    public function update_status(Order $order, Request $request)
    {
        $validated_data = $request->validate([
            'status' => ['required', Rule::in(Order::STATUSES)],
        ]);

        $order->update($validated_data);

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

        $data = [
            'registration_ids' => $firebaseToken,
            'notification' => [
                'title' => "You Have {$order->call_type_name} Call",
                'body' => "You have a new call from {$order->customer->last_name}",
                'content_available' => true,
                'priority' => 'high',
            ],
            'data' => [
                'order_id' => $order->id,
                'call_type' => $order->call_type_name,
                'access_token' => $order->access_token,
                'channel_name' => $order->channel_name,
                'full_name' => "{$order->customer->first_name} {$order->customer->last_name}",
                'agora_app_id' => config('services.agora.app_id'),
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

    private function create_access_token($channel_name, $duration)
    {
        return RtcTokenBuilder::buildTokenWithUid(
            config('services.agora.app_id'),
            config('services.agora.app_certificate'),
            $channel_name,
            0,
            RtcTokenBuilder::RolePublisher,
            time() + $duration,
        );
    }

    private function get_payment_status($payment_id)
    {
        $paypal = new PayPal;
        $paypal->getAccessToken();

        $response = $paypal->getPaymentDetails($payment_id);

        return $response['state'] ?? Order::PAYMENT_STATUS_CREATED;
    }
}
