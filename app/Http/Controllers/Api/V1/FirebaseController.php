<?php

namespace App\Http\Controllers\Api\V1;

use App\Models\User;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Http;

class FirebaseController extends Controller
{
    public function send(User $user)
    {
        if (is_null($user->fcm_key)) {
            return $this->validationError('fcm_key', "the target user doesn't have a fcm_key");
        }

        $firebaseToken = [$user->fcm_key];

        $data = [
            'registration_ids' => $firebaseToken,
            'notification' => [
                'title' => 'Test Title Push Notification',
                'body' => 'Test Body Push Notification. Hello World!',
                'content_available' => true,
                'priority' => 'high',
            ],
        ];

        $response = Http::withToken(config('services.firebase.server_key'), 'key=')
            ->contentType('application/json')
            ->post('https://fcm.googleapis.com/fcm/send', $data);

        if ($response->status() !== 200) {
            return response()->json(['message' => 'Failed to send push notification', 'body' => $response->json()], $response->status());
        }

        if ($response->json()['failure'] > 0) {
            return response()->json(['message' => 'Failed to send push notification', 'body' => $response->json()], $response->status());
        }

        return response()->json(['message' => 'Push notification sent successfully'], 200);
    }
}
