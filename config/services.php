<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    |
    | This file is for storing the credentials for third party services such
    | as Mailgun, Postmark, AWS and more. This file provides the de facto
    | location for this type of information, allowing packages to have
    | a conventional file to locate the various service credentials.
    |
    */

    'mailgun' => [
        'domain' => env('MAILGUN_DOMAIN'),
        'secret' => env('MAILGUN_SECRET'),
        'endpoint' => env('MAILGUN_ENDPOINT', 'api.mailgun.net'),
    ],

    'postmark' => [
        'token' => env('POSTMARK_TOKEN'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],
    'google' => [
        'client_id' => '475923740117-fpn64p2almld4gf271adcah9gfh89bor.apps.googleusercontent.com',
        'client_secret' => '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',
        'redirect' => env('GOOGLE_REDIRECT_URI'),
    ],
    'facebook' => [
        'client_id' => env('FACEBOOK_CLIENT_ID'),
        'client_secret' => env('FACEBOOK_CLIENT_SECRET'),
        'redirect' => env('FACEBOOK_REDIRECT_URI'),
    ],
    'linkedin' => [
        'client_id' => '77zjrhn8mshjbf',
        'client_secret' => 'yGBz1bol37TidjJX',
        'redirect' => env('LINKEDIN_REDIRECT_URI'),
    ],
    'firebase' => [
        'server_key' => 'AAAAbs9D9dU:APA91bFsQX46kI05jDRxxAmTzDtE_LjaNAKz5yP0EIg2P8o-onLOnCgGXh1BVGmht56RSaJWM9HPG0v69Ge5qjFeid08bRiF5cp9qreM3tQZjOnUEuTPoi8Tk1UINT90i0PxrucI3XEQ',
    ],
    'agora' => [
        'app_id' => 'b7a24677b9e84f03aa6c0175c1651a2d',
        'app_certificate' => '1779875a496a406fa14f9aa36dd35505',
    ],
];
