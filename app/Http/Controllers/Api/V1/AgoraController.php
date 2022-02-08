<?php

namespace App\Http\Controllers\Api\V1;

use Str;
use Illuminate\Http\Request;
use App\Agora\RtcTokenBuilder;
use App\Http\Controllers\Controller;

class AgoraController extends Controller
{
    public function create(Request $request)
    {
        $access_token = RtcTokenBuilder::buildTokenWithUid(
            config('services.agora.app_id'),
            config('services.agora.app_certificate'),
            Str::random(),
            0,
            RtcTokenBuilder::RolePublisher,
            time() + 3600,
        );

        return response()->json([
            'access_token' => $access_token,
        ]);
    }
}
