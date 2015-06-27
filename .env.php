<?php

/**
 * Array of IP addresses that are allowed to see debug information. To add more seprate each entry
 * by a comma and enclose in double or single quotes. Only IPv4 Addressed supported.
 *
 * Default: 127.0.0.1 (localhost)
 * @var array
 */
$allowedIps = ['127.0.0.1'];

if (isset($_SERVER['REMOTE_ADDR'])) {
    // Check for cloudflare use
    if (isset($_SERVER['HTTP_CF_CONNECTING_IP'])) {
        $ip = $_SERVER['HTTP_CF_CONNECTING_IP'];
    } else {
        $ip = $_SERVER['REMOTE_ADDR'];
    }

    if (in_array($ip, $allowedIps)) {
        $debug = true;
    } else {
        $debug = false;
    }
} else {
    $debug = false;
}

/**
 * If Memcached exists then use that instead for better preformance.
 */
if (class_exists('Memcached')) {
    $session_driver = 'memcached';
    $cache_driver   = 'memcached';
} else {
    $session_driver = 'database';
    $cache_driver   = 'file';
}

return [

    /**
     * Do not change these settings unless
     * you know what you're doing
     */
    'APP_ENV'        => 'production',
    'APP_DEBUG'      => $debug,

    // Supported: "file", "database", "apc", "memcached", "redis", "array"
    'SESSION_DRIVER' => $session_driver,
    'CACHE_DRIVER'   => $cache_driver,

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
    'APP_KEY'        => 'YourSecretKey!!!'
];
