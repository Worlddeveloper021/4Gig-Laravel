<?php

return [
    'twilio' => [
        'default' => 'twilio',
        'connections' => [
            'twilio' => [
                /*
                |--------------------------------------------------------------------------
                | SID
                |--------------------------------------------------------------------------
                |
                | Your Twilio Account SID #
                |
                */
                'sid' => env('TWILIO_SID', 'ACe97c96a7611a588333916755f593816e'),

                /*
                |--------------------------------------------------------------------------
                | Access Token
                |--------------------------------------------------------------------------
                |
                | Access token that can be found in your Twilio dashboard
                |
                */
                'token' => env('TWILIO_TOKEN', 'afd22dab67cc9a7af5d4c02657110989'),

                /*
                |--------------------------------------------------------------------------
                | From Number
                |--------------------------------------------------------------------------
                |
                | The Phone number registered with Twilio that your SMS & Calls will come from
                |
                */
                'from' => env('TWILIO_FROM', '+18646741892'),
            ],
        ],
    ],
];
