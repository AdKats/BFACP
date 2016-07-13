<?php

return [

    /*
    |--------------------------------------------------------------------------
    | API Stuff
    |--------------------------------------------------------------------------
    |
    | Set the public key, private key and site id provided by Pusher
    |
    */
    'app_key'    => env('PUSHER_KEY'),
    'app_secret' => env('PUSHER_SECRET'),
    'app_id'     => env('PUSHER_APP_ID'),
    /*
    |--------------------------------------------------------------------------
    | Options
    |--------------------------------------------------------------------------
    |
    | Various options.
    |
    */
    'options'    => [

        'scheme'    => 'http', // e.g. http or https
        'host' => env('PUSHER_HOST', 'api.pusherapp.com'),
        // the host e.g. api.pusherapp.com. No trailing forward slash.
        'port'      => 80, // the http port
        'timeout'   => 30, // the HTTP timeout
        'encrypted' => true, // quick option to use scheme of https and port 443.
        'debug'     => false, // You can optionally turn on debugging for all requests by setting debug to true.
        'cluster'   => env('PUSHER_CLUSTER', 'mt1'),

    ],

];
