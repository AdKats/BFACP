<?php

/**
 * Array of IP addresses that are allowed to see debug information. To add more seprate each entry
 * by a comma and enclose in double or single quotes. Only IPv4 Addressed supported.
 *
 * Default: 127.0.0.1 (localhost)
 * @var array
 */
$allowedIps = ['127.0.0.1'];

return [

    /**
     * Do not change these settings unless
     * you know what you're doing
     */
    'APP_ENV'        => 'production',
    'APP_DEBUG'      => in_array($_SERVER['REMOTE_ADDR'], $allowedIps) ? true : false,
    'SESSION_DRIVER' => 'file',
    'CACHE_DRIVER'   => 'file',

    /**
     * Database Settings
     */
    'DB_HOST'        => 'localhost',
    'DB_USER'        => 'root',
    'DB_PASS'        => '',
    'DB_NAME'        => 'mydatabase',

    /**
     * Set your app key here
     */
    'APP_KEY'        => '',
];
