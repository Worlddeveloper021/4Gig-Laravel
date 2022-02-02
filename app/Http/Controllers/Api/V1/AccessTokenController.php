<?php

namespace App\Http\Controllers\Api\V1;

use Twilio\Jwt\AccessToken;
use Illuminate\Http\Request;
use Twilio\Jwt\Grants\VideoGrant;
use Twilio\Jwt\Grants\VoiceGrant;
use App\Http\Controllers\Controller;

class AccessTokenController extends Controller
{
    public function voice(Request $request)
    {
        $account_sid = config('twilio.twilio.connections.twilio.sid');
        $auth_token = config('twilio.twilio.connections.twilio.token');

        $token = new AccessToken($account_sid, $account_sid, $auth_token, 3600);

        // Grant access to Voice
        $grant = new VoiceGrant();
        $token->addGrant($grant);

        return response()->json([
            'token' => $token->toJWT(),
        ]);
    }

    public function video(Request $request)
    {
        $account_sid = config('twilio.twilio.connections.twilio.sid');
        $auth_token = config('twilio.twilio.connections.twilio.token');
        $room = $request->get('room', 'test_room');

        $token = new AccessToken($account_sid, $account_sid, $auth_token, 3600);

        // Grant access to Video
        $grant = new VideoGrant();
        $grant->setRoom($room);
        $token->addGrant($grant);

        return response()->json([
            'token' => $token->toJWT(),
        ]);
    }
}
