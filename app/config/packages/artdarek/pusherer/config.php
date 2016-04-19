<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Pusher Config
    |--------------------------------------------------------------------------
    |
    | Pusher is a simple hosted API for quickly, easily and securely adding
    | realtime bi-directional functionality via WebSockets to web and mobile
    | apps, or any other Internet connected device.
    |
    | NOTE: The options debug, host, port and timeout is deprecated.
    | Please use this values inside the options field.
    */

    /**
     * App id
     */
    'app_id'  => getenv('PUSHER_APP_ID'),
    /**
     * App Key
     */
    'key'     => getenv('PUSHER_APP_KEY'),
    /**
     * App Secret
     */
    'secret'  => getenv('PUSHER_APP_SECRET'),
    /**
     * App Options
     * Available: scheme, host, port, timeout, encrypted
     */
    'options' => [
        'encrypted' => true,
    ],
];
